<?php
/**
 * PlanillaController - Controlador de Planillas (Remuneraciones)
 */

class PlanillaController extends Controller {
    private $planillaModel;
    private $docenteModel;
    private $logModel;
    
    public function __construct() {
        $this->planillaModel = new Planilla();
        $this->docenteModel = new Docente();
        $this->logModel = new LogActividad();
    }
    
    /**
     * Listado general de planillas del mes
     */
    public function index() {
        $this->requireAuth();
        
        $mes = isset($_GET['mes']) ? intval($_GET['mes']) : intval(date('m'));
        $anio = isset($_GET['anio']) ? intval($_GET['anio']) : intval(date('Y'));
        
        $planillas = $this->planillaModel->getByMesAnio($mes, $anio);
        
        // Calcular estadísticas
        $totalGenerado = 0;
        $totalPagado = 0;
        $totalPendiente = 0;
        $cantGeneradas = 0;
        $cantPagadas = 0;
        
        foreach ($planillas as $p) {
            $totalGenerado += $p['total_pagar'];
            if ($p['estado'] === 'pagada') {
                $totalPagado += $p['total_pagar'];
                $cantPagadas++;
            } else if ($p['estado'] === 'generada') {
                $totalPendiente += $p['total_pagar'];
                $cantGeneradas++;
            }
        }
        
        $data = [
            'planillas' => $planillas,
            'mes' => $mes,
            'anio' => $anio,
            'stats' => [
                'total_generado' => $totalGenerado,
                'total_pagado' => $totalPagado,
                'total_pendiente' => $totalPendiente,
                'cant_generadas' => $cantGeneradas,
                'cant_pagadas' => $cantPagadas,
                'total_personal' => count($planillas)
            ]
        ];
        
        $this->view('planillas/index', $data);
    }
    
    /**
     * Dashboard de previsualización y generación masiva de planillas
     */
    public function generate() {
        $this->requireRole(['admin', 'supervisor']);
        
        $mes = isset($_GET['mes']) ? intval($_GET['mes']) : intval(date('m'));
        $anio = isset($_GET['anio']) ? intval($_GET['anio']) : intval(date('Y'));
        
        $docentes = $this->docenteModel->getAll(['estado' => 'activo'], 'apellidos ASC, nombres ASC');
        $previaPlanillas = [];
        
        foreach ($docentes as $d) {
            // Verificar si ya tiene planilla guardada para evitar duplicar visualmente
            $guardada = $this->planillaModel->getByDocenteMesAnio($d['id'], $mes, $anio);
            
            // Calcular borrador
            $calculos = $this->planillaModel->calcularDetallesMensuales($d['id'], $mes, $anio);
            
            $previaPlanillas[] = [
                'docente' => $d,
                'calculos' => $calculos,
                'registrada' => ($guardada !== null),
                'planilla_id' => $guardada ? $guardada['id'] : null,
                'estado' => $guardada ? $guardada['estado'] : 'no_generada'
            ];
        }
        
        $data = [
            'previaPlanillas' => $previaPlanillas,
            'mes' => $mes,
            'anio' => $anio
        ];
        
        $this->view('planillas/generate', $data);
    }
    
    /**
     * Guardar/Generar masivamente las planillas del mes
     */
    public function store() {
        $this->requireRole(['admin']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/planillas');
        }
        
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Error de seguridad: Token CSRF inválido';
            $this->redirect('/planillas');
        }
        
        $mes = intval($_POST['mes'] ?? date('m'));
        $anio = intval($_POST['anio'] ?? date('Y'));
        
        $docentes = $this->docenteModel->getAll(['estado' => 'activo']);
        $generadosCount = 0;
        
        foreach ($docentes as $d) {
            // Validar si ya existe
            $existente = $this->planillaModel->getByDocenteMesAnio($d['id'], $mes, $anio);
            
            if (!$existente) {
                // Calcular remuneraciones
                $calculos = $this->planillaModel->calcularDetallesMensuales($d['id'], $mes, $anio);
                
                $data = [
                    'docente_id' => $d['id'],
                    'mes' => $mes,
                    'anio' => $anio,
                    'sueldo_base' => $calculos['sueldo_base'],
                    'descuentos' => $calculos['descuentos'],
                    'bonificaciones' => $calculos['bonificaciones'],
                    'total_pagar' => $calculos['total_pagar'],
                    'estado' => 'generada'
                ];
                
                $this->planillaModel->create($data);
                $generadosCount++;
            }
        }
        
        if ($generadosCount > 0) {
            $this->logModel->registrar(
                $_SESSION['user_id'],
                'generar_planillas',
                'planillas',
                "Se generaron masivamente {$generadosCount} planillas para el periodo {$mes}/{$anio}"
            );
            $_SESSION['success'] = "Se generaron exitosamente {$generadosCount} planillas del mes.";
        } else {
            $_SESSION['warning'] = "No había planillas nuevas por generar para el periodo {$mes}/{$anio}.";
        }
        
        $this->redirect("/planillas?mes={$mes}&anio={$anio}");
    }
    
    /**
     * Previsualizar detalle de una planilla individual (Boleta en pantalla)
     */
    public function ver($id) {
        $this->requireAuth();
        
        $planilla = $this->planillaModel->getById($id);
        if (!$planilla) {
            $_SESSION['error'] = 'Planilla no encontrada';
            $this->redirect('/planillas');
        }
        
        $docente = $this->docenteModel->getById($planilla['docente_id']);
        
        // Obtener desglose de cálculos asistenciales del mes
        $detalles = $this->planillaModel->calcularDetallesMensuales($planilla['docente_id'], $planilla['mes'], $planilla['anio'], $planilla['sueldo_base']);
        
        $data = [
            'planilla' => $planilla,
            'docente' => $docente,
            'detalles' => $detalles
        ];
        
        $this->view('planillas/ver', $data);
    }
    
    /**
     * Marcar una planilla como Pagada
     */
    public function pagar($id) {
        $this->requireRole(['admin', 'supervisor']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/planillas');
        }
        
        $planilla = $this->planillaModel->getById($id);
        if ($planilla) {
            if ($this->planillaModel->update($id, ['estado' => 'pagada'])) {
                $docente = $this->docenteModel->getById($planilla['docente_id']);
                
                $this->logModel->registrar(
                    $_SESSION['user_id'],
                    'pagar_planilla',
                    'planillas',
                    "Planilla marcada como pagada para el docente {$docente['nombres']} {$docente['apellidos']} (Periodo {$planilla['mes']}/{$planilla['anio']})"
                );
                
                $_SESSION['success'] = 'Planilla marcada como pagada exitosamente.';
            } else {
                $_SESSION['error'] = 'Error al actualizar el estado de la planilla.';
            }
            $this->redirect("/planillas/ver/{$id}");
        } else {
            $_SESSION['error'] = 'Planilla no encontrada.';
            $this->redirect('/planillas');
        }
    }
    
    /**
     * Eliminar una planilla generada
     */
    public function delete($id) {
        $this->requireRole(['admin']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/planillas');
        }
        
        $planilla = $this->planillaModel->getById($id);
        if ($planilla) {
            $mes = $planilla['mes'];
            $anio = $planilla['anio'];
            $docente = $this->docenteModel->getById($planilla['docente_id']);
            
            if ($this->planillaModel->delete($id)) {
                $this->logModel->registrar(
                    $_SESSION['user_id'],
                    'eliminar_planilla',
                    'planillas',
                    "Planilla eliminada para docente {$docente['nombres']} {$docente['apellidos']} (Periodo {$mes}/{$anio})"
                );
                $_SESSION['success'] = 'Planilla eliminada exitosamente.';
            } else {
                $_SESSION['error'] = 'Error al eliminar la planilla.';
            }
            $this->redirect("/planillas?mes={$mes}&anio={$anio}");
        } else {
            $_SESSION['error'] = 'Planilla no encontrada.';
            $this->redirect('/planillas');
        }
    }
    
    /**
     * Exportar Boleta de Pago en PDF con FPDF
     */
    public function pdf($id) {
        $this->requireAuth();
        
        $planilla = $this->planillaModel->getById($id);
        if (!$planilla) {
            $_SESSION['error'] = 'Planilla no encontrada';
            $this->redirect('/planillas');
        }
        
        $docente = $this->docenteModel->getById($planilla['docente_id']);
        
        // Calcular desglose
        $detalles = $this->planillaModel->calcularDetallesMensuales($planilla['docente_id'], $planilla['mes'], $planilla['anio'], $planilla['sueldo_base']);
        
        // Obtener carrera
        $carreraNombre = 'Sin Carrera/Área';
        if ($docente['carrera_id']) {
            $carrera = (new Carrera())->getById($docente['carrera_id']);
            if ($carrera) {
                $carreraNombre = $carrera['nombre'];
            }
        }
        
        // Meses en español
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
            7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        $nombreMes = $meses[$planilla['mes']];
        
        // Generación del PDF
        require_once BASE_PATH . '/libs/fpdf/fpdf.php';
        
        $pdf = new FPDF();
        $pdf->AddPage();
        
        // Marco de la boleta
        $pdf->Rect(10, 10, 190, 277);
        
        // Cabecera / Empresa
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, utf8_decode('SIARH - BOLETA DE PAGO'), 0, 1, 'C');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 5, utf8_decode('Instituto Superior Tecnológico - Control de RRHH'), 0, 1, 'C');
        $pdf->Cell(0, 5, "Periodo Laboral: {$nombreMes} de {$planilla['anio']}", 0, 1, 'C');
        $pdf->Ln(5);
        
        // Línea divisoria
        $pdf->Line(10, 35, 200, 35);
        $pdf->Ln(5);
        
        // Datos del empleado
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(95, 6, utf8_decode('DATOS DEL DOCENTE'), 0, 0, 'L');
        $pdf->Cell(95, 6, utf8_decode('DETALLE DE LA BOLETA'), 0, 1, 'L');
        
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(35, 5, 'DNI:', 0, 0, 'L');
        $pdf->Cell(60, 5, $docente['dni'], 0, 0, 'L');
        $pdf->Cell(45, 5, utf8_decode('Código de Boleta:'), 0, 0, 'L');
        $pdf->Cell(50, 5, 'BOP-' . str_pad($planilla['id'], 6, '0', STR_PAD_LEFT), 0, 1, 'L');
        
        $pdf->Cell(35, 5, 'Docente:', 0, 0, 'L');
        $pdf->Cell(60, 5, utf8_decode($docente['apellidos'] . ', ' . $docente['nombres']), 0, 0, 'L');
        $pdf->Cell(45, 5, 'Estado de Pago:', 0, 0, 'L');
        $pdf->Cell(50, 5, strtoupper($planilla['estado']), 0, 1, 'L');
        
        $pdf->Cell(35, 5, utf8_decode('Código Empleado:'), 0, 0, 'L');
        $pdf->Cell(60, 5, $docente['codigo_empleado'], 0, 0, 'L');
        $pdf->Cell(45, 5, 'Fecha de Emisión:', 0, 0, 'L');
        $pdf->Cell(50, 5, date('d/m/Y', strtotime($planilla['created_at'])), 0, 1, 'L');
        
        $pdf->Cell(35, 5, utf8_decode('Área / Carrera:'), 0, 0, 'L');
        $pdf->Cell(60, 5, utf8_decode($carreraNombre), 0, 0, 'L');
        $pdf->Cell(45, 5, '', 0, 0, 'L');
        $pdf->Cell(50, 5, '', 0, 1, 'L');
        $pdf->Ln(8);
        
        // Tabla de Ingresos y Egresos (Estructura de Boleta de RRHH)
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(230, 230, 230);
        $pdf->Cell(95, 7, 'CONCEPTO / DESCRIPCION', 1, 0, 'C', true);
        $pdf->Cell(30, 7, 'INGRESOS', 1, 0, 'C', true);
        $pdf->Cell(30, 7, 'EGRESOS', 1, 0, 'C', true);
        $pdf->Cell(35, 7, 'SUBTOTAL', 1, 1, 'C', true);
        
        $pdf->SetFont('Arial', '', 9);
        
        // 1. Sueldo Base (Ingreso)
        $pdf->Cell(95, 7, 'Sueldo Mensual Base contratado', 1, 0, 'L');
        $pdf->Cell(30, 7, 'S/ ' . number_format($planilla['sueldo_base'], 2), 1, 0, 'R');
        $pdf->Cell(30, 7, '-', 1, 0, 'R');
        $pdf->Cell(35, 7, 'S/ ' . number_format($planilla['sueldo_base'], 2), 1, 1, 'R');
        
        // 2. Bonificación por asistencia perfecta
        $pdf->Cell(95, 7, utf8_decode('Bono por asistencia y puntualidad perfecta (Puntualidad 100%)'), 1, 0, 'L');
        $pdf->Cell(30, 7, 'S/ ' . number_format($planilla['bonificaciones'], 2), 1, 0, 'R');
        $pdf->Cell(30, 7, '-', 1, 0, 'R');
        $pdf->Cell(35, 7, 'S/ ' . number_format($planilla['bonificaciones'], 2), 1, 1, 'R');
        
        // 3. Descuento por inasistencias/faltas
        $pdf->Cell(95, 7, utf8_decode('Descuento por inasistencias (' . $detalles['total_faltas'] . ' faltas registradas)'), 1, 0, 'L');
        $pdf->Cell(30, 7, '-', 1, 0, 'R');
        $pdf->Cell(30, 7, 'S/ ' . number_format($detalles['descuento_falta'], 2), 1, 0, 'R');
        $pdf->Cell(35, 7, '-S/ ' . number_format($detalles['descuento_falta'], 2), 1, 1, 'R');
        
        // 4. Descuento por minutos de tardanza
        $pdf->Cell(95, 7, utf8_decode('Descuento por tardanzas acumuladas (' . $detalles['total_minutos_tardanza'] . ' minutos)'), 1, 0, 'L');
        $pdf->Cell(30, 7, '-', 1, 0, 'R');
        $pdf->Cell(30, 7, 'S/ ' . number_format($detalles['descuento_tardanza'], 2), 1, 0, 'R');
        $pdf->Cell(35, 7, '-S/ ' . number_format($detalles['descuento_tardanza'], 2), 1, 1, 'R');
        
        // Filas vacías para estética
        for ($i = 0; $i < 3; $i++) {
            $pdf->Cell(95, 7, '', 1, 0, 'L');
            $pdf->Cell(30, 7, '', 1, 0, 'R');
            $pdf->Cell(30, 7, '', 1, 0, 'R');
            $pdf->Cell(35, 7, '', 1, 1, 'R');
        }
        
        // Resumen
        $pdf->SetFont('Arial', 'B', 10);
        $totalIngresos = $planilla['sueldo_base'] + $planilla['bonificaciones'];
        $pdf->Cell(95, 8, 'TOTALES BRUTOS:', 1, 0, 'R');
        $pdf->Cell(30, 8, 'S/ ' . number_format($totalIngresos, 2), 1, 0, 'R');
        $pdf->Cell(30, 8, 'S/ ' . number_format($planilla['descuentos'], 2), 1, 0, 'R');
        $pdf->SetFillColor(240, 240, 240);
        $pdf->Cell(35, 8, 'S/ ' . number_format($planilla['total_pagar'], 2), 1, 1, 'R', true);
        
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(155, 10, 'TOTAL NETO A PERCIBIR (NETO A PAGAR):', 1, 0, 'R');
        $pdf->SetFillColor(220, 240, 220);
        $pdf->Cell(35, 10, 'S/ ' . number_format($planilla['total_pagar'], 2), 1, 1, 'R', true);
        $pdf->Ln(20);
        
        // Firmas y notas
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->MultiCell(0, 4, utf8_decode("Nota: La presente boleta detalla las remuneraciones devengadas correspondientes al mes laborado. Los descuentos se derivan directamente del registro automatizado del sistema de control de asistencia de la institución."), 0, 'J');
        $pdf->Ln(30);
        
        // Línea de firmas
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(85, 5, '-------------------------------------------------------', 0, 0, 'C');
        $pdf->Cell(20, 5, '', 0, 0, 'C');
        $pdf->Cell(85, 5, '-------------------------------------------------------', 0, 1, 'C');
        
        $pdf->Cell(85, 5, 'Firma del Docente', 0, 0, 'C');
        $pdf->Cell(20, 5, '', 0, 0, 'C');
        $pdf->Cell(85, 5, 'Firma Autorizada RRHH', 0, 1, 'C');
        
        $pdf->Cell(85, 5, 'DNI: ' . $docente['dni'], 0, 0, 'C');
        $pdf->Cell(20, 5, '', 0, 0, 'C');
        $pdf->Cell(85, 5, 'SIARH RRHH', 0, 1, 'C');
        
        $nombreArchivo = 'boleta_pago_BOP-' . str_pad($planilla['id'], 6, '0', STR_PAD_LEFT) . '.pdf';
        $pdf->Output('I', $nombreArchivo);
        exit;
    }
}

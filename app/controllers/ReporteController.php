<?php
/**
 * ReporteController - Controlador de Reportes
 */

class ReporteController extends Controller {
    private $asistenciaModel;
    private $docenteModel;
    
    public function __construct() {
        $this->asistenciaModel = new Asistencia();
        $this->docenteModel = new Docente();
    }
    
    /**
     * Dashboard de Reportes
     */
    public function index() {
        $this->requireAuth();
        
        $data = [
            'pageTitle' => 'Reportes y Estadísticas'
        ];
        
        $this->view('reportes/index', $data);
    }
    
    /**
     * Exportar reporte de asistencias
     */
    public function exportarAsistencias() {
        $this->requireAuth();
        
        $fechaInicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
        $fechaFin = $_GET['fecha_fin'] ?? date('Y-m-t');
        
        // Headers para descarga Excel
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment;filename="reporte_asistencias_' . date('Ymd_His') . '.xls"');
        header('Cache-Control: max-age=0');
        
        // BOM para UTF-8 en Excel
        echo "\xEF\xBB\xBF";
        
        echo "<table border='1'>";
        echo "<tr><th colspan='8' style='background-color: #f0f0f0; font-size: 14pt;'>Reporte de Asistencias ({$fechaInicio} al {$fechaFin})</th></tr>";
        echo "<tr>
                <th style='background-color: #e0e0e0;'>Fecha</th>
                <th style='background-color: #e0e0e0;'>Código</th>
                <th style='background-color: #e0e0e0;'>Docente</th>
                <th style='background-color: #e0e0e0;'>DNI</th>
                <th style='background-color: #e0e0e0;'>Carrera</th>
                <th style='background-color: #e0e0e0;'>Hora Entrada</th>
                <th style='background-color: #e0e0e0;'>Hora Salida</th>
                <th style='background-color: #e0e0e0;'>Estado</th>
              </tr>";
              
        // Aquí idealmente tendríamos un método en el modelo para rangos de fechas
        // Por ahora simularemos iterando, pero en producción deberíamos añadir getAsistenciasRango($inicio, $fin)
        // Usaremos getAsistenciasDelDia para simplificar por ahora, o modificaremos el modelo después.
        // VOy a usar una consulta directa por ahora para no bloquearme.
        
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT a.*, d.nombres, d.apellidos, d.dni, d.codigo_empleado, c.nombre as carrera
                FROM asistencias a
                JOIN docentes d ON a.docente_id = d.id
                LEFT JOIN carreras c ON d.carrera_id = c.id
                WHERE a.fecha BETWEEN :inicio AND :fin
                ORDER BY a.fecha DESC, a.hora_entrada ASC";
                
        $stmt = $db->prepare($sql);
        $stmt->execute(['inicio' => $fechaInicio, 'fin' => $fechaFin]);
        $asistencias = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($asistencias as $a) {
            echo "<tr>
                    <td>{$a['fecha']}</td>
                    <td>{$a['codigo_empleado']}</td>
                    <td>{$a['nombres']} {$a['apellidos']}</td>
                    <td>{$a['dni']}</td>
                    <td>{$a['carrera']}</td>
                    <td>{$a['hora_entrada']}</td>
                    <td>{$a['hora_salida']}</td>
                    <td>" . ucfirst($a['estado']) . "</td>
                  </tr>";
        }
        
        echo "</table>";
        exit;
    }
    
    /**
     * Exportar lista de docentes
     */
    public function exportarDocentes() {
        $this->requireAuth();
        
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment;filename="lista_docentes_' . date('Ymd') . '.xls"');
        header('Cache-Control: max-age=0');
        
        echo "\xEF\xBB\xBF";
        echo "<table border='1'>";
        echo "<tr><th colspan='7' style='background-color: #f0f0f0; font-size: 14pt;'>Lista de Personal Docente</th></tr>";
        echo "<tr>
                <th style='background-color: #e0e0e0;'>Código</th>
                <th style='background-color: #e0e0e0;'>Apellidos y Nombres</th>
                <th style='background-color: #e0e0e0;'>DNI</th>
                <th style='background-color: #e0e0e0;'>Email</th>
                <th style='background-color: #e0e0e0;'>Teléfono</th>
                <th style='background-color: #e0e0e0;'>Carrera</th>
                <th style='background-color: #e0e0e0;'>Estado</th>
              </tr>";
              
        $docentes = $this->docenteModel->getAllWithCarrera();
        
        foreach ($docentes as $d) {
            echo "<tr>
                    <td>{$d['codigo_empleado']}</td>
                    <td>{$d['apellidos']}, {$d['nombres']}</td>
                    <td>'{$d['dni']}</td>
                    <td>{$d['email']}</td>
                    <td>{$d['telefono']}</td>
                    <td>{$d['carrera_nombre']}</td>
                    <td>" . ucfirst($d['estado']) . "</td>
                  </tr>";
        }
        
        echo "</table>";
        exit;
    }
    
    /**
     * Reporte de asistencias en PDF (Descarga automática)
     */
    public function imprimirAsistencias() {
        $this->requireAuth();
        
        $fechaInicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
        $fechaFin = $_GET['fecha_fin'] ?? date('Y-m-d');
        
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT a.*, d.nombres, d.apellidos, d.dni, d.codigo_empleado, c.nombre as carrera
                FROM asistencias a
                JOIN docentes d ON a.docente_id = d.id
                LEFT JOIN carreras c ON d.carrera_id = c.id
                WHERE a.fecha BETWEEN :inicio AND :fin
                ORDER BY a.fecha DESC, a.hora_entrada ASC";
                
        $stmt = $db->prepare($sql);
        $stmt->execute(['inicio' => $fechaInicio, 'fin' => $fechaFin]);
        $asistencias = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Generar PDF con FPDF
        require_once 'libs/fpdf/fpdf.php';
        
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, utf8_decode('REPORTE DE ASISTENCIAS'), 0, 1, 'C');
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(0, 10, "Rango de fechas: {$fechaInicio} al {$fechaFin}", 0, 1, 'C');
        $pdf->Ln(5);
        
        // Encabezados
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(230, 230, 230);
        $pdf->Cell(25, 8, 'Fecha', 1, 0, 'C', true);
        $pdf->Cell(75, 8, 'Docente', 1, 0, 'C', true);
        $pdf->Cell(30, 8, 'Entrada', 1, 0, 'C', true);
        $pdf->Cell(30, 8, 'Salida', 1, 0, 'C', true);
        $pdf->Cell(30, 8, 'Estado', 1, 1, 'C', true);
        
        // Datos
        $pdf->SetFont('Arial', '', 9);
        foreach ($asistencias as $a) {
            $pdf->Cell(25, 7, $a['fecha'], 1, 0, 'C');
            $pdf->Cell(75, 7, utf8_decode($a['apellidos'] . ', ' . $a['nombres']), 1, 0, 'L');
            $pdf->Cell(30, 7, $a['hora_entrada'], 1, 0, 'C');
            $pdf->Cell(30, 7, $a['hora_salida'] ?: '--:--:--', 1, 0, 'C');
            $pdf->Cell(30, 7, utf8_decode(ucfirst($a['estado'])), 1, 1, 'C');
        }
        
        $nombreArchivo = 'reporte_asistencias_' . date('Ymd_His') . '.pdf';
        $pdf->Output('D', $nombreArchivo);
        exit;
    }
    
    /**
     * Reporte de docentes en PDF (Descarga automática)
     */
    public function imprimirDocentes() {
        $this->requireAuth();
        
        $docentes = $this->docenteModel->getAllWithCarrera();
        
        // Generar PDF con FPDF
        require_once 'libs/fpdf/fpdf.php';
        
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, utf8_decode('LISTA DE PERSONAL DOCENTE'), 0, 1, 'C');
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(0, 10, 'Generado el: ' . date('d/m/Y H:i:s'), 0, 1, 'C');
        $pdf->Ln(5);
        
        // Encabezados
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(230, 230, 230);
        $pdf->Cell(20, 8, 'Cod.', 1, 0, 'C', true);
        $pdf->Cell(70, 8, 'Apellidos y Nombres', 1, 0, 'C', true);
        $pdf->Cell(25, 8, 'DNI', 1, 0, 'C', true);
        $pdf->Cell(50, 8, 'Carrera', 1, 0, 'C', true);
        $pdf->Cell(25, 8, 'Estado', 1, 1, 'C', true);
        
        // Datos
        $pdf->SetFont('Arial', '', 9);
        foreach ($docentes as $d) {
            $pdf->Cell(20, 7, $d['codigo_empleado'], 1, 0, 'C');
            $pdf->Cell(70, 7, utf8_decode($d['apellidos'] . ', ' . $d['nombres']), 1, 0, 'L');
            $pdf->Cell(25, 7, $d['dni'], 1, 0, 'C');
            $pdf->Cell(50, 7, utf8_decode($d['carrera_nombre']), 1, 0, 'L');
            $pdf->Cell(25, 7, ucfirst($d['estado']), 1, 1, 'C');
        }
        
        $nombreArchivo = 'lista_docentes_' . date('Ymd_His') . '.pdf';
        $pdf->Output('D', $nombreArchivo);
        exit;
    }

    /**
     * Ver historial de actividad
     */
    public function logs() {
        $this->requireRole(['super_admin', 'admin']);
        
        $logModel = new LogActividad();
        $logs = $logModel->getRecientes(150); // Mostrar los últimos 150
        
        $data = [
            'pageTitle' => 'Historial de Actividad (Logs)',
            'logs' => $logs
        ];
        
        $this->view('reportes/logs', $data);
    }
}

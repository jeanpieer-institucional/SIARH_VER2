<?php
/**
 * AsistenciaController - Controlador de Control de Asistencia
 */

class AsistenciaController extends Controller {
    private $asistenciaModel;
    private $docenteModel;
    private $licenciaModel;
    private $logModel;
    
    public function __construct() {
        $this->asistenciaModel = new Asistencia();
        $this->docenteModel = new Docente();
        $this->licenciaModel = new Licencia();
        $this->logModel = new LogActividad();
    }
    
    /**
     * Vista principal de asistencias
     */
    public function index() {
        $this->requireAuth();
        
        $fecha = $_GET['fecha'] ?? date('Y-m-d');
        $asistencias = $this->asistenciaModel->getAsistenciasDelDia($fecha);
        
        $data = [
            'asistencias' => $asistencias,
            'fecha' => $fecha
        ];
        
        $this->view('asistencias/index', $data);
    }
    
    /**
     * Formulario de registro manual
     */
    public function registrar() {
        $this->requireRole(['admin', 'supervisor']);
        
        $data = [
            'docentes' => $this->docenteModel->getAll(['estado' => 'activo'], 'apellidos, nombres')
        ];
        
        $this->view('asistencias/registro', $data);
    }
    
    /**
     * Guardar asistencia manual
     */
    public function store() {
        $this->requireRole(['admin', 'supervisor']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/asistencias/registrar');
        }
        
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Error de seguridad: Token CSRF inválido';
            $this->redirect('/asistencias/registrar');
        }
        
        $docenteId = $_POST['docente_id'];
        $fecha = $_POST['fecha'] ?? date('Y-m-d');
        $hora = $_POST['hora'] ?? date('H:i:s');
        
        // Verificar si el docente tiene licencia activa
        if ($this->licenciaModel->tieneActiva($docenteId, $fecha)) {
            $_SESSION['warning'] = 'El docente tiene una licencia activa para esta fecha';
        }
        
        $result = $this->asistenciaModel->registrar($docenteId, $fecha, $hora, 'manual');
        
        if ($result) {
            $docente = $this->docenteModel->getById($docenteId);
            $this->logModel->registrar(
                $_SESSION['user_id'],
                'registrar_asistencia',
                'asistencias',
                "Asistencia registrada para: {$docente['nombres']} {$docente['apellidos']}"
            );
            
            $_SESSION['success'] = 'Asistencia registrada exitosamente';
        } else {
            $_SESSION['error'] = 'Error al registrar asistencia';
        }
        
        $this->redirect('/asistencias');
    }
    
    /**
     * Registro biométrico (API para lector de huella)
     */
    public function registrarBiometrico() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Método no permitido'], 405);
        }
        
        $huellaData = $_POST['huella_data'] ?? null;
        
        if (!$huellaData) {
            $this->json(['success' => false, 'message' => 'Datos de huella no proporcionados'], 400);
        }
        
        // Aquí se compararía la huella con la base de datos
        // Por ahora, simulamos encontrando por DNI
        $dni = $_POST['dni'] ?? null;
        
        if ($dni) {
            $docentes = $this->docenteModel->search($dni);
            
            if (!empty($docentes)) {
                $docente = $docentes[0];
                $fecha = date('Y-m-d');
                $hora = date('H:i:s');
                
                $result = $this->asistenciaModel->registrar($docente['id'], $fecha, $hora, 'biometrico');
                
                if ($result) {
                    $this->logModel->registrar(
                        null,
                        'registrar_asistencia_biometrico',
                        'asistencias',
                        "Asistencia biométrica: {$docente['nombres']} {$docente['apellidos']}"
                    );
                    
                    $this->json([
                        'success' => true,
                        'message' => 'Asistencia registrada',
                        'docente' => "{$docente['nombres']} {$docente['apellidos']}",
                        'hora' => $hora
                    ]);
                }
            }
        }
        
        $this->json(['success' => false, 'message' => 'Huella no reconocida'], 404);
    }
    
    /**
     * Reporte de asistencias
     */
    public function reporte() {
        $this->requireAuth();
        
        $fechaInicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
        $fechaFin = $_GET['fecha_fin'] ?? date('Y-m-t');
        $carreraId = $_GET['carrera_id'] ?? null;
        
        $stats = $this->asistenciaModel->getEstadisticas($fechaInicio, $fechaFin, $carreraId);
        
        $data = [
            'stats' => $stats,
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'carrera_id' => $carreraId,
            'carreras' => (new Carrera())->getActivas()
        ];
        
        $this->view('asistencias/reporte', $data);
    }
    
    /**
     * Exportar a Excel
     */
    public function exportarExcel() {
        $this->requireAuth();
        
        $fecha = $_GET['fecha'] ?? date('Y-m-d');
        $asistencias = $this->asistenciaModel->getAsistenciasDelDia($fecha);
        
        // Headers para descarga
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="asistencias_' . $fecha . '.xls"');
        header('Cache-Control: max-age=0');
        
        echo "<table border='1'>";
        echo "<tr>
                <th>Código</th>
                <th>Docente</th>
                <th>DNI</th>
                <th>Carrera</th>
                <th>Hora Entrada</th>
                <th>Hora Salida</th>
                <th>Estado</th>
                <th>Minutos Tardanza</th>
              </tr>";
        
        foreach ($asistencias as $a) {
            echo "<tr>
                    <td>{$a['codigo_empleado']}</td>
                    <td>{$a['nombre_completo']}</td>
                    <td>{$a['dni']}</td>
                    <td>{$a['carrera']}</td>
                    <td>{$a['hora_entrada']}</td>
                    <td>{$a['hora_salida']}</td>
                    <td>{$a['estado']}</td>
                    <td>{$a['minutos_tardanza']}</td>
                  </tr>";
        }
        
        echo "</table>";
        exit;
    }
}

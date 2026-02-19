<?php
/**
 * DashboardController - Controlador del Dashboard
 */

class DashboardController extends Controller {
    private $docenteModel;
    private $asistenciaModel;
    private $licenciaModel;
    private $logModel;
    
    public function __construct() {
        $this->docenteModel = new Docente();
        $this->asistenciaModel = new Asistencia();
        $this->licenciaModel = new Licencia();
        $this->logModel = new LogActividad();
    }
    
    /**
     * Dashboard principal
     */
    public function index() {
        $this->requireAuth();
        
        try {
            $data = [
                'user' => $this->getCurrentUser(),
                'stats' => $this->getEstadisticas(),
                'asistencias_hoy' => $this->asistenciaModel->getAsistenciasDelDia(),
                'licencias_pendientes' => $this->licenciaModel->getPendientes(),
                'licencias_por_vencer' => $this->licenciaModel->getPorVencer(),
                'logs_recientes' => $this->logModel->getRecientes(10)
            ];
        } catch (Exception $e) {
            // Si hay error, mostrar dashboard con datos vacíos
            $data = [
                'user' => $this->getCurrentUser(),
                'stats' => [
                    'total_docentes' => 0,
                    'docentes_activos' => 0,
                    'docentes_con_huella' => 0,
                    'docentes_sin_huella' => 0,
                    'asistencias_hoy' => 0,
                    'presentes_hoy' => 0,
                    'tardanzas_hoy' => 0,
                    'licencias_pendientes' => 0,
                    'licencias_por_vencer' => 0,
                    'asistencias_mes' => 0,
                    'puntualidad_mes' => 0
                ],
                'asistencias_hoy' => [],
                'licencias_pendientes' => [],
                'licencias_por_vencer' => [],
                'logs_recientes' => []
            ];
            
            $_SESSION['warning'] = 'La base de datos está vacía. Por favor, importa el archivo schema.sql desde phpMyAdmin.';
        }
        
        $this->view('dashboard/index', $data);
    }
    
    /**
     * Obtener estadísticas generales
     */
    private function getEstadisticas() {
        $stats = [];
        
        try {
            // Estadísticas de docentes
            $docenteStats = $this->docenteModel->getEstadisticas();
            $stats['total_docentes'] = $docenteStats['total'];
            $stats['docentes_activos'] = $docenteStats['activos'];
            $stats['docentes_con_huella'] = $docenteStats['con_huella'];
            $stats['docentes_sin_huella'] = $docenteStats['total'] - $docenteStats['con_huella'];
        } catch (Exception $e) {
            $stats['total_docentes'] = 0;
            $stats['docentes_activos'] = 0;
            $stats['docentes_con_huella'] = 0;
            $stats['docentes_sin_huella'] = 0;
        }
        
        try {
            // Asistencias de hoy
            $hoy = date('Y-m-d');
            $asistenciasHoy = $this->asistenciaModel->getAsistenciasDelDia($hoy);
            $stats['asistencias_hoy'] = count($asistenciasHoy);
            $stats['presentes_hoy'] = count(array_filter($asistenciasHoy, function($a) {
                return $a['estado'] === 'presente';
            }));
            $stats['tardanzas_hoy'] = count(array_filter($asistenciasHoy, function($a) {
                return $a['estado'] === 'tardanza';
            }));
        } catch (Exception $e) {
            $stats['asistencias_hoy'] = 0;
            $stats['presentes_hoy'] = 0;
            $stats['tardanzas_hoy'] = 0;
        }
        
        try {
            // Licencias
            $stats['licencias_pendientes'] = count($this->licenciaModel->getPendientes());
            $stats['licencias_por_vencer'] = count($this->licenciaModel->getPorVencer());
        } catch (Exception $e) {
            $stats['licencias_pendientes'] = 0;
            $stats['licencias_por_vencer'] = 0;
        }
        
        try {
            // Estadísticas del mes
            $primerDia = date('Y-m-01');
            $ultimoDia = date('Y-m-t');
            
            $statsAsistencia = $this->asistenciaModel->getEstadisticas($primerDia, $ultimoDia);
            $stats['asistencias_mes'] = $statsAsistencia['total_registros'];
            $stats['puntualidad_mes'] = $statsAsistencia['total_registros'] > 0 
                ? round(($statsAsistencia['presentes'] / $statsAsistencia['total_registros']) * 100, 1)
                : 0;
        } catch (Exception $e) {
            $stats['asistencias_mes'] = 0;
            $stats['puntualidad_mes'] = 0;
        }
        
        return $stats;
    }
    
    /**
     * Obtener datos para gráficos (AJAX)
     */
    public function getChartData() {
        $this->requireAuth();
        
        $tipo = $_GET['tipo'] ?? 'asistencia_semanal';
        
        switch ($tipo) {
            case 'asistencia_semanal':
                $data = $this->getAsistenciaSemanal();
                break;
            case 'asistencia_por_carrera':
                $data = $this->getAsistenciaPorCarrera();
                break;
            case 'tardanzas_mes':
                $data = $this->getTardanzasMes();
                break;
            default:
                $data = [];
        }
        
        $this->json($data);
    }
    
    /**
     * Datos de asistencia semanal
     */
    private function getAsistenciaSemanal() {
        $datos = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $fecha = date('Y-m-d', strtotime("-{$i} days"));
            $asistencias = $this->asistenciaModel->getAsistenciasDelDia($fecha);
            
            $datos['labels'][] = date('D d', strtotime($fecha));
            $datos['presentes'][] = count(array_filter($asistencias, function($a) {
                return $a['estado'] === 'presente';
            }));
            $datos['tardanzas'][] = count(array_filter($asistencias, function($a) {
                return $a['estado'] === 'tardanza';
            }));
            $datos['ausentes'][] = count(array_filter($asistencias, function($a) {
                return $a['estado'] === 'ausente';
            }));
        }
        
        return $datos;
    }
    
    /**
     * Datos de asistencia por carrera
     */
    private function getAsistenciaPorCarrera() {
        $carreraModel = new Carrera();
        $carreras = $carreraModel->getActivas();
        
        $datos = [
            'labels' => [],
            'values' => []
        ];
        
        $hoy = date('Y-m-d');
        
        foreach ($carreras as $carrera) {
            $stats = $this->asistenciaModel->getEstadisticas($hoy, $hoy, $carrera['id']);
            $datos['labels'][] = $carrera['nombre'];
            $datos['values'][] = $stats['presentes'] + $stats['tardanzas'];
        }
        
        return $datos;
    }
    
    /**
     * Datos de tardanzas del mes
     */
    private function getTardanzasMes() {
        $primerDia = date('Y-m-01');
        $ultimoDia = date('Y-m-t');
        
        $sql = "SELECT DATE(fecha) as dia, COUNT(*) as total
                FROM asistencias
                WHERE fecha BETWEEN :inicio AND :fin
                  AND estado = 'tardanza'
                GROUP BY DATE(fecha)
                ORDER BY fecha";
        
        $result = $this->asistenciaModel->query($sql, [
            'inicio' => $primerDia,
            'fin' => $ultimoDia
        ]);
        
        $datos = [
            'labels' => [],
            'values' => []
        ];
        
        foreach ($result as $row) {
            $datos['labels'][] = date('d/m', strtotime($row['dia']));
            $datos['values'][] = $row['total'];
        }
        
        return $datos;
    }
}

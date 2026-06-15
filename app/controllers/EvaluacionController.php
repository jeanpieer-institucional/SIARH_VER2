<?php
/**
 * EvaluacionController - Controlador de Evaluación de Desempeño
 */

class EvaluacionController extends Controller {
    private $evaluacionModel;
    private $docenteModel;
    private $logModel;
    
    public function __construct() {
        $this->evaluacionModel = new Evaluacion();
        $this->docenteModel = new Docente();
        $this->logModel = new LogActividad();
    }
    
    /**
     * Listado general de evaluaciones de docentes
     */
    public function index() {
        $this->requireAuth();
        
        $docenteId = $_GET['docente_id'] ?? '';
        $docenteSeleccionado = null;
        $evaluaciones = [];
        $promedios = null;
        
        // Obtener todos los docentes
        $docentes = $this->docenteModel->getAll([], 'apellidos ASC, nombres ASC');
        
        if ($docenteId) {
            $docenteSeleccionado = $this->docenteModel->getById($docenteId);
            if ($docenteSeleccionado) {
                $evaluaciones = $this->evaluacionModel->getByDocente($docenteId);
                $promedios = $this->evaluacionModel->getPromedioPorDocente($docenteId);
            }
        }
        
        // Agregar promedio general a cada docente para la lista principal
        foreach ($docentes as &$docente) {
            $prom = $this->evaluacionModel->getPromedioPorDocente($docente['id']);
            $docente['promedio_general'] = $prom['promedio_general'] ?? 0;
            $docente['total_evaluaciones'] = $prom['total_evaluaciones'] ?? 0;
        }
        
        $data = [
            'docentes' => $docentes,
            'docenteId' => $docenteId,
            'docenteSeleccionado' => $docenteSeleccionado,
            'evaluaciones' => $evaluaciones,
            'promedios' => $promedios
        ];
        
        $this->view('evaluaciones/index', $data);
    }
    
    /**
     * Formulario de creación de evaluación
     */
    public function create() {
        $this->requireRole(['admin', 'supervisor']);
        
        $docentes = $this->docenteModel->getAll(['estado' => 'activo'], 'apellidos ASC, nombres ASC');
        $docenteId = $_GET['docente_id'] ?? '';
        
        $data = [
            'docentes' => $docentes,
            'docenteId' => $docenteId
        ];
        
        $this->view('evaluaciones/form', $data);
    }
    
    /**
     * Guardar una nueva evaluación
     */
    public function store() {
        $this->requireRole(['admin', 'supervisor']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/evaluaciones');
        }
        
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Error de seguridad: Token CSRF inválido';
            $this->redirect('/evaluaciones');
        }
        
        $docenteId = $_POST['docente_id'] ?? '';
        $periodo = trim($_POST['periodo'] ?? '');
        $puntuacionMetodologia = intval($_POST['puntuacion_metodologia'] ?? 0);
        $puntuacionPuntualidad = intval($_POST['puntuacion_puntualidad'] ?? 0);
        $puntuacionRelacion = intval($_POST['puntuacion_relacion'] ?? 0);
        $comentarios = trim($_POST['comentarios'] ?? '');
        
        if (empty($docenteId) || empty($periodo) || $puntuacionMetodologia < 1 || $puntuacionMetodologia > 5 || $puntuacionPuntualidad < 1 || $puntuacionPuntualidad > 5 || $puntuacionRelacion < 1 || $puntuacionRelacion > 5) {
            $_SESSION['error'] = 'Por favor, complete todos los campos obligatorios con valoraciones válidas';
            $this->redirect('/evaluaciones/create?docente_id=' . $docenteId);
        }
        
        $data = [
            'docente_id' => $docenteId,
            'evaluador_id' => $_SESSION['user_id'],
            'periodo' => $periodo,
            'puntuacion_metodologia' => $puntuacionMetodologia,
            'puntuacion_puntualidad' => $puntuacionPuntualidad,
            'puntuacion_relacion' => $puntuacionRelacion,
            'comentarios' => $comentarios
        ];
        
        $evaluacionId = $this->evaluacionModel->create($data);
        
        if ($evaluacionId) {
            $docente = $this->docenteModel->getById($docenteId);
            $this->logModel->registrar(
                $_SESSION['user_id'],
                'crear_evaluacion',
                'evaluaciones',
                "Evaluación registrada para docente {$docente['nombres']} {$docente['apellidos']} (Periodo: {$periodo})"
            );
            
            $_SESSION['success'] = 'Evaluación registrada exitosamente';
            $this->redirect('/evaluaciones?docente_id=' . $docenteId);
        } else {
            $_SESSION['error'] = 'Error al registrar la evaluación';
            $this->redirect('/evaluaciones/create?docente_id=' . $docenteId);
        }
    }
    
    /**
     * Eliminar una evaluación
     */
    public function delete($id) {
        $this->requireRole(['admin']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/evaluaciones');
        }
        
        $evaluacion = $this->evaluacionModel->getById($id);
        
        if ($evaluacion) {
            $docenteId = $evaluacion['docente_id'];
            $docente = $this->docenteModel->getById($docenteId);
            
            if ($this->evaluacionModel->delete($id)) {
                $this->logModel->registrar(
                    $_SESSION['user_id'],
                    'eliminar_evaluacion',
                    'evaluaciones',
                    "Evaluación eliminada para docente {$docente['nombres']} {$docente['apellidos']} (ID: {$id})"
                );
                $_SESSION['success'] = 'Evaluación eliminada con éxito';
            } else {
                $_SESSION['error'] = 'Error al eliminar la evaluación';
            }
            $this->redirect('/evaluaciones?docente_id=' . $docenteId);
        } else {
            $_SESSION['error'] = 'Evaluación no encontrada';
            $this->redirect('/evaluaciones');
        }
    }
}

<?php
/**
 * HorarioController - Controlador de Horarios
 */

class HorarioController extends Controller {
    private $horarioModel;
    private $docenteModel;
    private $logModel;
    
    public function __construct() {
        $this->horarioModel = new Horario();
        $this->docenteModel = new Docente();
        $this->logModel = new LogActividad();
    }
    
    /**
     * Mostrar listado de horarios de docentes
     */
    public function index() {
        $this->requireAuth();
        
        $docenteId = $_GET['docente_id'] ?? '';
        $docenteSeleccionado = null;
        $horarios = [];
        
        // Obtener todos los docentes para el selector
        $docentes = $this->docenteModel->getAll([], 'apellidos ASC, nombres ASC');
        
        if ($docenteId) {
            $docenteSeleccionado = $this->docenteModel->getById($docenteId);
            if ($docenteSeleccionado) {
                $horarios = $this->horarioModel->getByDocente($docenteId);
            }
        }
        
        // Agregar conteo de horarios a cada docente para la lista principal
        foreach ($docentes as &$docente) {
            $sql = "SELECT COUNT(*) as total FROM horarios_docente WHERE docente_id = :docente_id AND estado = 'activo'";
            $res = $this->horarioModel->query($sql, ['docente_id' => $docente['id']]);
            $docente['total_horarios'] = $res[0]['total'] ?? 0;
        }
        
        $data = [
            'docentes' => $docentes,
            'docenteId' => $docenteId,
            'docenteSeleccionado' => $docenteSeleccionado,
            'horarios' => $horarios
        ];
        
        $this->view('horarios/index', $data);
    }
    
    /**
     * Formulario de creación
     */
    public function create() {
        $this->requireRole(['admin', 'supervisor']);
        
        $docentes = $this->docenteModel->getAll(['estado' => 'activo'], 'apellidos ASC, nombres ASC');
        $docenteId = $_GET['docente_id'] ?? '';
        
        $data = [
            'docentes' => $docentes,
            'docenteId' => $docenteId,
            'dias' => ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo']
        ];
        
        $this->view('horarios/form', $data);
    }
    
    /**
     * Guardar nuevo horario
     */
    public function store() {
        $this->requireRole(['admin', 'supervisor']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/horarios');
        }
        
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Error de seguridad: Token CSRF inválido';
            $this->redirect('/horarios');
        }
        
        $docenteId = $_POST['docente_id'] ?? '';
        $diaSemana = $_POST['dia_semana'] ?? '';
        $horaEntrada = $_POST['hora_entrada'] ?? '';
        $horaSalida = $_POST['hora_salida'] ?? '';
        
        if (empty($docenteId) || empty($diaSemana) || empty($horaEntrada) || empty($horaSalida)) {
            $_SESSION['error'] = 'Por favor complete todos los campos obligatorios';
            $this->redirect('/horarios/create?docente_id=' . $docenteId);
        }
        
        // Validar que la hora de entrada sea menor que la de salida
        if (strtotime($horaEntrada) >= strtotime($horaSalida)) {
            $_SESSION['error'] = 'La hora de entrada debe ser menor que la hora de salida';
            $this->redirect('/horarios/create?docente_id=' . $docenteId);
        }
        
        // Verificar si existe cruce de horarios para este docente
        if ($this->horarioModel->verificarCruce($docenteId, $diaSemana, $horaEntrada, $horaSalida)) {
            $_SESSION['error'] = 'Existe un cruce de horarios para este docente en el día seleccionado';
            $this->redirect('/horarios/create?docente_id=' . $docenteId);
        }
        
        $data = [
            'docente_id' => $docenteId,
            'dia_semana' => $diaSemana,
            'hora_entrada' => $horaEntrada,
            'hora_salida' => $horaSalida,
            'estado' => 'activo'
        ];
        
        $horarioId = $this->horarioModel->create($data);
        
        if ($horarioId) {
            $docente = $this->docenteModel->getById($docenteId);
            $this->logModel->registrar(
                $_SESSION['user_id'],
                'crear_horario',
                'horarios',
                "Horario creado para docente {$docente['nombres']} {$docente['apellidos']}: {$diaSemana} {$horaEntrada} - {$horaSalida}"
            );
            
            $_SESSION['success'] = 'Horario asignado exitosamente';
            $this->redirect('/horarios?docente_id=' . $docenteId);
        } else {
            $_SESSION['error'] = 'Error al registrar el horario';
            $this->redirect('/horarios/create?docente_id=' . $docenteId);
        }
    }
    
    /**
     * Formulario de edición
     */
    public function edit($id) {
        $this->requireRole(['admin', 'supervisor']);
        
        $horario = $this->horarioModel->getById($id);
        
        if (!$horario) {
            $_SESSION['error'] = 'Horario no encontrado';
            $this->redirect('/horarios');
        }
        
        $docentes = $this->docenteModel->getAll(['estado' => 'activo'], 'apellidos ASC, nombres ASC');
        
        $data = [
            'horario' => $horario,
            'docentes' => $docentes,
            'docenteId' => $horario['docente_id'],
            'dias' => ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo']
        ];
        
        $this->view('horarios/form', $data);
    }
    
    /**
     * Actualizar horario
     */
    public function update($id) {
        $this->requireRole(['admin', 'supervisor']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/horarios');
        }
        
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Error de seguridad: Token CSRF inválido';
            $this->redirect('/horarios');
        }
        
        $horario = $this->horarioModel->getById($id);
        if (!$horario) {
            $_SESSION['error'] = 'Horario no encontrado';
            $this->redirect('/horarios');
        }
        
        $docenteId = $_POST['docente_id'] ?? '';
        $diaSemana = $_POST['dia_semana'] ?? '';
        $horaEntrada = $_POST['hora_entrada'] ?? '';
        $horaSalida = $_POST['hora_salida'] ?? '';
        $estado = $_POST['estado'] ?? 'activo';
        
        if (empty($docenteId) || empty($diaSemana) || empty($horaEntrada) || empty($horaSalida)) {
            $_SESSION['error'] = 'Por favor complete todos los campos obligatorios';
            $this->redirect('/horarios/edit/' . $id);
        }
        
        // Validar que la hora de entrada sea menor que la de salida
        if (strtotime($horaEntrada) >= strtotime($horaSalida)) {
            $_SESSION['error'] = 'La hora de entrada debe ser menor que la hora de salida';
            $this->redirect('/horarios/edit/' . $id);
        }
        
        // Verificar si existe cruce de horarios para este docente (excluyendo el actual)
        if ($estado === 'activo' && $this->horarioModel->verificarCruce($docenteId, $diaSemana, $horaEntrada, $horaSalida, $id)) {
            $_SESSION['error'] = 'Existe un cruce de horarios para este docente en el día seleccionado';
            $this->redirect('/horarios/edit/' . $id);
        }
        
        $data = [
            'docente_id' => $docenteId,
            'dia_semana' => $diaSemana,
            'hora_entrada' => $horaEntrada,
            'hora_salida' => $horaSalida,
            'estado' => $estado
        ];
        
        $datosAnteriores = $this->horarioModel->getById($id);
        
        if ($this->horarioModel->update($id, $data)) {
            $docente = $this->docenteModel->getById($docenteId);
            $datosNuevos = $this->horarioModel->getById($id);
            
            $this->logModel->registrar(
                $_SESSION['user_id'],
                'actualizar_horario',
                'horarios',
                "Horario actualizado para docente {$docente['nombres']} {$docente['apellidos']}: {$diaSemana} {$horaEntrada} - {$horaSalida}",
                $datosAnteriores,
                $datosNuevos
            );
            
            $_SESSION['success'] = 'Horario actualizado exitosamente';
            $this->redirect('/horarios?docente_id=' . $docenteId);
        } else {
            $_SESSION['error'] = 'Error al actualizar el horario';
            $this->redirect('/horarios/edit/' . $id);
        }
    }
    
    /**
     * Eliminar horario
     */
    public function delete($id) {
        $this->requireRole(['admin']);
        
        $horario = $this->horarioModel->getById($id);
        
        if ($horario) {
            $docenteId = $horario['docente_id'];
            $docente = $this->docenteModel->getById($docenteId);
            
            if ($this->horarioModel->delete($id)) {
                $this->logModel->registrar(
                    $_SESSION['user_id'],
                    'eliminar_horario',
                    'horarios',
                    "Horario eliminado para docente {$docente['nombres']} {$docente['apellidos']}: {$horario['dia_semana']} {$horario['hora_entrada']} - {$horario['hora_salida']}"
                );
                
                $_SESSION['success'] = 'Horario eliminado exitosamente';
            } else {
                $_SESSION['error'] = 'Error al eliminar el horario';
            }
            $this->redirect('/horarios?docente_id=' . $docenteId);
        } else {
            $_SESSION['error'] = 'Horario no encontrado';
            $this->redirect('/horarios');
        }
    }
}

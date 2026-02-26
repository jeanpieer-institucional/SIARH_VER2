<?php
/**
 * CarreraController - Controlador de Gestión de Carreras/Departamentos
 */

class CarreraController extends Controller {
    private $carreraModel;
    private $logModel;
    
    public function __construct() {
        $this->carreraModel = new Carrera();
        $this->logModel = new LogActividad();
    }
    
    /**
     * Lista de carreras
     */
    public function index() {
        $this->requireAuth();
        
        $carreras = $this->carreraModel->getAll([], 'nombre ASC');
        
        // Agregar conteos opcionales de docentes por carrera
        foreach ($carreras as &$carrera) {
            $stats = $this->carreraModel->getWithStats($carrera['id']);
            $carrera['total_docentes'] = $stats['total_docentes'] ?? 0;
            $carrera['docentes_activos'] = $stats['docentes_activos'] ?? 0;
        }
        
        $data = [
            'carreras' => $carreras
        ];
        
        $this->view('carreras/index', $data);
    }
    
    /**
     * Formulario de creación
     */
    public function create() {
        $this->requireRole(['admin', 'supervisor']);
        
        $this->view('carreras/form');
    }
    
    /**
     * Guardar nueva carrera
     */
    public function store() {
        $this->requireRole(['admin', 'supervisor']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/carreras');
        }
        
        // Validar código único
        $carreraExistente = $this->carreraModel->getAll(['codigo' => $_POST['codigo']]);
        if (!empty($carreraExistente)) {
            $_SESSION['error'] = 'El código ya está registrado para otra carrera';
            $this->redirect('/carreras/create');
        }
        
        // Crear carrera
        $data = [
            'nombre' => trim($_POST['nombre']),
            'codigo' => trim(strtoupper($_POST['codigo'])),
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'estado' => $_POST['estado'] ?? 'activo'
        ];
        
        $carreraId = $this->carreraModel->create($data);
        
        if ($carreraId) {
            $this->logModel->registrar(
                $_SESSION['user_id'],
                'crear_carrera',
                'carreras',
                "Carrera/Departamento creado: {$_POST['nombre']}"
            );
            
            $_SESSION['success'] = 'Carrera registrada exitosamente';
            $this->redirect('/carreras');
        } else {
            $_SESSION['error'] = 'Error al registrar la carrera';
            $this->redirect('/carreras/create');
        }
    }
    
    /**
     * Formulario de edición
     */
    public function edit($id) {
        $this->requireRole(['admin', 'supervisor']);
        
        $carrera = $this->carreraModel->getById($id);
        
        if (!$carrera) {
            $_SESSION['error'] = 'Carrera no encontrada';
            $this->redirect('/carreras');
        }
        
        $data = [
            'carrera' => $carrera
        ];
        
        $this->view('carreras/form', $data);
    }
    
    /**
     * Actualizar carrera
     */
    public function update($id) {
        $this->requireRole(['admin', 'supervisor']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/carreras');
        }
        
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Error de seguridad: Token CSRF inválido';
            $this->redirect("/carreras/edit/{$id}");
        }
        
        // Validar código único
        $carreraExistente = $this->carreraModel->getAll(['codigo' => $_POST['codigo']]);
        if (!empty($carreraExistente) && $carreraExistente[0]['id'] != $id) {
            $_SESSION['error'] = 'El código ya está registrado para otra carrera';
            $this->redirect("/carreras/edit/{$id}");
        }
        
        $data = [
            'nombre' => trim($_POST['nombre']),
            'codigo' => trim(strtoupper($_POST['codigo'])),
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'estado' => $_POST['estado']
        ];
        
        // Log - Antes
        $datosAnteriores = $this->carreraModel->getById($id);
        
        if ($this->carreraModel->update($id, $data)) {
            // Log - Después
            $datosNuevos = $this->carreraModel->getById($id);
            
            $this->logModel->registrar(
                $_SESSION['user_id'],
                'actualizar_carrera',
                'carreras',
                "Carrera/Departamento actualizado: {$_POST['nombre']}",
                $datosAnteriores,
                $datosNuevos
            );
            
            $_SESSION['success'] = 'Carrera actualizada exitosamente';
        } else {
            $_SESSION['error'] = 'Error al actualizar la carrera';
        }
        
        $this->redirect('/carreras');
    }
    
    /**
     * Eliminar carrera
     */
    public function delete($id) {
        $this->requireRole(['admin']);
        
        $carrera = $this->carreraModel->getById($id);
        
        if ($carrera) {
            // Eliminar
            if ($this->carreraModel->delete($id)) {
                $this->logModel->registrar(
                    $_SESSION['user_id'],
                    'eliminar_carrera',
                    'carreras',
                    "Carrera/Departamento eliminado: {$carrera['nombre']}"
                );
                
                $_SESSION['success'] = 'Carrera eliminada exitosamente';
            } else {
                $_SESSION['error'] = 'Error al eliminar la carrera (puede tener docentes vinculados en la base de datos)';
            }
        }
        
        $this->redirect('/carreras');
    }
}

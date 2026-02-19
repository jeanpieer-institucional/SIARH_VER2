<?php
/**
 * DocenteController - Controlador de Gestión de Docentes
 */

class DocenteController extends Controller {
    private $docenteModel;
    private $carreraModel;
    private $usuarioModel;
    private $logModel;
    
    public function __construct() {
        $this->docenteModel = new Docente();
        $this->carreraModel = new Carrera();
        $this->usuarioModel = new Usuario();
        $this->logModel = new LogActividad();
    }
    
    /**
     * Lista de docentes
     */
    public function index() {
        $this->requireAuth();
        
        $search = $_GET['search'] ?? '';
        $estado = $_GET['estado'] ?? '';
        $carreraId = $_GET['carrera_id'] ?? '';
        
        if ($search) {
            $docentes = $this->docenteModel->search($search, [
                'estado' => $estado,
                'carrera_id' => $carreraId
            ]);
        } else {
            $docentes = $this->docenteModel->getAllWithCarrera();
        }
        
        $data = [
            'docentes' => $docentes,
            'carreras' => $this->carreraModel->getActivas(),
            'search' => $search,
            'estado' => $estado,
            'carrera_id' => $carreraId
        ];
        
        $this->view('docentes/index', $data);
    }
    
    /**
     * Formulario de creación
     */
    public function create() {
        $this->requireRole(['admin', 'supervisor']);
        
        $data = [
            'carreras' => $this->carreraModel->getActivas(),
            'codigo_empleado' => $this->docenteModel->generarCodigoEmpleado()
        ];
        
        $this->view('docentes/form', $data);
    }
    
    /**
     * Guardar nuevo docente
     */
    public function store() {
        $this->requireRole(['admin', 'supervisor']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/docentes');
        }
        
        // Validar DNI único
        if ($this->docenteModel->dniExists($_POST['dni'])) {
            $_SESSION['error'] = 'El DNI ya está registrado';
            $this->redirect('/docentes/create');
        }
        
        // Crear usuario si se proporcionó
        $usuarioId = null;
        if (!empty($_POST['crear_usuario'])) {
            $usuarioId = $this->usuarioModel->createUser([
                'username' => $_POST['dni'],
                'password' => $_POST['dni'], // Password inicial = DNI
                'rol' => 'docente',
                'email' => $_POST['email'],
                'estado' => 'activo'
            ]);
        }
        
        // Crear docente
        $docenteData = [
            'codigo_empleado' => $_POST['codigo_empleado'],
            'nombres' => $_POST['nombres'],
            'apellidos' => $_POST['apellidos'],
            'dni' => $_POST['dni'],
            'email' => $_POST['email'],
            'telefono' => $_POST['telefono'] ?? null,
            'carrera_id' => $_POST['carrera_id'] ?? null,
            'usuario_id' => $usuarioId,
            'estado' => $_POST['estado'] ?? 'activo',
            'fecha_ingreso' => $_POST['fecha_ingreso'] ?? date('Y-m-d'),
            'direccion' => $_POST['direccion'] ?? null
        ];
        
        $docenteId = $this->docenteModel->create($docenteData);
        
        if ($docenteId) {
            $this->logModel->registrar(
                $_SESSION['user_id'],
                'crear_docente',
                'docentes',
                "Docente creado: {$_POST['nombres']} {$_POST['apellidos']}"
            );
            
            $_SESSION['success'] = 'Docente registrado exitosamente';
            $this->redirect('/docentes');
        } else {
            $_SESSION['error'] = 'Error al registrar docente';
            $this->redirect('/docentes/create');
        }
    }
    
    /**
     * Formulario de edición
     */
    public function edit($id) {
        $this->requireRole(['admin', 'supervisor']);
        
        $docente = $this->docenteModel->getById($id);
        
        if (!$docente) {
            $_SESSION['error'] = 'Docente no encontrado';
            $this->redirect('/docentes');
        }
        
        $data = [
            'docente' => $docente,
            'carreras' => $this->carreraModel->getActivas()
        ];
        
        $this->view('docentes/form', $data);
    }
    
    /**
     * Actualizar docente
     */
    public function update($id) {
        $this->requireRole(['admin', 'supervisor']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/docentes');
        }
        
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Error de seguridad: Token CSRF inválido';
            $this->redirect("/docentes/edit/{$id}");
        }
        
        // Validar DNI único (excluyendo el actual)
        if ($this->docenteModel->dniExists($_POST['dni'], $id)) {
            $_SESSION['error'] = 'El DNI ya está registrado';
            $this->redirect("/docentes/edit/{$id}");
        }
        
        $docenteData = [
            'nombres' => $_POST['nombres'],
            'apellidos' => $_POST['apellidos'],
            'dni' => $_POST['dni'],
            'email' => $_POST['email'],
            'telefono' => $_POST['telefono'] ?? null,
            'carrera_id' => $_POST['carrera_id'] ?? null,
            'estado' => $_POST['estado'],
            'direccion' => $_POST['direccion'] ?? null
        ];
        
        if ($this->docenteModel->update($id, $docenteData)) {
            $this->logModel->registrar(
                $_SESSION['user_id'],
                'actualizar_docente',
                'docentes',
                "Docente actualizado: {$_POST['nombres']} {$_POST['apellidos']}"
            );
            
            $_SESSION['success'] = 'Docente actualizado exitosamente';
        } else {
            $_SESSION['error'] = 'Error al actualizar docente';
        }
        
        $this->redirect('/docentes');
    }
    
    /**
     * Eliminar docente
     */
    public function delete($id) {
        $this->requireRole(['admin']);
        
        $docente = $this->docenteModel->getById($id);
        
        if ($docente) {
            if ($this->docenteModel->delete($id)) {
                $this->logModel->registrar(
                    $_SESSION['user_id'],
                    'eliminar_docente',
                    'docentes',
                    "Docente eliminado: {$docente['nombres']} {$docente['apellidos']}"
                );
                
                $_SESSION['success'] = 'Docente eliminado exitosamente';
            } else {
                $_SESSION['error'] = 'Error al eliminar docente';
            }
        }
        
        $this->redirect('/docentes');
    }
    
    /**
     * Registrar huella digital
     */
    public function registrarHuella($id) {
        $this->requireRole(['admin', 'supervisor']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Método no permitido'], 405);
        }
        
        // En producción, aquí se procesaría la huella del lector biométrico
        // Por ahora, simulamos el registro
        
        $huellaData = $_POST['huella_data'] ?? null;
        
        if ($huellaData) {
            $updated = $this->docenteModel->update($id, [
                'tiene_huella' => true
            ]);
            
            if ($updated) {
                $this->logModel->registrar(
                    $_SESSION['user_id'],
                    'registrar_huella',
                    'docentes',
                    "Huella registrada para docente ID: {$id}"
                );
                
                $this->json(['success' => true, 'message' => 'Huella registrada exitosamente']);
            }
        }
        
        $this->json(['success' => false, 'message' => 'Error al registrar huella'], 400);
    }
    
    /**
     * Búsqueda AJAX
     */
    public function search() {
        $this->requireAuth();
        
        try {
            $term = $_GET['term'] ?? '';
            $filters = [
                'estado' => $_GET['estado'] ?? '',
                'carrera_id' => $_GET['carrera_id'] ?? ''
            ];
            
            $results = $this->docenteModel->search($term, $filters);
            $this->json($results);
        } catch (Exception $e) {
            error_log("Search Error: " . $e->getMessage());
            $this->json(['error' => 'Error interno al buscar docentes'], 500);
        }
    }
}

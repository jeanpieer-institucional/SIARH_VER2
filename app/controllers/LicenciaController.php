<?php
/**
 * LicenciaController - Controlador de Licencias y Permisos
 */

class LicenciaController extends Controller {
    private $licenciaModel;
    private $docenteModel;
    private $logModel;
    
    public function __construct() {
        $this->licenciaModel = new Licencia();
        $this->docenteModel = new Docente();
        $this->logModel = new LogActividad();
    }
    
    /**
     * Lista de licencias
     */
    public function index() {
        $this->requireAuth();
        
        $estado = $_GET['estado'] ?? '';
        $filtros = [];
        
        if ($estado) {
            $filtros['estado'] = $estado;
        }
        
        // Si es docente, solo ver sus propias licencias
        if ($_SESSION['user_role'] === 'docente') {
            $docente = $this->docenteModel->getByUsuarioId($_SESSION['user_id']);
            if ($docente) {
                $filtros['docente_id'] = $docente['id'];
            } else {
                // Failsafe por si el usuario docente no tiene un registro docente enlazado aún
                $filtros['docente_id'] = -1; 
            }
        }
        
        $licencias = $this->licenciaModel->getAllWithDetails($filtros);
        
        // Pendientes solo para admins/supervisores
        $pendientes = 0;
        if (in_array($_SESSION['user_role'], ['admin', 'supervisor'])) {
            $pendientes = count($this->licenciaModel->getPendientes());
        }
        
        $data = [
            'licencias' => $licencias,
            'estado' => $estado,
            'pendientes' => $pendientes
        ];
        
        $this->view('licencias/index', $data);
    }
    
    /**
     * Formulario de solicitud
     */
    public function create() {
        $this->requireAuth();
        
        $docentes = [];
        $docenteActual = null;
        
        if ($_SESSION['user_role'] === 'docente') {
            $docenteActual = $this->docenteModel->getByUsuarioId($_SESSION['user_id']);
            if (!$docenteActual) {
                $_SESSION['error'] = 'No tiene un perfil de docente asignado.';
                $this->redirect('/licencias');
            }
        } else {
            $docentes = $this->docenteModel->getAll(['estado' => 'activo'], 'apellidos, nombres');
        }
        
        $data = [
            'docentes' => $docentes,
            'docenteActual' => $docenteActual
        ];
        
        $this->view('licencias/form', $data);
    }
    
    /**
     * Guardar solicitud
     */
    public function store() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/licencias/create');
        }
        
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Error de seguridad: Token CSRF inválido';
            $this->redirect('/licencias/create');
        }
        
        $docenteId = $_POST['docente_id'] ?? null;
        
        // Forzar ID si es docente
        if ($_SESSION['user_role'] === 'docente') {
            $docente = $this->docenteModel->getByUsuarioId($_SESSION['user_id']);
            $docenteId = $docente ? $docente['id'] : null;
        }
        
        if (!$docenteId) {
            $_SESSION['error'] = 'Docente no válido.';
            $this->redirect('/licencias/create');
        }
        
        $licenciaData = [
            'docente_id' => $docenteId,
            'tipo' => $_POST['tipo'],
            'fecha_inicio' => $_POST['fecha_inicio'],
            'fecha_fin' => $_POST['fecha_fin'],
            'motivo' => $_POST['motivo'],
            'estado' => 'pendiente'
        ];
        
        // Manejar archivo adjunto
        if (isset($_FILES['documento']) && $_FILES['documento']['error'] === 0) {
            $uploadDir = UPLOAD_DIR . '/licencias/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Validar tipo de archivo
            $allowedMimeTypes = ['application/pdf', 'image/jpeg', 'image/png', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            $fileInfo = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $fileInfo->file($_FILES['documento']['tmp_name']);
            
            if (!in_array($mimeType, $allowedMimeTypes)) {
                $_SESSION['error'] = 'Tipo de archivo no permitido. Solo PDF, JPG, PNG, DOC, DOCX.';
                $this->redirect('/licencias/create');
            }
            
            // Validar tamaño (Max 5MB)
            if ($_FILES['documento']['size'] > 5 * 1024 * 1024) {
                $_SESSION['error'] = 'El archivo excede el tamaño máximo de 5MB.';
                $this->redirect('/licencias/create');
            }
            
            // Generar nombre seguro
            $extension = pathinfo($_FILES['documento']['name'], PATHINFO_EXTENSION);
            $fileName = uniqid('lic_', true) . '.' . $extension;
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['documento']['tmp_name'], $targetPath)) {
                $licenciaData['documento_adjunto'] = $fileName;
            }
        }
        
        $licenciaId = $this->licenciaModel->create($licenciaData);
        
        if ($licenciaId) {
            $docenteInfo = $this->docenteModel->getById($docenteId);
            $this->logModel->registrar(
                $_SESSION['user_id'],
                'crear_licencia',
                'licencias',
                "Licencia solicitada para: {$docenteInfo['nombres']} {$docenteInfo['apellidos']}"
            );
            
            $_SESSION['success'] = 'Solicitud de licencia enviada exitosamente';
            $this->redirect('/licencias');
        } else {
            $_SESSION['error'] = 'Error al enviar solicitud';
            $this->redirect('/licencias/create');
        }
    }
    
    /**
     * Aprobar licencia
     */
    public function aprobar($id) {
        $this->requireRole(['admin', 'supervisor']);
        
        $comentarios = $_POST['comentarios'] ?? '';
        
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Error de seguridad: Token CSRF inválido';
            $this->redirect('/licencias');
        }
        
        if ($this->licenciaModel->aprobar($id, $_SESSION['user_id'], $comentarios)) {
            $licencia = $this->licenciaModel->getById($id);
            $docente = $this->docenteModel->getById($licencia['docente_id']);
            
            $this->logModel->registrar(
                $_SESSION['user_id'],
                'aprobar_licencia',
                'licencias',
                "Licencia aprobada para: {$docente['nombres']} {$docente['apellidos']}"
            );
            
            $_SESSION['success'] = 'Licencia aprobada exitosamente';
        } else {
            $_SESSION['error'] = 'Error al aprobar licencia';
        }
        
        $this->redirect('/licencias');
    }
    
    /**
     * Rechazar licencia
     */
    public function rechazar($id) {
        $this->requireRole(['admin', 'supervisor']);
        
        $comentarios = $_POST['comentarios'] ?? 'Sin comentarios';
        
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Error de seguridad: Token CSRF inválido';
            $this->redirect('/licencias');
        }
        
        if ($this->licenciaModel->rechazar($id, $_SESSION['user_id'], $comentarios)) {
            $licencia = $this->licenciaModel->getById($id);
            $docente = $this->docenteModel->getById($licencia['docente_id']);
            
            $this->logModel->registrar(
                $_SESSION['user_id'],
                'rechazar_licencia',
                'licencias',
                "Licencia rechazada para: {$docente['nombres']} {$docente['apellidos']}"
            );
            
            $_SESSION['success'] = 'Licencia rechazada';
        } else {
            $_SESSION['error'] = 'Error al rechazar licencia';
        }
        
        $this->redirect('/licencias');
    }
    
    /**
     * Ver detalles de licencia
     */
    public function ver($id) {
        $this->requireAuth();
        
        $licencia = $this->licenciaModel->getById($id);
        
        if (!$licencia) {
            $_SESSION['error'] = 'Licencia no encontrada';
            $this->redirect('/licencias');
        }
        
        $docente = $this->docenteModel->getById($licencia['docente_id']);
        
        $data = [
            'licencia' => $licencia,
            'docente' => $docente
        ];
        
        $this->view('licencias/ver', $data);
    }
}

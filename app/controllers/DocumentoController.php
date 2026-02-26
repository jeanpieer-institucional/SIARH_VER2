<?php
/**
 * DocumentoController - Controlador de Gestión Documental de Docentes
 */

class DocumentoController extends Controller {
    private $documentoModel;
    private $logModel;
    private $docenteModel;
    
    public function __construct() {
        $this->documentoModel = new Documento();
        $this->logModel = new LogActividad();
        $this->docenteModel = new Docente();
    }
    
    /**
     * Subir archivo
     */
    public function upload() {
        $this->requireRole(['admin', 'supervisor']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/docentes');
        }
        
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Error de seguridad: Token CSRF inválido';
            $this->redirect('/docentes/edit/' . $_POST['docente_id']);
        }
        
        $docenteId = $_POST['docente_id'];
        $tipoDocumento = $_POST['tipo_documento'];
        $file = $_FILES['documento'] ?? null;
        
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'Error al subir el archivo.';
            $this->redirect("/docentes/edit/{$docenteId}");
        }
        
        // Validar tipo de archivo
        $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        if (!in_array($file['type'], $allowedTypes)) {
            $_SESSION['error'] = 'Formato de archivo no permitido. Solo PDF, JPG, PNG o Word.';
            $this->redirect("/docentes/edit/{$docenteId}");
        }
        
        // Validar tamaño máximo (Ej. 5MB)
        $maxSize = 5 * 1024 * 1024;
        if ($file['size'] > $maxSize) {
            $_SESSION['error'] = 'El archivo es demasiado grande (máximo 5MB).';
            $this->redirect("/docentes/edit/{$docenteId}");
        }
        
        $docente = $this->docenteModel->getById($docenteId);
        if (!$docente) {
            $this->redirect("/docentes");
        }
        
        // Crear carpeta si no existe
        $uploadDir = BASE_PATH . '/public/uploads/documentos/' . $docenteId;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileName = time() . '_' . basename($file['name']);
        $fileName = preg_replace("/[^a-zA-Z0-9.\-_]/", "", $fileName); // Limpiar nombre
        $filePath = $uploadDir . '/' . $fileName;
        
        $dbPath = 'uploads/documentos/' . $docenteId . '/' . $fileName;
        
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            $docId = $this->documentoModel->create([
                'docente_id' => $docenteId,
                'nombre_archivo' => $file['name'],
                'ruta_archivo' => $dbPath,
                'tipo_documento' => $tipoDocumento,
                'subido_por' => $_SESSION['user_id']
            ]);
            
            if ($docId) {
                $this->logModel->registrar(
                    $_SESSION['user_id'],
                    'subir_documento',
                    'documentos',
                    "Documento '{$file['name']}' ({$tipoDocumento}) subido para {$docente['nombres']} {$docente['apellidos']}"
                );
                $_SESSION['success'] = 'Documento subido exitosamente.';
            } else {
                unlink($filePath); // Borrar si falló la BD
                $_SESSION['error'] = 'Error al guardar en base de datos.';
            }
        } else {
            $_SESSION['error'] = 'Error al mover el archivo subido.';
        }
        
        $this->redirect("/docentes/edit/{$docenteId}");
    }
    
    /**
     * Descargar archivo
     */
    public function download($id) {
        $this->requireAuth();
        
        $documento = $this->documentoModel->getById($id);
        
        if (!$documento) {
            die("Documento no encontrado.");
        }
        
        $filePath = BASE_PATH . '/public/' . $documento['ruta_archivo'];
        
        if (file_exists($filePath)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($documento['nombre_archivo']).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filePath));
            flush();
            readfile($filePath);
            
            $this->logModel->registrar(
                $_SESSION['user_id'],
                'descargar_documento',
                'documentos',
                "Descarga de documento: {$documento['nombre_archivo']}"
            );
            exit;
        } else {
            die("El archivo físico no existe en el servidor.");
        }
    }
    
    /**
     * Eliminar archivo
     */
    public function delete($id) {
        $this->requireRole('admin');
        
        $documento = $this->documentoModel->getById($id);
        
        if (!$documento) {
            $_SESSION['error'] = 'Documento no encontrado.';
            $this->redirect($_SERVER['HTTP_REFERER'] ?? '/docentes');
        }
        
        $docenteId = $documento['docente_id'];
        $filePath = BASE_PATH . '/public/' . $documento['ruta_archivo'];
        
        if ($this->documentoModel->delete($id)) {
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            
            $this->logModel->registrar(
                $_SESSION['user_id'],
                'eliminar_documento',
                'documentos',
                "Documento eliminado: {$documento['nombre_archivo']}"
            );
            
            $_SESSION['success'] = 'Documento eliminado correctamente.';
        } else {
            $_SESSION['error'] = 'Error al eliminar el documento de la base de datos.';
        }
        
        $this->redirect("/docentes/edit/{$docenteId}");
    }
}

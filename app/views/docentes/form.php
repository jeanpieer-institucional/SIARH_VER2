<?php 
$pageTitle = isset($docente) ? 'Editar Docente' : 'Nuevo Docente';
require_once __DIR__ . '/../layouts/header.php'; 
$isEdit = isset($docente);
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-<?= $isEdit ? 'edit' : 'plus' ?>"></i>
            <?= $pageTitle ?>
        </h3>
        <a href="<?= APP_URL ?>/docentes" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Volver
        </a>
    </div>
    
    <div class="card-body">
        <form action="<?= APP_URL ?>/docentes/<?= $isEdit ? 'update/' . $docente['id'] : 'store' ?>" method="POST">
            <?= Csrf::input() ?>
            <div class="grid grid-cols-2">
                <!-- Información Personal -->
                <div>
                    <h4 style="margin-bottom: var(--spacing-lg); color: var(--primary);">
                        <i class="fas fa-user"></i> Información Personal
                    </h4>
                    
                    <div class="form-group">
                        <label class="form-label">Código de Empleado *</label>
                        <input 
                            type="text" 
                            name="codigo_empleado" 
                            class="form-control" 
                            value="<?= $docente['codigo_empleado'] ?? $codigo_empleado ?>"
                            <?= $isEdit ? 'readonly' : '' ?>
                            required
                        >
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Nombres *</label>
                        <input 
                            type="text" 
                            name="nombres" 
                            class="form-control" 
                            value="<?= $docente['nombres'] ?? '' ?>"
                            required
                        >
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Apellidos *</label>
                        <input 
                            type="text" 
                            name="apellidos" 
                            class="form-control" 
                            value="<?= $docente['apellidos'] ?? '' ?>"
                            required
                        >
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">DNI *</label>
                        <input 
                            type="text" 
                            name="dni" 
                            class="form-control" 
                            value="<?= $docente['dni'] ?? '' ?>"
                            maxlength="8"
                            pattern="[0-9]{8}"
                            required
                        >
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Fecha de Ingreso</label>
                        <input 
                            type="date" 
                            name="fecha_ingreso" 
                            class="form-control" 
                            value="<?= $docente['fecha_ingreso'] ?? date('Y-m-d') ?>"
                        >
                    </div>
                </div>
                
                <!-- Información de Contacto y Asignación -->
                <div>
                    <h4 style="margin-bottom: var(--spacing-lg); color: var(--primary);">
                        <i class="fas fa-address-book"></i> Contacto y Asignación
                    </h4>
                    
                    <div class="form-group">
                        <label class="form-label">Email *</label>
                        <input 
                            type="email" 
                            name="email" 
                            class="form-control" 
                            value="<?= $docente['email'] ?? '' ?>"
                            required
                        >
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Teléfono</label>
                        <input 
                            type="tel" 
                            name="telefono" 
                            class="form-control" 
                            value="<?= $docente['telefono'] ?? '' ?>"
                            maxlength="9"
                        >
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Carrera/Departamento</label>
                        <select name="carrera_id" class="form-control">
                            <option value="">Seleccione una carrera</option>
                            <?php foreach ($carreras as $carrera): ?>
                                <option value="<?= $carrera['id'] ?>" 
                                    <?= isset($docente) && $docente['carrera_id'] == $carrera['id'] ? 'selected' : '' ?>>
                                    <?= $carrera['nombre'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Estado *</label>
                        <select name="estado" class="form-control" required>
                            <option value="activo" <?= isset($docente) && $docente['estado'] === 'activo' ? 'selected' : '' ?>>
                                Activo
                            </option>
                            <option value="inactivo" <?= isset($docente) && $docente['estado'] === 'inactivo' ? 'selected' : '' ?>>
                                Inactivo
                            </option>
                            <option value="licencia" <?= isset($docente) && $docente['estado'] === 'licencia' ? 'selected' : '' ?>>
                                En Licencia
                            </option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Dirección</label>
                        <textarea 
                            name="direccion" 
                            class="form-control" 
                            rows="3"
                        ><?= $docente['direccion'] ?? '' ?></textarea>
                    </div>
                    
                    <?php if (!$isEdit): ?>
                    <div class="form-group">
                        <label style="display: flex; align-items: center; gap: var(--spacing-sm); cursor: pointer;">
                            <input type="checkbox" name="crear_usuario" value="1">
                            <span>Crear usuario de acceso al sistema</span>
                        </label>
                        <small class="text-secondary">
                            Usuario: DNI | Contraseña inicial: DNI
                        </small>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="flex-between mt-2" style="padding-top: var(--spacing-lg); border-top: 1px solid var(--border-color);">
                <a href="<?= APP_URL ?>/docentes" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    <?= $isEdit ? 'Actualizar' : 'Guardar' ?> Docente
                </button>
            </div>
        </form>
    </div>
</div>

<?php if ($isEdit): ?>
<div class="card mt-lg">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">
            <i class="fas fa-folder-open"></i> Carpeta Documental del Docente
        </h3>
        <button class="btn btn-primary btn-sm" onclick="document.getElementById('upload-modal').style.display='block'">
            <i class="fas fa-upload"></i> Subir Documento
        </button>
    </div>
    <div class="card-body">
        <?php 
            $docModel = new Documento();
            $documentos = $docModel->getByDocente($docente['id']);
        ?>
        
        <?php if (empty($documentos)): ?>
            <p class="text-center text-secondary py-md">No hay documentos adjuntos para este docente.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Archivo</th>
                            <th>Tipo</th>
                            <th>Subido el</th>
                            <th>Por</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($documentos as $doc): ?>
                        <tr>
                            <td>
                                <i class="fas fa-file-alt text-primary mr-sm"></i>
                                <?= htmlspecialchars($doc['nombre_archivo']) ?>
                            </td>
                            <td><span class="badge badge-info"><?= htmlspecialchars($doc['tipo_documento']) ?></span></td>
                            <td><?= date('d/m/Y H:i', strtotime($doc['fecha_subida'])) ?></td>
                            <td><?= htmlspecialchars($doc['subido_por_username'] ?? 'Sistema') ?></td>
                            <td>
                                <a href="<?= APP_URL ?>/documentos/download/<?= $doc['id'] ?>" class="btn btn-icon btn-secondary" title="Descargar">
                                    <i class="fas fa-download"></i>
                                </a>
                                <?php if ($_SESSION['user_role'] === 'admin'): ?>
                                <button type="button" class="btn btn-icon btn-error" onclick="if(confirm('¿Eliminar documento?')) { document.getElementById('del-doc-<?= $doc['id'] ?>').submit(); }">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <form id="del-doc-<?= $doc['id'] ?>" action="<?= APP_URL ?>/documentos/delete/<?= $doc['id'] ?>" method="POST" style="display:none;"></form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal de Subida -->
<div id="upload-modal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div class="card" style="width: 100%; max-width: 500px; margin: 10% auto; background: var(--bg-card); position: relative;">
        <div class="card-header d-flex flex-between">
            <h3>Subir Documento</h3>
            <button class="btn btn-icon" onclick="document.getElementById('upload-modal').style.display='none'"><i class="fas fa-times"></i></button>
        </div>
        <div class="card-body">
            <form action="<?= APP_URL ?>/documentos/upload" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="docente_id" value="<?= $docente['id'] ?>">
                
                <div class="form-group">
                    <label class="form-label">Tipo de Documento</label>
                    <select name="tipo_documento" class="form-control" required>
                        <option value="Currículum Vitae">Currículum Vitae (CV)</option>
                        <option value="Contrato">Contrato Laboral</option>
                        <option value="DNI/Identidad">Documento de Identidad</option>
                        <option value="Certificado">Certificado Médico</option>
                        <option value="Resolución">Resolución / Amonestación</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>
                
                <div class="form-group mt-md">
                    <label class="form-label">Archivo (PDF, Word, Imagen)</label>
                    <input type="file" name="documento" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                    <small class="text-secondary mt-sm d-block">Tamaño máximo: 5MB</small>
                </div>
                
                <div class="mt-lg flex-end">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-upload"></i> Subir</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    // Close modal if clicked outside
    window.onclick = function(event) {
        let modal = document.getElementById('upload-modal');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>
<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

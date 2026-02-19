<?php 
$pageTitle = 'Nueva Solicitud de Licencia';
require_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-file-alt"></i>
            Solicitar Licencia o Permiso
        </h3>
        <a href="<?= APP_URL ?>/licencias" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Volver
        </a>
    </div>
    
    <div class="card-body">
        <form action="<?= APP_URL ?>/licencias/store" method="POST" enctype="multipart/form-data">
            <?= Csrf::input() ?>
            <div class="grid grid-cols-2">
                <div class="form-group">
                    <label class="form-label">Docente *</label>
                    <select name="docente_id" class="form-control" required>
                        <option value="">Seleccione un docente</option>
                        <?php foreach ($docentes as $docente): ?>
                            <option value="<?= $docente['id'] ?>">
                                <?= $docente['codigo_empleado'] ?> - <?= $docente['nombres'] . ' ' . $docente['apellidos'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Tipo de Licencia *</label>
                    <select name="tipo" class="form-control" required>
                        <option value="">Seleccione el tipo</option>
                        <option value="medica">Médica</option>
                        <option value="personal">Personal</option>
                        <option value="vacaciones">Vacaciones</option>
                        <option value="otros">Otros</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Fecha de Inicio *</label>
                    <input 
                        type="date" 
                        name="fecha_inicio" 
                        class="form-control" 
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label class="form-label">Fecha de Fin *</label>
                    <input 
                        type="date" 
                        name="fecha_fin" 
                        class="form-control" 
                        required
                    >
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Motivo *</label>
                <textarea 
                    name="motivo" 
                    class="form-control" 
                    rows="4"
                    placeholder="Describa el motivo de la solicitud..."
                    required
                ></textarea>
            </div>
            
            <div class="form-group">
                <label class="form-label">Documento Adjunto (opcional)</label>
                <input 
                    type="file" 
                    name="documento" 
                    class="form-control"
                    accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                >
                <small class="text-secondary">
                    Formatos permitidos: PDF, JPG, PNG, DOC, DOCX (Máx. 5MB)
                </small>
            </div>
            
            <div class="flex-between mt-2" style="padding-top: var(--spacing-lg); border-top: 1px solid var(--border-color);">
                <a href="<?= APP_URL ?>/licencias" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i>
                    Enviar Solicitud
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

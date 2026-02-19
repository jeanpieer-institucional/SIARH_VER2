<?php 
$pageTitle = 'Registrar Asistencia';
require_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-clipboard-check"></i>
            Registro Manual de Asistencia
        </h3>
        <a href="<?= APP_URL ?>/asistencias" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Volver
        </a>
    </div>
    
    <div class="card-body">
        <form action="<?= APP_URL ?>/asistencias/store" method="POST">
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
                    <label class="form-label">Fecha *</label>
                    <input 
                        type="date" 
                        name="fecha" 
                        class="form-control" 
                        value="<?= date('Y-m-d') ?>"
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label class="form-label">Hora *</label>
                    <input 
                        type="time" 
                        name="hora" 
                        class="form-control" 
                        value="<?= date('H:i') ?>"
                        required
                    >
                </div>
            </div>
            
            <div class="flex-between mt-2" style="padding-top: var(--spacing-lg); border-top: 1px solid var(--border-color);">
                <a href="<?= APP_URL ?>/asistencias" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Registrar Asistencia
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

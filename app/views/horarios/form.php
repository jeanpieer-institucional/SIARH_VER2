<?php require_once BASE_PATH . '/app/views/layouts/header.php'; ?>

<?php
$isEdit = isset($horario);
$actionUrl = $isEdit ? APP_URL . '/horarios/update/' . $horario['id'] : APP_URL . '/horarios/store';
$title = $isEdit ? 'Editar Horario Semanal' : 'Asignar Nuevo Horario';
$currentDocenteId = $isEdit ? $horario['docente_id'] : ($docenteId ?? '');
?>

<div class="card mb-lg" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h2><?= $title ?></h2>
        <a href="<?= APP_URL ?>/horarios<?= $currentDocenteId ? '?docente_id=' . $currentDocenteId : '' ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
    
    <div class="card-body">
        <form action="<?= $actionUrl ?>" method="POST">
            <?= Csrf::input() ?>
            
            <div class="grid" style="grid-template-columns: 1fr 1fr; gap: var(--spacing-lg);">
                
                <!-- Seleccionar Docente -->
                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label" for="docente_id">Docente <span class="text-error">*</span></label>
                    <select id="docente_id" name="docente_id" class="form-control" required <?= $isEdit ? 'disabled' : '' ?>>
                        <option value="">-- Seleccionar Docente --</option>
                        <?php foreach ($docentes as $doc): ?>
                            <option value="<?= $doc['id'] ?>" <?= ($currentDocenteId == $doc['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($doc['apellidos'] . ', ' . $doc['nombres'] . ' (' . $doc['codigo_empleado'] . ')') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <!-- Si está en modo edición, enviamos el ID en un campo oculto porque el select está deshabilitado -->
                    <?php if ($isEdit): ?>
                        <input type="hidden" name="docente_id" value="<?= $horario['docente_id'] ?>">
                    <?php endif; ?>
                </div>
                
                <!-- Seleccionar Día -->
                <div class="form-group">
                    <label class="form-label" for="dia_semana">Día de la Semana <span class="text-error">*</span></label>
                    <select id="dia_semana" name="dia_semana" class="form-control" required>
                        <option value="">-- Seleccionar Día --</option>
                        <?php foreach ($dias as $dia): ?>
                            <option value="<?= $dia ?>" <?= ($isEdit && $horario['dia_semana'] === $dia) ? 'selected' : '' ?> class="text-capitalize">
                                <?= ucfirst($dia) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Estado (Solo al Editar) -->
                <?php if ($isEdit): ?>
                <div class="form-group">
                    <label class="form-label" for="estado">Estado</label>
                    <select id="estado" name="estado" class="form-control">
                        <option value="activo" <?= ($horario['estado'] === 'activo') ? 'selected' : '' ?>>Activo</option>
                        <option value="inactivo" <?= ($horario['estado'] === 'inactivo') ? 'selected' : '' ?>>Inactivo</option>
                    </select>
                </div>
                <?php else: ?>
                <div class="form-group">
                    <!-- Espacio vacío para mantener la grilla alineada -->
                </div>
                <?php endif; ?>
                
                <!-- Hora de Entrada -->
                <div class="form-group">
                    <label class="form-label" for="hora_entrada">Hora de Entrada (Clase) <span class="text-error">*</span></label>
                    <input 
                        type="time" 
                        id="hora_entrada" 
                        name="hora_entrada" 
                        class="form-control" 
                        value="<?= $isEdit ? htmlspecialchars($horario['hora_entrada']) : '08:00' ?>" 
                        required
                    >
                </div>
                
                <!-- Hora de Salida -->
                <div class="form-group">
                    <label class="form-label" for="hora_salida">Hora de Salida (Clase) <span class="text-error">*</span></label>
                    <input 
                        type="time" 
                        id="hora_salida" 
                        name="hora_salida" 
                        class="form-control" 
                        value="<?= $isEdit ? htmlspecialchars($horario['hora_salida']) : '17:00' ?>" 
                        required
                    >
                </div>
                
            </div>
            
            <div class="mt-lg" style="display: flex; gap: var(--spacing-md); justify-content: flex-end;">
                <a href="<?= APP_URL ?>/horarios<?= $currentDocenteId ? '?docente_id=' . $currentDocenteId : '' ?>" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> <?= $isEdit ? 'Actualizar' : 'Guardar' ?> Horario
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>

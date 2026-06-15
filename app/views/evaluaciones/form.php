<?php require_once BASE_PATH . '/app/views/layouts/header.php'; ?>

<?php
$currentDocenteId = $docenteId ?? '';
?>

<div class="card mb-lg" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h2>Registrar Evaluación de Desempeño</h2>
        <a href="<?= APP_URL ?>/evaluaciones<?= $currentDocenteId ? '?docente_id=' . $currentDocenteId : '' ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
    
    <div class="card-body">
        <form action="<?= APP_URL ?>/evaluaciones/store" method="POST">
            <?= Csrf::input() ?>
            
            <div class="grid" style="grid-template-columns: 1fr 1fr; gap: var(--spacing-lg);">
                
                <!-- Seleccionar Docente -->
                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label" for="docente_id">Docente a Evaluar <span class="text-error">*</span></label>
                    <select id="docente_id" name="docente_id" class="form-control" required>
                        <option value="">-- Seleccionar Docente --</option>
                        <?php foreach ($docentes as $doc): ?>
                            <option value="<?= $doc['id'] ?>" <?= ($currentDocenteId == $doc['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($doc['apellidos'] . ', ' . $doc['nombres'] . ' (' . $doc['codigo_empleado'] . ')') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Periodo Académico / Laboral -->
                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label" for="periodo">Periodo de Evaluación <span class="text-error">*</span></label>
                    <input 
                        type="text" 
                        id="periodo" 
                        name="periodo" 
                        class="form-control" 
                        placeholder="Ej. Semestre 2026-I, Mayo 2026, Anual 2026" 
                        required
                        maxlength="50"
                    >
                </div>
                
                <!-- Calificación de Metodología -->
                <div class="form-group">
                    <label class="form-label" for="puntuacion_metodologia">Metodología y Preparación de Clases <span class="text-error">*</span></label>
                    <select id="puntuacion_metodologia" name="puntuacion_metodologia" class="form-control" required style="color: #ffc107; font-weight: bold;">
                        <option value="" style="color: var(--text-color); font-weight: normal;">-- Calificar (1 a 5) --</option>
                        <option value="5">★★★★★ Excelente (5)</option>
                        <option value="4">★★★★☆ Muy Bueno (4)</option>
                        <option value="3">★★★☆☆ Aceptable (3)</option>
                        <option value="2">★★☆☆☆ Deficiente (2)</option>
                        <option value="1">★☆☆☆☆ Insuficiente (1)</option>
                    </select>
                </div>
                
                <!-- Calificación de Puntualidad -->
                <div class="form-group">
                    <label class="form-label" for="puntuacion_puntualidad">Puntualidad y Cumplimiento de Horarios <span class="text-error">*</span></label>
                    <select id="puntuacion_puntualidad" name="puntuacion_puntualidad" class="form-control" required style="color: #ffc107; font-weight: bold;">
                        <option value="" style="color: var(--text-color); font-weight: normal;">-- Calificar (1 a 5) --</option>
                        <option value="5">★★★★★ Excelente (5)</option>
                        <option value="4">★★★★☆ Muy Bueno (4)</option>
                        <option value="3">★★★☆☆ Aceptable (3)</option>
                        <option value="2">★★☆☆☆ Deficiente (2)</option>
                        <option value="1">★☆☆☆☆ Insuficiente (1)</option>
                    </select>
                </div>
                
                <!-- Calificación de Relación con Alumnos -->
                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label" for="puntuacion_relacion">Relación con los Alumnos y Clima del Aula <span class="text-error">*</span></label>
                    <select id="puntuacion_relacion" name="puntuacion_relacion" class="form-control" required style="color: #ffc107; font-weight: bold;">
                        <option value="" style="color: var(--text-color); font-weight: normal;">-- Calificar (1 a 5) --</option>
                        <option value="5">★★★★★ Excelente (5)</option>
                        <option value="4">★★★★☆ Muy Bueno (4)</option>
                        <option value="3">★★★☆☆ Aceptable (3)</option>
                        <option value="2">★★☆☆☆ Deficiente (2)</option>
                        <option value="1">★☆☆☆☆ Insuficiente (1)</option>
                    </select>
                </div>
                
                <!-- Comentarios y Retroalimentación -->
                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label" for="comentarios">Retroalimentación / Comentarios de Desempeño</label>
                    <textarea 
                        id="comentarios" 
                        name="comentarios" 
                        class="form-control" 
                        rows="4" 
                        placeholder="Escribe comentarios, fortalezas o áreas de mejora del docente..."
                    ></textarea>
                </div>
                
            </div>
            
            <div class="mt-lg" style="display: flex; gap: var(--spacing-md); justify-content: flex-end;">
                <a href="<?= APP_URL ?>/evaluaciones<?= $currentDocenteId ? '?docente_id=' . $currentDocenteId : '' ?>" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Registrar Evaluación
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>

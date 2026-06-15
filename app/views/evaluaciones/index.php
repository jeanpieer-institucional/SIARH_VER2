<?php require_once BASE_PATH . '/app/views/layouts/header.php'; ?>

<!-- Función Helper para renderizar estrellas -->
<?php
function renderStars($rating) {
    $html = '<span style="color: #ffc107; font-size: 0.95rem;">';
    $rounded = round($rating * 2) / 2; // Redondear a la mitad más cercana
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $rounded) {
            $html .= '<i class="fas fa-star"></i>'; // Estrella llena
        } else if ($i - 0.5 == $rounded) {
            $html .= '<i class="fas fa-star-half-alt"></i>'; // Media estrella
        } else {
            $html .= '<i class="far fa-star"></i>'; // Estrella vacía
        }
    }
    $html .= '</span>';
    return $html;
}
?>

<div class="row">
    <!-- Si no hay docente seleccionado, mostrar listado general de docentes -->
    <?php if (!$docenteSeleccionado): ?>
    <div class="col-12">
        <div class="card mb-lg">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2>Evaluaciones de Desempeño</h2>
                <div class="actions">
                    <a href="<?= APP_URL ?>/evaluaciones/create" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nueva Evaluación
                    </a>
                </div>
            </div>
            
            <div class="card-body">
                <p class="text-secondary mb-md">Consulte las valoraciones generales de los docentes o agregue una nueva evaluación periódica.</p>
                
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Docente</th>
                                <th>Evaluaciones</th>
                                <th>Calificación Promedio</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($docentes)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-lg">No hay docentes registrados.</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($docentes as $docente): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($docente['codigo_empleado']) ?></strong></td>
                                    <td><?= htmlspecialchars($docente['apellidos'] . ', ' . $docente['nombres']) ?></td>
                                    <td>
                                        <span class="badge badge-info"><?= $docente['total_evaluaciones'] ?> evaluaciones</span>
                                    </td>
                                    <td>
                                        <?php if ($docente['total_evaluaciones'] > 0): ?>
                                            <?= renderStars($docente['promedio_general']) ?> 
                                            <span style="font-size: 0.9em; font-weight: bold;">(<?= number_format($docente['promedio_general'], 1) ?>/5)</span>
                                        <?php else: ?>
                                            <span class="text-secondary font-sm">Sin calificar</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= APP_URL ?>/evaluaciones?docente_id=<?= $docente['id'] ?>" class="btn btn-secondary btn-sm">
                                            <i class="fas fa-chart-line"></i> Ver Historial
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Si hay docente seleccionado, mostrar su dashboard de promedios e historial de evaluaciones -->
    <?php else: ?>
    <div class="col-12">
        <div class="card mb-lg">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <a href="<?= APP_URL ?>/evaluaciones" class="btn btn-secondary btn-sm mb-xs">
                        <i class="fas fa-arrow-left"></i> Volver a Lista
                    </a>
                    <h2 class="mt-xs">Desempeño de: <?= htmlspecialchars($docenteSeleccionado['nombres'] . ' ' . $docenteSeleccionado['apellidos']) ?></h2>
                    <p class="text-secondary"><?= htmlspecialchars($docenteSeleccionado['codigo_empleado']) ?> | DNI: <?= htmlspecialchars($docenteSeleccionado['dni']) ?></p>
                </div>
                
                <?php if ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'super_admin' || $_SESSION['user_role'] === 'supervisor'): ?>
                <a href="<?= APP_URL ?>/evaluaciones/create?docente_id=<?= $docenteSeleccionado['id'] ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Evaluar Docente
                </a>
                <?php endif; ?>
            </div>
            
            <div class="card-body">
                <!-- Resumen / Promedio de Categorías -->
                <div class="grid mb-lg" style="grid-template-columns: repeat(4, 1fr); gap: var(--spacing-md);">
                    <div class="card text-center" style="background-color: var(--bg-color); border: 1px solid var(--border-color);">
                        <div class="card-body">
                            <span class="text-secondary">Metodología / Clases</span>
                            <h3 class="mt-xs"><?= number_format($promedios['promedio_metodologia'], 1) ?> / 5.0</h3>
                            <div><?= renderStars($promedios['promedio_metodologia']) ?></div>
                        </div>
                    </div>
                    
                    <div class="card text-center" style="background-color: var(--bg-color); border: 1px solid var(--border-color);">
                        <div class="card-body">
                            <span class="text-secondary">Puntualidad General</span>
                            <h3 class="mt-xs"><?= number_format($promedios['promedio_puntualidad'], 1) ?> / 5.0</h3>
                            <div><?= renderStars($promedios['promedio_puntualidad']) ?></div>
                        </div>
                    </div>
                    
                    <div class="card text-center" style="background-color: var(--bg-color); border: 1px solid var(--border-color);">
                        <div class="card-body">
                            <span class="text-secondary">Relación con Alumnos</span>
                            <h3 class="mt-xs"><?= number_format($promedios['promedio_relacion'], 1) ?> / 5.0</h3>
                            <div><?= renderStars($promedios['promedio_relacion']) ?></div>
                        </div>
                    </div>
                    
                    <div class="card text-center" style="background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white;">
                        <div class="card-body">
                            <span style="color: rgba(255,255,255,0.8);">Calificación General</span>
                            <h2 class="mt-xs" style="font-size: 1.8rem;">
                                <?= number_format($promedios['promedio_general'], 1) ?> <span style="font-size: 0.6em;">/ 5.0</span>
                            </h2>
                            <span class="badge" style="background: rgba(255,255,255,0.2); color: white; margin-top: var(--spacing-xs);">
                                <?= $promedios['total_evaluaciones'] ?> Evaluaciones
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Histórico de Evaluaciones -->
                <h3>Historial de Evaluaciones</h3>
                
                <div class="table-responsive mt-md">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Periodo</th>
                                <th>Metodología</th>
                                <th>Puntualidad</th>
                                <th>Relación</th>
                                <th>Comentarios</th>
                                <th>Evaluador / Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($evaluaciones)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-lg">No se han registrado evaluaciones para este docente.</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($evaluaciones as $eval): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($eval['periodo']) ?></strong></td>
                                    <td><?= renderStars($eval['puntuacion_metodologia']) ?> (<?= $eval['puntuacion_metodologia'] ?>)</td>
                                    <td><?= renderStars($eval['puntuacion_puntualidad']) ?> (<?= $eval['puntuacion_puntualidad'] ?>)</td>
                                    <td><?= renderStars($eval['puntuacion_relacion']) ?> (<?= $eval['puntuacion_relacion'] ?>)</td>
                                    <td>
                                        <p class="text-secondary font-sm" style="max-width: 300px; word-wrap: break-word;">
                                            <?= htmlspecialchars($eval['comentarios'] ?: 'Sin comentarios adicionales') ?>
                                        </p>
                                    </td>
                                    <td>
                                        <div class="font-sm"><?= htmlspecialchars($eval['evaluador_nombre']) ?></div>
                                        <div class="text-secondary font-xxs"><?= date('d/m/Y h:i A', strtotime($eval['created_at'])) ?></div>
                                    </td>
                                    <td>
                                        <?php if ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'super_admin'): ?>
                                        <button type="button" class="btn btn-icon btn-error" title="Eliminar Evaluación" onclick="confirmDelete(<?= $eval['id'] ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <form id="delete-form-<?= $eval['id'] ?>" action="<?= APP_URL ?>/evaluaciones/delete/<?= $eval['id'] ?>" method="POST" style="display: none;"></form>
                                        <?php else: ?>
                                        <span class="text-secondary font-xs">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
function confirmDelete(id) {
    if (confirm("¿Está seguro de que desea eliminar este registro de evaluación?\nEsta acción es irreversible.")) {
        document.getElementById(`delete-form-${id}`).submit();
    }
}
</script>

<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>

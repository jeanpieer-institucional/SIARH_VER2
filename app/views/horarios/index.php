<?php require_once BASE_PATH . '/app/views/layouts/header.php'; ?>

<div class="row">
    <!-- Si no hay docente seleccionado, mostrar listado general de docentes -->
    <?php if (!$docenteSeleccionado): ?>
    <div class="col-12">
        <div class="card mb-lg">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2>Gestión de Horarios</h2>
                <div class="actions">
                    <a href="<?= APP_URL ?>/horarios/create" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Asignar Horario
                    </a>
                </div>
            </div>
            
            <div class="card-body">
                <p class="text-secondary mb-md">Selecciona un docente para visualizar o configurar su horario semanal de clases.</p>
                
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Docente</th>
                                <th>Email</th>
                                <th>Horas Registradas</th>
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
                                    <td><?= htmlspecialchars($docente['email']) ?></td>
                                    <td>
                                        <?php if ($docente['total_horarios'] > 0): ?>
                                            <span class="badge badge-success"><?= $docente['total_horarios'] ?> días configurados</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning">Sin horario</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= APP_URL ?>/horarios?docente_id=<?= $docente['id'] ?>" class="btn btn-secondary btn-sm">
                                            <i class="fas fa-calendar-alt"></i> Configurar Horario
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
    
    <!-- Si hay docente seleccionado, mostrar su horario semanal -->
    <?php else: ?>
    <div class="col-12">
        <div class="card mb-lg">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <a href="<?= APP_URL ?>/horarios" class="btn btn-secondary btn-sm mb-xs">
                        <i class="fas fa-arrow-left"></i> Volver a Docentes
                    </a>
                    <h2 class="mt-xs">Horario de: <?= htmlspecialchars($docenteSeleccionado['nombres'] . ' ' . $docenteSeleccionado['apellidos']) ?></h2>
                    <p class="text-secondary"><?= htmlspecialchars($docenteSeleccionado['codigo_empleado']) ?> | DNI: <?= htmlspecialchars($docenteSeleccionado['dni']) ?></p>
                </div>
                
                <?php if ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'super_admin' || $_SESSION['user_role'] === 'supervisor'): ?>
                <a href="<?= APP_URL ?>/horarios/create?docente_id=<?= $docenteSeleccionado['id'] ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Agregar Jornada/Día
                </a>
                <?php endif; ?>
            </div>
            
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Día</th>
                                <th>Hora Entrada</th>
                                <th>Hora Salida</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($horarios)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-lg">
                                    <i class="far fa-calendar-times" style="font-size: 2.5rem; color: var(--border-color); display: block; margin-bottom: var(--spacing-sm);"></i>
                                    Este docente aún no tiene un horario registrado.
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($horarios as $horario): ?>
                                <tr>
                                    <td class="text-capitalize"><strong><?= htmlspecialchars($horario['dia_semana']) ?></strong></td>
                                    <td><?= date('h:i A', strtotime($horario['hora_entrada'])) ?></td>
                                    <td><?= date('h:i A', strtotime($horario['hora_salida'])) ?></td>
                                    <td>
                                        <?php if ($horario['estado'] === 'activo'): ?>
                                            <span class="badge badge-success">Activo</span>
                                        <?php else: ?>
                                            <span class="badge badge-error">Inactivo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <?php if ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'super_admin' || $_SESSION['user_role'] === 'supervisor'): ?>
                                            <a href="<?= APP_URL ?>/horarios/edit/<?= $horario['id'] ?>" class="btn btn-icon btn-secondary" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php endif; ?>
                                            
                                            <?php if ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'super_admin'): ?>
                                            <button type="button" class="btn btn-icon btn-error" title="Eliminar" onclick="confirmDelete(<?= $horario['id'] ?>, '<?= $horario['dia_semana'] ?>')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <form id="delete-form-<?= $horario['id'] ?>" action="<?= APP_URL ?>/horarios/delete/<?= $horario['id'] ?>" method="POST" style="display: none;"></form>
                                            <?php endif; ?>
                                        </div>
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
function confirmDelete(id, day) {
    if (confirm(`¿Está seguro de que desea eliminar la jornada del día "${day}"?\nEsta acción es irreversible.`)) {
        document.getElementById(`delete-form-${id}`).submit();
    }
}
</script>

<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>

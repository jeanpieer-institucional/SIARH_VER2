<?php require_once BASE_PATH . '/app/views/layouts/header.php'; ?>

<div class="card mb-lg">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h2>Gestión de Carreras y Departamentos</h2>
        <?php if ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'super_admin' || $_SESSION['user_role'] === 'supervisor'): ?>
        <a href="<?= APP_URL ?>/carreras/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nueva Carrera
        </a>
        <?php endif; ?>
    </div>
    
    <div class="card-body">
        
        <div class="table-responsive mt-md">
            <table class="table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Docentes</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($carreras)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-lg">No hay carreras registradas.</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($carreras as $carrera): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($carrera['codigo']) ?></strong></td>
                            <td><?= htmlspecialchars($carrera['nombre']) ?></td>
                            <td>
                                <span class="text-secondary" style="font-size: 0.85em; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;" title="<?= htmlspecialchars($carrera['descripcion']) ?>">
                                    <?= htmlspecialchars($carrera['descripcion'] ?: 'Sin descripción') ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-info">
                                    <?= $carrera['docentes_activos'] ?> / <?= $carrera['total_docentes'] ?> Activos
                                </span>
                            </td>
                            <td>
                                <?php if ($carrera['estado'] === 'activo'): ?>
                                    <span class="badge badge-success">Activo</span>
                                <?php else: ?>
                                    <span class="badge badge-error">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <?php if ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'super_admin' || $_SESSION['user_role'] === 'supervisor'): ?>
                                    <a href="<?= APP_URL ?>/carreras/edit/<?= $carrera['id'] ?>" class="btn btn-icon btn-secondary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php endif; ?>
                                    
                                    <?php if ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'super_admin'): ?>
                                    <button type="button" class="btn btn-icon btn-error" title="Eliminar" onclick="confirmDelete(<?= $carrera['id'] ?>, '<?= htmlspecialchars($carrera['nombre']) ?>')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <form id="delete-form-<?= $carrera['id'] ?>" action="<?= APP_URL ?>/carreras/delete/<?= $carrera['id'] ?>" method="POST" style="display: none;"></form>
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

<script>
function confirmDelete(id, name) {
    if (confirm(`¿Está seguro que desea eliminar la carrera "${name}"?\nEsta acción es irreversible y podría fallar si hay docentes vinculados.`)) {
        document.getElementById(`delete-form-${id}`).submit();
    }
}
</script>

<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>

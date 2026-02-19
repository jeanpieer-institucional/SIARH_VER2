<?php 
$pageTitle = 'Gestión de Usuarios';
require_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-users-cog"></i>
            Usuarios del Sistema
        </h3>
        <a href="<?= APP_URL ?>/usuarios/create" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Nuevo Usuario
        </a>
    </div>
    
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Último Acceso</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <div class="avatar-circle" style="width: 30px; height: 30px; font-size: 0.8rem;">
                                    <?= strtoupper(substr($usuario['username'], 0, 2)) ?>
                                </div>
                                <span class="font-medium"><?= $usuario['username'] ?></span>
                            </div>
                        </td>
                        <td><?= $usuario['email'] ?></td>
                        <td>
                            <?php 
                                $rolLabel = ucfirst($usuario['rol']);
                                $badgeClass = 'info';
                                
                                if ($usuario['rol'] === 'super_admin') {
                                    $rolLabel = 'Super Admin';
                                    $badgeClass = 'danger'; // O 'primary' para destacar
                                } else if ($usuario['rol'] === 'admin') {
                                    $badgeClass = 'warning';
                                }
                            ?>
                            <span class="badge badge-<?= $badgeClass ?>">
                                <?= $rolLabel ?>
                            </span>
                        </td>
                        <td>
                            <span class="status-badge status-<?= $usuario['estado'] ?>">
                                <?= ucfirst($usuario['estado']) ?>
                            </span>
                        </td>
                        <td class="text-secondary text-sm">
                            <?= $usuario['ultimo_acceso'] ?? 'Nunca' ?>
                        </td>
                        <td>
                            <div class="actions">
                                <a href="<?= APP_URL ?>/usuarios/edit/<?= $usuario['id'] ?>" class="btn-icon" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <?php if ($_SESSION['user_role'] === 'super_admin' && $usuario['id'] != $_SESSION['user_id']): ?>
                                <a href="<?= APP_URL ?>/usuarios/delete/<?= $usuario['id'] ?>" 
                                   class="btn-icon text-error" 
                                   title="Eliminar"
                                   onclick="return confirm('¿Está seguro de eliminar este usuario? Esta acción no se puede deshacer.')">
                                    <i class="fas fa-trash"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

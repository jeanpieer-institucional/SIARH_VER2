<?php 
$pageTitle = 'Mi Perfil';
require_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-user-circle"></i>
            Información del Usuario
        </h3>
    </div>
    <div class="card-body">
        <div class="grid grid-cols-2">
            <div class="form-group">
                <label class="text-secondary">Nombre de Usuario</label>
                <div class="text-lg font-bold"><?= $user['username'] ?></div>
            </div>
            <div class="form-group">
                <label class="text-secondary">Rol del Sistema</label>
                <div>
                    <span class="status-badge status-<?= $user['rol'] === 'admin' ? 'activo' : 'pendiente' ?>">
                        <?= ucfirst($user['rol']) ?>
                    </span>
                </div>
            </div>
            <div class="form-group">
                <label class="text-secondary">Email</label>
                <div><?= $user['email'] ?? 'No registrado' ?></div>
            </div>
            <div class="form-group">
                <label class="text-secondary">Último Acceso</label>
                <div><?= $user['ultimo_acceso'] ?? 'Primer inicio de sesión' ?></div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-key"></i>
            Cambiar Contraseña
        </h3>
    </div>
    <div class="card-body">
        <form action="<?= APP_URL ?>/perfil/update_password" method="POST">
            <?= Csrf::input() ?>
            
            <div class="form-group">
                <label class="form-label">Contraseña Actual *</label>
                <input 
                    type="password" 
                    name="current_password" 
                    class="form-control" 
                    required
                >
            </div>
            
            <div class="grid grid-cols-2">
                <div class="form-group">
                    <label class="form-label">Nueva Contraseña *</label>
                    <input 
                        type="password" 
                        name="new_password" 
                        class="form-control" 
                        required
                        minlength="6"
                    >
                    <small class="text-secondary">Mínimo 6 caracteres</small>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Confirmar Nueva Contraseña *</label>
                    <input 
                        type="password" 
                        name="confirm_password" 
                        class="form-control" 
                        required
                        minlength="6"
                    >
                </div>
            </div>
            
            <div class="mt-4 text-right">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Actualizar Contraseña
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

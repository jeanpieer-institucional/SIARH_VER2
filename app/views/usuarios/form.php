<?php 
$isEdit = isset($usuario);
$pageTitle = $isEdit ? 'Editar Usuario' : 'Nuevo Usuario';
require_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-<?= $isEdit ? 'user-edit' : 'user-plus' ?>"></i>
            <?= $pageTitle ?>
        </h3>
        <a href="<?= APP_URL ?>/usuarios" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Volver
        </a>
    </div>
    
    <div class="card-body">
        <form action="<?= APP_URL ?>/usuarios/<?= $isEdit ? 'update/' . $usuario['id'] : 'store' ?>" method="POST">
            <?= Csrf::input() ?>
            
            <div class="grid grid-cols-2">
                <div class="form-group">
                    <label class="form-label">Nombre de Usuario *</label>
                    <input 
                        type="text" 
                        name="username" 
                        class="form-control" 
                        value="<?= $usuario['username'] ?? '' ?>"
                        required
                        <?= $isEdit ? 'readonly' : '' ?>
                    >
                    <?php if ($isEdit): ?>
                    <small class="text-secondary">El nombre de usuario no se puede cambiar</small>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input 
                        type="email" 
                        name="email" 
                        class="form-control" 
                        value="<?= $usuario['email'] ?? '' ?>"
                    >
                </div>
                
                <div class="form-group">
                    <label class="form-label">Rol *</label>
                    <select name="rol" class="form-control" required>
                        <option value="docente" <?= ($usuario['rol'] ?? '') === 'docente' ? 'selected' : '' ?>>Docente</option>
                        <option value="supervisor" <?= ($usuario['rol'] ?? '') === 'supervisor' ? 'selected' : '' ?>>Supervisor</option>
                        <option value="admin" <?= ($usuario['rol'] ?? '') === 'admin' ? 'selected' : '' ?>>Administrador</option>
                        <?php if ($_SESSION['user_role'] === 'super_admin'): ?>
                        <option value="super_admin" <?= ($usuario['rol'] ?? '') === 'super_admin' ? 'selected' : '' ?>>Super Admin</option>
                        <?php endif; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Estado *</label>
                    <select name="estado" class="form-control" required>
                        <option value="activo" <?= ($usuario['estado'] ?? '') === 'activo' ? 'selected' : '' ?>>Activo</option>
                        <option value="inactivo" <?= ($usuario['estado'] ?? '') === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Contraseña <?= $isEdit ? '(Dejar en blanco para mantener)' : '*' ?></label>
                    <input 
                        type="password" 
                        name="password" 
                        class="form-control" 
                        <?= $isEdit ? '' : 'required' ?>
                        minlength="6"
                    >
                </div>
            </div>
            
            <div class="mt-4 flex-between">
                <a href="<?= APP_URL ?>/usuarios" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    <?= $isEdit ? 'Actualizar' : 'Guardar' ?> Usuario
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

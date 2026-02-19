<?php 
$pageTitle = isset($docente) ? 'Editar Docente' : 'Nuevo Docente';
require_once __DIR__ . '/../layouts/header.php'; 
$isEdit = isset($docente);
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-<?= $isEdit ? 'edit' : 'plus' ?>"></i>
            <?= $pageTitle ?>
        </h3>
        <a href="<?= APP_URL ?>/docentes" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Volver
        </a>
    </div>
    
    <div class="card-body">
        <form action="<?= APP_URL ?>/docentes/<?= $isEdit ? 'update/' . $docente['id'] : 'store' ?>" method="POST">
            <?= Csrf::input() ?>
            <div class="grid grid-cols-2">
                <!-- Información Personal -->
                <div>
                    <h4 style="margin-bottom: var(--spacing-lg); color: var(--primary);">
                        <i class="fas fa-user"></i> Información Personal
                    </h4>
                    
                    <div class="form-group">
                        <label class="form-label">Código de Empleado *</label>
                        <input 
                            type="text" 
                            name="codigo_empleado" 
                            class="form-control" 
                            value="<?= $docente['codigo_empleado'] ?? $codigo_empleado ?>"
                            <?= $isEdit ? 'readonly' : '' ?>
                            required
                        >
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Nombres *</label>
                        <input 
                            type="text" 
                            name="nombres" 
                            class="form-control" 
                            value="<?= $docente['nombres'] ?? '' ?>"
                            required
                        >
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Apellidos *</label>
                        <input 
                            type="text" 
                            name="apellidos" 
                            class="form-control" 
                            value="<?= $docente['apellidos'] ?? '' ?>"
                            required
                        >
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">DNI *</label>
                        <input 
                            type="text" 
                            name="dni" 
                            class="form-control" 
                            value="<?= $docente['dni'] ?? '' ?>"
                            maxlength="8"
                            pattern="[0-9]{8}"
                            required
                        >
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Fecha de Ingreso</label>
                        <input 
                            type="date" 
                            name="fecha_ingreso" 
                            class="form-control" 
                            value="<?= $docente['fecha_ingreso'] ?? date('Y-m-d') ?>"
                        >
                    </div>
                </div>
                
                <!-- Información de Contacto y Asignación -->
                <div>
                    <h4 style="margin-bottom: var(--spacing-lg); color: var(--primary);">
                        <i class="fas fa-address-book"></i> Contacto y Asignación
                    </h4>
                    
                    <div class="form-group">
                        <label class="form-label">Email *</label>
                        <input 
                            type="email" 
                            name="email" 
                            class="form-control" 
                            value="<?= $docente['email'] ?? '' ?>"
                            required
                        >
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Teléfono</label>
                        <input 
                            type="tel" 
                            name="telefono" 
                            class="form-control" 
                            value="<?= $docente['telefono'] ?? '' ?>"
                            maxlength="9"
                        >
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Carrera/Departamento</label>
                        <select name="carrera_id" class="form-control">
                            <option value="">Seleccione una carrera</option>
                            <?php foreach ($carreras as $carrera): ?>
                                <option value="<?= $carrera['id'] ?>" 
                                    <?= isset($docente) && $docente['carrera_id'] == $carrera['id'] ? 'selected' : '' ?>>
                                    <?= $carrera['nombre'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Estado *</label>
                        <select name="estado" class="form-control" required>
                            <option value="activo" <?= isset($docente) && $docente['estado'] === 'activo' ? 'selected' : '' ?>>
                                Activo
                            </option>
                            <option value="inactivo" <?= isset($docente) && $docente['estado'] === 'inactivo' ? 'selected' : '' ?>>
                                Inactivo
                            </option>
                            <option value="licencia" <?= isset($docente) && $docente['estado'] === 'licencia' ? 'selected' : '' ?>>
                                En Licencia
                            </option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Dirección</label>
                        <textarea 
                            name="direccion" 
                            class="form-control" 
                            rows="3"
                        ><?= $docente['direccion'] ?? '' ?></textarea>
                    </div>
                    
                    <?php if (!$isEdit): ?>
                    <div class="form-group">
                        <label style="display: flex; align-items: center; gap: var(--spacing-sm); cursor: pointer;">
                            <input type="checkbox" name="crear_usuario" value="1">
                            <span>Crear usuario de acceso al sistema</span>
                        </label>
                        <small class="text-secondary">
                            Usuario: DNI | Contraseña inicial: DNI
                        </small>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="flex-between mt-2" style="padding-top: var(--spacing-lg); border-top: 1px solid var(--border-color);">
                <a href="<?= APP_URL ?>/docentes" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    <?= $isEdit ? 'Actualizar' : 'Guardar' ?> Docente
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

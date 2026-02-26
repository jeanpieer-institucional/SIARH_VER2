<?php require_once BASE_PATH . '/app/views/layouts/header.php'; ?>

<?php
$isEdit = isset($carrera);
$actionUrl = $isEdit ? APP_URL . '/carreras/update/' . $carrera['id'] : APP_URL . '/carreras/store';
$title = $isEdit ? 'Editar Carrera / Departamento' : 'Registrar Nueva Carrera';
?>

<div class="card mb-lg" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h2><?= $title ?></h2>
        <a href="<?= APP_URL ?>/carreras" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver a Lista
        </a>
    </div>
    
    <div class="card-body">
        <form action="<?= $actionUrl ?>" method="POST">
            <?= Csrf::input() ?>
            
            <div class="grid" style="grid-template-columns: 1fr 1fr; gap: var(--spacing-lg);">
                
                <div class="form-group">
                    <label class="form-label" for="codigo">Código Único <span class="text-error">*</span></label>
                    <input 
                        type="text" 
                        id="codigo" 
                        name="codigo" 
                        class="form-control" 
                        value="<?= $isEdit ? htmlspecialchars($carrera['codigo']) : '' ?>" 
                        required
                        maxlength="20"
                        placeholder="Ej. ING-SIS"
                        style="text-transform: uppercase;"
                    >
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="nombre">Nombre de la Carrera <span class="text-error">*</span></label>
                    <input 
                        type="text" 
                        id="nombre" 
                        name="nombre" 
                        class="form-control" 
                        value="<?= $isEdit ? htmlspecialchars($carrera['nombre']) : '' ?>" 
                        required
                        maxlength="100"
                    >
                </div>
                
                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label" for="descripcion">Descripción</label>
                    <textarea 
                        id="descripcion" 
                        name="descripcion" 
                        class="form-control" 
                        rows="3"
                    ><?= $isEdit ? htmlspecialchars($carrera['descripcion']) : '' ?></textarea>
                </div>
                
                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label" for="estado">Estado</label>
                    <select id="estado" name="estado" class="form-control">
                        <option value="activo" <?= ($isEdit && $carrera['estado'] === 'activo') ? 'selected' : '' ?>>Activo</option>
                        <option value="inactivo" <?= ($isEdit && $carrera['estado'] === 'inactivo') ? 'selected' : '' ?>>Inactivo</option>
                    </select>
                </div>
                
            </div>
            
            <div class="mt-lg" style="display: flex; gap: var(--spacing-md); justify-content: flex-end;">
                <a href="<?= APP_URL ?>/carreras" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> <?= $isEdit ? 'Actualizar' : 'Guardar' ?> Carrera
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>

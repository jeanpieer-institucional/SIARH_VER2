<?php 
$pageTitle = 'Configuración del Sistema';
require_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="row justify-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-cog"></i>
                    Ajustes Generales
                </h3>
            </div>
            
            <div class="card-body">
                <form action="<?= APP_URL ?>/configuracion/update" method="POST">
                    <?= Csrf::input() ?>
                    <!-- Información de la Empresa -->
                    <h4 class="mb-3 text-primary">Información de la Institución</h4>
                    
                    <div class="form-group mb-3">
                        <label class="form-label">Nombre de la Institución</label>
                        <input type="text" name="empresa_nombre" class="form-control" 
                               value="<?= htmlspecialchars($settings['empresa_nombre'] ?? '') ?>" required>
                    </div>
                    
                    <div class="form-group mb-4">
                        <label class="form-label">Dirección</label>
                        <input type="text" name="empresa_direccion" class="form-control" 
                               value="<?= htmlspecialchars($settings['empresa_direccion'] ?? '') ?>">
                    </div>
                    
                    <hr class="mb-4">
                    
                    <!-- Configuración Regional -->
                    <h4 class="mb-3 text-primary">Configuración Regional</h4>
                    
                    <div class="form-group mb-4">
                        <label class="form-label">Zona Horaria</label>
                        <select name="timezone" class="form-control">
                            <option value="America/Lima" <?= ($settings['timezone'] ?? '') === 'America/Lima' ? 'selected' : '' ?>>America/Lima (GMT-5)</option>
                            <option value="America/Bogota" <?= ($settings['timezone'] ?? '') === 'America/Bogota' ? 'selected' : '' ?>>America/Bogota (GMT-5)</option>
                            <option value="America/La_Paz" <?= ($settings['timezone'] ?? '') === 'America/La_Paz' ? 'selected' : '' ?>>America/La_Paz (GMT-4)</option>
                        </select>
                    </div>
                    
                    <hr class="mb-4">
                    
                    <!-- Configuración de Asistencia -->
                    <h4 class="mb-3 text-primary">Control de Asistencia</h4>
                    
                    <div class="flex-between mb-3 bg-light p-3 rounded">
                        <label class="form-label mb-0" for="permitir_registro_tardio">Permitir registro tardío (con marca de tardanza)</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="permitir_registro_tardio" name="permitir_registro_tardio" 
                                   <?= !empty($settings['permitir_registro_tardio']) ? 'checked' : '' ?>>
                        </div>
                    </div>
                    
                    <div class="form-group mb-4">
                        <label class="form-label">Minutos de tolerancia</label>
                        <div class="input-group">
                            <input type="number" name="minutos_tolerancia" class="form-control" min="0" max="60" 
                                   value="<?= $settings['minutos_tolerancia'] ?? 15 ?>">
                            <span class="input-group-text">minutos</span>
                        </div>
                        <small class="text-secondary">Tiempo permitido después de la hora de entrada antes de marcar tardanza.</small>
                    </div>
                    
                    <hr class="mb-4">
                    
                    <!-- Sistema -->
                    <h4 class="mb-3 text-primary">Mantenimiento</h4>
                    
                    <div class="form-group mb-4">
                        <label class="form-label">Frecuencia de Backup Automático</label>
                        <select name="backup_frecuencia" class="form-control">
                            <option value="diario" <?= ($settings['backup_frecuencia'] ?? '') === 'diario' ? 'selected' : '' ?>>Diario</option>
                            <option value="semanal" <?= ($settings['backup_frecuencia'] ?? '') === 'semanal' ? 'selected' : '' ?>>Semanal</option>
                            <option value="mensual" <?= ($settings['backup_frecuencia'] ?? '') === 'mensual' ? 'selected' : '' ?>>Mensual</option>
                            <option value="manual" <?= ($settings['backup_frecuencia'] ?? '') === 'manual' ? 'selected' : '' ?>>Solo Manual</option>
                        </select>
                    </div>
                    
                    <div class="flex-end gap-2 mt-4">
                        <button type="reset" class="btn btn-secondary">
                            <i class="fas fa-undo"></i> Restaurar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
            
            <?php if (isset($settings['updated_at'])): ?>
            <div class="card-footer text-center text-sm text-secondary">
                Última actualización: <?= $settings['updated_at'] ?> por <?= $settings['updated_by'] ?? 'Sistema' ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

<?php 
$pageTitle = 'Dashboard';
$includeCharts = true;
require_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="grid grid-cols-4 mb-2">
    <!-- Total Docentes -->
    <div class="card stat-card" style="background: linear-gradient(135deg, #6366f1, #818cf8);">
        <div class="stat-icon">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-value"><?= $stats['total_docentes'] ?></div>
        <div class="stat-label">Total Docentes</div>
    </div>
    
    <!-- Docentes Activos -->
    <div class="card stat-card" style="background: linear-gradient(135deg, #10b981, #34d399);">
        <div class="stat-icon">
            <i class="fas fa-user-check"></i>
        </div>
        <div class="stat-value"><?= $stats['docentes_activos'] ?></div>
        <div class="stat-label">Docentes Activos</div>
    </div>
    
    <!-- Asistencias Hoy -->
    <div class="card stat-card" style="background: linear-gradient(135deg, #f59e0b, #fbbf24);">
        <div class="stat-icon">
            <i class="fas fa-clipboard-check"></i>
        </div>
        <div class="stat-value"><?= $stats['asistencias_hoy'] ?></div>
        <div class="stat-label">Asistencias Hoy</div>
    </div>
    
    <!-- Licencias Pendientes -->
    <div class="card stat-card" style="background: linear-gradient(135deg, #ec4899, #f472b6);">
        <div class="stat-icon">
            <i class="fas fa-file-alt"></i>
        </div>
        <div class="stat-value"><?= $stats['licencias_pendientes'] ?></div>
        <div class="stat-label">Licencias Pendientes</div>
    </div>
</div>

<div class="grid grid-cols-2 mb-2">
    <!-- Gráfico de Asistencia Semanal -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Asistencia Semanal</h3>
        </div>
        <div class="card-body">
            <canvas id="asistencia-chart" height="200"></canvas>
        </div>
    </div>
    
    <!-- Gráfico por Carrera -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Asistencia por Carrera (Hoy)</h3>
        </div>
        <div class="card-body">
            <canvas id="carrera-chart" height="200"></canvas>
        </div>
    </div>
</div>

<div class="grid grid-cols-2">
    <!-- Asistencias de Hoy -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-calendar-day"></i>
                Asistencias de Hoy
            </h3>
            <span class="badge badge-info"><?= count($asistencias_hoy) ?> registros</span>
        </div>
        <div class="card-body">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Docente</th>
                            <th>Hora Entrada</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($asistencias_hoy)): ?>
                            <tr>
                                <td colspan="3" class="text-center">No hay registros hoy</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach (array_slice($asistencias_hoy, 0, 5) as $asistencia): ?>
                            <tr>
                                <td><?= $asistencia['nombre_completo'] ?></td>
                                <td><?= substr($asistencia['hora_entrada'], 0, 5) ?></td>
                                <td>
                                    <span class="badge badge-<?= $asistencia['estado'] === 'presente' ? 'success' : ($asistencia['estado'] === 'tardanza' ? 'warning' : 'error') ?>">
                                        <?= ucfirst($asistencia['estado']) ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if (count($asistencias_hoy) > 5): ?>
                <div class="text-center mt-1">
                    <a href="<?= APP_URL ?>/asistencias" class="btn btn-outline btn-sm">
                        Ver todas las asistencias
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Alertas y Notificaciones -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-bell"></i>
                Alertas
            </h3>
        </div>
        <div class="card-body">
            <?php if ($stats['docentes_sin_huella'] > 0): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-fingerprint"></i>
                    <span><?= $stats['docentes_sin_huella'] ?> docentes sin huella registrada</span>
                </div>
            <?php endif; ?>
            
            <?php if ($stats['licencias_pendientes'] > 0): ?>
                <div class="alert alert-info">
                    <i class="fas fa-file-alt"></i>
                    <span><?= $stats['licencias_pendientes'] ?> licencias pendientes de aprobación</span>
                </div>
            <?php endif; ?>
            
            <?php if ($stats['licencias_por_vencer'] > 0): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-calendar-times"></i>
                    <span><?= $stats['licencias_por_vencer'] ?> licencias por vencer (próximos 7 días)</span>
                </div>
            <?php endif; ?>
            
            <?php if ($stats['tardanzas_hoy'] > 0): ?>
                <div class="alert alert-error">
                    <i class="fas fa-clock"></i>
                    <span><?= $stats['tardanzas_hoy'] ?> tardanzas registradas hoy</span>
                </div>
            <?php endif; ?>
            
            <div style="margin-top: var(--spacing-lg); padding-top: var(--spacing-lg); border-top: 1px solid var(--border-color);">
                <h4 style="margin-bottom: var(--spacing-md); font-size: var(--font-size-base);">
                    Estadísticas del Mes
                </h4>
                <div style="display: flex; justify-content: space-between; margin-bottom: var(--spacing-sm);">
                    <span class="text-secondary">Total Asistencias:</span>
                    <strong><?= $stats['asistencias_mes'] ?></strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span class="text-secondary">Puntualidad:</span>
                    <strong class="text-success"><?= $stats['puntualidad_mes'] ?>%</strong>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

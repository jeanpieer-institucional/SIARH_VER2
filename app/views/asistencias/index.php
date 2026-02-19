<?php 
$pageTitle = 'Control de Asistencias';
require_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="flex-between mb-2">
    <div>
        <h2 style="margin: 0;">
            <i class="fas fa-calendar-day"></i>
            Asistencias del <?= date('d/m/Y', strtotime($fecha)) ?>
        </h2>
    </div>
    <div class="flex gap-1">
        <input 
            type="date" 
            id="fecha-filter" 
            class="form-control" 
            value="<?= $fecha ?>"
            onchange="window.location.href='<?= APP_URL ?>/asistencias?fecha=' + this.value"
        >
        <?php if (in_array($_SESSION['user_role'], ['admin', 'supervisor'])): ?>
        <a href="<?= APP_URL ?>/asistencias/registrar" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Registrar Asistencia
        </a>
        <?php endif; ?>
        <a href="<?= APP_URL ?>/asistencias/exportar-excel?fecha=<?= $fecha ?>" class="btn btn-success">
            <i class="fas fa-file-excel"></i>
            Exportar Excel
        </a>
    </div>
</div>

<!-- Resumen del Día -->
<div class="grid grid-cols-4 mb-2">
    <?php
    $presentes = count(array_filter($asistencias, fn($a) => $a['estado'] === 'presente'));
    $tardanzas = count(array_filter($asistencias, fn($a) => $a['estado'] === 'tardanza'));
    $ausentes = count(array_filter($asistencias, fn($a) => $a['estado'] === 'ausente'));
    $total = count($asistencias);
    ?>
    
    <div class="card" style="background: linear-gradient(135deg, #10b981, #34d399); color: white;">
        <div class="stat-value"><?= $presentes ?></div>
        <div class="stat-label">Presentes</div>
    </div>
    
    <div class="card" style="background: linear-gradient(135deg, #f59e0b, #fbbf24); color: white;">
        <div class="stat-value"><?= $tardanzas ?></div>
        <div class="stat-label">Tardanzas</div>
    </div>
    
    <div class="card" style="background: linear-gradient(135deg, #ef4444, #f87171); color: white;">
        <div class="stat-value"><?= $ausentes ?></div>
        <div class="stat-label">Ausentes</div>
    </div>
    
    <div class="card" style="background: linear-gradient(135deg, #6366f1, #818cf8); color: white;">
        <div class="stat-value"><?= $total ?></div>
        <div class="stat-label">Total Registros</div>
    </div>
</div>

<!-- Tabla de Asistencias -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Detalle de Asistencias</h3>
        <span class="badge badge-info"><?= $total ?> registros</span>
    </div>
    
    <div class="card-body">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Docente</th>
                        <th>DNI</th>
                        <th>Carrera</th>
                        <th>Hora Entrada</th>
                        <th>Hora Salida</th>
                        <th>Estado</th>
                        <th>Tardanza</th>
                        <th>Tipo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($asistencias)): ?>
                        <tr>
                            <td colspan="9" class="text-center">No hay registros para esta fecha</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($asistencias as $asistencia): ?>
                        <tr>
                            <td><strong><?= $asistencia['codigo_empleado'] ?></strong></td>
                            <td><?= $asistencia['nombre_completo'] ?></td>
                            <td><?= $asistencia['dni'] ?></td>
                            <td><?= $asistencia['carrera'] ?? 'N/A' ?></td>
                            <td>
                                <i class="fas fa-sign-in-alt text-success"></i>
                                <?= substr($asistencia['hora_entrada'], 0, 5) ?>
                            </td>
                            <td>
                                <?php if ($asistencia['hora_salida']): ?>
                                    <i class="fas fa-sign-out-alt text-error"></i>
                                    <?= substr($asistencia['hora_salida'], 0, 5) ?>
                                <?php else: ?>
                                    <span class="text-tertiary">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge badge-<?= 
                                    $asistencia['estado'] === 'presente' ? 'success' : 
                                    ($asistencia['estado'] === 'tardanza' ? 'warning' : 'error') 
                                ?>">
                                    <?= ucfirst($asistencia['estado']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($asistencia['minutos_tardanza'] > 0): ?>
                                    <span class="text-warning">
                                        <i class="fas fa-clock"></i>
                                        <?= $asistencia['minutos_tardanza'] ?> min
                                    </span>
                                <?php else: ?>
                                    <span class="text-tertiary">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge badge-secondary">
                                    <i class="fas fa-<?= $asistencia['tipo_registro'] === 'biometrico' ? 'fingerprint' : 'keyboard' ?>"></i>
                                    <?= ucfirst($asistencia['tipo_registro']) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

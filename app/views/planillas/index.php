<?php require_once BASE_PATH . '/app/views/layouts/header.php'; ?>

<?php
$meses = [
    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
    7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
];
?>

<!-- Sección de Filtros y Acciones -->
<div class="card mb-md">
    <div class="card-body">
        <form method="GET" action="<?= APP_URL ?>/planillas" class="grid" style="grid-template-columns: 1fr 1fr auto auto; gap: var(--spacing-md); align-items: flex-end;">
            <div class="form-group">
                <label class="form-label" for="mes">Mes</label>
                <select id="mes" name="mes" class="form-control">
                    <?php foreach ($meses as $num => $nombre): ?>
                        <option value="<?= $num ?>" <?= ($mes == $num) ? 'selected' : '' ?>><?= $nombre ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="anio">Año</label>
                <select id="anio" name="anio" class="form-control">
                    <?php for ($y = date('Y') - 2; $y <= date('Y') + 1; $y++): ?>
                        <option value="<?= $y ?>" <?= ($anio == $y) ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-secondary" style="width: 100%;">
                    <i class="fas fa-search"></i> Consultar
                </button>
            </div>
            
            <?php if ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'super_admin'): ?>
            <div class="form-group">
                <a href="<?= APP_URL ?>/planillas/generate?mes=<?= $mes ?>&anio=<?= $anio ?>" class="btn btn-primary">
                    <i class="fas fa-calculator"></i> Calcular/Generar Mes
                </a>
            </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Tarjetas de Estadísticas / Resumen -->
<div class="grid mb-lg" style="grid-template-columns: repeat(4, 1fr); gap: var(--spacing-md);">
    <div class="card card-metric" style="background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white;">
        <div class="card-body">
            <span class="text-secondary" style="color: rgba(255,255,255,0.8);">Total Generado (Neto)</span>
            <h3 class="mt-xs">S/ <?= number_format($stats['total_generado'], 2) ?></h3>
            <span class="badge" style="background: rgba(255,255,255,0.2); color: white; margin-top: var(--spacing-xs);"><?= $stats['total_personal'] ?> Boletas</span>
        </div>
    </div>
    
    <div class="card card-metric">
        <div class="card-body">
            <span class="text-secondary">Total Pagado</span>
            <h3 class="mt-xs text-success">S/ <?= number_format($stats['total_pagado'], 2) ?></h3>
            <span class="badge badge-success" style="margin-top: var(--spacing-xs);"><?= $stats['cant_pagadas'] ?> Pagadas</span>
        </div>
    </div>
    
    <div class="card card-metric">
        <div class="card-body">
            <span class="text-secondary">Total Pendiente</span>
            <h3 class="mt-xs text-warning">S/ <?= number_format($stats['total_pendiente'], 2) ?></h3>
            <span class="badge badge-warning" style="margin-top: var(--spacing-xs);"><?= $stats['cant_generadas'] ?> Pendientes</span>
        </div>
    </div>
    
    <div class="card card-metric">
        <div class="card-body">
            <span class="text-secondary">Periodo Consultado</span>
            <h3 class="mt-xs"><?= $meses[$mes] ?></h3>
            <span class="text-secondary font-sm"><?= $anio ?></span>
        </div>
    </div>
</div>

<!-- Tabla de Boletas del Mes -->
<div class="card mb-lg">
    <div class="card-header">
        <h2>Remuneraciones del Periodo: <?= $meses[$mes] ?> - <?= $anio ?></h2>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Código Boleta</th>
                        <th>Docente</th>
                        <th>Sueldo Base</th>
                        <th>Descuentos</th>
                        <th>Bonos</th>
                        <th>Neto a Pagar</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($planillas)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-lg">
                            <i class="fas fa-file-invoice-dollar" style="font-size: 2.5rem; color: var(--border-color); display: block; margin-bottom: var(--spacing-sm);"></i>
                            No se han generado planillas para este periodo mensual.
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($planillas as $p): ?>
                        <tr>
                            <td><strong>BOP-<?= str_pad($p['id'], 6, '0', STR_PAD_LEFT) ?></strong></td>
                            <td>
                                <strong><?= htmlspecialchars($p['docente_nombre']) ?></strong>
                                <div class="text-secondary font-xs"><?= htmlspecialchars($p['codigo_empleado']) ?> | <?= htmlspecialchars($p['carrera']) ?></div>
                            </td>
                            <td>S/ <?= number_format($p['sueldo_base'], 2) ?></td>
                            <td class="text-error">-S/ <?= number_format($p['descuentos'], 2) ?></td>
                            <td class="text-success">+S/ <?= number_format($p['bonificaciones'], 2) ?></td>
                            <td><strong>S/ <?= number_format($p['total_pagar'], 2) ?></strong></td>
                            <td>
                                <?php if ($p['estado'] === 'pagada'): ?>
                                    <span class="badge badge-success">Pagada</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">Generada</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="<?= APP_URL ?>/planillas/ver/<?= $p['id'] ?>" class="btn btn-icon btn-secondary" title="Ver Boleta Detallada">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?= APP_URL ?>/planillas/pdf/<?= $p['id'] ?>" target="_blank" class="btn btn-icon btn-secondary" title="Descargar Boleta PDF" style="color: var(--error);">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                    <?php if ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'super_admin'): ?>
                                    <button type="button" class="btn btn-icon btn-error" title="Eliminar Boleta" onclick="confirmDelete(<?= $p['id'] ?>, '<?= htmlspecialchars($p['docente_nombre']) ?>')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <form id="delete-form-<?= $p['id'] ?>" action="<?= APP_URL ?>/planillas/delete/<?= $p['id'] ?>" method="POST" style="display: none;"></form>
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
    if (confirm(`¿Está seguro de que desea eliminar la boleta de pago del docente "${name}"?\nEsta acción eliminará el registro de la planilla del mes.`)) {
        document.getElementById(`delete-form-${id}`).submit();
    }
}
</script>

<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>

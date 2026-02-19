<?php 
$pageTitle = 'Gestión de Licencias';
require_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-file-alt"></i>
            Licencias y Permisos
        </h3>
        <div class="flex gap-1">
            <?php if ($pendientes > 0): ?>
                <span class="badge badge-warning" style="padding: var(--spacing-md) var(--spacing-lg);">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= $pendientes ?> pendientes
                </span>
            <?php endif; ?>
            <a href="<?= APP_URL ?>/licencias/create" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Nueva Solicitud
            </a>
        </div>
    </div>
    
    <div class="card-body">
        <!-- Filtros -->
        <div class="flex gap-1 mb-2">
            <a href="<?= APP_URL ?>/licencias" 
               class="btn <?= empty($estado) ? 'btn-primary' : 'btn-secondary' ?>">
                Todas
            </a>
            <a href="<?= APP_URL ?>/licencias?estado=pendiente" 
               class="btn <?= $estado === 'pendiente' ? 'btn-warning' : 'btn-secondary' ?>">
                Pendientes
            </a>
            <a href="<?= APP_URL ?>/licencias?estado=aprobado" 
               class="btn <?= $estado === 'aprobado' ? 'btn-success' : 'btn-secondary' ?>">
                Aprobadas
            </a>
            <a href="<?= APP_URL ?>/licencias?estado=rechazado" 
               class="btn <?= $estado === 'rechazado' ? 'btn-error' : 'btn-secondary' ?>">
                Rechazadas
            </a>
        </div>
        
        <!-- Tabla de Licencias -->
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Docente</th>
                        <th>Tipo</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Fin</th>
                        <th>Días</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($licencias)): ?>
                        <tr>
                            <td colspan="7" class="text-center">No hay licencias registradas</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($licencias as $licencia): ?>
                        <?php
                        $dias = (strtotime($licencia['fecha_fin']) - strtotime($licencia['fecha_inicio'])) / 86400 + 1;
                        ?>
                        <tr>
                            <td>
                                <strong><?= $licencia['nombre_docente'] ?? $licencia['codigo_empleado'] ?></strong>
                                <br>
                                <small class="text-secondary"><?= $licencia['carrera'] ?? 'N/A' ?></small>
                            </td>
                            <td>
                                <span class="badge badge-info">
                                    <?= ucfirst($licencia['tipo']) ?>
                                </span>
                            </td>
                            <td><?= date('d/m/Y', strtotime($licencia['fecha_inicio'])) ?></td>
                            <td><?= date('d/m/Y', strtotime($licencia['fecha_fin'])) ?></td>
                            <td><?= $dias ?> día<?= $dias > 1 ? 's' : '' ?></td>
                            <td>
                                <span class="badge badge-<?= 
                                    $licencia['estado'] === 'aprobado' ? 'success' : 
                                    ($licencia['estado'] === 'pendiente' ? 'warning' : 'error') 
                                ?>">
                                    <?= ucfirst($licencia['estado']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="flex gap-1">
                                    <?php if ($licencia['estado'] === 'pendiente' && in_array($_SESSION['user_role'], ['admin', 'supervisor'])): ?>
                                        <button onclick="aprobarLicencia(<?= $licencia['id'] ?>)" 
                                                class="btn btn-sm btn-success" 
                                                title="Aprobar">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button onclick="rechazarLicencia(<?= $licencia['id'] ?>)" 
                                                class="btn btn-sm btn-error" 
                                                title="Rechazar">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    <?php endif; ?>
                                    <a href="<?= APP_URL ?>/licencias/ver/<?= $licencia['id'] ?>" 
                                       class="btn btn-sm btn-primary" 
                                       title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
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

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

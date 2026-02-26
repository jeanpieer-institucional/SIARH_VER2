<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-history"></i> Historial de Actividad (Auditoría)
        </h3>
        <a href="<?= APP_URL ?>/reportes" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver a Reportes
        </a>
    </div>
    
    <div class="card-body">
        <div class="table-responsive">
            <table class="table" style="font-size: 0.9em;">
                <thead>
                    <tr>
                        <th>Fecha y Hora</th>
                        <th>Usuario</th>
                        <th>Módulo</th>
                        <th>Acción</th>
                        <th>Descripción</th>
                        <th>IP</th>
                        <th>Detalles (Cambios)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                        <tr><td colspan="7" class="text-center">No hay registros de actividad.</td></tr>
                    <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?></td>
                            <td><?= htmlspecialchars($log['username'] ?? 'Sistema') ?></td>
                            <td><span class="badge badge-primary"><?= htmlspecialchars($log['modulo']) ?></span></td>
                            <td><strong><?= htmlspecialchars($log['accion']) ?></strong></td>
                            <td style="max-width: 250px; white-space: normal;"><?= htmlspecialchars($log['descripcion']) ?></td>
                            <td><small><?= htmlspecialchars($log['ip_address']) ?></small></td>
                            <td>
                                <?php if ($log['datos_anteriores'] || $log['datos_nuevos']): ?>
                                    <button class="btn btn-sm btn-info" onclick="verDetalles(<?= htmlspecialchars(json_encode($log['datos_anteriores'])) ?>, <?= htmlspecialchars(json_encode($log['datos_nuevos'])) ?>)">
                                        <i class="fas fa-eye"></i> Ver
                                    </button>
                                <?php else: ?>
                                    <span class="text-secondary">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal para Detalles -->
<div id="logDetailModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1050; align-items: center; justify-content: center;">
    <div class="card" style="width: 90%; max-width: 800px; max-height: 90vh; overflow-y: auto;">
        <div class="card-header d-flex flex-between">
            <h3>Detalles del Cambio</h3>
            <button class="btn btn-icon" onclick="document.getElementById('logDetailModal').style.display='none'"><i class="fas fa-times"></i></button>
        </div>
        <div class="card-body grid grid-cols-2 gap-4">
            <div>
                <h4 class="text-error"><i class="fas fa-minus-circle"></i> Datos Anteriores</h4>
                <pre id="datos-antes" style="background: #f8dbdb; padding: 10px; border-radius: 4px; overflow-x: auto; white-space: pre-wrap; font-size: 0.85em;"></pre>
            </div>
            <div>
                <h4 class="text-success"><i class="fas fa-plus-circle"></i> Datos Nuevos</h4>
                <pre id="datos-despues" style="background: #e2f8e2; padding: 10px; border-radius: 4px; overflow-x: auto; white-space: pre-wrap; font-size: 0.85em;"></pre>
            </div>
        </div>
    </div>
</div>

<script>
    function verDetalles(antes, despues) {
        document.getElementById('datos-antes').textContent = antes ? JSON.stringify(JSON.parse(antes), null, 2) : 'No hay datos anteriores';
        document.getElementById('datos-despues').textContent = despues ? JSON.stringify(JSON.parse(despues), null, 2) : 'No hay datos nuevos';
        document.getElementById('logDetailModal').style.display = 'flex';
    }
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

<?php require_once BASE_PATH . '/app/views/layouts/header.php'; ?>

<?php
$meses = [
    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
    7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
];
?>

<div class="row">
    <div class="col-12">
        <div class="card mb-lg">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <a href="<?= APP_URL ?>/planillas?mes=<?= $mes ?>&anio=<?= $anio ?>" class="btn btn-secondary btn-sm mb-xs">
                        <i class="fas fa-arrow-left"></i> Volver a Planillas
                    </a>
                    <h2 class="mt-xs">Generación de Planillas: Periodo <?= $meses[$mes] ?> - <?= $anio ?></h2>
                </div>
            </div>
            
            <div class="card-body">
                <div class="alert alert-warning mb-lg">
                    <i class="fas fa-info-circle"></i>
                    <span>
                        <strong>Importante:</strong> Esta es una pantalla de previsualización. Las planillas se estiman en tiempo real según el sueldo base base y las tardanzas/inasistencias registradas. Haz clic en el botón <strong>"Confirmar y Guardar Planillas del Mes"</strong> al final de la página para registrarlas definitivamente.
                    </span>
                </div>
                
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Docente</th>
                                <th>Sueldo Base</th>
                                <th>Descuentos Tardanza</th>
                                <th>Descuentos Faltas</th>
                                <th>Bonificaciones</th>
                                <th>Neto Estimado</th>
                                <th>Estado en Sistema</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $nuevosParaGenerar = 0; 
                            foreach ($previaPlanillas as $item): 
                                $doc = $item['docente'];
                                $calc = $item['calculos'];
                                if (!$item['registrada']) {
                                    $nuevosParaGenerar++;
                                }
                            ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($doc['apellidos'] . ', ' . $doc['nombres']) ?></strong>
                                    <div class="text-secondary font-xs"><?= htmlspecialchars($doc['codigo_empleado']) ?></div>
                                </td>
                                <td>S/ <?= number_format($calc['sueldo_base'], 2) ?></td>
                                <td class="text-error">
                                    -S/ <?= number_format($calc['descuento_tardanza'], 2) ?>
                                    <div class="text-secondary font-xxs">(<?= $calc['total_minutos_tardanza'] ?> mins)</div>
                                </td>
                                <td class="text-error">
                                    -S/ <?= number_format($calc['descuento_falta'], 2) ?>
                                    <div class="text-secondary font-xxs">(<?= $calc['total_faltas'] ?> faltas)</div>
                                </td>
                                <td class="text-success">
                                    +S/ <?= number_format($calc['bonificaciones'], 2) ?>
                                    <?php if ($calc['bonificaciones'] > 0): ?>
                                        <div class="text-success font-xxs">(Asist. Perfecta)</div>
                                    <?php endif; ?>
                                </td>
                                <td><strong>S/ <?= number_format($calc['total_pagar'], 2) ?></strong></td>
                                <td>
                                    <?php if ($item['registrada']): ?>
                                        <span class="badge badge-success">
                                            <i class="fas fa-check"></i> Guardado (<?= ucfirst($item['estado']) ?>)
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">Pendiente de registro</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Formulario de confirmación -->
                <div class="mt-lg p-md card" style="background-color: var(--bg-color); border: 1px solid var(--border-color);">
                    <form action="<?= APP_URL ?>/planillas/store" method="POST" class="d-flex justify-content-between align-items-center">
                        <?= Csrf::input() ?>
                        <input type="hidden" name="mes" value="<?= $mes ?>">
                        <input type="hidden" name="anio" value="<?= $anio ?>">
                        
                        <div>
                            <h4>Total de planillas por registrar en este mes: <span class="text-primary"><?= $nuevosParaGenerar ?></span></h4>
                            <p class="text-secondary font-xs mt-xs">Las planillas que ya están marcadas como "Guardado" no se duplicarán.</p>
                        </div>
                        
                        <div>
                            <a href="<?= APP_URL ?>/planillas?mes=<?= $mes ?>&anio=<?= $anio ?>" class="btn btn-secondary mr-2">Cancelar</a>
                            <?php if ($nuevosParaGenerar > 0): ?>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Confirmar y Guardar Planillas del Mes
                                </button>
                            <?php else: ?>
                                <button type="button" class="btn btn-primary" disabled title="No hay planillas pendientes por generar">
                                    <i class="fas fa-save"></i> Confirmar y Guardar Planillas del Mes
                                </button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
                
            </div>
        </div>
    </div>
</div>

<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>

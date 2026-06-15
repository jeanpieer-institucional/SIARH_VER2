<?php require_once BASE_PATH . '/app/views/layouts/header.php'; ?>

<?php
$meses = [
    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
    7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
];
?>

<div class="row">
    <div class="col-12" style="max-width: 900px; margin: 0 auto;">
        
        <!-- Controles Superiores -->
        <div class="d-flex justify-content-between align-items-center mb-md">
            <a href="<?= APP_URL ?>/planillas?mes=<?= $planilla['mes'] ?>&anio=<?= $planilla['anio'] ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver a Planillas
            </a>
            
            <div class="actions" style="display: flex; gap: var(--spacing-sm);">
                <a href="<?= APP_URL ?>/planillas/pdf/<?= $planilla['id'] ?>" target="_blank" class="btn btn-secondary" style="color: var(--error);">
                    <i class="fas fa-file-pdf"></i> Descargar PDF
                </a>
                
                <?php if ($planilla['estado'] === 'generada' && ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'super_admin' || $_SESSION['user_role'] === 'supervisor')): ?>
                <button type="button" class="btn btn-primary" onclick="document.getElementById('pagar-form').submit();">
                    <i class="fas fa-money-check-alt"></i> Marcar como Pagada
                </button>
                <form id="pagar-form" action="<?= APP_URL ?>/planillas/pagar/<?= $planilla['id'] ?>" method="POST" style="display: none;"></form>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Boleta de Pago -->
        <div class="card mb-lg" style="border: 2px solid var(--border-color); padding: var(--spacing-lg);">
            <div style="text-align: center; border-bottom: 2px solid var(--border-color); padding-bottom: var(--spacing-md); margin-bottom: var(--spacing-lg);">
                <h1 style="color: var(--primary); font-size: 1.8rem; margin: 0;">SIARH - BOLETA DE PAGO</h1>
                <p class="text-secondary" style="margin: var(--spacing-xs) 0 0 0;">Control de Asistencia y Remuneraciones del Docente</p>
                <h3 class="mt-xs">Periodo: <?= $meses[$planilla['mes']] ?> - <?= $planilla['anio'] ?></h3>
            </div>
            
            <!-- Datos del Docente y Boleta -->
            <div class="grid mb-lg" style="grid-template-columns: 1fr 1fr; gap: var(--spacing-lg);">
                <div>
                    <h4 class="mb-sm text-primary">DATOS DEL DOCENTE</h4>
                    <p style="margin: var(--spacing-xs) 0;"><strong>Nombre Completo:</strong> <?= htmlspecialchars($docente['apellidos'] . ', ' . $docente['nombres']) ?></p>
                    <p style="margin: var(--spacing-xs) 0;"><strong>DNI:</strong> <?= htmlspecialchars($docente['dni']) ?></p>
                    <p style="margin: var(--spacing-xs) 0;"><strong>Código Empleado:</strong> <?= htmlspecialchars($docente['codigo_empleado']) ?></p>
                    <p style="margin: var(--spacing-xs) 0;"><strong>Email:</strong> <?= htmlspecialchars($docente['email']) ?></p>
                </div>
                <div>
                    <h4 class="mb-sm text-primary">DETALLES DE LA EMISIÓN</h4>
                    <p style="margin: var(--spacing-xs) 0;"><strong>Código Boleta:</strong> BOP-<?= str_pad($planilla['id'], 6, '0', STR_PAD_LEFT) ?></p>
                    <p style="margin: var(--spacing-xs) 0;"><strong>Fecha de Generación:</strong> <?= date('d/m/Y h:i A', strtotime($planilla['created_at'])) ?></p>
                    <p style="margin: var(--spacing-xs) 0;"><strong>Estado de Boleta:</strong> 
                        <?php if ($planilla['estado'] === 'pagada'): ?>
                            <span class="badge badge-success">Pagada</span>
                        <?php else: ?>
                            <span class="badge badge-warning">Generada (Pendiente)</span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            
            <!-- Conceptos / Desglose de Cálculo -->
            <div class="table-responsive mb-lg">
                <table class="table" style="border: 1px solid var(--border-color);">
                    <thead>
                        <tr style="background-color: var(--bg-color);">
                            <th>Concepto / Descripción</th>
                            <th style="text-align: right;">Ingresos</th>
                            <th style="text-align: right;">Egresos</th>
                            <th style="text-align: right;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Sueldo Base -->
                        <tr>
                            <td>Sueldo Mensual Base Contratado</td>
                            <td style="text-align: right;" class="text-success">S/ <?= number_format($planilla['sueldo_base'], 2) ?></td>
                            <td style="text-align: right;">-</td>
                            <td style="text-align: right;"><strong>S/ <?= number_format($planilla['sueldo_base'], 2) ?></strong></td>
                        </tr>
                        
                        <!-- Bonificación por Asistencia Perfecta -->
                        <tr>
                            <td>
                                Bono Especial de Puntualidad y Asistencia Perfecta
                                <div class="text-secondary font-xxs">Otorgado por 0 tardanzas y 0 faltas registradas.</div>
                            </td>
                            <td style="text-align: right;" class="text-success">S/ <?= number_format($planilla['bonificaciones'], 2) ?></td>
                            <td style="text-align: right;">-</td>
                            <td style="text-align: right;"><strong>S/ <?= number_format($planilla['bonificaciones'], 2) ?></strong></td>
                        </tr>
                        
                        <!-- Descuento por Faltas -->
                        <tr>
                            <td>
                                Descuento por Faltas / Inasistencias
                                <div class="text-secondary font-xxs"><?= $detalles['total_faltas'] ?> faltas registradas (Sueldo Base / 30 por falta)</div>
                            </td>
                            <td style="text-align: right;">-</td>
                            <td style="text-align: right;" class="text-error">S/ <?= number_format($detalles['descuento_falta'], 2) ?></td>
                            <td style="text-align: right;" class="text-error"><strong>-S/ <?= number_format($detalles['descuento_falta'], 2) ?></strong></td>
                        </tr>
                        
                        <!-- Descuento por Tardanzas -->
                        <tr>
                            <td>
                                Descuento por Minutos de Tardanzas Acumuladas
                                <div class="text-secondary font-xxs"><?= $detalles['total_minutos_tardanza'] ?> minutos en total (S/ 0.50 por minuto de tardanza)</div>
                            </td>
                            <td style="text-align: right;">-</td>
                            <td style="text-align: right;" class="text-error">S/ <?= number_format($detalles['descuento_tardanza'], 2) ?></td>
                            <td style="text-align: right;" class="text-error"><strong>-S/ <?= number_format($detalles['descuento_tardanza'], 2) ?></strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Totales -->
            <div style="max-width: 400px; margin-left: auto;">
                <div class="d-flex justify-content-between py-xs" style="border-bottom: 1px solid var(--border-color);">
                    <span>Total Ingresos Brutos:</span>
                    <span class="text-success"><strong>S/ <?= number_format($planilla['sueldo_base'] + $planilla['bonificaciones'], 2) ?></strong></span>
                </div>
                <div class="d-flex justify-content-between py-xs" style="border-bottom: 1px solid var(--border-color);">
                    <span>Total Descuentos (Egresos):</span>
                    <span class="text-error"><strong>S/ <?= number_format($planilla['descuentos'], 2) ?></strong></span>
                </div>
                <div class="d-flex justify-content-between py-md mt-sm" style="border-top: 2px solid var(--border-color); background-color: var(--bg-color); padding: var(--spacing-sm); border-radius: var(--border-radius);">
                    <span style="font-size: 1.1rem;"><strong>Neto a Pagar:</strong></span>
                    <span style="font-size: 1.1rem; color: var(--primary);"><strong>S/ <?= number_format($planilla['total_pagar'], 2) ?></strong></span>
                </div>
            </div>
            
            <!-- Firma Mockup -->
            <div class="grid" style="grid-template-columns: 1fr 1fr; gap: var(--spacing-lg); margin-top: 50px; text-align: center;">
                <div style="margin-top: 50px;">
                    <div style="width: 200px; margin: 0 auto; border-top: 1px solid var(--border-color);"></div>
                    <p class="font-sm mt-xs">Firma del Docente</p>
                    <p class="text-secondary font-xxs">DNI: <?= htmlspecialchars($docente['dni']) ?></p>
                </div>
                <div style="margin-top: 50px;">
                    <div style="width: 200px; margin: 0 auto; border-top: 1px solid var(--border-color);"></div>
                    <p class="font-sm mt-xs">Firma Autorizada RRHH</p>
                    <p class="text-secondary font-xxs">SIARH RRHH</p>
                </div>
            </div>
        </div>
        
    </div>
</div>

<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>

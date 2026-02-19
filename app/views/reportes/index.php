<?php 
$pageTitle = 'Reportes y Estadísticas';
require_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="row">
    <!-- Reporte de Asistencias -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-clipboard-check"></i>
                    Reporte de Asistencias
                </h3>
            </div>
            <div class="card-body">
                <p class="text-secondary mb-4">
                    Genera un reporte detallado de las asistencias, tardanzas y faltas en un rango de fechas específico.
                    El archivo se descargará en formato Excel (.xls).
                </p>
                
                <form action="<?= APP_URL ?>/reportes/exportar-asistencias" method="GET" target="_blank">
                    <div class="form-group mb-3">
                        <label class="form-label">Fecha Inicio</label>
                        <input type="date" name="fecha_inicio" class="form-control" value="<?= date('Y-m-01') ?>" required>
                    </div>
                    
                    <div class="form-group mb-4">
                        <label class="form-label">Fecha Fin</label>
                        <input type="date" name="fecha_fin" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-file-excel"></i>
                        Exportar a Excel
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Reporte de Docentes -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-users"></i>
                    Lista de Personal
                </h3>
            </div>
            <div class="card-body">
                <p class="text-secondary mb-4">
                    Descarga la lista completa del personal docente registrado en el sistema, incluyendo sus datos de contacto y estado actual.
                </p>
                
                <div class="alert alert-info mb-4">
                    <i class="fas fa-info-circle"></i>
                    Este reporte incluye todos los docentes activos, inactivos y en licencia.
                </div>
                
                <a href="<?= APP_URL ?>/reportes/exportar-docentes" target="_blank" class="btn btn-secondary w-100 mt-auto">
                    <i class="fas fa-file-excel"></i>
                    Descargar Lista de Docentes
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-pie"></i>
                    Resumen Rápido
                </h3>
            </div>
            <div class="card-body">
                <div class="grid-dashboard">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: var(--primary-light); color: var(--primary);">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Total Docentes</h3>
                            <p class="text-xl fw-bold">--</p>
                            <span class="text-sm text-secondary">Registrados en el sistema</span>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon" style="background: var(--success-light); color: var(--success);">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Asistencias Hoy</h3>
                            <p class="text-xl fw-bold">--</p>
                            <span class="text-sm text-secondary">Registros de entrada</span>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon" style="background: var(--warning-light); color: var(--warning);">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Tardanzas Hoy</h3>
                            <p class="text-xl fw-bold">--</p>
                            <span class="text-sm text-secondary">Registros fuera de horario</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title><?= $pageTitle ?? 'Dashboard' ?> - SIARH</title>
    <link rel="stylesheet" href="<?= APP_URL ?>/public/assets/css/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <i class="fas fa-user-shield"></i>
                    <span>SIARH</span>
                </div>
            </div>
            
            <nav class="sidebar-nav">
                <a href="<?= APP_URL ?>/dashboard" class="nav-item">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
                
                <a href="<?= APP_URL ?>/perfil" class="nav-item">
                    <i class="fas fa-user-circle"></i>
                    <span>Mi Perfil</span>
                </a>
                
                <a href="<?= APP_URL ?>/docentes" class="nav-item">
                    <i class="fas fa-users"></i>
                    <span>Docentes</span>
                </a>
                
                <a href="<?= APP_URL ?>/carreras" class="nav-item">
                    <i class="fas fa-building"></i>
                    <span>Carreras</span>
                </a>
                
                <a href="<?= APP_URL ?>/asistencias" class="nav-item">
                    <i class="fas fa-clipboard-check"></i>
                    <span>Asistencias</span>
                </a>
                
                <a href="<?= APP_URL ?>/licencias" class="nav-item">
                    <i class="fas fa-file-alt"></i>
                    <span>Licencias</span>
                </a>
                
                <a href="<?= APP_URL ?>/reportes" class="nav-item">
                    <i class="fas fa-chart-bar"></i>
                    <span>Reportes</span>
                </a>
                
                <?php if ($_SESSION['user_role'] === 'super_admin' || $_SESSION['user_role'] === 'admin'): ?>
                <a href="<?= APP_URL ?>/reportes/logs" class="nav-item">
                    <i class="fas fa-history"></i>
                    <span>Auditoría</span>
                </a>
                <?php endif; ?>
                
                <!-- MÓDULOS FUTUROS (INACTIVOS)
                <?php if ($_SESSION['user_role'] === 'super_admin' || $_SESSION['user_role'] === 'admin'): ?>
                <a href="<?= APP_URL ?>/horarios" class="nav-item">
                    <i class="fas fa-clock"></i>
                    <span>Horarios</span>
                </a>
                
                <a href="<?= APP_URL ?>/planillas" class="nav-item">
                    <i class="fas fa-money-check-alt"></i>
                    <span>Planillas</span>
                </a>
                
                <a href="<?= APP_URL ?>/evaluaciones" class="nav-item">
                    <i class="fas fa-star"></i>
                    <span>Evaluaciones</span>
                </a>
                <?php endif; ?>
                -->
                
                <?php if ($_SESSION['user_role'] === 'super_admin' || $_SESSION['user_role'] === 'admin'): ?>
                <a href="<?= APP_URL ?>/configuracion" class="nav-item">
                    <i class="fas fa-cog"></i>
                    <span>Configuración</span>
                </a>
                <?php endif; ?>
                
                <?php if ($_SESSION['user_role'] === 'super_admin'): ?>
                <a href="<?= APP_URL ?>/usuarios" class="nav-item">
                    <i class="fas fa-users-cog"></i>
                    <span>Gestión de Acceso</span>
                </a>
                <?php endif; ?>
                
                <a href="<?= APP_URL ?>/auth/logout" class="nav-item">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Cerrar Sesión</span>
                </a>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <!-- Topbar -->
            <header class="topbar">
                <div class="topbar-left">
                    <button id="sidebar-toggle" class="btn btn-secondary">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h2><?= $pageTitle ?? 'Dashboard' ?></h2>
                </div>
                
                <div class="topbar-right">
                    <button id="theme-toggle" class="btn btn-secondary">
                        <i class="fas fa-moon"></i>
                    </button>
                    <div class="header-right">
                        <!-- Notificaciones (Inactivo) -->
                        <!--
                        <a href="#" class="btn btn-icon mr-3" style="position: relative;" title="Notificaciones">
                            <i class="fas fa-bell"></i>
                            <span class="badge badge-error" style="position: absolute; top: 0; right: 0; padding: 2px 5px; font-size: 0.6em; transform: translate(30%, -30%);">0</span>
                        </a>
                        -->
                        <div class="user-info">
                            <span class="user-role badge badge-primary"><?= ucfirst(str_replace('_', ' ', $_SESSION['user_role'])) ?></span>
                            <span class="user-name"><?= htmlspecialchars($_SESSION['username']) ?></span>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Content -->
            <div class="content-wrapper">
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?= $_SESSION['error'] ?></span>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <span><?= $_SESSION['success'] ?></span>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['warning'])): ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span><?= $_SESSION['warning'] ?></span>
                    </div>
                    <?php unset($_SESSION['warning']); ?>
                <?php endif; ?>

<?php
/**
 * SIARH - Sistema Integral de Asistencia y Recursos Humanos
 * Punto de Entrada Principal
 */

// Cargar configuración
require_once dirname(__FILE__) . '/config/config.php';

// Crear instancia del router
$router = new Router();

// ==================== RUTAS PÚBLICAS ====================
$router->get('/', function() {
    header('Location: ' . APP_URL . '/login');
    exit;
});

$router->get('/login', function() {
    $controller = new AuthController();
    $controller->showLogin();
});

$router->post('/auth/login', function() {
    $controller = new AuthController();
    $controller->login();
});

$router->get('/auth/logout', function() {
    $controller = new AuthController();
    $controller->logout();
});

$router->get('/auth/check-session', function() {
    $controller = new AuthController();
    $controller->checkSession();
});

// ==================== DASHBOARD ====================
$router->get('/dashboard', function() {
    $controller = new DashboardController();
    $controller->index();
});

$router->get('/dashboard/chart-data', function() {
    $controller = new DashboardController();
    $controller->getChartData();
});

// ==================== DOCENTES ====================
$router->get('/docentes', function() {
    $controller = new DocenteController();
    $controller->index();
});

$router->get('/docentes/create', function() {
    $controller = new DocenteController();
    $controller->create();
});

$router->post('/docentes/store', function() {
    $controller = new DocenteController();
    $controller->store();
});

$router->get('/docentes/edit/{id}', function($id) {
    $controller = new DocenteController();
    $controller->edit($id);
});

$router->post('/docentes/update/{id}', function($id) {
    $controller = new DocenteController();
    $controller->update($id);
});

$router->post('/docentes/delete/{id}', function($id) {
    $controller = new DocenteController();
    $controller->delete($id);
});

$router->get('/docentes/search', function() {
    $controller = new DocenteController();
    $controller->search();
});

$router->post('/docentes/registrar-huella/{id}', function($id) {
    $controller = new DocenteController();
    $controller->registrarHuella($id);
});

// ==================== CARRERAS ====================
$router->get('/carreras', function() {
    $controller = new CarreraController();
    $controller->index();
});

$router->get('/carreras/create', function() {
    $controller = new CarreraController();
    $controller->create();
});

$router->post('/carreras/store', function() {
    $controller = new CarreraController();
    $controller->store();
});

$router->get('/carreras/edit/{id}', function($id) {
    $controller = new CarreraController();
    $controller->edit($id);
});

$router->post('/carreras/update/{id}', function($id) {
    $controller = new CarreraController();
    $controller->update($id);
});

$router->post('/carreras/delete/{id}', function($id) {
    $controller = new CarreraController();
    $controller->delete($id);
});

// ==================== DOCUMENTOS ====================
$router->post('/documentos/upload', function() {
    $controller = new DocumentoController();
    $controller->upload();
});

$router->get('/documentos/download/{id}', function($id) {
    $controller = new DocumentoController();
    $controller->download($id);
});

$router->post('/documentos/delete/{id}', function($id) {
    $controller = new DocumentoController();
    $controller->delete($id);
});

// ==================== ASISTENCIAS ====================
$router->get('/asistencias', function() {
    $controller = new AsistenciaController();
    $controller->index();
});

$router->get('/asistencias/registrar', function() {
    $controller = new AsistenciaController();
    $controller->registrar();
});

$router->post('/asistencias/store', function() {
    $controller = new AsistenciaController();
    $controller->store();
});

$router->post('/asistencias/registrar-biometrico', function() {
    $controller = new AsistenciaController();
    $controller->registrarBiometrico();
});

$router->get('/asistencias/reporte', function() {
    $controller = new AsistenciaController();
    $controller->reporte();
});

$router->get('/asistencias/exportar-excel', function() {
    $controller = new AsistenciaController();
    $controller->exportarExcel();
});

// ==================== LICENCIAS ====================
$router->get('/licencias', function() {
    $controller = new LicenciaController();
    $controller->index();
});

$router->get('/licencias/create', function() {
    $controller = new LicenciaController();
    $controller->create();
});

$router->post('/licencias/store', function() {
    $controller = new LicenciaController();
    $controller->store();
});

$router->post('/licencias/aprobar/{id}', function($id) {
    $controller = new LicenciaController();
    $controller->aprobar($id);
});

$router->post('/licencias/rechazar/{id}', function($id) {
    $controller = new LicenciaController();
    $controller->rechazar($id);
});

$router->get('/licencias/ver/{id}', function($id) {
    $controller = new LicenciaController();
    $controller->ver($id);
});

/* ==================== MÓDULOS FUTUROS (INACTIVOS) ====================
$router->get('/horarios', function() {
    $controller = new HorarioController();
    $controller->index();
});
$router->get('/planillas', function() {
    $controller = new PlanillaController();
    $controller->index();
});
$router->get('/evaluaciones', function() {
    $controller = new EvaluacionController();
    $controller->index();
});
======================================================================== */

// ==================== REPORTES ====================
$router->get('/reportes', function() {
    $controller = new ReporteController();
    $controller->index();
});

$router->get('/reportes/asistencias-excel', function() {
    $controller = new ReporteController();
    $controller->exportarAsistencias();
});

$router->get('/reportes/docentes-excel', function() {
    $controller = new ReporteController();
    $controller->exportarDocentes();
});

$router->get('/reportes/logs', function() {
    $controller = new ReporteController();
    $controller->logs();
});

// ==================== CONFIGURACIÓN ====================
$router->get('/configuracion', function() {
    $controller = new ConfigController();
    $controller->index();
});

$router->post('/configuracion/update', function() {
    $controller = new ConfigController();
    $controller->update();
});

// ==================== PERFIL ====================
$router->get('/perfil', function() {
    $controller = new ProfileController();
    $controller->index();
});

$router->post('/perfil/update_password', function() {
    $controller = new ProfileController();
    $controller->updatePassword();
});

// ==================== USUARIOS (ADMIN) ====================
$router->get('/usuarios', function() {
    $controller = new UsuarioController();
    $controller->index();
});

$router->get('/usuarios/create', function() {
    $controller = new UsuarioController();
    $controller->create();
});

$router->post('/usuarios/store', function() {
    $controller = new UsuarioController();
    $controller->store();
});

$router->get('/usuarios/edit/{id}', function($id) {
    $controller = new UsuarioController();
    $controller->edit($id);
});

$router->post('/usuarios/update/{id}', function($id) {
    $controller = new UsuarioController();
    $controller->update($id);
});

$router->get('/usuarios/delete/{id}', function($id) {
    $controller = new UsuarioController();
    $controller->delete($id);
});

// ==================== 404 ====================
$router->setNotFound(function() {
    http_response_code(404);
    echo '<!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>404 - Página no encontrada</title>
        <link rel="stylesheet" href="' . APP_URL . '/public/assets/css/style.css">
        <style>
            .error-container {
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-direction: column;
                text-align: center;
                padding: var(--spacing-xl);
            }
            .error-code {
                font-size: 8rem;
                font-weight: 700;
                background: linear-gradient(135deg, var(--primary), var(--secondary));
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                margin-bottom: var(--spacing-md);
            }
        </style>
    </head>
    <body>
        <div class="error-container">
            <div class="error-code">404</div>
            <h1>Página no encontrada</h1>
            <p class="text-secondary">La página que buscas no existe.</p>
            <a href="' . APP_URL . '/dashboard" class="btn btn-primary" style="margin-top: var(--spacing-xl);">
                <i class="fas fa-home"></i> Volver al inicio
            </a>
        </div>
    </body>
    </html>';
});

// Ejecutar router
$router->run();

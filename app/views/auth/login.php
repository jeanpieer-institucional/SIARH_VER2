<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIARH</title>
    <link rel="stylesheet" href="<?= APP_URL ?>/public/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: var(--spacing-lg);
        }
        
        .login-card {
            background: var(--bg-primary);
            border-radius: var(--border-radius-lg);
            padding: var(--spacing-2xl);
            box-shadow: var(--shadow-xl);
            width: 100%;
            max-width: 450px;
            animation: fadeInUp 0.5s ease;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-header {
            text-align: center;
            margin-bottom: var(--spacing-2xl);
        }
        
        .login-logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto var(--spacing-lg);
            font-size: 2rem;
            color: white;
        }
        
        .login-title {
            font-size: var(--font-size-3xl);
            margin-bottom: var(--spacing-sm);
        }
        
        .login-subtitle {
            color: var(--text-secondary);
            font-size: var(--font-size-sm);
        }
        
        .login-form {
            margin-top: var(--spacing-2xl);
        }
        
        .input-group {
            position: relative;
            margin-bottom: var(--spacing-lg);
        }
        
        .input-group i {
            position: absolute;
            left: var(--spacing-md);
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-tertiary);
        }
        
        .input-group input {
            padding-left: 3rem;
        }
        
        .login-footer {
            margin-top: var(--spacing-xl);
            text-align: center;
            color: var(--text-secondary);
            font-size: var(--font-size-sm);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-logo">
                    <i class="fas fa-user-shield"></i>
                </div>
                <h1 class="login-title">SIARH</h1>
                <p class="login-subtitle">Sistema Integral de Asistencia y Recursos Humanos</p>
            </div>
            
            <?php if (isset($_GET['timeout']) && $_GET['timeout'] === 'inactivity'): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Su sesión ha expirado por inactividad. Inicie sesión nuevamente.</span>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['timeout']) && $_GET['timeout'] === 'absolute'): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <span>El límite de tiempo de sesión activa ha expirado. Inicie sesión nuevamente.</span>
                </div>
            <?php endif; ?>

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
            
            <form action="<?= APP_URL ?>/auth/login" method="POST" class="login-form">
                <?= Csrf::input() ?>
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input 
                        type="text" 
                        name="username" 
                        class="form-control" 
                        placeholder="Usuario"
                        required
                        autofocus
                    >
                </div>
                
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input 
                        type="password" 
                        name="password" 
                        class="form-control" 
                        placeholder="Contraseña"
                        required
                    >
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-sign-in-alt"></i>
                    Iniciar Sesión
                </button>
            </form>
            
            <div class="login-footer">
                <p>&copy; <?= date('Y') ?> SIARH. Todos los derechos reservados.</p>
                <p>Versión <?= APP_VERSION ?></p>
            </div>
        </div>
    </div>
    
    <script src="<?= APP_URL ?>/public/assets/js/main.js"></script>
</body>
</html>

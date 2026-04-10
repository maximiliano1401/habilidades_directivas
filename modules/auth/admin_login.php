<?php
require_once __DIR__ . '/../../config/config.php';
iniciarSesionSegura();

// Si ya está autenticado como admin, redirigir al panel
if (esAdmin()) {
    header('Location: ' . BASE_URL . 'modules/admin/panel.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Por favor completa todos los campos';
    } else {
        try {
            $pdo = obtenerConexion();
            
            // Buscar administrador
            $stmt = $pdo->prepare("SELECT * FROM administradores WHERE email = ? AND activo = 1");
            $stmt->execute([$email]);
            $admin = $stmt->fetch();
            
            if ($admin && password_verify($password, $admin['password'])) {
                // Autenticación exitosa
                $_SESSION['tipo_usuario'] = 'admin';
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['nombre'] = $admin['nombre'];
                $_SESSION['email'] = $admin['email'];
                
                // Actualizar último acceso
                $stmt = $pdo->prepare("UPDATE administradores SET ultimo_acceso = NOW() WHERE id = ?");
                $stmt->execute([$admin['id']]);
                
                // Registrar actividad
                registrarActividadAdmin($admin['id'], 'login', 'sistema', null, 'Inicio de sesión exitoso');
                
                header('Location: ' . BASE_URL . 'modules/admin/panel.php');
                exit;
            } else {
                $error = 'Credenciales inválidas';
                
                // Registrar intento fallido
                if ($admin) {
                    registrarActividadAdmin($admin['id'], 'login_fallido', 'sistema', null, 'Intento de login fallido');
                }
            }
        } catch (Exception $e) {
            error_log("Error en admin login: " . $e->getMessage());
            $error = 'Error en el sistema. Intenta nuevamente.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Administrador - <?php echo TITULO_SISTEMA; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-dark: #2C3E50;
            --secondary-dark: #34495E;
            --accent-gold: #F39C12;
            --bg-light: #F8F9FA;
            --text-dark: #2C3E50;
            --border-light: #E5E7EB;
        }
        
        * { 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; 
        }
        
        body {
            background: var(--bg-light);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-dark);
        }
        
        .navbar-custom {
            background: var(--primary-dark);
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(44, 62, 80, 0.1);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }
        
        .admin-login-card {
            background: white;
            border-radius: 8px;
            padding: 3rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            max-width: 450px;
            width: 100%;
            margin-top: 80px;
            animation: fadeInUp 0.6s ease-out;
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
        
        .admin-badge {
            background: var(--primary-dark);
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 4px;
            display: inline-block;
            margin-bottom: 2rem;
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        
        .admin-icon {
            width: 80px;
            height: 80px;
            background: var(--accent-gold);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }
        
        .admin-icon i {
            font-size: 2.5rem;
            color: white;
        }
        
        h2 {
            color: var(--primary-dark);
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .subtitle {
            color: #6B7280;
            font-size: 0.9rem;
            margin-bottom: 2rem;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--primary-dark);
            margin-bottom: 0.5rem;
        }
        
        .form-control {
            border: 1px solid var(--border-light);
            border-radius: 4px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--accent-gold);
            box-shadow: 0 0 0 0.2rem rgba(243, 156, 18, 0.25);
        }
        
        .btn-admin-login {
            background: var(--accent-gold);
            border: none;
            color: white;
            padding: 0.875rem;
            border-radius: 4px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.9rem;
        }
        
        .btn-admin-login:hover {
            background: #E67E22;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(243, 156, 18, 0.3);
        }
        
        .back-link {
            color: #6B7280;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }
        
        .back-link:hover {
            color: var(--accent-gold);
        }
        
        .security-notice {
            background: #FEF3C7;
            border-left: 4px solid var(--accent-gold);
            color: #92400E;
            padding: 1rem 1.5rem;
            border-radius: 4px;
            font-size: 0.85rem;
            margin-top: 1.5rem;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar-custom">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center w-100">
                <span class="text-white h5 mb-0 fw-bold">
                    <i class="bi bi-mortarboard-fill me-2"></i>
                    <?php echo TITULO_SISTEMA; ?>
                </span>
                <a href="<?php echo BASE_URL; ?>" class="btn btn-light btn-sm">
                    <i class="bi bi-arrow-left"></i> Volver al Inicio
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="admin-login-card">
                    <div class="text-center">
                        <div class="admin-badge">
                            <i class="bi bi-shield-check me-2"></i>ÁREA RESTRINGIDA
                        </div>
                        
                        <div class="admin-icon">
                            <i class="bi bi-shield-lock-fill"></i>
                        </div>
                        
                        <h2>Panel de Administración</h2>
                        <p class="subtitle">Acceso exclusivo para administradores del sistema</p>
                    </div>
                    
                    <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="bi bi-envelope me-1"></i> Email
                            </label>
                            <input 
                                type="email" 
                                class="form-control" 
                                id="email" 
                                name="email" 
                                required
                                placeholder="admin@sistema.com"
                                autocomplete="username"
                            >
                        </div>
                        
                        <div class="mb-4">
                            <label for="password" class="form-label">
                                <i class="bi bi-key me-1"></i> Contraseña
                            </label>
                            <input 
                                type="password" 
                                class="form-control" 
                                id="password" 
                                name="password" 
                                required
                                placeholder="••••••••"
                                autocomplete="current-password"
                            >
                        </div>
                        
                        <button type="submit" class="btn btn-admin-login">
                            <i class="bi bi-box-arrow-in-right me-2"></i>
                            Acceder al Panel
                        </button>
                    </form>
                    
                    <div class="text-center mt-4">
                        <a href="login.php" class="back-link">
                            <i class="bi bi-arrow-left me-1"></i>
                            Volver al login público
                        </a>
                    </div>
                    
                    <div class="security-notice">
                        <i class="bi bi-shield-exclamation me-2"></i>
                        <strong>Aviso de Seguridad:</strong> Este panel es solo para administradores autorizados. Todos los accesos son registrados y monitoreados.
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

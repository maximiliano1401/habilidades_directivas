<?php
require_once 'config.php';
iniciarSesionSegura();

// Si ya está autenticado, redirigir
if (estaAutenticado()) {
    if (esUsuario()) {
        header('Location: formulario.php');
    } else {
        header('Location: dashboard_empresa.php');
    }
    exit;
}

$error = '';
$tipo_login = $_GET['tipo'] ?? 'usuario'; // 'usuario' o 'empresa'

// Procesar login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = limpiarEntrada($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $tipo = $_POST['tipo'] ?? 'usuario';
    
    if (empty($email) || empty($password)) {
        $error = 'Por favor, complete todos los campos.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'El correo electrónico no es válido.';
    } else {
        try {
            $pdo = obtenerConexion();
            
            if ($tipo === 'empresa') {
                // Login de empresa
                $stmt = $pdo->prepare("SELECT * FROM empresas WHERE email = ? AND activo = 1");
                $stmt->execute([$email]);
                $empresa = $stmt->fetch();
                
                if ($empresa && password_verify($password, $empresa['password'])) {
                    // Crear sesión
                    $_SESSION['empresa_id'] = $empresa['id'];
                    $_SESSION['tipo_usuario'] = 'empresa';
                    $_SESSION['nombre'] = $empresa['nombre'];
                    $_SESSION['email'] = $empresa['email'];
                    
                    // Registrar sesión en BD
                    $session_id = session_id();
                    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
                    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
                    $fecha_expiracion = date('Y-m-d H:i:s', time() + 3600 * 24); // 24 horas
                    
                    $stmt = $pdo->prepare("
                        INSERT INTO sesiones (session_id, tipo_usuario, empresa_id, ip_address, user_agent, fecha_expiracion)
                        VALUES (?, 'empresa', ?, ?, ?, ?)
                    ");
                    $stmt->execute([$session_id, $empresa['id'], $ip, $user_agent, $fecha_expiracion]);
                    $_SESSION['session_db_id'] = $pdo->lastInsertId();
                    
                    // Registrar actividad
                    registrarActividad('empresa', null, $empresa['id'], 'login', 'Inicio de sesión exitoso');
                    
                    header('Location: dashboard_empresa.php');
                    exit;
                } else {
                    $error = 'Credenciales incorrectas.';
                    registrarActividad('empresa', null, null, 'login_fallido', "Intento fallido para: $email");
                }
                
            } else {
                // Login de usuario
                $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? AND activo = 1");
                $stmt->execute([$email]);
                $usuario = $stmt->fetch();
                
                if ($usuario && password_verify($password, $usuario['password'])) {
                    // Crear sesión
                    $_SESSION['usuario_id'] = $usuario['id'];
                    $_SESSION['empresa_id'] = $usuario['empresa_id'];
                    $_SESSION['tipo_usuario'] = 'usuario';
                    $_SESSION['nombre'] = $usuario['nombre'];
                    $_SESSION['email'] = $usuario['email'];
                    $_SESSION['puesto'] = $usuario['puesto'];
                    
                    // Registrar sesión en BD
                    $session_id = session_id();
                    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
                    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
                    $fecha_expiracion = date('Y-m-d H:i:s', time() + 3600 * 24); // 24 horas
                    
                    $stmt = $pdo->prepare("
                        INSERT INTO sesiones (session_id, tipo_usuario, usuario_id, ip_address, user_agent, fecha_expiracion)
                        VALUES (?, 'usuario', ?, ?, ?, ?)
                    ");
                    $stmt->execute([$session_id, $usuario['id'], $ip, $user_agent, $fecha_expiracion]);
                    $_SESSION['session_db_id'] = $pdo->lastInsertId();
                    
                    // Registrar actividad
                    registrarActividad('usuario', $usuario['id'], $usuario['empresa_id'], 'login', 'Inicio de sesión exitoso');
                    
                    header('Location: formulario.php');
                    exit;
                } else {
                    $error = 'Credenciales incorrectas.';
                    registrarActividad('usuario', null, null, 'login_fallido', "Intento fallido para: $email");
                }
            }
            
        } catch (Exception $e) {
            error_log("Error en login: " . $e->getMessage());
            $error = 'Error en el sistema. Por favor, intente más tarde.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - <?php echo TITULO_SISTEMA; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-dark: #2C3E50;
            --secondary-dark: #34495E;
            --accent-gold: #F39C12;
            --bg-light: #F8F9FA;
        }
        
        * {
            font-family: 'Inter', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--secondary-dark) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 2rem 0;
        }
        
        .login-container {
            max-width: 480px;
            margin: 0 auto;
        }
        
        .login-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        
        .login-header {
            background: var(--primary-dark);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .login-header i {
            font-size: 3rem;
            color: var(--accent-gold);
            margin-bottom: 1rem;
        }
        
        .login-body {
            padding: 2.5rem;
        }
        
        .form-label {
            font-weight: 500;
            color: var(--primary-dark);
            margin-bottom: 0.5rem;
        }
        
        .form-control {
            padding: 0.75rem 1rem;
            border: 2px solid #E5E7EB;
            border-radius: 6px;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--accent-gold);
            box-shadow: 0 0 0 3px rgba(243, 156, 18, 0.1);
        }
        
        .btn-login {
            background: var(--accent-gold);
            border: none;
            color: white;
            padding: 0.75rem;
            font-weight: 600;
            border-radius: 6px;
            width: 100%;
            transition: all 0.3s;
        }
        
        .btn-login:hover {
            background: #E67E22;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(243, 156, 18, 0.3);
        }
        
        .tipo-selector {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .tipo-selector input[type="radio"] {
            display: none;
        }
        
        .tipo-selector label {
            flex: 1;
            padding: 1rem;
            border: 2px solid #E5E7EB;
            border-radius: 6px;
            cursor: pointer;
            text-align: center;
            transition: all 0.3s;
            font-weight: 500;
        }
        
        .tipo-selector input[type="radio"]:checked + label {
            background: var(--accent-gold);
            color: white;
            border-color: var(--accent-gold);
        }
        
        .alert {
            border-radius: 6px;
            border: none;
        }
        
        .divider {
            text-align: center;
            margin: 2rem 0;
            position: relative;
        }
        
        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #E5E7EB;
        }
        
        .divider span {
            background: white;
            padding: 0 1rem;
            position: relative;
            color: #9CA3AF;
            font-size: 0.9rem;
        }
        
        .back-link {
            color: var(--primary-dark);
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 1rem;
        }
        
        .back-link:hover {
            color: var(--accent-gold);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="login-card">
                <div class="login-header">
                    <i class="bi bi-shield-lock"></i>
                    <h4 class="mb-2">Iniciar Sesión</h4>
                    <p class="mb-0 small opacity-75">Sistema de Evaluación de Habilidades</p>
                </div>
                
                <div class="login-body">
                    <?php if ($error): ?>
                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <div><?php echo $error; ?></div>
                    </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="tipo-selector">
                            <input type="radio" name="tipo" id="tipo_usuario" value="usuario" 
                                   <?php echo $tipo_login === 'usuario' ? 'checked' : ''; ?>>
                            <label for="tipo_usuario">
                                <i class="bi bi-person-fill d-block mb-1" style="font-size: 1.5rem;"></i>
                                Usuario
                            </label>
                            
                            <input type="radio" name="tipo" id="tipo_empresa" value="empresa"
                                   <?php echo $tipo_login === 'empresa' ? 'checked' : ''; ?>>
                            <label for="tipo_empresa">
                                <i class="bi bi-building d-block mb-1" style="font-size: 1.5rem;"></i>
                                Empresa
                            </label>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="bi bi-envelope me-1"></i> Correo Electrónico
                            </label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   placeholder="correo@ejemplo.com" required 
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                        </div>
                        
                        <div class="mb-4">
                            <label for="password" class="form-label">
                                <i class="bi bi-lock me-1"></i> Contraseña
                            </label>
                            <input type="password" class="form-control" id="password" name="password" 
                                   placeholder="••••••••" required>
                        </div>
                        
                        <button type="submit" class="btn btn-login">
                            <i class="bi bi-box-arrow-in-right me-2"></i>
                            Iniciar Sesión
                        </button>
                    </form>
                    
                    <div class="divider">
                        <span>¿No tienes cuenta?</span>
                    </div>
                    
                    <div class="text-center">
                        <a href="registro_usuario.php" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-person-plus"></i> Registrar Usuario
                        </a>
                    </div>
                    
                    <div class="text-center mt-3">
                        <a href="index.php" class="back-link">
                            <i class="bi bi-arrow-left"></i>
                            Volver al inicio
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-3">
                <small class="text-white opacity-75">
                    <i class="bi bi-info-circle"></i> 
                    Credenciales de prueba:<br>
                    <strong>Usuario:</strong> juan.perez@demo.com / usuario123<br>
                    <strong>Empresa:</strong> empresa@demo.com / empresa123
                </small>
            </div>
            
            <div class="text-center mt-4">
                <a href="admin_login.php" class="text-white-50 text-decoration-none" style="font-size: 0.75rem; opacity: 0.5;">
                    <i class="bi bi-shield-lock"></i> Acceso Administrador
                </a>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

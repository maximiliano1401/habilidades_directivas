<?php
require_once 'config.php';
iniciarSesionSegura();

// Si ya está autenticado, redirigir
if (estaAutenticado()) {
    header('Location: ' . (esUsuario() ? 'formulario.php' : 'dashboard_empresa.php'));
    exit;
}

$error = '';
$success = '';

// Cargar empresas para el selector
$empresas = [];
try {
    $pdo = obtenerConexion();
    $stmt = $pdo->query("SELECT id, nombre FROM empresas WHERE activo = 1 ORDER BY nombre");
    $empresas = $stmt->fetchAll();
} catch (Exception $e) {
    error_log("Error al cargar empresas: " . $e->getMessage());
}

// Procesar registro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = limpiarEntrada($_POST['nombre'] ?? '');
    $email = limpiarEntrada($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $empresa_id = intval($_POST['empresa_id'] ?? 0);
    $puesto = limpiarEntrada($_POST['puesto'] ?? '');
    $departamento = limpiarEntrada($_POST['departamento'] ?? '');
    $telefono = limpiarEntrada($_POST['telefono'] ?? '');
    
    // Validaciones
    if (empty($nombre) || empty($email) || empty($password) || empty($empresa_id)) {
        $error = 'Por favor, complete todos los campos obligatorios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'El correo electrónico no es válido.';
    } elseif (strlen($password) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres.';
    } elseif ($password !== $password_confirm) {
        $error = 'Las contraseñas no coinciden.';
    } else {
        try {
            $pdo = obtenerConexion();
            
            // Verificar si el email ya existe
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->fetch()) {
                $error = 'El correo electrónico ya está registrado.';
            } else {
                // Insertar usuario
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                
                $stmt = $pdo->prepare("
                    INSERT INTO usuarios (nombre, email, password, empresa_id, puesto, departamento, telefono)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                
                $stmt->execute([
                    $nombre,
                    $email,
                    $password_hash,
                    $empresa_id,
                    $puesto,
                    $departamento,
                    $telefono
                ]);
                
                $usuario_id = $pdo->lastInsertId();
                
                // Registrar actividad
                registrarActividad('usuario', $usuario_id, $empresa_id, 'registro', 'Nuevo usuario registrado');
                
                $success = 'Registro exitoso. Ahora puede iniciar sesión.';
                
                // Redirigir al login después de 2 segundos
                header("refresh:2;url=login.php");
            }
            
        } catch (Exception $e) {
            error_log("Error en registro de usuario: " . $e->getMessage());
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
    <title>Registro de Usuario - <?php echo TITULO_SISTEMA; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-dark: #2C3E50;
            --secondary-dark: #34495E;
            --accent-gold: #F39C12;
        }
        
        * { font-family: 'Inter', sans-serif; }
        
        body {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--secondary-dark) 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }
        
        .register-container {
            max-width: 600px;
            margin: 0 auto;
        }
        
        .register-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        
        .register-header {
            background: var(--primary-dark);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .register-header i {
            font-size: 2.5rem;
            color: var(--accent-gold);
            margin-bottom: 0.5rem;
        }
        
        .register-body {
            padding: 2.5rem;
        }
        
        .form-label {
            font-weight: 500;
            color: var(--primary-dark);
            margin-bottom: 0.5rem;
        }
        
        .required {
            color: #DC2626;
        }
        
        .form-control, .form-select {
            padding: 0.75rem 1rem;
            border: 2px solid #E5E7EB;
            border-radius: 6px;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--accent-gold);
            box-shadow: 0 0 0 3px rgba(243, 156, 18, 0.1);
        }
        
        .btn-register {
            background: var(--accent-gold);
            border: none;
            color: white;
            padding: 0.75rem;
            font-weight: 600;
            border-radius: 6px;
            width: 100%;
            transition: all 0.3s;
        }
        
        .btn-register:hover {
            background: #E67E22;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(243, 156, 18, 0.3);
        }
        
        .back-link {
            color: var(--primary-dark);
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .back-link:hover {
            color: var(--accent-gold);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="register-container">
            <div class="register-card">
                <div class="register-header">
                    <i class="bi bi-person-plus-fill"></i>
                    <h4 class="mb-2">Registro de Usuario</h4>
                    <p class="mb-0 small opacity-75">Complete el formulario para crear su cuenta</p>
                </div>
                
                <div class="register-body">
                    <?php if ($error): ?>
                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <div><?php echo $error; ?></div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                    <div class="alert alert-success d-flex align-items-center" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <div><?php echo $success; ?> Redirigiendo...</div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!$success): ?>
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="nombre" class="form-label">
                                    <i class="bi bi-person me-1"></i> Nombre Completo <span class="required">*</span>
                                </label>
                                <input type="text" class="form-control" id="nombre" name="nombre" 
                                       placeholder="Juan Pérez García" required
                                       value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>">
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label for="email" class="form-label">
                                    <i class="bi bi-envelope me-1"></i> Correo Electrónico <span class="required">*</span>
                                </label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       placeholder="correo@ejemplo.com" required
                                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">
                                    <i class="bi bi-lock me-1"></i> Contraseña <span class="required">*</span>
                                </label>
                                <input type="password" class="form-control" id="password" name="password" 
                                       placeholder="Mínimo 6 caracteres" required minlength="6">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="password_confirm" class="form-label">
                                    <i class="bi bi-lock-fill me-1"></i> Confirmar Contraseña <span class="required">*</span>
                                </label>
                                <input type="password" class="form-control" id="password_confirm" name="password_confirm" 
                                       placeholder="Repetir contraseña" required minlength="6">
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label for="empresa_id" class="form-label">
                                    <i class="bi bi-building me-1"></i> Empresa <span class="required">*</span>
                                </label>
                                <select class="form-select" id="empresa_id" name="empresa_id" required>
                                    <option value="">Seleccione una empresa...</option>
                                    <?php foreach ($empresas as $empresa): ?>
                                    <option value="<?php echo $empresa['id']; ?>"
                                            <?php echo (isset($_POST['empresa_id']) && $_POST['empresa_id'] == $empresa['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($empresa['nombre']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">Si su empresa no aparece, solicite su registro</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="puesto" class="form-label">
                                    <i class="bi bi-briefcase me-1"></i> Puesto
                                </label>
                                <input type="text" class="form-control" id="puesto" name="puesto" 
                                       placeholder="Gerente, Coordinador, etc."
                                       value="<?php echo isset($_POST['puesto']) ? htmlspecialchars($_POST['puesto']) : ''; ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="departamento" class="form-label">
                                    <i class="bi bi-diagram-3 me-1"></i> Departamento
                                </label>
                                <input type="text" class="form-control" id="departamento" name="departamento" 
                                       placeholder="Ventas, RRHH, etc."
                                       value="<?php echo isset($_POST['departamento']) ? htmlspecialchars($_POST['departamento']) : ''; ?>">
                            </div>
                            
                            <div class="col-md-12 mb-4">
                                <label for="telefono" class="form-label">
                                    <i class="bi bi-telephone me-1"></i> Teléfono
                                </label>
                                <input type="tel" class="form-control" id="telefono" name="telefono" 
                                       placeholder="5555555555"
                                       value="<?php echo isset($_POST['telefono']) ? htmlspecialchars($_POST['telefono']) : ''; ?>">
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-register">
                            <i class="bi bi-check-circle me-2"></i>
                            Crear Cuenta
                        </button>
                    </form>
                    <?php endif; ?>
                    
                    <hr class="my-4">
                    
                    <div class="text-center">
                        <p class="mb-2">¿Ya tiene una cuenta?</p>
                        <a href="login.php" class="back-link">
                            <i class="bi bi-box-arrow-in-right"></i>
                            Iniciar Sesión
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validar que las contraseñas coincidan
        document.getElementById('password_confirm').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirm = this.value;
            
            if (confirm && password !== confirm) {
                this.setCustomValidity('Las contraseñas no coinciden');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html>

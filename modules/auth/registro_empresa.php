<?php
require_once __DIR__ . '/../../config/config.php';
iniciarSesionSegura();

// Si ya está autenticado, redirigir
if (estaAutenticado()) {
    header('Location: ' . BASE_URL . (esUsuario() ? 'modules/evaluacion/formulario.php' : 'modules/empresa/dashboard.php'));
    exit;
}

$error = '';
$success = '';

// Procesar registro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = limpiarEntrada($_POST['nombre'] ?? '');
    $email = limpiarEntrada($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $rfc = limpiarEntrada($_POST['rfc'] ?? '');
    $telefono = limpiarEntrada($_POST['telefono'] ?? '');
    $direccion = limpiarEntrada($_POST['direccion'] ?? '');
    
    // Validaciones
    if (empty($nombre) || empty($email) || empty($password)) {
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
            $stmt = $pdo->prepare("SELECT id FROM empresas WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->fetch()) {
                $error = 'El correo electrónico ya está registrado.';
            } else {
                // Insertar empresa
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                
                $stmt = $pdo->prepare("
                    INSERT INTO empresas (nombre, email, password, rfc, telefono, direccion)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                
                $stmt->execute([
                    $nombre,
                    $email,
                    $password_hash,
                    $rfc,
                    $telefono,
                    $direccion
                ]);
                
                $empresa_id = $pdo->lastInsertId();
                
                // Registrar actividad
                registrarActividad('empresa', null, $empresa_id, 'registro', 'Nueva empresa registrada');
                
                $success = 'Registro exitoso. Ahora puede iniciar sesión.';
                
                // Redirigir al login después de 2 segundos
                header("refresh:2;url=login.php?tipo=empresa");
            }
            
        } catch (Exception $e) {
            error_log("Error en registro de empresa: " . $e->getMessage());
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
    <title>Registro de Empresa - <?php echo TITULO_SISTEMA; ?></title>
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
        
        .info-box {
            background: #FEF3C7;
            border-left: 4px solid var(--accent-gold);
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="register-container">
            <div class="register-card">
                <div class="register-header">
                    <i class="bi bi-building-add"></i>
                    <h4 class="mb-2">Registro de Empresa</h4>
                    <p class="mb-0 small opacity-75">Complete el formulario para registrar su organización</p>
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
                    <div class="info-box">
                        <small>
                            <i class="bi bi-info-circle me-1"></i>
                            <strong>Nota:</strong> Una vez registrada su empresa, los usuarios podrán 
                            seleccionarla al crear su cuenta y usted podrá visualizar sus resultados.
                        </small>
                    </div>
                    
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="nombre" class="form-label">
                                    <i class="bi bi-building me-1"></i> Nombre de la Empresa <span class="required">*</span>
                                </label>
                                <input type="text" class="form-control" id="nombre" name="nombre" 
                                       placeholder="Empresa S.A. de C.V." required
                                       value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>">
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label for="email" class="form-label">
                                    <i class="bi bi-envelope me-1"></i> Correo Electrónico Corporativo <span class="required">*</span>
                                </label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       placeholder="contacto@empresa.com" required
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
                            
                            <div class="col-md-6 mb-3">
                                <label for="rfc" class="form-label">
                                    <i class="bi bi-card-text me-1"></i> RFC
                                </label>
                                <input type="text" class="form-control" id="rfc" name="rfc" 
                                       placeholder="ABC123456XYZ" maxlength="13"
                                       value="<?php echo isset($_POST['rfc']) ? htmlspecialchars($_POST['rfc']) : ''; ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="telefono" class="form-label">
                                    <i class="bi bi-telephone me-1"></i> Teléfono
                                </label>
                                <input type="tel" class="form-control" id="telefono" name="telefono" 
                                       placeholder="5555555555"
                                       value="<?php echo isset($_POST['telefono']) ? htmlspecialchars($_POST['telefono']) : ''; ?>">
                            </div>
                            
                            <div class="col-md-12 mb-4">
                                <label for="direccion" class="form-label">
                                    <i class="bi bi-geo-alt me-1"></i> Dirección
                                </label>
                                <textarea class="form-control" id="direccion" name="direccion" rows="2" 
                                          placeholder="Calle, Número, Colonia, Ciudad"><?php echo isset($_POST['direccion']) ? htmlspecialchars($_POST['direccion']) : ''; ?></textarea>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-register">
                            <i class="bi bi-check-circle me-2"></i>
                            Registrar Empresa
                        </button>
                    </form>
                    <?php endif; ?>
                    
                    <hr class="my-4">
                    
                    <div class="text-center">
                        <p class="mb-2">¿Ya tiene una cuenta?</p>
                        <a href="login.php?tipo=empresa" class="back-link">
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

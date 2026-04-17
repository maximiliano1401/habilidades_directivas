<?php
require_once __DIR__ . '/../../config/config.php';
iniciarSesionSegura();
requerirUsuario();

// Obtener nombre del usuario desde la sesión
$nombre_usuario = $_SESSION['nombre'] ?? 'Usuario';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluación Enviada - <?php echo TITULO_SISTEMA; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-dark: #2C3E50;
            --accent-gold: #F39C12;
            --success-green: #27AE60;
        }
        
        * { font-family: 'Inter', sans-serif; }
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .success-card {
            background: white;
            border-radius: 16px;
            padding: 3rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            text-align: center;
            max-width: 600px;
            animation: slideUp 0.5s ease-out;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .success-icon {
            width: 100px;
            height: 100px;
            background: var(--success-green);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            animation: scaleIn 0.5s ease-out 0.2s both;
        }
        
        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }
        
        .success-icon i {
            font-size: 3rem;
            color: white;
        }
        
        .checkmark {
            animation: checkmark 0.8s ease-out 0.5s both;
        }
        
        @keyframes checkmark {
            0% {
                stroke-dashoffset: 100;
            }
            100% {
                stroke-dashoffset: 0;
            }
        }
        
        h1 {
            color: var(--success-green);
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid var(--accent-gold);
            padding: 1.5rem;
            margin: 2rem 0;
            text-align: left;
            border-radius: 4px;
        }
        
        .info-box i {
            color: var(--accent-gold);
            font-size: 1.5rem;
            margin-right: 1rem;
        }
        
        .btn-primary {
            background: var(--primary-dark);
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: #1a252f;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(44, 62, 80, 0.3);
        }
        
        /* ===== RESPONSIVE MOBILE ===== */
        @media (max-width: 767.98px) {
            .success-card {
                padding: 2rem 1.5rem;
                margin: 1rem;
                border-radius: 12px;
            }
            .success-icon {
                width: 70px;
                height: 70px;
            }
            .success-icon i {
                font-size: 2rem;
            }
            h1 {
                font-size: 1.4rem;
            }
            .lead {
                font-size: 0.95rem;
            }
            .info-box {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="success-card">
        <div class="success-icon">
            <i class="bi bi-check-lg"></i>
        </div>
        
        <h1>¡Evaluación Enviada Exitosamente!</h1>
        
        <p class="lead text-muted mb-4">
            Gracias por completar la evaluación de habilidades directivas
        </p>
        
        <div class="info-box">
            <div class="d-flex align-items-start">
                <i class="bi bi-info-circle-fill"></i>
                <div>
                    <h6 class="mb-2 fw-bold">¿Qué sigue?</h6>
                    <p class="mb-0 small">
                        Tu evaluación ha sido registrada correctamente. Los resultados serán 
                        revisados por tu empresa y estarán disponibles para ellos en el 
                        panel de administración. Si tienes preguntas sobre tus resultados, 
                        contacta con el departamento de recursos humanos de tu organización.
                    </p>
                </div>
            </div>
        </div>
        
        <div class="alert alert-light border" role="alert">
            <small>
                <i class="bi bi-person-circle me-2"></i>
                <strong><?php echo htmlspecialchars($nombre_usuario); ?></strong><br>
                <span class="text-muted">
                    Evaluación completada el <?php echo date('d/m/Y \a \l\a\s H:i'); ?>
                </span>
            </small>
        </div>
        
        <a href="<?php echo BASE_URL; ?>modules/auth/logout.php" class="btn btn-primary mt-3">
            <i class="bi bi-box-arrow-right me-2"></i>
            Cerrar Sesión
        </a>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

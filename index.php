<?php 
require_once __DIR__ . '/config/config.php';
iniciarSesionSegura();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo TITULO_SISTEMA; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
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
            color: var(--text-dark);
        }
        
        .navbar-custom {
            background: var(--primary-dark);
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(44, 62, 80, 0.1);
        }
        
        .hero-section {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            padding: 4rem 3rem;
            margin: 3rem 0;
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
        
        .stats-bar {
            background: var(--primary-dark);
            color: white;
            padding: 2rem 0;
            margin-bottom: 3rem;
        }
        
        .stat-item {
            text-align: center;
            padding: 1rem;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--accent-gold);
            display: block;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .feature-box {
            background: white;
            border: 1px solid var(--border-light);
            border-radius: 6px;
            padding: 2rem 1.5rem;
            margin: 1rem 0;
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .feature-box:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(44, 62, 80, 0.12);
            border-color: var(--accent-gold);
        }
        
        .feature-icon {
            font-size: 2rem;
            color: var(--accent-gold);
            margin-bottom: 1rem;
            display: block;
        }
        
        .feature-box h5 {
            color: var(--primary-dark);
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 0.75rem;
        }
        
        .btn-comenzar {
            background: var(--accent-gold);
            color: white;
            border: none;
            padding: 14px 48px;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 4px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .btn-comenzar:hover {
            background: #E67E22;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(243, 156, 18, 0.3);
            color: white;
        }
        
        h1 {
            color: var(--primary-dark);
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        h3, h4 {
            color: var(--primary-dark);
            font-weight: 600;
        }
        
        .section-title {
            position: relative;
            padding-bottom: 1rem;
            margin-bottom: 2rem;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 3px;
            background: var(--accent-gold);
        }
        
        .habilidad-item {
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--border-light);
        }
        
        .habilidad-item:last-child {
            border-bottom: none;
        }
        
        .habilidad-item h6 {
            color: var(--primary-dark);
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .habilidad-item .small {
            color: #6B7280;
        }
        
        .info-badge {
            background: #FEF3C7;
            color: #92400E;
            padding: 1rem 1.5rem;
            border-radius: 4px;
            border-left: 4px solid var(--accent-gold);
            margin: 2rem 0;
        }
        
        /* ===== RESPONSIVE MOBILE ===== */
        @media (max-width: 767.98px) {
            .navbar-custom .container {
                padding: 0 1rem;
            }
            .navbar-custom .d-flex {
                flex-wrap: wrap;
                gap: 0.25rem;
            }
            .navbar-custom .h5 {
                font-size: 1rem;
            }
            .navbar-custom .small {
                font-size: 0.7rem;
            }
            .stats-bar {
                padding: 1rem 0;
            }
            .stat-number {
                font-size: 1.8rem;
            }
            .stat-label {
                font-size: 0.75rem;
            }
            .stat-item {
                padding: 0.5rem;
            }
            .hero-section {
                padding: 2rem 1.25rem;
                margin: 1.5rem 0;
            }
            .hero-section .display-5 {
                font-size: 1.5rem;
            }
            .hero-section .lead {
                font-size: 0.95rem;
            }
            .hero-section .lead br {
                display: none;
            }
            .feature-box {
                padding: 1.25rem 1rem;
                margin: 0.5rem 0;
            }
            .feature-icon {
                font-size: 1.5rem;
            }
            .btn-comenzar {
                padding: 12px 24px;
                font-size: 0.9rem;
                width: 100%;
                margin-bottom: 0.5rem;
            }
            .btn-outline-secondary.btn-lg {
                width: 100%;
                margin-top: 0.5rem;
            }
            .info-badge {
                font-size: 0.85rem;
                padding: 0.75rem 1rem;
                line-height: 1.8;
            }
            .section-title {
                font-size: 1.1rem;
                margin-bottom: 1.5rem;
            }
            .habilidad-item h6 {
                font-size: 0.9rem;
            }
        }
        
        @media (max-width: 575.98px) {
            .stats-bar .row > div {
                flex: 0 0 33.333%;
                max-width: 33.333%;
            }
            .hero-section .display-5 {
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar-custom">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center w-100">
                <span class="text-white h5 mb-0 fw-bold">
                    <?php if (!empty(LOGO_SISTEMA)): ?>
                        <img src="<?php echo BASE_URL . htmlspecialchars(LOGO_SISTEMA); ?>" alt="Logo" style="max-height: 30px; vertical-align: middle; margin-right: 8px;">
                    <?php else: ?>
                        <i class="bi bi-briefcase"></i>
                    <?php endif; ?>
                    <?php echo htmlspecialchars(TITULO_SISTEMA); ?>
                </span>
                <span class="text-white small">
                    <i class="bi bi-shield-check"></i> Confiable y Validado
                </span>
            </div>
        </div>
    </nav>

    <!-- Stats Bar -->
    <div class="stats-bar">
        <div class="container">
            <div class="row">
                <div class="col-4">
                    <div class="stat-item">
                        <span class="stat-number">11</span>
                        <span class="stat-label">Habilidades Clave</span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="stat-item">
                        <span class="stat-number"><?php 
                            $total_preguntas = 0;
                            foreach ($habilidades as $hab) {
                                $total_preguntas += count($hab['preguntas']);
                            }
                            echo $total_preguntas;
                        ?></span>
                        <span class="stat-label">Criterios de Evaluación</span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="stat-item">
                        <span class="stat-number">15</span>
                        <span class="stat-label">Minutos Promedio</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="hero-section">
            <div class="text-center mb-5">
                <h1 class="display-5 mb-3">
                    Evaluación de Habilidades Directivas
                </h1>
                <p class="lead text-muted">
                    Identifica tus fortalezas y áreas de desarrollo profesional mediante<br>
                    un análisis exhaustivo basado en competencias gerenciales
                </p>
            </div>

            <div class="row mb-5">
                <div class="col-md-12">
                    <h3 class="section-title">Acerca de la Evaluación</h3>
                    <p class="text-muted" style="line-height: 1.8;">
                        Las habilidades directivas representan el conjunto de competencias esenciales que determinan 
                        la efectividad de los profesionales en posiciones de liderazgo y gestión. Esta herramienta 
                        proporciona un diagnóstico preciso de sus capacidades actuales, permitiéndole diseñar un 
                        plan de desarrollo profesional estratégico.
                    </p>
                </div>
            </div>

            <div class="row">
                <?php 
                $features = [
                    ['icon' => 'bi-clipboard-data', 'title' => 'Evaluación Integral', 'desc' => 'Análisis completo de 11 dimensiones directivas'],
                    ['icon' => 'bi-graph-up-arrow', 'title' => 'Análisis Detallado', 'desc' => 'Interpretación profesional de resultados'],
                    ['icon' => 'bi-bullseye', 'title' => 'Plan de Acción', 'desc' => 'Recomendaciones específicas y priorizadas'],
                    ['icon' => 'bi-file-earmark-text', 'title' => 'Reporte Ejecutivo', 'desc' => 'Documento descargable en formato PDF'],
                ];
                
                foreach ($features as $feature): ?>
                <div class="col-6 col-lg-3">
                    <div class="feature-box text-center">
                        <i class="bi <?php echo $feature['icon']; ?> feature-icon"></i>
                        <h5><?php echo $feature['title']; ?></h5>
                        <p class="small text-muted mb-0"><?php echo $feature['desc']; ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="row mt-5">
                <div class="col-md-12">
                    <h4 class="section-title">Competencias Evaluadas</h4>
                    <div class="row">
                        <?php foreach ($habilidades as $habilidad): ?>
                        <div class="col-md-6">
                            <div class="habilidad-item">
                                <h6>
                                    <i class="bi bi-check-circle text-success" style="font-size: 0.9rem;"></i>
                                    <?php echo $habilidad['nombre']; ?>
                                </h6>
                                <p class="small text-muted mb-0"><?php echo $habilidad['descripcion']; ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="info-badge text-center mt-5">
                <strong><i class="bi bi-clock-history"></i> Duración estimada:</strong> 10-15 minutos | 
                <strong><i class="bi bi-file-text"></i> Preguntas:</strong> <?php echo $total_preguntas; ?> | 
                <strong><i class="bi bi-bar-chart"></i> Escala:</strong> Likert 1-5
            </div>

            <div class="text-center mt-4">
                <?php
                if (estaAutenticado()): 
                    if (esUsuario()): ?>
                        <a href="modules/evaluacion/formulario.php" class="btn btn-comenzar">
                            Iniciar Evaluación <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    <?php else: ?>
                        <a href="modules/empresa/dashboard.php" class="btn btn-comenzar">
                            Ver Dashboard <i class="bi bi-speedometer2 ms-2"></i>
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="modules/auth/login.php" class="btn btn-comenzar me-2">
                        <i class="bi bi-box-arrow-in-right me-2"></i> Iniciar Sesión
                    </a>
                    <a href="modules/auth/registro_usuario.php" class="btn btn-outline-secondary btn-lg">
                        <i class="bi bi-person-plus me-2"></i> Registrarse
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Animación de números en stats
        document.addEventListener('DOMContentLoaded', function() {
            const statNumbers = document.querySelectorAll('.stat-number');
            
            statNumbers.forEach(stat => {
                const target = parseInt(stat.textContent);
                let current = 0;
                const increment = target / 50;
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        stat.textContent = target;
                        clearInterval(timer);
                    } else {
                        stat.textContent = Math.floor(current);
                    }
                }, 20);
            });
        });
    </script>
</body>
</html>

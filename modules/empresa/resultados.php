<?php
require_once __DIR__ . '/../../config/config.php';
iniciarSesionSegura();

// Obtener ID de evaluación
$evaluacion_id = $_GET['id'] ?? ($_SESSION['evaluacion_id'] ?? null);

if (!$evaluacion_id) {
    header('Location: ' . BASE_URL);
    exit;
}

// Cargar evaluación desde archivo JSON
$archivo = DATOS_DIR . '/evaluacion_' . $evaluacion_id . '.json';

if (!file_exists($archivo)) {
    header('Location: ' . BASE_URL);
    exit;
}

$evaluacion = json_decode(file_get_contents($archivo), true);

// Ordenar habilidades por promedio (de mayor a menor para fortalezas)
$resultados = $evaluacion['resultados'];
uasort($resultados, function($a, $b) {
    return $b['promedio'] <=> $a['promedio'];
});

// Identificar fortalezas (promedio >= 4.0)
$fortalezas = array_filter($resultados, function($r) {
    return $r['promedio'] >= 4.0;
});

// Identificar áreas de mejora (promedio < 3.5)
$areas_mejora = array_filter($resultados, function($r) {
    return $r['promedio'] < 3.5;
});
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados - <?php echo TITULO_SISTEMA; ?></title>
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
        }
        
        .navbar-custom {
            background: var(--primary-dark);
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(44, 62, 80, 0.1);
        }
        
        .results-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            padding: 2.5rem;
            margin: 2rem 0;
        }
        
        .header-section {
            border-bottom: 1px solid var(--border-light);
            padding-bottom: 2rem;
            margin-bottom: 2rem;
        }
        
        .score-card {
            background: var(--primary-dark);
            color: white;
            border-radius: 8px;
            padding: 2rem 1.5rem;
            text-align: center;
            box-shadow: 0 4px 12px rgba(44, 62, 80, 0.15);
        }
        
        .score-display {
            font-size: 3.5rem;
            font-weight: 700;
            color: var(--accent-gold);
            margin: 1rem 0;
            animation: countUp 1s ease-out;
        }
        
        @keyframes countUp {
            from { opacity: 0; transform: scale(0.5); }
            to { opacity: 1; transform: scale(1); }
        }
        
        .badge-nivel {
            padding: 0.5rem 1.25rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            background: var(--accent-gold);
            color: white;
        }
        
        .stat-box {
            background: white;
            border: 1px solid var(--border-light);
            border-radius: 6px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .stat-box:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-dark);
            display: block;
        }
        
        .stat-label {
            font-size: 0.85rem;
            color: #6B7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .habilidad-card {
            border-radius: 6px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border-left: 4px solid;
            background: white;
            border: 1px solid var(--border-light);
            transition: all 0.3s ease;
        }
        
        .habilidad-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transform: translateX(4px);
        }
        
        .habilidad-card.success {
            border-left-color: #10B981;
        }
        
        .habilidad-card.info {
            border-left-color: #3B82F6;
        }
        
        .habilidad-card.warning {
            border-left-color: #F39C12;
        }
        
        .habilidad-card.danger {
            border-left-color: #EF4444;
        }
        
        .chart-container {
            position: relative;
            height: 350px;
            margin: 2rem 0;
        }
        
        .insight-box {
            background: var(--bg-light);
            border-left: 4px solid var(--accent-gold);
            padding: 1.5rem;
            border-radius: 4px;
            margin: 1rem 0;
        }
        
        .strength-box {
            background: #ECFDF5;
            border-left: 4px solid #10B981;
            padding: 1.5rem;
            border-radius: 4px;
        }
        
        .improvement-box {
            background: #FEF3C7;
            border-left: 4px solid #F39C12;
            padding: 1.5rem;
            border-radius: 4px;
        }
        
        .btn-action {
            border-radius: 4px;
            padding: 12px 28px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }
        
        .btn-primary-custom {
            background: var(--accent-gold);
            border: none;
            color: white;
        }
        
        .btn-primary-custom:hover {
            background: #E67E22;
            transform: translateY(-2px);
            color: white;
        }
        
        .btn-outline-custom {
            background: white;
            border: 2px solid var(--primary-dark);
            color: var(--primary-dark);
        }
        
        .btn-outline-custom:hover {
            background: var(--primary-dark);
            color: white;
        }
        
        .section-header {
            margin: 3rem 0 1.5rem;
        }
        
        .section-header h4 {
            color: var(--primary-dark);
            font-weight: 600;
            position: relative;
            padding-bottom: 0.75rem;
        }
        
        .section-header h4::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background: var(--accent-gold);
        }
        
        .recommendation-item {
            padding: 1rem;
            background: white;
            border: 1px solid var(--border-light);
            border-radius: 4px;
            margin-bottom: 0.75rem;
        }
        
        .recommendation-item:hover {
            border-color: var(--accent-gold);
        }
        
        @media print {
            .navbar-custom, .action-buttons { display: none; }
            .results-container { box-shadow: none; }
        }
        
        /* ===== RESPONSIVE MOBILE ===== */
        @media (max-width: 767.98px) {
            .navbar-custom {
                padding: 0.75rem 0;
            }
            .navbar-custom .container {
                flex-wrap: wrap;
                gap: 0.5rem;
            }
            .results-container {
                padding: 1.25rem;
                margin: 1rem 0;
            }
            .results-container h2 {
                font-size: 1.3rem;
            }
            .header-section .row {
                flex-direction: column;
            }
            .score-card {
                padding: 1.5rem;
                margin-top: 1rem;
            }
            .score-display {
                font-size: 2.5rem;
            }
            .stat-box {
                padding: 1rem;
                margin-bottom: 0.5rem;
            }
            .stat-number {
                font-size: 1.5rem;
            }
            .chart-container {
                height: 260px;
            }
            .habilidad-card {
                padding: 1rem;
            }
            .habilidad-card .row .col-md-4 {
                text-align: left !important;
                margin-top: 0.5rem;
            }
            .habilidad-card .h2 {
                font-size: 1.5rem;
            }
            .btn-action {
                width: 100%;
                margin-bottom: 0.5rem;
                margin-left: 0 !important;
            }
            .section-header {
                margin: 2rem 0 1rem;
            }
            .strength-box, .improvement-box {
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar-custom">
        <div class="container">
            <a class="text-white text-decoration-none" href="<?php echo BASE_URL; ?>">
                <i class="bi bi-house"></i> Inicio
            </a>
            <span class="text-white">
                <i class="bi bi-file-earmark-bar-graph"></i> Reporte de Evaluación
            </span>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="results-container">
            <!-- Header -->
            <div class="header-section">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2 class="mb-3">
                            <i class="bi bi-clipboard-data text-warning me-2"></i>
                            Reporte de Habilidades Directivas
                        </h2>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-2 text-muted">
                                    <strong>Evaluado:</strong> <?php echo htmlspecialchars($evaluacion['nombre']); ?>
                                </p>
                                <p class="mb-2 text-muted">
                                    <strong>Email:</strong> <?php echo htmlspecialchars($evaluacion['email']); ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2 text-muted">
                                    <strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($evaluacion['fecha'])); ?>
                                </p>
                                <p class="mb-2 text-muted">
                                    <strong>Hora:</strong> <?php echo date('H:i', strtotime($evaluacion['fecha'])); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="score-card">
                            <div class="small mb-2">PUNTUACIÓN GENERAL</div>
                            <div class="score-display" data-score="<?php echo $evaluacion['promedio_general']; ?>">
                                <?php echo $evaluacion['promedio_general']; ?>
                            </div>
                            <div class="badge-nivel">
                                <?php echo $evaluacion['nivel_general']; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Dashboard -->
            <div class="row mb-4">
                <div class="col-6 col-md-3">
                    <div class="stat-box">
                        <span class="stat-number" data-count="<?php echo count($evaluacion['resultados']); ?>">0</span>
                        <span class="stat-label">Áreas Evaluadas</span>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-box">
                        <span class="stat-number" data-count="<?php echo count($fortalezas); ?>">0</span>
                        <span class="stat-label">Fortalezas</span>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-box">
                        <span class="stat-number" data-count="<?php echo count($areas_mejora); ?>">0</span>
                        <span class="stat-label">Áreas de Mejora</span>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-box">
                        <span class="stat-number" data-count="<?php echo $evaluacion['total_preguntas']; ?>">0</span>
                        <span class="stat-label">Criterios Evaluados</span>
                    </div>
                </div>
            </div>

            <!-- Análisis Rápido -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="strength-box">
                        <h6 class="mb-3">
                            <i class="bi bi-star-fill text-success me-2"></i>
                            <strong>Fortalezas Identificadas</strong>
                        </h6>
                        <?php if (!empty($fortalezas)): ?>
                            <ul class="mb-0">
                                <?php foreach ($fortalezas as $habilidad): ?>
                                <li class="mb-1">
                                    <strong><?php echo $habilidad['nombre']; ?></strong>
                                    <span class="badge bg-success ms-2"><?php echo $habilidad['promedio']; ?></span>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="mb-0 text-muted">Continúe trabajando en todas sus habilidades para alcanzar el nivel de fortaleza.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="improvement-box">
                        <h6 class="mb-3">
                            <i class="bi bi-graph-up-arrow text-warning me-2"></i>
                            <strong>Oportunidades de Desarrollo</strong>
                        </h6>
                        <?php if (!empty($areas_mejora)): ?>
                            <ul class="mb-0">
                                <?php foreach ($areas_mejora as $habilidad): ?>
                                <li class="mb-1">
                                    <strong><?php echo $habilidad['nombre']; ?></strong>
                                    <span class="badge bg-warning text-dark ms-2"><?php echo $habilidad['promedio']; ?></span>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="mb-0 text-muted">Excelente desempeño. Mantiene un buen nivel en todas las áreas evaluadas.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Gráfico de Perfil -->
            <div class="section-header">
                <h4><i class="bi bi-bar-chart-line me-2"></i>Perfil de Competencias</h4>
            </div>
            <div class="chart-container">
                <canvas id="radarChart"></canvas>
            </div>

            <!-- Detalle por Habilidad -->
            <div class="section-header">
                <h4><i class="bi bi-list-check me-2"></i>Análisis Detallado</h4>
            </div>
            <?php foreach ($evaluacion['resultados'] as $resultado): ?>
            <div class="habilidad-card <?php echo $resultado['clase']; ?>">
                <div class="row align-items-center">
                    <div class="col-md-5">
                        <h6 class="mb-2"><?php echo $resultado['nombre']; ?></h6>
                        <p class="small text-muted mb-0"><?php echo $resultado['descripcion']; ?></p>
                    </div>
                    <div class="col-md-2 text-center">
                        <div class="h2 fw-bold mb-0"><?php echo $resultado['promedio']; ?></div>
                        <small class="text-muted">de 5.0</small>
                    </div>
                    <div class="col-md-5">
                        <span class="badge bg-<?php echo $resultado['clase']; ?> mb-2">
                            <?php echo $resultado['nivel']; ?>
                        </span>
                        <p class="small mb-0"><?php echo $resultado['mensaje']; ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>

            <!-- Recomendaciones -->
            <div class="section-header">
                <h4><i class="bi bi-lightbulb me-2"></i>Plan de Acción Recomendado</h4>
            </div>
            
            <div class="insight-box">
                <?php if ($evaluacion['promedio_general'] >= 4.0): ?>
                <h6 class="mb-3"><i class="bi bi-trophy-fill text-warning me-2"></i>Nivel Sobresaliente</h6>
                <p>Su perfil de habilidades directivas demuestra un alto nivel de competencia. Le recomendamos:</p>
                <div class="recommendation-item">
                    <i class="bi bi-check2 text-success me-2"></i>
                    <strong>Mentoring:</strong> Compartir sus conocimientos como mentor de profesionales en desarrollo
                    </div>
                <div class="recommendation-item">
                    <i class="bi bi-check2 text-success me-2"></i>
                    <strong>Liderazgo Estratégico:</strong> Asumir roles de mayor responsabilidad y alcance organizacional
                </div>
                <div class="recommendation-item">
                    <i class="bi bi-check2 text-success me-2"></i>
                    <strong>Innovación:</strong> Liderar proyectos de transformación y mejora continua
                </div>
                <?php elseif ($evaluacion['promedio_general'] >= 3.0): ?>
                <h6 class="mb-3"><i class="bi bi-award-fill text-primary me-2"></i>Nivel Competente</h6>
                <p>Cuenta con una base sólida de habilidades directivas. Para continuar su desarrollo:</p>
                <div class="recommendation-item">
                    <i class="bi bi-check2 text-primary me-2"></i>
                    <strong>Capacitación Focalizada:</strong> Concentrate en las áreas con menor puntuación
                </div>
                <div class="recommendation-item">
                    <i class="bi bi-check2 text-primary me-2"></i>
                    <strong>Feedback 360°:</strong> Solicite retroalimentación regular de su equipo y superiores
                </div>
                <div class="recommendation-item">
                    <i class="bi bi-check2 text-primary me-2"></i>
                    <strong>Proyectos Desafiantes:</strong> Busque oportunidades que le permitan aplicar nuevas habilidades
                </div>
                <?php else: ?>
                <h6 class="mb-3"><i class="bi bi-gear-fill text-warning me-2"></i>Oportunidad de Desarrollo</h6>
                <p>Esta evaluación identifica áreas clave de mejora. Plan de acción sugerido:</p>
                <div class="recommendation-item">
                    <i class="bi bi-check2 text-warning me-2"></i>
                    <strong>Formación Estructurada:</strong> Participar en programas de desarrollo directivo
                </div>
                <div class="recommendation-item">
                    <i class="bi bi-check2 text-warning me-2"></i>
                    <strong>Coaching Ejecutivo:</strong> Considere trabajar con un coach especializado
                </div>
                <div class="recommendation-item">
                    <i class="bi bi-check2 text-warning me-2"></i>
                    <strong>Práctica Deliberada:</strong> Establecer metas específicas y medibles para cada competencia
                </div>
                <?php endif; ?>
            </div>

            <!-- Botones de Acción -->
            <div class="action-buttons text-center mt-5 pt-4" style="border-top: 1px solid var(--border-light);">
                <button onclick="window.print()" class="btn btn-primary-custom btn-action me-2">
                    <i class="bi bi-printer me-2"></i>Imprimir Reporte
                </button>
                <button onclick="alert('Función PDF próximamente disponible')" class="btn btn-outline-custom btn-action me-2" disabled>
                    <i class="bi bi-file-pdf me-2"></i>Descargar PDF
                </button>
                <a href="<?php echo BASE_URL; ?>" class="btn btn-outline-custom btn-action">
                    <i class="bi bi-house me-2"></i>Nueva Evaluación
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Animación de contadores
        document.addEventListener('DOMContentLoaded', function() {
            const counters = document.querySelectorAll('[data-count]');
            
            counters.forEach(counter => {
                const target = parseInt(counter.getAttribute('data-count'));
                let current = 0;
                const increment = target / 40;
                
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        counter.textContent = target;
                        clearInterval(timer);
                    } else {
                        counter.textContent = Math.floor(current);
                    }
                }, 30);
            });
        });

        // Gráfico de radar
        const habilidades = <?php echo json_encode(array_column($evaluacion['resultados'], 'nombre')); ?>;
        const promedios = <?php echo json_encode(array_column($evaluacion['resultados'], 'promedio')); ?>;

        const ctx = document.getElementById('radarChart').getContext('2d');
        const radarChart = new Chart(ctx, {
            type: 'radar',
            data: {
                labels: habilidades,
                datasets: [{
                    label: 'Nivel Actual',
                    data: promedios,
                    backgroundColor: 'rgba(243, 156, 18, 0.15)',
                    borderColor: '#F39C12',
                    borderWidth: 2,
                    pointBackgroundColor: '#F39C12',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: '#F39C12',
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    r: {
                        beginAtZero: true,
                        max: 5,
                        ticks: {
                            stepSize: 1,
                            font: {
                                family: 'Inter'
                            }
                        },
                        pointLabels: {
                            font: {
                                family: 'Inter',
                                size: 11,
                                weight: '500'
                            }
                        },
                        grid: {
                            color: '#E5E7EB'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#2C3E50',
                        titleFont: {
                            family: 'Inter',
                            size: 13
                        },
                        bodyFont: {
                            family: 'Inter',
                            size: 12
                        },
                        padding: 12,
                        cornerRadius: 4
                    }
                }
            }
        });
    </script>
</body>
</html>

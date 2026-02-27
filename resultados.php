<?php
require_once 'config.php';
session_start();

// Obtener ID de evaluación
$evaluacion_id = $_GET['id'] ?? ($_SESSION['evaluacion_id'] ?? null);

if (!$evaluacion_id) {
    header('Location: index.php');
    exit;
}

// Cargar evaluación desde archivo JSON
$archivo = DATOS_DIR . '/evaluacion_' . $evaluacion_id . '.json';

if (!file_exists($archivo)) {
    header('Location: index.php');
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
    <style>
        body {
            background: #f5f7fa;
        }
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .results-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            padding: 2rem;
            margin: 2rem 0;
        }
        .score-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            margin-bottom: 2rem;
        }
        .score-display {
            font-size: 4rem;
            font-weight: bold;
            margin: 1rem 0;
        }
        .habilidad-card {
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border-left: 5px solid;
            background: #f8f9fa;
        }
        .habilidad-card.success {
            border-left-color: #28a745;
        }
        .habilidad-card.info {
            border-left-color: #17a2b8;
        }
        .habilidad-card.warning {
            border-left-color: #ffc107;
        }
        .habilidad-card.danger {
            border-left-color: #dc3545;
        }
        .badge-nivel {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
        }
        .action-buttons {
            margin-top: 2rem;
            text-align: center;
        }
        .btn-download {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: bold;
        }
        .chart-container {
            position: relative;
            height: 400px;
            margin: 2rem 0;
        }
        .recommendation-box {
            background: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 1rem;
            border-radius: 5px;
            margin: 1rem 0;
        }
        .strength-box {
            background: #e8f5e9;
            border-left: 4px solid #4CAF50;
            padding: 1rem;
            border-radius: 5px;
            margin: 1rem 0;
        }
        .improvement-box {
            background: #fff3e0;
            border-left: 4px solid #FF9800;
            padding: 1rem;
            border-radius: 5px;
            margin: 1rem 0;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-house"></i> Inicio
            </a>
            <span class="navbar-text text-white">
                <i class="bi bi-trophy"></i> Resultados de Evaluación
            </span>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="results-container">
            <!-- Header con información del evaluado -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <h2><i class="bi bi-person-badge"></i> Resultados de Evaluación</h2>
                    <p class="text-muted mb-1">
                        <strong>Nombre:</strong> <?php echo htmlspecialchars($evaluacion['nombre']); ?>
                    </p>
                    <p class="text-muted mb-1">
                        <strong>Email:</strong> <?php echo htmlspecialchars($evaluacion['email']); ?>
                    </p>
                    <p class="text-muted">
                        <strong>Fecha:</strong> <?php echo date('d/m/Y H:i', strtotime($evaluacion['fecha'])); ?>
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="score-card">
                        <div>Puntuación General</div>
                        <div class="score-display"><?php echo $evaluacion['promedio_general']; ?></div>
                        <div class="badge badge-nivel bg-light text-dark">
                            <?php echo $evaluacion['nivel_general']; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráfico de radar -->
            <div class="mb-4">
                <h4 class="mb-3"><i class="bi bi-graph-up"></i> Perfil de Habilidades</h4>
                <div class="chart-container">
                    <canvas id="radarChart"></canvas>
                </div>
            </div>

            <!-- Análisis General -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="strength-box">
                        <h5><i class="bi bi-star-fill text-success"></i> Tus Fortalezas</h5>
                        <?php if (!empty($fortalezas)): ?>
                            <ul class="mb-0">
                                <?php foreach ($fortalezas as $habilidad): ?>
                                <li><strong><?php echo $habilidad['nombre']; ?></strong> (<?php echo $habilidad['promedio']; ?>)</li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="mb-0">Continúa trabajando en todas tus habilidades.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="improvement-box">
                        <h5><i class="bi bi-arrow-up-circle text-warning"></i> Áreas de Mejora</h5>
                        <?php if (!empty($areas_mejora)): ?>
                            <ul class="mb-0">
                                <?php foreach ($areas_mejora as $habilidad): ?>
                                <li><strong><?php echo $habilidad['nombre']; ?></strong> (<?php echo $habilidad['promedio']; ?>)</li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="mb-0">¡Excelente! Mantienes un buen nivel en todas las áreas.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Detalle por habilidad -->
            <h4 class="mb-3"><i class="bi bi-list-check"></i> Detalle por Habilidad</h4>
            <?php foreach ($evaluacion['resultados'] as $resultado): ?>
            <div class="habilidad-card <?php echo $resultado['clase']; ?>">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-2"><?php echo $resultado['nombre']; ?></h5>
                        <p class="small text-muted mb-0"><?php echo $resultado['descripcion']; ?></p>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="display-6 fw-bold"><?php echo $resultado['promedio']; ?></div>
                        <small class="text-muted">de 5.0</small>
                    </div>
                    <div class="col-md-3">
                        <span class="badge badge-nivel bg-<?php echo $resultado['clase']; ?> w-100">
                            <?php echo $resultado['nivel']; ?>
                        </span>
                        <p class="small mt-2 mb-0"><?php echo $resultado['mensaje']; ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>

            <!-- Recomendaciones -->
            <div class="mt-4">
                <h4 class="mb-3"><i class="bi bi-lightbulb"></i> Recomendaciones Personalizadas</h4>
                
                <?php if ($evaluacion['promedio_general'] >= 4.0): ?>
                <div class="recommendation-box">
                    <h6><i class="bi bi-check-circle-fill text-success"></i> ¡Excelente trabajo!</h6>
                    <p class="mb-0">
                        Tu perfil de habilidades directivas es sobresaliente. Te recomendamos:
                    </p>
                    <ul class="mt-2 mb-0">
                        <li>Compartir tus conocimientos mediante mentoring</li>
                        <li>Buscar oportunidades de liderazgo más desafiantes</li>
                        <li>Mantener actualización continua en tus áreas fuertes</li>
                    </ul>
                </div>
                <?php elseif ($evaluacion['promedio_general'] >= 3.0): ?>
                <div class="recommendation-box">
                    <h6><i class="bi bi-info-circle-fill text-info"></i> Buen nivel general</h6>
                    <p class="mb-0">
                        Tienes una base sólida de habilidades directivas. Para mejorar:
                    </p>
                    <ul class="mt-2 mb-0">
                        <li>Enfócate en las áreas con menor puntuación</li>
                        <li>Participa en cursos de desarrollo profesional</li>
                        <li>Busca feedback constante de tu equipo</li>
                        <li>Establece metas específicas de mejora</li>
                    </ul>
                </div>
                <?php else: ?>
                <div class="recommendation-box">
                    <h6><i class="bi bi-exclamation-triangle-fill text-warning"></i> Oportunidades de desarrollo</h6>
                    <p class="mb-0">
                        Hay varias áreas donde puedes crecer. Te recomendamos:
                    </p>
                    <ul class="mt-2 mb-0">
                        <li>Invertir en capacitación específica</li>
                        <li>Buscar un mentor experimentado</li>
                        <li>Practicar habilidades en proyectos pequeños</li>
                        <li>Leer sobre liderazgo y gestión</li>
                        <li>Solicitar retroalimentación frecuente</li>
                    </ul>
                </div>
                <?php endif; ?>
            </div>

            <!-- Botones de acción -->
            <div class="action-buttons">
                <button onclick="window.print()" class="btn btn-primary btn-download me-2">
                    <i class="bi bi-printer"></i> Imprimir Resultados
                </button>
                <button onclick="descargarPDF()" class="btn btn-success btn-download me-2" disabled title="Función próximamente">
                    <i class="bi bi-file-earmark-pdf"></i> Descargar PDF
                </button>
                <a href="index.php" class="btn btn-outline-primary btn-download">
                    <i class="bi bi-house"></i> Volver al Inicio
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Datos para el gráfico de radar
        const habilidades = <?php echo json_encode(array_column($evaluacion['resultados'], 'nombre')); ?>;
        const promedios = <?php echo json_encode(array_column($evaluacion['resultados'], 'promedio')); ?>;

        // Crear gráfico de radar
        const ctx = document.getElementById('radarChart').getContext('2d');
        const radarChart = new Chart(ctx, {
            type: 'radar',
            data: {
                labels: habilidades,
                datasets: [{
                    label: 'Tu Evaluación',
                    data: promedios,
                    backgroundColor: 'rgba(102, 126, 234, 0.2)',
                    borderColor: 'rgba(102, 126, 234, 1)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgba(102, 126, 234, 1)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgba(102, 126, 234, 1)'
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
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Función para descargar PDF (placeholder)
        function descargarPDF() {
            alert('La función de descarga en PDF estará disponible próximamente.');
        }

        // Estilo de impresión
        window.onbeforeprint = function() {
            document.querySelector('.navbar').style.display = 'none';
            document.querySelector('.action-buttons').style.display = 'none';
        };

        window.onafterprint = function() {
            document.querySelector('.navbar').style.display = 'block';
            document.querySelector('.action-buttons').style.display = 'block';
        };
    </script>
</body>
</html>

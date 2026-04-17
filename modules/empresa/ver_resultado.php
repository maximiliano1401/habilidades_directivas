<?php
require_once __DIR__ . '/../../config/config.php';
iniciarSesionSegura();
requerirEmpresa();

$empresa_id = obtenerEmpresaActual();
$cuestionario_id = intval($_GET['id'] ?? 0);

if (!$cuestionario_id) {
    header('Location: dashboard.php');
    exit;
}

$pdo = obtenerConexion();

// Verificar que el cuestionario pertenece a la empresa
$stmt = $pdo->prepare("
    SELECT 
        c.*,
        u.nombre AS usuario_nombre,
        u.email AS usuario_email,
        u.puesto AS usuario_puesto,
        u.departamento AS usuario_departamento,
        e.nombre AS empresa_nombre
    FROM cuestionarios c
    INNER JOIN usuarios u ON c.usuario_id = u.id
    INNER JOIN empresas e ON c.empresa_id = e.id
    WHERE c.id = ? AND c.empresa_id = ? AND c.estado = 'completado'
");
$stmt->execute([$cuestionario_id, $empresa_id]);
$cuestionario = $stmt->fetch();

if (!$cuestionario) {
    header('Location: ' . BASE_URL . 'modules/empresa/dashboard.php');
    exit;
}

// Obtener resultados por habilidad
$stmt = $pdo->prepare("
    SELECT * FROM resultados_habilidades
    WHERE cuestionario_id = ?
    ORDER BY promedio DESC
");
$stmt->execute([$cuestionario_id]);
$resultados = $stmt->fetchAll();

// Agrupar resultados por ID para procesamiento
$resultados_array = [];
foreach ($resultados as $r) {
    $resultados_array[$r['habilidad_id']] = $r;
}

// Identificar fortalezas y áreas de mejora
$fortalezas = array_filter($resultados, function($r) {
    return $r['promedio'] >= 4.0;
});

$areas_mejora = array_filter($resultados, function($r) {
    return $r['promedio'] < 3.5;
});
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado - Cuestionario #<?php echo $cuestionario_id; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        :root {
            --primary-dark: #2C3E50;
            --accent-gold: #F39C12;
        }
        
        * { font-family: 'Inter', sans-serif; }
        
        body { background: #F8F9FA; }
        
        .navbar-custom {
            background: var(--primary-dark);
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(44, 62, 80, 0.1);
        }
        
        .results-container {
            background: white;
            border-radius: 8px;
            padding: 2.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin: 2rem 0;
        }
        
        .score-card {
            background: var(--primary-dark);
            color: white;
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
        }
        
        .score-display {
            font-size: 3.5rem;
            font-weight: 700;
            color: var(--accent-gold);
        }
        
        .habilidad-card {
            border-radius: 6px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border-left: 4px solid;
            background: white;
            border: 1px solid #E5E7EB;
        }
        
        .habilidad-card.success { border-left-color: #10B981; }
        .habilidad-card.info { border-left-color: #3B82F6; }
        .habilidad-card.warning { border-left-color: #F39C12; }
        .habilidad-card.danger { border-left-color: #EF4444; }
        
        .chart-container {
            position: relative;
            height: 400px;
        }
        
        /* ===== RESPONSIVE MOBILE ===== */
        @media (max-width: 767.98px) {
            .navbar-custom {
                padding: 0.75rem 0;
            }
            .results-container {
                padding: 1.25rem;
                margin: 1rem 0;
            }
            .results-container h2 {
                font-size: 1.3rem;
            }
            .score-card {
                padding: 1.5rem;
                margin-top: 1rem;
            }
            .score-display {
                font-size: 2.5rem;
            }
            .chart-container {
                height: 280px;
            }
            .habilidad-card {
                padding: 1rem;
            }
            .habilidad-card .d-flex {
                flex-direction: column;
                gap: 0.5rem;
            }
            .habilidad-card .text-end {
                text-align: left !important;
            }
            .habilidad-card .row .col-md-4 {
                text-align: left !important;
                margin-top: 0.5rem;
            }
            .habilidad-card .h2 {
                font-size: 1.5rem;
            }
            .habilidad-card .h3 {
                font-size: 1.3rem;
            }
            .row.mt-5 .col-12.text-center .btn {
                display: block;
                width: 100%;
                margin-bottom: 0.5rem;
                margin-left: 0 !important;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar-custom">
        <div class="container">
            <a class="text-white text-decoration-none" href="dashboard.php">
                <i class="bi bi-arrow-left"></i> Volver al Dashboard
            </a>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="results-container">
            <!-- Header -->
            <div class="row mb-4 pb-4 border-bottom">
                <div class="col-md-8">
                    <h2>
                        <i class="bi bi-clipboard-data text-warning"></i>
                        Reporte de Evaluación
                    </h2>
                    <div class="mt-3">
                        <p class="mb-1"><strong>Usuario:</strong> <?php echo htmlspecialchars($cuestionario['usuario_nombre']); ?></p>
                        <p class="mb-1"><strong>Email:</strong> <?php echo htmlspecialchars($cuestionario['usuario_email']); ?></p>
                        <p class="mb-1"><strong>Puesto:</strong> <?php echo htmlspecialchars($cuestionario['usuario_puesto'] ?? 'No especificado'); ?></p>
                        <p class="mb-1"><strong>Departamento:</strong> <?php echo htmlspecialchars($cuestionario['usuario_departamento'] ?? 'No especificado'); ?></p>
                        <p class="mb-1"><strong>Fecha:</strong> <?php echo date('d/m/Y H:i', strtotime($cuestionario['fecha_completado'])); ?></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="score-card">
                        <p class="mb-2">Calificación General</p>
                        <div class="score-display"><?php echo number_format($cuestionario['promedio_general'], 2); ?></div>
                        <p class="mb-0 mt-2">
                            <span class="badge bg-warning"><?php echo htmlspecialchars($cuestionario['nivel_general']); ?></span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Gráfico Radar -->
            <div class="row mb-5">
                <div class="col-12">
                    <h4 class="mb-4">Perfil de Habilidades</h4>
                    <div class="chart-container">
                        <canvas id="radarChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Fortalezas -->
            <?php if (count($fortalezas) > 0): ?>
            <div class="row mb-4">
                <div class="col-12">
                    <h4 class="mb-3">
                        <i class="bi bi-trophy-fill text-success"></i>
                        Fortalezas (≥ 4.0)
                    </h4>
                    <?php foreach ($fortalezas as $f): ?>
                    <div class="habilidad-card success">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1"><?php echo htmlspecialchars($f['habilidad_nombre']); ?></h5>
                                <p class="text-muted small mb-0"><?php echo htmlspecialchars($f['habilidad_descripcion']); ?></p>
                            </div>
                            <div class="text-end">
                                <div class="h3 mb-0 text-success"><?php echo number_format($f['promedio'], 2); ?></div>
                                <small class="text-muted"><?php echo $f['nivel']; ?></small>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Áreas de Mejora -->
            <?php if (count($areas_mejora) > 0): ?>
            <div class="row mb-4">
                <div class="col-12">
                    <h4 class="mb-3">
                        <i class="bi bi-exclamation-triangle-fill text-warning"></i>
                        Áreas de Oportunidad (< 3.5)
                    </h4>
                    <?php foreach ($areas_mejora as $am): ?>
                    <div class="habilidad-card warning">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1"><?php echo htmlspecialchars($am['habilidad_nombre']); ?></h5>
                                <p class="text-muted small mb-0"><?php echo htmlspecialchars($am['mensaje']); ?></p>
                            </div>
                            <div class="text-end">
                                <div class="h3 mb-0 text-warning"><?php echo number_format($am['promedio'], 2); ?></div>
                                <small class="text-muted"><?php echo $am['nivel']; ?></small>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Todas las Habilidades -->
            <div class="row">
                <div class="col-12">
                    <h4 class="mb-3">Detalle por Habilidad</h4>
                    <?php foreach ($resultados as $resultado): ?>
                    <div class="habilidad-card <?php echo htmlspecialchars($resultado['clase']); ?>">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5><?php echo htmlspecialchars($resultado['habilidad_nombre']); ?></h5>
                                <p class="text-muted small mb-2"><?php echo htmlspecialchars($resultado['habilidad_descripcion']); ?></p>
                                <p class="mb-0"><em><?php echo htmlspecialchars($resultado['mensaje']); ?></em></p>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="h2 mb-0"><?php echo number_format($resultado['promedio'], 2); ?></div>
                                <span class="badge bg-<?php echo $resultado['clase']; ?>">
                                    <?php echo htmlspecialchars($resultado['nivel']); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Acciones -->
            <div class="row mt-5">
                <div class="col-12 text-center">
                    <a href="dashboard.php" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left"></i> Volver al Dashboard
                    </a>
                    <a href="exportar_pdf.php?id=<?php echo $cuestionario_id; ?>" class="btn btn-danger ms-2">
                        <i class="bi bi-file-earmark-pdf"></i> Descargar PDF
                    </a>
                    <button class="btn btn-warning ms-2" onclick="window.print()">
                        <i class="bi bi-printer"></i> Imprimir Reporte
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Gráfico Radar
        const ctx = document.getElementById('radarChart');
        const resultadosData = <?php echo json_encode($resultados); ?>;
        
        const labels = resultadosData.map(r => r.habilidad_nombre);
        const data = resultadosData.map(r => parseFloat(r.promedio));
        
        new Chart(ctx, {
            type: 'radar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Promedio',
                    data: data,
                    fill: true,
                    backgroundColor: 'rgba(243, 156, 18, 0.2)',
                    borderColor: 'rgb(243, 156, 18)',
                    pointBackgroundColor: 'rgb(243, 156, 18)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgb(243, 156, 18)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
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
    </script>
</body>
</html>

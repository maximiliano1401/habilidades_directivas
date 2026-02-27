<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo TITULO_SISTEMA; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .hero-section {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 3rem;
            margin: 2rem 0;
        }
        .feature-box {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1rem 0;
            transition: transform 0.3s;
        }
        .feature-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .feature-icon {
            font-size: 2.5rem;
            color: #667eea;
            margin-bottom: 1rem;
        }
        .btn-comenzar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 15px 50px;
            font-size: 1.2rem;
            font-weight: bold;
            border-radius: 50px;
            transition: transform 0.3s;
        }
        .btn-comenzar:hover {
            transform: scale(1.05);
        }
        h1 {
            color: #667eea;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="hero-section">
            <div class="text-center mb-5">
                <h1 class="display-4 mb-4">
                    <i class="bi bi-person-badge"></i>
                    Habilidades Directivas
                </h1>
                <p class="lead text-muted">
                    Descubre y desarrolla tus competencias gerenciales y de liderazgo
                </p>
            </div>

            <div class="row mb-5">
                <div class="col-md-12">
                    <h3 class="mb-4">¿Qué son las Habilidades Directivas?</h3>
                    <p class="text-justify">
                        Las habilidades directivas son el conjunto de capacidades y competencias que permiten a una persona 
                        desempeñarse efectivamente en roles de gestión, liderazgo y toma de decisiones. Estas habilidades 
                        son fundamentales para el éxito profesional y el desarrollo de equipos de alto rendimiento.
                    </p>
                </div>
            </div>

            <div class="row">
                <?php 
                $features = [
                    ['icon' => 'bi-clipboard-check', 'title' => 'Evaluación Completa', 'desc' => 'Analiza 11 áreas clave de habilidades directivas'],
                    ['icon' => 'bi-graph-up', 'title' => 'Resultados Detallados', 'desc' => 'Obtén un análisis personalizado de tus fortalezas'],
                    ['icon' => 'bi-lightbulb', 'title' => 'Recomendaciones', 'desc' => 'Identifica áreas de mejora y desarrollo'],
                    ['icon' => 'bi-file-earmark-pdf', 'title' => 'Reporte en PDF', 'desc' => 'Descarga tu evaluación completa'],
                ];
                
                foreach ($features as $feature): ?>
                <div class="col-md-6 col-lg-3">
                    <div class="feature-box text-center">
                        <i class="bi <?php echo $feature['icon']; ?> feature-icon"></i>
                        <h5><?php echo $feature['title']; ?></h5>
                        <p class="small text-muted"><?php echo $feature['desc']; ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="row mt-5">
                <div class="col-md-12">
                    <h4 class="mb-3">Áreas de Evaluación:</h4>
                    <div class="row">
                        <?php foreach ($habilidades as $habilidad): ?>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h6><i class="bi bi-check-circle-fill text-success"></i> <?php echo $habilidad['nombre']; ?></h6>
                                <p class="small text-muted ms-4"><?php echo $habilidad['descripcion']; ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="text-center mt-5">
                <p class="mb-4">
                    <strong>Tiempo estimado:</strong> 10-15 minutos | 
                    <strong>Preguntas:</strong> <?php 
                        $total_preguntas = 0;
                        foreach ($habilidades as $hab) {
                            $total_preguntas += count($hab['preguntas']);
                        }
                        echo $total_preguntas;
                    ?>
                </p>
                <a href="formulario.php" class="btn btn-primary btn-comenzar">
                    Comenzar Evaluación <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

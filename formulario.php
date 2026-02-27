<?php 
require_once 'config.php';
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Evaluación - <?php echo TITULO_SISTEMA; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: #f5f7fa;
        }
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .form-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            padding: 2rem;
            margin: 2rem 0;
        }
        .progress-section {
            position: sticky;
            top: 20px;
        }
        .habilidad-section {
            border-left: 4px solid #667eea;
            padding-left: 1.5rem;
            margin-bottom: 2rem;
        }
        .pregunta-item {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .likert-scale {
            display: flex;
            justify-content: space-between;
            margin-top: 1rem;
        }
        .likert-option {
            flex: 1;
            text-align: center;
            margin: 0 5px;
        }
        .likert-option input[type="radio"] {
            display: none;
        }
        .likert-option label {
            display: block;
            padding: 0.75rem;
            background: white;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 0.9rem;
        }
        .likert-option input[type="radio"]:checked + label {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: #667eea;
            font-weight: bold;
        }
        .likert-option label:hover {
            border-color: #667eea;
            transform: translateY(-2px);
        }
        .section-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }
        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 40px;
            font-size: 1.1rem;
            font-weight: bold;
            border-radius: 50px;
        }
        .required-field {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
            <span class="navbar-text text-white">
                <i class="bi bi-person-badge"></i> Evaluación de Habilidades Directivas
            </span>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-lg-8">
                <div class="form-container">
                    <h2 class="mb-4">Formulario de Evaluación</h2>
                    <p class="text-muted mb-4">
                        Por favor, completa todos los campos. Evalúa cada afirmación según tu percepción 
                        con la escala del 1 al 5.
                    </p>

                    <form id="evaluacionForm" method="POST" action="procesar.php">
                        <!-- Datos Personales -->
                        <div class="section-header">
                            <h4 class="mb-0"><i class="bi bi-person-fill"></i> Datos Personales</h4>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label for="nombre" class="form-label">
                                    Nombre Completo <span class="required-field">*</span>
                                </label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">
                                    Correo Electrónico <span class="required-field">*</span>
                                </label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>

                        <!-- Preguntas por Habilidad -->
                        <div class="section-header">
                            <h4 class="mb-0"><i class="bi bi-clipboard-check"></i> Evaluación de Habilidades</h4>
                        </div>

                        <?php foreach ($habilidades as $index => $habilidad): ?>
                        <div class="habilidad-section" id="habilidad-<?php echo $habilidad['id']; ?>">
                            <h5 class="text-primary mb-3">
                                <?php echo ($index + 1) . '. ' . $habilidad['nombre']; ?>
                            </h5>
                            <p class="text-muted small mb-4"><?php echo $habilidad['descripcion']; ?></p>

                            <?php foreach ($habilidad['preguntas'] as $idx => $pregunta): ?>
                            <div class="pregunta-item">
                                <p class="mb-2">
                                    <strong><?php echo ($idx + 1); ?>.</strong> <?php echo $pregunta; ?>
                                    <span class="required-field">*</span>
                                </p>
                                
                                <div class="likert-scale">
                                    <?php foreach ($escala_likert as $valor => $etiqueta): ?>
                                    <div class="likert-option">
                                        <input 
                                            type="radio" 
                                            id="<?php echo $habilidad['id'] . '_' . $idx . '_' . $valor; ?>" 
                                            name="<?php echo $habilidad['id']; ?>[<?php echo $idx; ?>]" 
                                            value="<?php echo $valor; ?>"
                                            required
                                        >
                                        <label for="<?php echo $habilidad['id'] . '_' . $idx . '_' . $valor; ?>">
                                            <div class="fw-bold"><?php echo $valor; ?></div>
                                            <div class="small"><?php echo $etiqueta; ?></div>
                                        </label>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endforeach; ?>

                        <div class="text-center mt-5">
                            <button type="submit" class="btn btn-primary btn-submit">
                                <i class="bi bi-check-circle"></i> Enviar Evaluación
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="progress-section">
                    <div class="form-container">
                        <h5 class="mb-3"><i class="bi bi-list-check"></i> Progreso</h5>
                        <div class="progress mb-3" style="height: 25px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                 role="progressbar" 
                                 style="width: 0%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"
                                 id="progressBar">0%</div>
                        </div>
                        <p class="small text-muted" id="progressText">0 de <?php 
                            echo $total_preguntas = array_sum(array_map(function($h) { 
                                return count($h['preguntas']); 
                            }, $habilidades)); 
                        ?> preguntas respondidas</p>

                        <hr>

                        <h6 class="mb-3">Habilidades a evaluar:</h6>
                        <div class="small">
                            <?php foreach ($habilidades as $hab): ?>
                            <div class="mb-2">
                                <i class="bi bi-circle-fill text-primary" style="font-size: 0.5rem;"></i>
                                <?php echo $hab['nombre']; ?>
                                <span class="text-muted">(<?php echo count($hab['preguntas']); ?>)</span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Actualizar barra de progreso
        const form = document.getElementById('evaluacionForm');
        const totalPreguntas = <?php echo $total_preguntas; ?>;
        const progressBar = document.getElementById('progressBar');
        const progressText = document.getElementById('progressText');

        form.addEventListener('change', function(e) {
            if (e.target.type === 'radio') {
                const respondidas = form.querySelectorAll('input[type="radio"]:checked').length;
                const porcentaje = Math.round((respondidas / totalPreguntas) * 100);
                
                progressBar.style.width = porcentaje + '%';
                progressBar.textContent = porcentaje + '%';
                progressText.textContent = respondidas + ' de ' + totalPreguntas + ' preguntas respondidas';
            }
        });

        // Validación antes de enviar
        form.addEventListener('submit', function(e) {
            const respondidas = form.querySelectorAll('input[type="radio"]:checked').length;
            
            if (respondidas < totalPreguntas) {
                e.preventDefault();
                alert('Por favor, responde todas las preguntas antes de enviar la evaluación.');
                return false;
            }

            // Confirmar envío
            if (!confirm('¿Estás seguro de enviar tu evaluación? Revisa que todas las respuestas sean correctas.')) {
                e.preventDefault();
                return false;
            }
        });

        // Smooth scroll al hacer clic en una habilidad
        document.querySelectorAll('a[href^="#habilidad-"]').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });
    </script>
</body>
</html>

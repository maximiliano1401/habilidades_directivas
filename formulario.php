<?php 
require_once 'config.php';
session_start();

// Agrupar habilidades en pasos (3 habilidades por paso aproximadamente)
$pasos = [
    1 => array_slice($habilidades, 0, 3),   // Técnicas, Interpersonales, Sociales
    2 => array_slice($habilidades, 3, 3),   // Académicas, Innovación, Prácticas
    3 => array_slice($habilidades, 6, 3),   // Físicas, Pensamiento, Directivas
    4 => array_slice($habilidades, 9, 2),   // Liderazgo, Empresariales
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluación - <?php echo TITULO_SISTEMA; ?></title>
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
        }
        
        .navbar-custom {
            background: var(--primary-dark);
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(44, 62, 80, 0.1);
        }
        
        .form-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            padding: 2.5rem;
            margin: 2rem 0;
        }
        
        /* Stepper */
        .stepper-wrapper {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3rem;
            position: relative;
        }
        
        .stepper-wrapper::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--border-light);
            z-index: 0;
        }
        
        .stepper-item {
            flex: 1;
            text-align: center;
            position: relative;
            z-index: 1;
        }
        
        .stepper-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: white;
            border: 2px solid var(--border-light);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.5rem;
            font-weight: 600;
            color: #9CA3AF;
            transition: all 0.3s ease;
        }
        
        .stepper-item.active .stepper-circle {
            background: var(--accent-gold);
            border-color: var(--accent-gold);
            color: white;
        }
        
        .stepper-item.completed .stepper-circle {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
            color: white;
        }
        
        .stepper-label {
            font-size: 0.75rem;
            color: #9CA3AF;
            font-weight: 500;
        }
        
        .stepper-item.active .stepper-label {
            color: var(--accent-gold);
            font-weight: 600;
        }
        
        .stepper-item.completed .stepper-label {
            color: var(--primary-dark);
        }
        
        /* Paso del formulario */
        .paso-section {
            display: none;
            animation: fadeIn 0.4s ease-out;
        }
        
        .paso-section.active {
            display: block;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateX(20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        .habilidad-card {
            border-left: 3px solid var(--accent-gold);
            padding-left: 1.5rem;
            margin-bottom: 2.5rem;
        }
        
        .habilidad-card h5 {
            color: var(--primary-dark);
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .pregunta-item {
            background: #FAFAFA;
            border: 1px solid var(--border-light);
            border-radius: 6px;
            padding: 1.5rem;
            margin-bottom: 1.25rem;
            transition: all 0.3s ease;
        }
        
        .pregunta-item:hover {
            border-color: var(--accent-gold);
            box-shadow: 0 2px 8px rgba(243, 156, 18, 0.1);
        }
        
        .likert-scale {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 0.75rem;
            margin-top: 1rem;
        }
        
        .likert-option input[type="radio"] {
            display: none;
        }
        
        .likert-option label {
            display: block;
            padding: 0.75rem 0.5rem;
            background: white;
            border: 2px solid var(--border-light);
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 0.85rem;
            text-align: center;
        }
        
        .likert-option input[type="radio"]:checked + label {
            background: var(--accent-gold);
            color: white;
            border-color: var(--accent-gold);
            font-weight: 600;
        }
        
        .likert-option label:hover {
            border-color: var(--accent-gold);
        }
        
        .btn-nav {
            padding: 10px 30px;
            border-radius: 4px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        
        .btn-siguiente {
            background: var(--accent-gold);
            border: none;
            color: white;
        }
        
        .btn-siguiente:hover {
            background: #E67E22;
            color: white;
        }
        
        .btn-anterior {
            background: white;
            border: 2px solid var(--primary-dark);
            color: var(--primary-dark);
        }
        
        .btn-anterior:hover {
            background: var(--primary-dark);
            color: white;
        }
        
        .progress-sidebar {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            padding: 1.5rem;
            position: sticky;
            top: 20px;
        }
        
        .progress-bar-custom {
            height: 8px;
            background: var(--border-light);
            border-radius: 4px;
            overflow: hidden;
            margin: 1rem 0;
        }
        
        .progress-fill {
            height: 100%;
            background: var(--accent-gold);
            transition: width 0.4s ease;
        }
        
        .datos-personales-section {
            background: white;
            border: 1px solid var(--border-light);
            border-radius: 6px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .form-label {
            color: var(--primary-dark);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        
        .form-control {
            border: 1px solid var(--border-light);
            border-radius: 4px;
            padding: 0.75rem;
        }
        
        .form-control:focus {
            border-color: var(--accent-gold);
            box-shadow: 0 0 0 3px rgba(243, 156, 18, 0.1);
        }
        
        .required-field {
            color: #DC2626;
        }
    </style>
</head>
<body>
    <nav class="navbar-custom">
        <div class="container">
            <a class="text-white text-decoration-none" href="index.php">
                <i class="bi bi-arrow-left"></i> Volver al Inicio
            </a>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-lg-9">
                <div class="form-container">
                    <h2 class="mb-2">Evaluación de Habilidades Directivas</h2>
                    <p class="text-muted mb-4">Complete cada sección y evalúe cada afirmación según su percepción</p>

                    <!-- Stepper -->
                    <div class="stepper-wrapper">
                        <div class="stepper-item active" data-step="0">
                            <div class="stepper-circle">
                                <i class="bi bi-person"></i>
                            </div>
                            <div class="stepper-label">Datos</div>
                        </div>
                        <?php for($i = 1; $i <= count($pasos); $i++): ?>
                        <div class="stepper-item" data-step="<?php echo $i; ?>">
                            <div class="stepper-circle"><?php echo $i; ?></div>
                            <div class="stepper-label">Paso <?php echo $i; ?></div>
                        </div>
                        <?php endfor; ?>
                        <div class="stepper-item" data-step="<?php echo count($pasos) + 1; ?>">
                            <div class="stepper-circle">
                                <i class="bi bi-check-lg"></i>
                            </div>
                            <div class="stepper-label">Finalizar</div>
                        </div>
                    </div>

                    <form id="evaluacionForm" method="POST" action="procesar.php">
                        <!-- Paso 0: Datos Personales -->
                        <div class="paso-section active" data-paso="0">
                            <div class="datos-personales-section">
                                <h4 class="mb-4">
                                    <i class="bi bi-person-badge text-warning"></i>
                                    Información Personal
                                </h4>
                                
                                <div class="row">
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
                            </div>

                            <div class="text-end mt-4">
                                <button type="button" class="btn btn-siguiente btn-nav" onclick="nextStep()">
                                    Siguiente <i class="bi bi-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Pasos con preguntas -->
                        <?php foreach ($pasos as $numPaso => $habilidadesPaso): ?>
                        <div class="paso-section" data-paso="<?php echo $numPaso; ?>">
                            <h4 class="mb-4">Paso <?php echo $numPaso; ?> de <?php echo count($pasos); ?></h4>
                            
                            <?php foreach ($habilidadesPaso as $habilidad): ?>
                            <div class="habilidad-card">
                                <h5>
                                    <i class="bi bi-bookmark-fill text-warning me-2"></i>
                                    <?php echo $habilidad['nombre']; ?>
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
                                                <div class="small" style="font-size: 0.7rem;"><?php echo $etiqueta; ?></div>
                                            </label>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endforeach; ?>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-anterior btn-nav" onclick="prevStep()">
                                    <i class="bi bi-arrow-left me-2"></i> Anterior
                                </button>
                                <?php if ($numPaso < count($pasos)): ?>
                                <button type="button" class="btn btn-siguiente btn-nav" onclick="nextStep()">
                                    Siguiente <i class="bi bi-arrow-right ms-2"></i>
                                </button>
                                <?php else: ?>
                                <button type="submit" class="btn btn-siguiente btn-nav">
                                    <i class="bi bi-check-circle me-2"></i> Enviar Evaluación
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </form>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="progress-sidebar">
                    <h6 class="mb-3">
                        <i class="bi bi-list-check"></i> Progreso General
                    </h6>
                    <div class="progress-bar-custom">
                        <div class="progress-fill" id="progressFill" style="width: 0%;"></div>
                    </div>
                    <p class="small text-muted mb-4" id="progressText">
                        0 de <?php 
                            echo $total_preguntas = array_sum(array_map(function($h) { 
                                return count($h['preguntas']); 
                            }, $habilidades)); 
                        ?> preguntas
                    </p>

                    <hr>

                    <h6 class="small mb-3 text-muted">SECCIONES</h6>
                    <div class="small">
                        <?php foreach ($habilidades as $hab): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span><?php echo $hab['nombre']; ?></span>
                            <span class="text-muted"><?php echo count($hab['preguntas']); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentStep = 0;
        const totalSteps = <?php echo count($pasos) + 1; ?>;
        const totalPreguntas = <?php echo $total_preguntas; ?>;

        function updateStepper() {
            document.querySelectorAll('.stepper-item').forEach((item, index) => {
                item.classList.remove('active', 'completed');
                if (index < currentStep) {
                    item.classList.add('completed');
                } else if (index === currentStep) {
                    item.classList.add('active');
                }
            });
        }

        function updateProgress() {
            const respondidas = document.querySelectorAll('input[type="radio"]:checked').length;
            const porcentaje = Math.round((respondidas / totalPreguntas) * 100);
            
            document.getElementById('progressFill').style.width = porcentaje + '%';
            document.getElementById('progressText').textContent = 
                respondidas + ' de ' + totalPreguntas + ' preguntas';
        }

        function nextStep() {
            const currentSection = document.querySelector(`.paso-section[data-paso="${currentStep}"]`);
            
            // Validar campos del paso actual
            const inputs = currentSection.querySelectorAll('input[required], select[required]');
            let valid = true;
            
            inputs.forEach(input => {
                if (input.type === 'radio') {
                    const name = input.name;
                    if (!currentSection.querySelector(`input[name="${name}"]:checked`)) {
                        valid = false;
                    }
                } else if (!input.value) {
                    valid = false;
                    input.classList.add('is-invalid');
                }
            });
            
            if (!valid) {
                alert('Por favor, complete todos los campos antes de continuar.');
                return;
            }
            
            if (currentStep < totalSteps) {
                currentSection.classList.remove('active');
                currentStep++;
                document.querySelector(`.paso-section[data-paso="${currentStep}"]`).classList.add('active');
                updateStepper();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }

        function prevStep() {
            if (currentStep > 0) {
                document.querySelector(`.paso-section[data-paso="${currentStep}"]`).classList.remove('active');
                currentStep--;
                document.querySelector(`.paso-section[data-paso="${currentStep}"]`).classList.add('active');
                updateStepper();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }

        // Actualizar progreso al cambiar respuestas
        document.getElementById('evaluacionForm').addEventListener('change', updateProgress);

        // Validación del formulario
        document.getElementById('evaluacionForm').addEventListener('submit', function(e) {
            const respondidas = document.querySelectorAll('input[type="radio"]:checked').length;
            
            if (respondidas < totalPreguntas) {
                e.preventDefault();
                alert('Por favor, responde todas las preguntas antes de enviar.');
                return false;
            }

            if (!confirm('¿Estás seguro de enviar tu evaluación?')) {
                e.preventDefault();
                return false;
            }
        });

        // Inicializar
        updateStepper();
        updateProgress();
    </script>
</body>
</html>

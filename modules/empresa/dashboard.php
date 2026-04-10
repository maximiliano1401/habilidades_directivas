<?php
require_once __DIR__ . '/../../config/config.php';
iniciarSesionSegura();
requerirEmpresa();

$empresa_id = obtenerEmpresaActual();
$pdo = obtenerConexion();

// Obtener información de la empresa
$stmt = $pdo->prepare("SELECT * FROM empresas WHERE id = ?");
$stmt->execute([$empresa_id]);
$empresa = $stmt->fetch();

// Obtener estadísticas generales
$stmt = $pdo->prepare("CALL obtener_estadisticas_empresa(?)");
$stmt->execute([$empresa_id]);
$estadisticas = $stmt->fetch();
$stmt->closeCursor();

// Obtener cuestionarios completados
$stmt = $pdo->prepare("
    SELECT 
        c.id,
        c.fecha_inicio,
        c.fecha_completado,
        c.promedio_general,
        c.nivel_general,
        c.total_preguntas,
        u.nombre AS usuario_nombre,
        u.email AS usuario_email,
        u.puesto AS usuario_puesto,
        u.departamento AS usuario_departamento
    FROM cuestionarios c
    INNER JOIN usuarios u ON c.usuario_id = u.id
    WHERE c.empresa_id = ? AND c.estado = 'completado'
    ORDER BY c.fecha_completado DESC
    LIMIT 50
");
$stmt->execute([$empresa_id]);
$cuestionarios = $stmt->fetchAll();

// Obtener cuestionarios en progreso
$stmt = $pdo->prepare("
    SELECT 
        c.id,
        c.fecha_inicio,
        c.preguntas_respondidas,
        c.total_preguntas,
        u.nombre AS usuario_nombre,
        u.email AS usuario_email
    FROM cuestionarios c
    INNER JOIN usuarios u ON c.usuario_id = u.id
    WHERE c.empresa_id = ? AND c.estado = 'en_progreso'
    ORDER BY c.fecha_inicio DESC
    LIMIT 20
");
$stmt->execute([$empresa_id]);
$en_progreso = $stmt->fetchAll();

// Obtener promedio por habilidad
$stmt = $pdo->prepare("
    SELECT 
        habilidad_nombre,
        AVG(promedio) as promedio_habilidad,
        COUNT(*) as total_evaluaciones
    FROM resultados_habilidades rh
    INNER JOIN cuestionarios c ON rh.cuestionario_id = c.id
    WHERE c.empresa_id = ? AND c.estado = 'completado'
    GROUP BY habilidad_nombre
    ORDER BY promedio_habilidad DESC
");
$stmt->execute([$empresa_id]);
$habilidades_promedio = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo htmlspecialchars($empresa['nombre']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        :root {
            --primary-dark: #2C3E50;
            --secondary-dark: #34495E;
            --accent-gold: #F39C12;
            --bg-light: #F8F9FA;
        }
        
        * { font-family: 'Inter', sans-serif; }
        
        body {
            background: var(--bg-light);
        }
        
        .navbar-custom {
            background: var(--primary-dark);
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(44, 62, 80, 0.1);
        }
        
        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border-left: 4px solid var(--accent-gold);
            transition: all 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
        }
        
        .stat-card .stat-icon {
            font-size: 2rem;
            color: var(--accent-gold);
        }
        
        .stat-card .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-dark);
        }
        
        .stat-card .stat-label {
            color: #6B7280;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .section-card {
            background: white;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
        }
        
        .table-responsive {
            border-radius: 6px;
            overflow: hidden;
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .table thead {
            background: var(--bg-light);
        }
        
        .badge {
            padding: 0.4rem 0.8rem;
            font-weight: 500;
        }
        
        .badge-excelente {
            background: #10B981;
            color: white;
        }
        
        .badge-bueno {
            background: #3B82F6;
            color: white;
        }
        
        .badge-regular {
            background: #F39C12;
            color: white;
        }
        
        .badge-mejora {
            background: #EF4444;
            color: white;
        }
        
        .progress-thin {
            height: 8px;
            border-radius: 4px;
        }
        
        .btn-ver {
            background: var(--accent-gold);
            border: none;
            color: white;
            padding: 0.4rem 1rem;
            border-radius: 4px;
            font-size: 0.85rem;
            transition: all 0.3s;
        }
        
        .btn-ver:hover {
            background: #E67E22;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar-custom">
        <div class="container-fluid px-4">
            <div class="d-flex justify-content-between align-items-center w-100">
                <div class="text-white">
                    <?php if (!empty(LOGO_SISTEMA)): ?>
                        <img src="<?php echo BASE_URL . htmlspecialchars(LOGO_SISTEMA); ?>" alt="Logo" style="max-height: 28px; vertical-align: middle; margin-right: 8px;">
                    <?php else: ?>
                        <i class="bi bi-building me-2"></i>
                    <?php endif; ?>
                    <strong><?php echo htmlspecialchars($empresa['nombre']); ?></strong>
                </div>
                <div>
                    <span class="text-white me-3">
                        <i class="bi bi-person-circle"></i>
                        <?php echo htmlspecialchars($_SESSION['nombre']); ?>
                    </span>
                    <a href="<?php echo BASE_URL; ?>modules/auth/logout.php" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-4 py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="mb-1">
                    <i class="bi bi-speedometer2 text-warning"></i>
                    Dashboard de Gestión
                </h3>
                <p class="text-muted mb-0">Panel de control y análisis de evaluaciones</p>
            </div>
            <div>
                <button class="btn btn-outline-primary btn-sm" onclick="window.location.reload()">
                    <i class="bi bi-arrow-clockwise"></i> Actualizar
                </button>
            </div>
        </div>

        <!-- Estadísticas Generales -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <div class="stat-label">Total Usuarios</div>
                            <div class="stat-value"><?php echo $estadisticas['total_usuarios'] ?? 0; ?></div>
                        </div>
                        <div class="stat-icon">
                            <i class="bi bi-people"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <div class="stat-label">Completados</div>
                            <div class="stat-value"><?php echo $estadisticas['cuestionarios_completados'] ?? 0; ?></div>
                        </div>
                        <div class="stat-icon">
                            <i class="bi bi-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <div class="stat-label">En Progreso</div>
                            <div class="stat-value"><?php echo $estadisticas['cuestionarios_en_progreso'] ?? 0; ?></div>
                        </div>
                        <div class="stat-icon">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <div class="stat-label">Promedio General</div>
                            <div class="stat-value">
                                <?php 
                                $prom = $estadisticas['promedio_general_empresa'] ?? 0;
                                echo number_format($prom, 2); 
                                ?>
                            </div>
                        </div>
                        <div class="stat-icon">
                            <i class="bi bi-graph-up"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Gráfico de Habilidades -->
            <div class="col-lg-6 mb-4">
                <div class="section-card">
                    <h5 class="mb-4">
                        <i class="bi bi-bar-chart-fill text-warning"></i>
                        Promedio por Habilidad
                    </h5>
                    <div style="position: relative; height: 300px;">
                        <canvas id="chartHabilidades"></canvas>
                    </div>
                </div>
            </div>

            <!-- Cuestionarios en Progreso -->
            <div class="col-lg-6 mb-4">
                <div class="section-card">
                    <h5 class="mb-4">
                        <i class="bi bi-hourglass-split text-warning"></i>
                        Evaluaciones en Progreso
                    </h5>
                    <?php if (count($en_progreso) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th>Inicio</th>
                                    <th>Progreso</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($en_progreso as $ep): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($ep['usuario_nombre']); ?></strong><br>
                                        <small class="text-muted"><?php echo htmlspecialchars($ep['usuario_email']); ?></small>
                                    </td>
                                    <td>
                                        <small><?php echo date('d/m/Y H:i', strtotime($ep['fecha_inicio'])); ?></small>
                                    </td>
                                    <td>
                                        <?php 
                                        $porcentaje = ($ep['total_preguntas'] > 0) 
                                            ? round(($ep['preguntas_respondidas'] / $ep['total_preguntas']) * 100) 
                                            : 0;
                                        ?>
                                        <div class="progress progress-thin">
                                            <div class="progress-bar bg-warning" style="width: <?php echo $porcentaje; ?>%"></div>
                                        </div>
                                        <small class="text-muted"><?php echo $porcentaje; ?>%</small>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <p class="text-muted text-center py-4">No hay evaluaciones en progreso</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Cuestionarios Completados -->
        <div class="section-card">
            <h5 class="mb-4">
                <i class="bi bi-clipboard-check text-warning"></i>
                Evaluaciones Completadas
            </h5>
            
            <?php if (count($cuestionarios) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Puesto</th>
                            <th>Departamento</th>
                            <th>Fecha Completado</th>
                            <th>Promedio</th>
                            <th>Nivel</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cuestionarios as $cuest): ?>
                        <tr>
                            <td><strong>#<?php echo $cuest['id']; ?></strong></td>
                            <td>
                                <strong><?php echo htmlspecialchars($cuest['usuario_nombre']); ?></strong><br>
                                <small class="text-muted"><?php echo htmlspecialchars($cuest['usuario_email']); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($cuest['usuario_puesto'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($cuest['usuario_departamento'] ?? '-'); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($cuest['fecha_completado'])); ?></td>
                            <td>
                                <strong class="text-primary"><?php echo number_format($cuest['promedio_general'], 2); ?></strong>
                            </td>
                            <td>
                                <?php
                                $clase_badge = 'badge-bueno';
                                if ($cuest['promedio_general'] >= 4.5) $clase_badge = 'badge-excelente';
                                elseif ($cuest['promedio_general'] < 3.5 && $cuest['promedio_general'] >= 2.5) $clase_badge = 'badge-regular';
                                elseif ($cuest['promedio_general'] < 2.5) $clase_badge = 'badge-mejora';
                                ?>
                                <span class="badge <?php echo $clase_badge; ?>">
                                    <?php echo htmlspecialchars($cuest['nivel_general']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="ver_resultado.php?id=<?php echo $cuest['id']; ?>" 
                                   class="btn btn-ver btn-sm">
                                    <i class="bi bi-eye"></i> Ver
                                </a>
                                <a href="exportar_pdf.php?id=<?php echo $cuest['id']; ?>" 
                                   class="btn btn-sm btn-outline-danger ms-1" title="Descargar PDF">
                                    <i class="bi bi-file-pdf"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-inbox" style="font-size: 3rem; color: #D1D5DB;"></i>
                <p class="text-muted mt-3">Aún no hay evaluaciones completadas</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Gráfico de habilidades
        const ctxHabilidades = document.getElementById('chartHabilidades');
        
        const habilidadesData = <?php echo json_encode($habilidades_promedio); ?>;
        const labels = habilidadesData.map(h => h.habilidad_nombre);
        const data = habilidadesData.map(h => parseFloat(h.promedio_habilidad));
        
        new Chart(ctxHabilidades, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Promedio',
                    data: data,
                    backgroundColor: 'rgba(243, 156, 18, 0.6)',
                    borderColor: 'rgba(243, 156, 18, 1)',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 5,
                        ticks: {
                            stepSize: 0.5
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

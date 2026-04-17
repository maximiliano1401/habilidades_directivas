<?php
require_once __DIR__ . '/../../config/config.php';
iniciarSesionSegura();
requerirAdmin();

$tipo = $_GET['tipo'] ?? '';
$id = intval($_GET['id'] ?? 0);

if (!$id || !in_array($tipo, ['usuario', 'empresa'])) {
    $_SESSION['error'] = 'Parámetros inválidos';
    header('Location: panel.php');
    exit;
}

$pdo = obtenerConexion();

if ($tipo === 'empresa') {
    $stmt = $pdo->prepare("SELECT * FROM empresas WHERE id = ?");
    $stmt->execute([$id]);
    $entidad = $stmt->fetch();
    $titulo = 'Editar Empresa';
} else {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->execute([$id]);
    $entidad = $stmt->fetch();
    $titulo = 'Editar Usuario';

    // Obtener lista de empresas activas para el select
    $stmtEmpresas = $pdo->query("SELECT id, nombre FROM empresas WHERE activo = 1 ORDER BY nombre");
    $listaEmpresas = $stmtEmpresas->fetchAll();
}

if (!$entidad) {
    $_SESSION['error'] = ucfirst($tipo) . ' no encontrado';
    header('Location: panel.php');
    exit;
}

$mensaje = $_SESSION['mensaje'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['mensaje'], $_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?> - <?php echo TITULO_SISTEMA; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-dark: #2C3E50;
            --accent-gold: #F39C12;
            --bg-light: #F8F9FA;
            --text-dark: #2C3E50;
        }
        * { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; }
        body { background: var(--bg-light); color: var(--text-dark); }
        .admin-navbar {
            background: var(--primary-dark);
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(44, 62, 80, 0.1);
        }
        .admin-navbar .nav-link {
            color: rgba(255,255,255,0.9);
            font-weight: 500;
        }
        .form-card {
            background: white;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .btn-admin {
            background: var(--accent-gold);
            border: none;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-admin:hover {
            background: #E67E22;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(243, 156, 18, 0.3);
            color: white;
        }
        .admin-badge {
            background: var(--accent-gold);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        /* ===== RESPONSIVE MOBILE ===== */
        @media (max-width: 767.98px) {
            .admin-navbar {
                padding: 0.75rem 0;
            }
            .admin-navbar h5 {
                font-size: 1rem;
            }
            .admin-navbar .fs-3 {
                font-size: 1.3rem !important;
            }
            .admin-navbar .d-flex {
                flex-wrap: wrap;
                gap: 0.5rem;
            }
            .container[style] {
                max-width: 100% !important;
                padding: 0 1rem;
            }
            .form-card {
                padding: 1.25rem;
            }
            .form-card .d-flex.align-items-center.mb-4 {
                flex-wrap: wrap;
                gap: 0.5rem;
            }
            .form-card .ms-auto {
                margin-left: 0 !important;
            }
            .d-flex.justify-content-between {
                flex-direction: column;
                gap: 0.75rem;
            }
            .d-flex.justify-content-between .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="admin-navbar">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i class="bi bi-mortarboard-fill fs-3 me-3" style="color: var(--accent-gold);"></i>
                    <div>
                        <h5 class="text-white mb-0"><?php echo htmlspecialchars($titulo); ?></h5>
                        <small class="text-white-50"><?php echo TITULO_SISTEMA; ?></small>
                    </div>
                </div>
                <div>
                    <a href="panel.php" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Volver al Panel
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container py-4" style="max-width: 700px;">
        <?php if ($mensaje): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?php echo htmlspecialchars($mensaje); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo htmlspecialchars($error); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="form-card">
            <div class="d-flex align-items-center mb-4">
                <i class="bi <?php echo $tipo === 'empresa' ? 'bi-building' : 'bi-person'; ?> fs-3 me-3" style="color: var(--accent-gold);"></i>
                <div>
                    <h4 class="mb-0"><?php echo htmlspecialchars($entidad['nombre']); ?></h4>
                    <small class="text-muted"><?php echo htmlspecialchars($entidad['email']); ?> · ID: <?php echo $entidad['id']; ?></small>
                </div>
                <span class="ms-auto admin-badge">
                    <?php echo $entidad['activo'] ? 'ACTIVO' : 'INACTIVO'; ?>
                </span>
            </div>

            <form method="POST" action="acciones.php">
                <input type="hidden" name="accion" value="editar_<?php echo $tipo; ?>">
                <input type="hidden" name="id" value="<?php echo $entidad['id']; ?>">

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nombre *</label>
                    <input type="text" class="form-control" name="nombre" required
                           value="<?php echo htmlspecialchars($entidad['nombre']); ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Email *</label>
                    <input type="email" class="form-control" name="email" required
                           value="<?php echo htmlspecialchars($entidad['email']); ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Teléfono</label>
                    <input type="text" class="form-control" name="telefono"
                           value="<?php echo htmlspecialchars($entidad['telefono'] ?? ''); ?>"
                           placeholder="555-1234-5678">
                </div>

                <?php if ($tipo === 'empresa'): ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">RFC</label>
                        <input type="text" class="form-control" name="rfc"
                               value="<?php echo htmlspecialchars($entidad['rfc'] ?? ''); ?>"
                               placeholder="ABC123456XXX">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Dirección</label>
                        <textarea class="form-control" name="direccion" rows="2"
                                  placeholder="Dirección de la empresa"><?php echo htmlspecialchars($entidad['direccion'] ?? ''); ?></textarea>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Puesto</label>
                            <input type="text" class="form-control" name="puesto"
                                   value="<?php echo htmlspecialchars($entidad['puesto'] ?? ''); ?>"
                                   placeholder="Ej: Gerente de Ventas">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Departamento</label>
                            <input type="text" class="form-control" name="departamento"
                                   value="<?php echo htmlspecialchars($entidad['departamento'] ?? ''); ?>"
                                   placeholder="Ej: Recursos Humanos">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Empresa *</label>
                        <select class="form-select" name="empresa_id" required>
                            <?php foreach ($listaEmpresas as $emp): ?>
                            <option value="<?php echo $emp['id']; ?>"
                                <?php echo $emp['id'] == $entidad['empresa_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($emp['nombre']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Estado</label>
                    <select class="form-select" name="activo">
                        <option value="1" <?php echo $entidad['activo'] ? 'selected' : ''; ?>>Activo</option>
                        <option value="0" <?php echo !$entidad['activo'] ? 'selected' : ''; ?>>Inactivo</option>
                    </select>
                </div>

                <hr>

                <div class="d-flex justify-content-between">
                    <a href="panel.php" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg me-1"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-admin px-4">
                        <i class="bi bi-check-circle me-2"></i>Guardar Cambios
                    </button>
                </div>
            </form>
        </div>

        <!-- Info adicional -->
        <div class="form-card mt-3">
            <h6 class="text-muted mb-3"><i class="bi bi-info-circle me-2"></i>Información del Registro</h6>
            <div class="row text-muted small">
                <div class="col-md-6">
                    <strong>Fecha de registro:</strong><br>
                    <?php echo date('d/m/Y H:i', strtotime($entidad['fecha_registro'])); ?>
                </div>
                <div class="col-md-6">
                    <strong>Último acceso:</strong><br>
                    <?php echo $entidad['ultimo_acceso'] ? date('d/m/Y H:i', strtotime($entidad['ultimo_acceso'])) : 'Nunca'; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
require_once __DIR__ . '/../../config/config.php';
iniciarSesionSegura();
requerirAdmin();

$config = obtenerTodasConfigSistema();
$nombre_sistema = $config['nombre_sistema'] ?? 'Sistema de Evaluación de Habilidades Directivas';
$logo_url = $config['logo_url'] ?? '';

$mensaje = $_SESSION['mensaje'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['mensaje'], $_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración del Sistema - <?php echo TITULO_SISTEMA; ?></title>
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
        .form-card {
            background: white;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 1.5rem;
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
        .logo-preview {
            max-height: 80px;
            max-width: 300px;
            object-fit: contain;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 0.5rem;
            background: #f8f9fa;
        }
        .logo-preview-container {
            min-height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #fafafa;
        }
        .upload-area:hover {
            border-color: var(--accent-gold);
            background: #fffbf0;
        }
        .admin-badge {
            background: var(--accent-gold);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <nav class="admin-navbar">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i class="bi bi-gear-fill fs-3 me-3" style="color: var(--accent-gold);"></i>
                    <div>
                        <h5 class="text-white mb-0">Configuración del Sistema</h5>
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

        <!-- Nombre del Sistema -->
        <div class="form-card">
            <h5 class="mb-4"><i class="bi bi-type me-2" style="color: var(--accent-gold);"></i>Nombre del Sistema</h5>
            <form method="POST" action="acciones.php">
                <input type="hidden" name="accion" value="actualizar_config">
                <input type="hidden" name="clave" value="nombre_sistema">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nombre que se muestra en todo el sistema</label>
                    <input type="text" class="form-control" name="valor" required maxlength="255"
                           value="<?php echo htmlspecialchars($nombre_sistema); ?>"
                           placeholder="Ej: Sistema de Evaluación de Habilidades Directivas">
                    <div class="form-text">Se mostrará en títulos de página, encabezados y navbar.</div>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-admin">
                        <i class="bi bi-check-circle me-2"></i>Guardar Nombre
                    </button>
                </div>
            </form>
        </div>

        <!-- Logo del Sistema -->
        <div class="form-card">
            <h5 class="mb-4"><i class="bi bi-image me-2" style="color: var(--accent-gold);"></i>Logo del Sistema</h5>
            
            <!-- Vista previa actual -->
            <div class="mb-4">
                <label class="form-label fw-semibold">Logo actual</label>
                <div class="logo-preview-container">
                    <?php if (!empty($logo_url)): ?>
                        <img src="<?php echo BASE_URL . htmlspecialchars($logo_url); ?>" alt="Logo del sistema" class="logo-preview" id="logoPreview">
                    <?php else: ?>
                        <div class="text-muted" id="logoPlaceholder">
                            <i class="bi bi-image fs-1 d-block mb-2"></i>
                            No hay logo configurado
                        </div>
                        <img src="" alt="Logo del sistema" class="logo-preview d-none" id="logoPreview">
                    <?php endif; ?>
                </div>
            </div>

            <form method="POST" action="acciones.php" enctype="multipart/form-data">
                <input type="hidden" name="accion" value="subir_logo">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Subir nuevo logo</label>
                    <div class="upload-area" onclick="document.getElementById('logoInput').click()">
                        <i class="bi bi-cloud-arrow-up fs-2 d-block mb-2" style="color: var(--accent-gold);"></i>
                        <span id="uploadText">Haz clic para seleccionar una imagen</span>
                        <br><small class="text-muted">PNG, JPG, SVG o WEBP · Máximo 2 MB</small>
                    </div>
                    <input type="file" class="d-none" id="logoInput" name="logo" 
                           accept="image/png,image/jpeg,image/svg+xml,image/webp"
                           onchange="previewLogo(this)">
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-admin" id="btnSubirLogo">
                        <i class="bi bi-upload me-2"></i>Subir Logo
                    </button>
                </div>
            </form>

            <?php if (!empty($logo_url)): ?>
            <hr>
            <form method="POST" action="acciones.php" onsubmit="return confirm('¿Eliminar el logo actual?')">
                <input type="hidden" name="accion" value="eliminar_logo">
                <button type="submit" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-trash me-1"></i>Eliminar Logo
                </button>
            </form>
            <?php endif; ?>
        </div>

        <!-- Vista previa -->
        <div class="form-card">
            <h5 class="mb-3"><i class="bi bi-eye me-2" style="color: var(--accent-gold);"></i>Vista Previa del Navbar</h5>
            <div style="background: #2C3E50; border-radius: 8px; padding: 1rem 1.5rem;" id="navbarPreview">
                <div class="d-flex align-items-center">
                    <?php if (!empty($logo_url)): ?>
                        <img src="<?php echo BASE_URL . htmlspecialchars($logo_url); ?>" alt="Logo" 
                             style="max-height: 35px; margin-right: 10px;" id="previewNavLogo">
                    <?php endif; ?>
                    <span class="text-white fw-bold" id="previewNavName">
                        <?php echo htmlspecialchars($nombre_sistema); ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function previewLogo(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                
                if (file.size > 2 * 1024 * 1024) {
                    alert('El archivo no debe superar los 2 MB');
                    input.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('logoPreview');
                    const placeholder = document.getElementById('logoPlaceholder');
                    preview.src = e.target.result;
                    preview.classList.remove('d-none');
                    if (placeholder) placeholder.classList.add('d-none');
                    document.getElementById('uploadText').textContent = file.name;
                };
                reader.readAsDataURL(file);
            }
        }
    </script>
</body>
</html>

<?php
require_once __DIR__ . '/../../config/config.php';
iniciarSesionSegura();
requerirAdmin();

$admin_id = obtenerAdminActual();
$admin_nombre = $_SESSION['nombre'] ?? 'Admin';

// Obtener datos
$empresas = obtenerTodasEmpresas();
$usuarios = obtenerTodosUsuarios();
$stats = obtenerEstadisticasSistema();

// Mensajes de éxito/error
$mensaje = $_SESSION['mensaje'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['mensaje'], $_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - <?php echo TITULO_SISTEMA; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
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
            color: var(--text-dark);
        }
        
        .admin-navbar {
            background: var(--primary-dark);
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(44, 62, 80, 0.1);
        }
        
        .admin-navbar .nav-link {
            color: rgba(255,255,255,0.9);
            font-weight: 500;
            margin: 0 0.5rem;
            transition: all 0.3s ease;
        }
        
        .admin-navbar .nav-link:hover {
            color: white;
            transform: translateY(-2px);
        }
        
        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border-left: 4px solid var(--accent-gold);
        }
        
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(44, 62, 80, 0.12);
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-dark);
            line-height: 1;
        }
        
        .stat-label {
            color: #6B7280;
            font-size: 0.9rem;
            font-weight: 500;
            margin-top: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .table-card {
            background: white;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-top: 2rem;
        }
        
        .badge-activo {
            background: #10b981;
            color: white;
        }
        
        .badge-inactivo {
            background: #ef4444;
            color: white;
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
        
        .table {
            margin-bottom: 0;
        }
        
        .table thead {
            background: var(--bg-light);
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
                        <h5 class="text-white mb-0">Panel de Administración</h5>
                        <small class="text-white-50"><?php echo TITULO_SISTEMA; ?></small>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <a href="configuracion.php" class="btn btn-outline-light btn-sm me-2" title="Configuración">
                        <i class="bi bi-gear"></i>
                    </a>
                    <span class="text-white me-3">
                        <i class="bi bi-person-circle me-2"></i>
                        <strong><?php echo htmlspecialchars($admin_nombre); ?></strong>
                        <span class="admin-badge ms-2">ADMIN</span>
                    </span>
                    <a href="<?php echo BASE_URL; ?>modules/auth/logout.php" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <!-- Mensajes -->
        <?php if ($mensaje): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <?php echo htmlspecialchars($mensaje); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?php echo htmlspecialchars($error); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Estadísticas -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <i class="bi bi-building fs-2 mb-2" style="color: var(--accent-gold);"></i>
                    <div class="stat-number"><?php echo $stats['total_empresas_activas']; ?></div>
                    <div class="stat-label">Empresas Activas</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <i class="bi bi-people fs-2 mb-2" style="color: var(--accent-gold);"></i>
                    <div class="stat-number"><?php echo $stats['total_usuarios_activos']; ?></div>
                    <div class="stat-label">Usuarios Activos</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <i class="bi bi-clipboard-check fs-2 mb-2" style="color: var(--accent-gold);"></i>
                    <div class="stat-number"><?php echo $stats['total_cuestionarios_completados']; ?></div>
                    <div class="stat-label">Cuestionarios Completados</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <i class="bi bi-graph-up fs-2 mb-2" style="color: var(--accent-gold);"></i>
                    <div class="stat-number"><?php echo number_format($stats['promedio_general_sistema'] ?? 0, 1); ?></div>
                    <div class="stat-label">Promedio General</div>
                </div>
            </div>
        </div>

        <!-- Gestión de Empresas -->
        <div class="table-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>
                    <i class="bi bi-building me-2"></i>Gestión de Empresas
                </h4>
                <button class="btn btn-admin" data-bs-toggle="modal" data-bs-target="#modalCrearEmpresa">
                    <i class="bi bi-plus-circle me-2"></i>Nueva Empresa
                </button>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Usuarios</th>
                            <th>Cuestionarios</th>
                            <th>Fecha Registro</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($empresas as $empresa): ?>
                        <tr>
                            <td><?php echo $empresa['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($empresa['nombre']); ?></strong>
                                <br><small class="text-muted">
                                    <?php echo $empresa['rfc'] ? 'RFC: ' . htmlspecialchars($empresa['rfc']) : 'Sin RFC'; ?>
                                </small>
                            </td>
                            <td><?php echo htmlspecialchars($empresa['email']); ?></td>
                            <td><span class="badge bg-info"><?php echo $empresa['total_usuarios']; ?></span></td>
                            <td><span class="badge bg-primary"><?php echo $empresa['total_cuestionarios']; ?></span></td>
                            <td><?php echo date('d/m/Y', strtotime($empresa['fecha_registro'])); ?></td>
                            <td>
                                <?php if ($empresa['activo']): ?>
                                    <span class="badge badge-activo">Activa</span>
                                <?php else: ?>
                                    <span class="badge badge-inactivo">Inactiva</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="editar.php?tipo=empresa&id=<?php echo $empresa['id']; ?>" class="btn btn-sm btn-info me-1" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <?php if ($empresa['activo']): ?>
                                <button class="btn btn-sm btn-warning me-1" onclick="restaurarPassword('empresa', <?php echo $empresa['id']; ?>, '<?php echo htmlspecialchars($empresa['nombre']); ?>')" title="Restaurar Contraseña">
                                    <i class="bi bi-key"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="confirmarEliminar('empresa', <?php echo $empresa['id']; ?>, '<?php echo htmlspecialchars($empresa['nombre']); ?>')">
                                    <i class="bi bi-trash"></i>
                                </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Gestión de Usuarios -->
        <div class="table-card">
            <h4>
                <i class="bi bi-people me-2"></i>Gestión de Usuarios
            </h4>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Empresa</th>
                            <th>Cuestionarios</th>
                            <th>Fecha Registro</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?php echo $usuario['id']; ?></td>
                            <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['empresa_nombre'] ?? 'N/A'); ?></td>
                            <td><span class="badge bg-primary"><?php echo $usuario['total_cuestionarios']; ?></span></td>
                            <td><?php echo date('d/m/Y', strtotime($usuario['fecha_registro'])); ?></td>
                            <td>
                                <?php if ($usuario['activo']): ?>
                                    <span class="badge badge-activo">Activo</span>
                                <?php else: ?>
                                    <span class="badge badge-inactivo">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="editar.php?tipo=usuario&id=<?php echo $usuario['id']; ?>" class="btn btn-sm btn-info me-1" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <?php if ($usuario['activo']): ?>
                                <button class="btn btn-sm btn-warning me-1" onclick="restaurarPassword('usuario', <?php echo $usuario['id']; ?>, '<?php echo htmlspecialchars($usuario['nombre']); ?>')" title="Restaurar Contraseña">
                                    <i class="bi bi-key"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="confirmarEliminar('usuario', <?php echo $usuario['id']; ?>, '<?php echo htmlspecialchars($usuario['nombre']); ?>')">
                                    <i class="bi bi-trash"></i>
                                </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Crear Empresa -->
    <div class="modal fade" id="modalCrearEmpresa" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-building-add me-2"></i>Crear Nueva Empresa
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="acciones.php">
                    <input type="hidden" name="accion" value="crear_empresa">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nombre de la Empresa *</label>
                            <input type="text" class="form-control" name="nombre" required placeholder="Ej: Constructora ABC">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" class="form-control" name="email" required placeholder="rh@empresa.com">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contraseña Temporal *</label>
                            <input type="password" class="form-control" name="password" required minlength="6" placeholder="Mínimo 6 caracteres">
                            <small class="text-muted">La empresa deberá cambiarla en el primer acceso</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">RFC (opcional)</label>
                            <input type="text" class="form-control" name="rfc" placeholder="ABC123456XXX">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Teléfono (opcional)</label>
                            <input type="text" class="form-control" name="telefono" placeholder="555-1234-5678">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-admin">
                            <i class="bi bi-check-circle me-2"></i>Crear Empresa
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Restaurar Contraseña -->
    <div class="modal fade" id="modalRestaurarPassword" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-key me-2"></i>Restaurar Contraseña
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Se generará una nueva contraseña temporal para:</p>
                    <div class="alert alert-info">
                        <strong id="restaurarNombre"></strong>
                        <br><small id="restaurarTipoTexto"></small>
                    </div>
                    <div id="passwordGenerada" style="display:none;">
                        <div class="alert alert-success">
                            <strong>Nueva contraseña generada:</strong>
                            <div class="input-group mt-2">
                                <input type="text" class="form-control" id="nuevaPasswordTexto" readonly>
                                <button class="btn btn-outline-secondary" type="button" onclick="copiarPassword()">
                                    <i class="bi bi-clipboard"></i> Copiar
                                </button>
                            </div>
                            <small class="text-muted mt-2 d-block">Comparta esta contraseña de forma segura con el usuario.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-warning" id="btnConfirmarRestaurar" onclick="confirmarRestaurar()">
                        <i class="bi bi-key me-2"></i>Generar Nueva Contraseña
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Form oculto para eliminaciones -->
    <form id="formEliminar" method="POST" action="acciones.php" style="display:none;">
        <input type="hidden" name="accion" id="eliminarAccion">
        <input type="hidden" name="id" id="eliminId">
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let restaurarTipo = '';
        let restaurarId = 0;

        function restaurarPassword(tipo, id, nombre) {
            restaurarTipo = tipo;
            restaurarId = id;
            document.getElementById('restaurarNombre').textContent = nombre;
            document.getElementById('restaurarTipoTexto').textContent = tipo === 'empresa' ? 'Cuenta de empresa' : 'Cuenta de usuario';
            document.getElementById('passwordGenerada').style.display = 'none';
            document.getElementById('btnConfirmarRestaurar').style.display = 'inline-block';
            new bootstrap.Modal(document.getElementById('modalRestaurarPassword')).show();
        }

        function confirmarRestaurar() {
            document.getElementById('btnConfirmarRestaurar').disabled = true;
            document.getElementById('btnConfirmarRestaurar').innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Generando...';
            
            fetch('acciones.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `accion=restaurar_password&tipo=${restaurarTipo}&id=${restaurarId}&ajax=1`
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('nuevaPasswordTexto').value = data.password;
                    document.getElementById('passwordGenerada').style.display = 'block';
                    document.getElementById('btnConfirmarRestaurar').style.display = 'none';
                } else {
                    alert('Error: ' + data.error);
                    document.getElementById('btnConfirmarRestaurar').disabled = false;
                    document.getElementById('btnConfirmarRestaurar').innerHTML = '<i class="bi bi-key me-2"></i>Generar Nueva Contraseña';
                }
            })
            .catch(err => {
                alert('Error de conexión');
                document.getElementById('btnConfirmarRestaurar').disabled = false;
                document.getElementById('btnConfirmarRestaurar').innerHTML = '<i class="bi bi-key me-2"></i>Generar Nueva Contraseña';
            });
        }

        function copiarPassword() {
            const input = document.getElementById('nuevaPasswordTexto');
            input.select();
            navigator.clipboard.writeText(input.value).then(() => {
                const btn = input.nextElementSibling;
                btn.innerHTML = '<i class="bi bi-check"></i> Copiado';
                setTimeout(() => btn.innerHTML = '<i class="bi bi-clipboard"></i> Copiar', 2000);
            });
        }

        function confirmarEliminar(tipo, id, nombre) {
            const tipos = {
                'empresa': 'la empresa',
                'usuario': 'el usuario'
            };
            
            if (confirm(`¿Estás seguro de desactivar ${tipos[tipo]} "${nombre}"?\n\nEsta acción no eliminará los datos pero la cuenta quedará inactiva.`)) {
                document.getElementById('eliminarAccion').value = 'eliminar_' + tipo;
                document.getElementById('eliminId').value = id;
                document.getElementById('formEliminar').submit();
            }
        }
    </script>
</body>
</html>

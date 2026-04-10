<?php
require_once __DIR__ . '/../../config/config.php';
iniciarSesionSegura();
requerirAdmin();

$admin_id = obtenerAdminActual();
$accion = $_POST['accion'] ?? '';

try {
    switch ($accion) {
        case 'crear_empresa':
            $nombre = trim($_POST['nombre'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $rfc = trim($_POST['rfc'] ?? '') ?: null;
            $telefono = trim($_POST['telefono'] ?? '') ?: null;
            
            if (empty($nombre) || empty($email) || empty($password)) {
                throw new Exception('Nombre, email y contraseña son obligatorios');
            }
            
            if (strlen($password) < 6) {
                throw new Exception('La contraseña debe tener al menos 6 caracteres');
            }
            
            $empresa_id = crearEmpresaAdmin($admin_id, $nombre, $email, $password, $rfc, $telefono);
            
            $_SESSION['mensaje'] = "Empresa '$nombre' creada exitosamente. Email: $email";
            break;
            
        case 'eliminar_empresa':
            $empresa_id = intval($_POST['id'] ?? 0);
            
            if (!$empresa_id) {
                throw new Exception('ID de empresa inválido');
            }
            
            eliminarEmpresa($admin_id, $empresa_id);
            $_SESSION['mensaje'] = 'Empresa desactivada correctamente';
            break;
            
        case 'eliminar_usuario':
            $usuario_id = intval($_POST['id'] ?? 0);
            
            if (!$usuario_id) {
                throw new Exception('ID de usuario inválido');
            }
            
            eliminarUsuario($admin_id, $usuario_id);
            $_SESSION['mensaje'] = 'Usuario desactivado correctamente';
            break;
            
        case 'editar_empresa':
            $empresa_id = intval($_POST['id'] ?? 0);
            $nombre = trim($_POST['nombre'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $rfc = trim($_POST['rfc'] ?? '') ?: null;
            $telefono = trim($_POST['telefono'] ?? '') ?: null;
            $direccion = trim($_POST['direccion'] ?? '') ?: null;
            $activo = intval($_POST['activo'] ?? 1);

            if (!$empresa_id || empty($nombre) || empty($email)) {
                throw new Exception('ID, nombre y email son obligatorios');
            }

            $pdo = obtenerConexion();

            // Verificar que el email no esté duplicado en otra empresa
            $stmt = $pdo->prepare("SELECT id FROM empresas WHERE email = ? AND id != ?");
            $stmt->execute([$email, $empresa_id]);
            if ($stmt->fetch()) {
                throw new Exception('El email ya está registrado en otra empresa');
            }

            $stmt = $pdo->prepare("UPDATE empresas SET nombre = ?, email = ?, rfc = ?, telefono = ?, direccion = ?, activo = ? WHERE id = ?");
            $stmt->execute([$nombre, $email, $rfc, $telefono, $direccion, $activo, $empresa_id]);

            registrarActividadAdmin($admin_id, 'editar_empresa', 'empresa', $empresa_id,
                "Empresa '$nombre' actualizada");

            $_SESSION['mensaje'] = "Empresa '$nombre' actualizada correctamente";
            break;

        case 'editar_usuario':
            $usuario_id = intval($_POST['id'] ?? 0);
            $nombre = trim($_POST['nombre'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '') ?: null;
            $puesto = trim($_POST['puesto'] ?? '') ?: null;
            $departamento = trim($_POST['departamento'] ?? '') ?: null;
            $empresa_id_nuevo = intval($_POST['empresa_id'] ?? 0);
            $activo = intval($_POST['activo'] ?? 1);

            if (!$usuario_id || empty($nombre) || empty($email) || !$empresa_id_nuevo) {
                throw new Exception('ID, nombre, email y empresa son obligatorios');
            }

            $pdo = obtenerConexion();

            // Verificar que el email no esté duplicado en otro usuario
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
            $stmt->execute([$email, $usuario_id]);
            if ($stmt->fetch()) {
                throw new Exception('El email ya está registrado en otro usuario');
            }

            // Verificar que la empresa destino existe y está activa
            $stmt = $pdo->prepare("SELECT id FROM empresas WHERE id = ? AND activo = 1");
            $stmt->execute([$empresa_id_nuevo]);
            if (!$stmt->fetch()) {
                throw new Exception('La empresa seleccionada no existe o está inactiva');
            }

            // Obtener empresa actual del usuario para detectar cambio
            $stmt = $pdo->prepare("SELECT empresa_id FROM usuarios WHERE id = ?");
            $stmt->execute([$usuario_id]);
            $empresa_id_actual = (int) $stmt->fetchColumn();

            $stmt = $pdo->prepare("UPDATE usuarios SET nombre = ?, email = ?, telefono = ?, puesto = ?, departamento = ?, empresa_id = ?, activo = ? WHERE id = ?");
            $stmt->execute([$nombre, $email, $telefono, $puesto, $departamento, $empresa_id_nuevo, $activo, $usuario_id]);

            // Si cambió de empresa, mover también sus cuestionarios
            if ($empresa_id_actual !== $empresa_id_nuevo) {
                $stmt = $pdo->prepare("UPDATE cuestionarios SET empresa_id = ? WHERE usuario_id = ? AND empresa_id = ?");
                $stmt->execute([$empresa_id_nuevo, $usuario_id, $empresa_id_actual]);
            }

            registrarActividadAdmin($admin_id, 'editar_usuario', 'usuario', $usuario_id,
                "Usuario '$nombre' actualizado");

            $_SESSION['mensaje'] = "Usuario '$nombre' actualizado correctamente";
            break;

        case 'restaurar_password':
            $tipo = $_POST['tipo'] ?? '';
            $id = intval($_POST['id'] ?? 0);
            $ajax = $_POST['ajax'] ?? false;
            
            if (!$id || !in_array($tipo, ['usuario', 'empresa'])) {
                if ($ajax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'error' => 'Parámetros inválidos']);
                    exit;
                }
                throw new Exception('Parámetros inválidos');
            }
            
            $nueva_password = generarPasswordTemporal();
            $password_hash = password_hash($nueva_password, PASSWORD_DEFAULT);
            $pdo = obtenerConexion();
            
            if ($tipo === 'empresa') {
                $stmt = $pdo->prepare("SELECT nombre, email FROM empresas WHERE id = ? AND activo = 1");
                $stmt->execute([$id]);
                $entidad = $stmt->fetch();
                
                if (!$entidad) {
                    if ($ajax) {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => false, 'error' => 'Empresa no encontrada']);
                        exit;
                    }
                    throw new Exception('Empresa no encontrada');
                }
                
                $stmt = $pdo->prepare("UPDATE empresas SET password = ? WHERE id = ?");
                $stmt->execute([$password_hash, $id]);
            } else {
                $stmt = $pdo->prepare("SELECT nombre, email FROM usuarios WHERE id = ? AND activo = 1");
                $stmt->execute([$id]);
                $entidad = $stmt->fetch();
                
                if (!$entidad) {
                    if ($ajax) {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => false, 'error' => 'Usuario no encontrado']);
                        exit;
                    }
                    throw new Exception('Usuario no encontrado');
                }
                
                $stmt = $pdo->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
                $stmt->execute([$password_hash, $id]);
            }
            
            registrarActividadAdmin($admin_id, 'restaurar_password', $tipo, $id,
                "Contraseña restaurada para {$entidad['nombre']} ({$entidad['email']})");
            
            if ($ajax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'password' => $nueva_password]);
                exit;
            }
            
            $_SESSION['mensaje'] = "Contraseña restaurada para {$entidad['nombre']}";
            break;

        case 'actualizar_config':
            $clave = trim($_POST['clave'] ?? '');
            $valor = trim($_POST['valor'] ?? '');
            
            $clavesPermitidas = ['nombre_sistema'];
            if (!in_array($clave, $clavesPermitidas)) {
                throw new Exception('Configuración no permitida');
            }
            
            if (empty($valor)) {
                throw new Exception('El valor no puede estar vacío');
            }
            
            guardarConfigSistema($clave, $valor);
            registrarActividadAdmin($admin_id, 'actualizar_config', 'sistema', 0,
                "Configuración '$clave' actualizada");
            
            $_SESSION['mensaje'] = "Configuración actualizada correctamente";
            header('Location: configuracion.php');
            exit;

        case 'subir_logo':
            if (!isset($_FILES['logo']) || $_FILES['logo']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('No se seleccionó ningún archivo o hubo un error al subirlo');
            }
            
            $archivo = $_FILES['logo'];
            $maxSize = 2 * 1024 * 1024; // 2 MB
            
            if ($archivo['size'] > $maxSize) {
                throw new Exception('El archivo no debe superar los 2 MB');
            }
            
            $tiposPermitidos = ['image/png', 'image/jpeg', 'image/svg+xml', 'image/webp'];
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mimeReal = $finfo->file($archivo['tmp_name']);
            
            if (!in_array($mimeReal, $tiposPermitidos)) {
                throw new Exception('Tipo de archivo no permitido. Use PNG, JPG, SVG o WEBP');
            }
            
            $extensiones = ['image/png' => 'png', 'image/jpeg' => 'jpg', 'image/svg+xml' => 'svg', 'image/webp' => 'webp'];
            $ext = $extensiones[$mimeReal];
            $nombreArchivo = 'logo_' . time() . '.' . $ext;
            $rutaDestino = BASE_PATH . '/storage/uploads/' . $nombreArchivo;
            
            // Eliminar logo anterior si existe
            $logoAnterior = obtenerConfigSistema('logo_url', '');
            if (!empty($logoAnterior)) {
                $rutaAnterior = BASE_PATH . '/' . $logoAnterior;
                if (file_exists($rutaAnterior)) {
                    unlink($rutaAnterior);
                }
            }
            
            if (!move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
                throw new Exception('Error al guardar el archivo');
            }
            
            guardarConfigSistema('logo_url', 'storage/uploads/' . $nombreArchivo);
            registrarActividadAdmin($admin_id, 'subir_logo', 'sistema', 0, "Logo actualizado: $nombreArchivo");
            
            $_SESSION['mensaje'] = "Logo actualizado correctamente";
            header('Location: configuracion.php');
            exit;

        case 'eliminar_logo':
            $logoActual = obtenerConfigSistema('logo_url', '');
            if (!empty($logoActual)) {
                $rutaArchivo = BASE_PATH . '/' . $logoActual;
                if (file_exists($rutaArchivo)) {
                    unlink($rutaArchivo);
                }
                guardarConfigSistema('logo_url', '');
                registrarActividadAdmin($admin_id, 'eliminar_logo', 'sistema', 0, "Logo eliminado");
                $_SESSION['mensaje'] = "Logo eliminado correctamente";
            }
            header('Location: configuracion.php');
            exit;
            
        default:
            throw new Exception('Acción no válida');
    }
    
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
}

// Redirigir de vuelta al panel
header('Location: panel.php');
exit;
?>

<?php
require_once 'config.php';
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
            
        default:
            throw new Exception('Acción no válida');
    }
    
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
}

// Redirigir de vuelta al panel
header('Location: admin_panel.php');
exit;
?>

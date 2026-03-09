<?php
require_once 'config.php';
iniciarSesionSegura();

// Registrar cierre de sesión antes de destruir la información
$tipo = $_SESSION['tipo_usuario'] ?? null;
$usuario_id = $_SESSION['usuario_id'] ?? null;
$empresa_id = $_SESSION['empresa_id'] ?? null;
$admin_id = $_SESSION['admin_id'] ?? null;

// Registrar actividad según el tipo
if ($tipo === 'admin' && $admin_id) {
    registrarActividadAdmin($admin_id, 'logout', 'sistema', null, 'Cierre de sesión admin');
} elseif ($tipo) {
    registrarActividad($tipo, $usuario_id, $empresa_id, 'logout', 'Cierre de sesión');
}

// Cerrar sesión
cerrarSesion();

// Redirigir según el tipo de usuario
if ($tipo === 'admin') {
    header('Location: admin_login.php');
} else {
    header('Location: login.php');
}
exit;
?>

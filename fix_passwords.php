<?php
/**
 * Script para corregir las contraseñas de usuarios de prueba
 * Ejecutar una sola vez para actualizar los hashes de contraseñas
 */

require_once 'config.php';

echo "=== CORRECCIÓN DE CONTRASEÑAS ===\n\n";

try {
    $pdo = obtenerConexion();
    
    // Generar hashes correctos
    $hash_empresa123 = password_hash('empresa123', PASSWORD_DEFAULT);
    $hash_usuario123 = password_hash('usuario123', PASSWORD_DEFAULT);
    $hash_admin123 = password_hash('admin123', PASSWORD_DEFAULT);
    
    echo "Hashes generados:\n";
    echo "- empresa123: $hash_empresa123\n";
    echo "- usuario123: $hash_usuario123\n";
    echo "- admin123: $hash_admin123\n\n";
    
    // Actualizar empresas
    echo "Actualizando empresas...\n";
    $stmt = $pdo->prepare("UPDATE empresas SET password = ? WHERE email IN ('empresa@demo.com', 'contacto@ejemplo.com')");
    $stmt->execute([$hash_empresa123]);
    echo "✓ Empresas actualizadas: " . $stmt->rowCount() . " registros\n";
    
    // Actualizar usuarios
    echo "Actualizando usuarios...\n";
    $stmt = $pdo->prepare("UPDATE usuarios SET password = ? WHERE email IN ('juan.perez@demo.com', 'maria.gonzalez@demo.com', 'carlos.rodriguez@ejemplo.com')");
    $stmt->execute([$hash_usuario123]);
    echo "✓ Usuarios actualizados: " . $stmt->rowCount() . " registros\n";
    
    // Actualizar administrador
    echo "Actualizando administrador...\n";
    $stmt = $pdo->prepare("UPDATE administradores SET password = ? WHERE email = 'admin@sistema.com'");
    $stmt->execute([$hash_admin123]);
    echo "✓ Administrador actualizado: " . $stmt->rowCount() . " registro(s)\n\n";
    
    echo "=== CONTRASEÑAS ACTUALIZADAS CORRECTAMENTE ===\n\n";
    echo "Credenciales disponibles:\n\n";
    
    echo "EMPRESAS (Login en index.php → Empresa):\n";
    echo "  • empresa@demo.com / empresa123\n";
    echo "  • contacto@ejemplo.com / empresa123\n\n";
    
    echo "USUARIOS (Login en index.php → Usuario):\n";
    echo "  • juan.perez@demo.com / usuario123\n";
    echo "  • maria.gonzalez@demo.com / usuario123\n";
    echo "  • carlos.rodriguez@ejemplo.com / usuario123\n\n";
    
    echo "ADMINISTRADOR (Login en admin_login.php):\n";
    echo "  • admin@sistema.com / admin123\n\n";
    
    // Verificar que las contraseñas funcionan
    echo "Verificando contraseñas...\n";
    
    $stmt = $pdo->prepare("SELECT email, password FROM empresas WHERE email = 'empresa@demo.com'");
    $stmt->execute();
    $empresa = $stmt->fetch();
    if (password_verify('empresa123', $empresa['password'])) {
        echo "✓ Contraseña de empresa verificada correctamente\n";
    } else {
        echo "✗ ERROR: La contraseña de empresa NO funciona\n";
    }
    
    $stmt = $pdo->prepare("SELECT email, password FROM usuarios WHERE email = 'juan.perez@demo.com'");
    $stmt->execute();
    $usuario = $stmt->fetch();
    if (password_verify('usuario123', $usuario['password'])) {
        echo "✓ Contraseña de usuario verificada correctamente\n";
    } else {
        echo "✗ ERROR: La contraseña de usuario NO funciona\n";
    }
    
    $stmt = $pdo->prepare("SELECT email, password FROM administradores WHERE email = 'admin@sistema.com'");
    $stmt->execute();
    $admin = $stmt->fetch();
    if ($admin && password_verify('admin123', $admin['password'])) {
        echo "✓ Contraseña de administrador verificada correctamente\n";
    } else {
        echo "✗ ERROR: La contraseña de administrador NO funciona\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n=== PROCESO COMPLETADO ===\n";

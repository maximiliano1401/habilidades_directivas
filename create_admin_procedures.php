<?php
/**
 * Script para crear procedimientos almacenados del panel de administrador
 */

require_once 'config.php';

echo "=== CREANDO PROCEDIMIENTOS DEL ADMINISTRADOR ===\n\n";

try {
    $pdo = obtenerConexion();
    $pdo->exec("USE habilidades_directivas");
    
    echo "Eliminando procedimientos existentes...\n";
    
    try {
        $pdo->exec("DROP PROCEDURE IF EXISTS obtener_estadisticas_sistema");
        echo "✓ Eliminado: obtener_estadisticas_sistema\n";
    } catch (Exception $e) {
        echo "  (no existía)\n";
    }
    
    try {
        $pdo->exec("DROP PROCEDURE IF EXISTS registrar_actividad_admin");
        echo "✓ Eliminado: registrar_actividad_admin\n";
    } catch (Exception $e) {
        echo "  (no existía)\n";
    }
    
    echo "\nCreando procedimientos nuevos...\n";
    
    // Crear procedimiento: obtener_estadisticas_sistema
    $proc1 = "
    CREATE PROCEDURE obtener_estadisticas_sistema()
    BEGIN
        SELECT 
            (SELECT COUNT(*) FROM usuarios WHERE activo = 1) as total_usuarios_activos,
            (SELECT COUNT(*) FROM usuarios) as total_usuarios,
            (SELECT COUNT(*) FROM empresas WHERE activo = 1) as total_empresas_activas,
            (SELECT COUNT(*) FROM empresas) as total_empresas,
            (SELECT COUNT(*) FROM cuestionarios WHERE estado = 'completado') as total_cuestionarios_completados,
            (SELECT COUNT(*) FROM cuestionarios WHERE estado = 'en_progreso') as total_cuestionarios_progreso,
            (SELECT COUNT(*) FROM cuestionarios) as total_cuestionarios,
            (SELECT ROUND(AVG(promedio_general), 2) FROM cuestionarios WHERE estado = 'completado') as promedio_general_sistema;
    END";
    $pdo->exec($proc1);
    echo "✓ Creado: obtener_estadisticas_sistema\n";
    
    // Crear procedimiento: registrar_actividad_admin
    $proc2 = "
    CREATE PROCEDURE registrar_actividad_admin(
        IN p_admin_id INT,
        IN p_accion VARCHAR(50),
        IN p_entidad_tipo VARCHAR(20),
        IN p_entidad_id INT,
        IN p_detalles TEXT
    )
    BEGIN
        DECLARE client_ip VARCHAR(45);
        SET client_ip = '127.0.0.1';
        
        INSERT INTO logs_admin (admin_id, accion, entidad_tipo, entidad_id, detalles, ip_address)
        VALUES (p_admin_id, p_accion, p_entidad_tipo, p_entidad_id, p_detalles, client_ip);
    END";
    $pdo->exec($proc2);
    echo "✓ Creado: registrar_actividad_admin\n";
    
    echo "\n=== PROCEDIMIENTOS ADMINISTRATIVOS CREADOS ===\n";
    
    // Verificar que existen todos los procedimientos
    echo "\nListado de todos los procedimientos disponibles:\n";
    $stmt = $pdo->query("SHOW PROCEDURE STATUS WHERE Db = 'habilidades_directivas'");
    $procedures = $stmt->fetchAll();
    
    foreach ($procedures as $proc) {
        echo "  ✓ " . $proc['Name'] . "\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n=== TODOS LOS PROCEDIMIENTOS CREADOS EXITOSAMENTE ===\n";

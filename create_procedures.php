<?php
/**
 * Script para crear procedimientos almacenados faltantes
 */

require_once 'config.php';

echo "=== CREANDO PROCEDIMIENTOS ALMACENADOS ===\n\n";

try {
    $pdo = obtenerConexion();
    
    // Leer el archivo SQL
    $sql = file_get_contents('create_procedures.sql');
    
    // Separar por el delimitador DELIMITER
    $sql = str_replace('DELIMITER //', '', $sql);
    $sql = str_replace('DELIMITER ;', '', $sql);
    
    // Usar USE
    $pdo->exec("USE habilidades_directivas");
    
    echo "Eliminando procedimientos existentes...\n";
    
    // Eliminar procedimientos existentes
    try {
        $pdo->exec("DROP PROCEDURE IF EXISTS limpiar_sesiones_expiradas");
        echo "✓ Eliminado: limpiar_sesiones_expiradas\n";
    } catch (Exception $e) {
        echo "  (no existía)\n";
    }
    
    try {
        $pdo->exec("DROP PROCEDURE IF EXISTS obtener_estadisticas_empresa");
        echo "✓ Eliminado: obtener_estadisticas_empresa\n";
    } catch (Exception $e) {
        echo "  (no existía)\n";
    }
    
    try {
        $pdo->exec("DROP PROCEDURE IF EXISTS calcular_resultados_cuestionario");
        echo "✓ Eliminado: calcular_resultados_cuestionario\n";
    } catch (Exception $e) {
        echo "  (no existía)\n";
    }
    
    echo "\nCreando procedimientos nuevos...\n";
    
    // Crear procedimiento 1: limpiar_sesiones_expiradas
    $proc1 = "
    CREATE PROCEDURE limpiar_sesiones_expiradas()
    BEGIN
        DELETE FROM sesiones 
        WHERE fecha_expiracion < NOW() 
        OR (activa = 0 AND fecha_inicio < DATE_SUB(NOW(), INTERVAL 7 DAY));
    END";
    $pdo->exec($proc1);
    echo "✓ Creado: limpiar_sesiones_expiradas\n";
    
    // Crear procedimiento 2: obtener_estadisticas_empresa
    $proc2 = "
    CREATE PROCEDURE obtener_estadisticas_empresa(IN empresa_id_param INT)
    BEGIN
        SELECT 
            COUNT(DISTINCT u.id) AS total_usuarios,
            COUNT(DISTINCT c.id) AS total_cuestionarios,
            COUNT(DISTINCT CASE WHEN c.estado = 'completado' THEN c.id END) AS cuestionarios_completados,
            COUNT(DISTINCT CASE WHEN c.estado = 'en_progreso' THEN c.id END) AS cuestionarios_en_progreso,
            AVG(CASE WHEN c.estado = 'completado' THEN c.promedio_general END) AS promedio_general_empresa
        FROM empresas e
        LEFT JOIN usuarios u ON e.id = u.empresa_id AND u.activo = 1
        LEFT JOIN cuestionarios c ON e.id = c.empresa_id
        WHERE e.id = empresa_id_param
        GROUP BY e.id;
    END";
    $pdo->exec($proc2);
    echo "✓ Creado: obtener_estadisticas_empresa\n";
    
    // Crear procedimiento 3: calcular_resultados_cuestionario
    $proc3 = "
    CREATE PROCEDURE calcular_resultados_cuestionario(IN cuestionario_id_param INT)
    BEGIN
        DECLARE total_preguntas_var INT;
        DECLARE preguntas_respondidas_var INT;
        DECLARE promedio_general_var DECIMAL(3,2);
        
        -- Contar respuestas
        SELECT COUNT(*) INTO preguntas_respondidas_var
        FROM respuestas
        WHERE cuestionario_id = cuestionario_id_param;
        
        -- Calcular promedio general
        SELECT AVG(valor_respuesta) INTO promedio_general_var
        FROM respuestas
        WHERE cuestionario_id = cuestionario_id_param;
        
        -- Actualizar cuestionario
        UPDATE cuestionarios
        SET preguntas_respondidas = preguntas_respondidas_var,
            promedio_general = promedio_general_var
        WHERE id = cuestionario_id_param;
        
        SELECT preguntas_respondidas_var, promedio_general_var;
    END";
    $pdo->exec($proc3);
    echo "✓ Creado: calcular_resultados_cuestionario\n";
    
    echo "\n=== PROCEDIMIENTOS CREADOS EXITOSAMENTE ===\n";
    
    // Verificar que existen
    echo "\nVerificando procedimientos...\n";
    $stmt = $pdo->query("SHOW PROCEDURE STATUS WHERE Db = 'habilidades_directivas'");
    $procedures = $stmt->fetchAll();
    
    foreach ($procedures as $proc) {
        echo "  ✓ " . $proc['Name'] . "\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Detalles: " . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\n=== PROCESO COMPLETADO ===\n";

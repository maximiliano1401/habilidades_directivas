-- =====================================================
-- Crear procedimientos almacenados faltantes
-- =====================================================

USE habilidades_directivas;

DELIMITER //

-- Procedimiento: Limpiar sesiones expiradas
DROP PROCEDURE IF EXISTS limpiar_sesiones_expiradas //
CREATE PROCEDURE limpiar_sesiones_expiradas()
BEGIN
    DELETE FROM sesiones 
    WHERE fecha_expiracion < NOW() 
    OR (activa = 0 AND fecha_inicio < DATE_SUB(NOW(), INTERVAL 7 DAY));
END //

-- Procedimiento: Obtener estadísticas de empresa
DROP PROCEDURE IF EXISTS obtener_estadisticas_empresa //
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
END //

-- Procedimiento: Calcular resultados de cuestionario
DROP PROCEDURE IF EXISTS calcular_resultados_cuestionario //
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
END //

DELIMITER ;

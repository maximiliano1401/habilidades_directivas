-- Script para agregar sistema de administración
-- Ejecutar después de database.sql

USE habilidades_directivas;

-- Tabla de administradores
CREATE TABLE IF NOT EXISTS administradores (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultimo_acceso TIMESTAMP NULL,
    activo BOOLEAN DEFAULT TRUE,
    INDEX idx_email (email),
    INDEX idx_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Crear administrador por defecto
-- Email: admin@sistema.com
-- Contraseña: admin123
INSERT IGNORE INTO administradores (nombre, email, password) VALUES 
('Administrador del Sistema', 'admin@sistema.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Agregar columna para tracking de quien creó empresas
SET @column_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = 'habilidades_directivas' 
    AND TABLE_NAME = 'empresas' 
    AND COLUMN_NAME = 'creado_por_admin_id');

SET @sql_add_column = IF(@column_exists = 0,
    'ALTER TABLE empresas ADD COLUMN creado_por_admin_id INT NULL AFTER nombre',
    'SELECT "Columna creado_por_admin_id ya existe" as mensaje');
PREPARE stmt FROM @sql_add_column;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Agregar constraint si no existe
SET @constraint_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS 
    WHERE TABLE_SCHEMA = 'habilidades_directivas' 
    AND TABLE_NAME = 'empresas' 
    AND CONSTRAINT_NAME = 'fk_empresas_admin');

SET @sql_add_fk = IF(@constraint_exists = 0,
    'ALTER TABLE empresas ADD CONSTRAINT fk_empresas_admin FOREIGN KEY (creado_por_admin_id) REFERENCES administradores(id) ON DELETE SET NULL',
    'SELECT "Constraint fk_empresas_admin ya existe" as mensaje');
PREPARE stmt FROM @sql_add_fk;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Logs de actividad del administrador
CREATE TABLE IF NOT EXISTS logs_admin (
    id INT PRIMARY KEY AUTO_INCREMENT,
    admin_id INT NOT NULL,
    accion VARCHAR(50) NOT NULL,
    entidad_tipo ENUM('usuario', 'empresa', 'cuestionario', 'sistema') NOT NULL,
    entidad_id INT NULL,
    detalles TEXT,
    ip_address VARCHAR(45),
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_admin (admin_id),
    INDEX idx_fecha (fecha),
    INDEX idx_accion (accion),
    FOREIGN KEY (admin_id) REFERENCES administradores(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Vista para estadísticas generales del sistema
CREATE OR REPLACE VIEW vista_estadisticas_sistema AS
SELECT 
    (SELECT COUNT(*) FROM usuarios WHERE activo = 1) as total_usuarios_activos,
    (SELECT COUNT(*) FROM usuarios) as total_usuarios,
    (SELECT COUNT(*) FROM empresas WHERE activo = 1) as total_empresas_activas,
    (SELECT COUNT(*) FROM empresas) as total_empresas,
    (SELECT COUNT(*) FROM cuestionarios WHERE estado = 'completado') as total_cuestionarios_completados,
    (SELECT COUNT(*) FROM cuestionarios WHERE estado = 'en_progreso') as total_cuestionarios_progreso,
    (SELECT COUNT(*) FROM cuestionarios) as total_cuestionarios,
    (SELECT ROUND(AVG(promedio_general), 2) FROM cuestionarios WHERE estado = 'completado') as promedio_general_sistema;

-- Procedimiento para obtener estadísticas del sistema
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS obtener_estadisticas_sistema()
BEGIN
    SELECT * FROM vista_estadisticas_sistema;
    
    -- Top 5 empresas con más evaluaciones
    SELECT 
        e.nombre,
        COUNT(c.id) as total_evaluaciones,
        AVG(c.promedio_general) as promedio_empresa
    FROM empresas e
    LEFT JOIN cuestionarios c ON e.id = c.empresa_id AND c.estado = 'completado'
    WHERE e.activo = 1
    GROUP BY e.id, e.nombre
    ORDER BY total_evaluaciones DESC
    LIMIT 5;
    
    -- Actividad reciente (últimas 20 acciones)
    SELECT 
        'usuario' as tipo,
        u.nombre,
        u.email,
        la.accion,
        la.fecha
    FROM logs_actividad la
    JOIN usuarios u ON la.usuario_id = u.id
    WHERE la.tipo_usuario = 'usuario'
    ORDER BY la.fecha DESC
    LIMIT 20;
END //
DELIMITER ;

-- Función para registrar actividad del admin
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS registrar_actividad_admin(
    IN p_admin_id INT,
    IN p_accion VARCHAR(50),
    IN p_entidad_tipo VARCHAR(20),
    IN p_entidad_id INT,
    IN p_detalles TEXT
)
BEGIN
    INSERT INTO logs_admin (admin_id, accion, entidad_tipo, entidad_id, detalles, ip_address)
    VALUES (p_admin_id, p_accion, p_entidad_tipo, p_entidad_id, p_detalles, 
            SUBSTRING_INDEX(SUBSTRING_INDEX(
                COALESCE(
                    @client_ip,
                    '127.0.0.1'
                ), ',', 1
            ), ' ', -1));
END //
DELIMITER ;

SELECT 'Sistema de administración configurado correctamente' as mensaje;
SELECT 'Credenciales de administrador:' as info;
SELECT 'Email: admin@sistema.com' as email;
SELECT 'Contraseña: admin123' as password;
SELECT '¡IMPORTANTE! Cambia la contraseña después del primer acceso' as advertencia;

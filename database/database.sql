-- =====================================================
-- Base de Datos: Sistema de Evaluación de Habilidades Directivas
-- Versión: 2.0
-- Fecha: 8 de marzo de 2026
-- =====================================================

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS habilidades_directivas 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE habilidades_directivas;

-- =====================================================
-- TABLA: usuarios
-- Descripción: Almacena información de usuarios que responden cuestionarios
-- =====================================================
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    telefono VARCHAR(20) NULL,
    puesto VARCHAR(100) NULL,
    departamento VARCHAR(100) NULL,
    empresa_id INT NOT NULL,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    ultimo_acceso DATETIME NULL,
    activo TINYINT(1) DEFAULT 1,
    INDEX idx_empresa (empresa_id),
    INDEX idx_email (email),
    INDEX idx_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: empresas
-- Descripción: Almacena información de empresas que consultan resultados
-- =====================================================
CREATE TABLE empresas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rfc VARCHAR(20) NULL,
    telefono VARCHAR(20) NULL,
    direccion TEXT NULL,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    ultimo_acceso DATETIME NULL,
    activo TINYINT(1) DEFAULT 1,
    INDEX idx_email (email),
    INDEX idx_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: cuestionarios
-- Descripción: Almacena instancias de cuestionarios respondidos
-- =====================================================
CREATE TABLE cuestionarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    empresa_id INT NOT NULL,
    fecha_inicio DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_completado DATETIME NULL,
    promedio_general DECIMAL(3,2) NULL,
    nivel_general VARCHAR(50) NULL,
    estado ENUM('en_progreso', 'completado', 'abandonado') DEFAULT 'en_progreso',
    total_preguntas INT DEFAULT 0,
    preguntas_respondidas INT DEFAULT 0,
    INDEX idx_usuario (usuario_id),
    INDEX idx_empresa (empresa_id),
    INDEX idx_estado (estado),
    INDEX idx_fecha_completado (fecha_completado),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (empresa_id) REFERENCES empresas(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: respuestas
-- Descripción: Almacena respuestas individuales a cada pregunta
-- =====================================================
CREATE TABLE respuestas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cuestionario_id INT NOT NULL,
    habilidad_id VARCHAR(50) NOT NULL,
    habilidad_nombre VARCHAR(100) NOT NULL,
    pregunta_index INT NOT NULL,
    pregunta_texto TEXT NOT NULL,
    valor_respuesta INT NOT NULL CHECK (valor_respuesta BETWEEN 1 AND 5),
    fecha_respuesta DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_cuestionario (cuestionario_id),
    INDEX idx_habilidad (habilidad_id),
    FOREIGN KEY (cuestionario_id) REFERENCES cuestionarios(id) ON DELETE CASCADE,
    UNIQUE KEY unique_respuesta (cuestionario_id, habilidad_id, pregunta_index)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: resultados_habilidades
-- Descripción: Almacena resultados calculados por habilidad
-- =====================================================
CREATE TABLE resultados_habilidades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cuestionario_id INT NOT NULL,
    habilidad_id VARCHAR(50) NOT NULL,
    habilidad_nombre VARCHAR(100) NOT NULL,
    habilidad_descripcion TEXT NOT NULL,
    promedio DECIMAL(3,2) NOT NULL,
    nivel VARCHAR(50) NOT NULL,
    clase VARCHAR(20) NOT NULL,
    mensaje TEXT NOT NULL,
    fecha_calculo DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_cuestionario (cuestionario_id),
    INDEX idx_habilidad (habilidad_id),
    FOREIGN KEY (cuestionario_id) REFERENCES cuestionarios(id) ON DELETE CASCADE,
    UNIQUE KEY unique_resultado (cuestionario_id, habilidad_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: sesiones
-- Descripción: Gestión de sesiones de usuarios
-- =====================================================
CREATE TABLE sesiones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(128) NOT NULL UNIQUE,
    tipo_usuario ENUM('usuario', 'empresa') NOT NULL,
    usuario_id INT NULL,
    empresa_id INT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT NULL,
    fecha_inicio DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_expiracion DATETIME NOT NULL,
    activa TINYINT(1) DEFAULT 1,
    INDEX idx_session_id (session_id),
    INDEX idx_tipo (tipo_usuario),
    INDEX idx_fecha_expiracion (fecha_expiracion),
    INDEX idx_activa (activa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: progreso_cuestionario
-- Descripción: Almacena el progreso del usuario (autoguardado)
-- =====================================================
CREATE TABLE progreso_cuestionario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    cuestionario_id INT NOT NULL,
    paso_actual INT DEFAULT 0,
    datos_personales_completos TINYINT(1) DEFAULT 0,
    respuestas_guardadas TEXT NULL COMMENT 'JSON con respuestas temporales',
    ultima_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_usuario (usuario_id),
    INDEX idx_cuestionario (cuestionario_id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (cuestionario_id) REFERENCES cuestionarios(id) ON DELETE CASCADE,
    UNIQUE KEY unique_progreso (usuario_id, cuestionario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: logs_actividad
-- Descripción: Registro de actividad del sistema para auditoría
-- =====================================================
CREATE TABLE logs_actividad (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo_usuario ENUM('usuario', 'empresa', 'sistema') NOT NULL,
    usuario_id INT NULL,
    empresa_id INT NULL,
    accion VARCHAR(100) NOT NULL,
    descripcion TEXT NULL,
    ip_address VARCHAR(45) NULL,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_tipo (tipo_usuario),
    INDEX idx_usuario (usuario_id),
    INDEX idx_empresa (empresa_id),
    INDEX idx_fecha (fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- INSERTAR DATOS DE PRUEBA
-- =====================================================

-- Empresa de prueba
-- Password: empresa123
INSERT INTO empresas (nombre, email, password, rfc, telefono) VALUES
('Empresa Demo S.A. de C.V.', 'empresa@demo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'EDM990101ABC', '5555555555'),
('Corporativo Ejemplo', 'contacto@ejemplo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'CEJ000202CDE', '5555555556');

-- Usuarios de prueba
-- Password: usuario123
INSERT INTO usuarios (nombre, email, password, puesto, departamento, empresa_id) VALUES
('Juan Pérez García', 'juan.perez@demo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Gerente de Ventas', 'Ventas', 1),
('María González López', 'maria.gonzalez@demo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Coordinadora de RRHH', 'Recursos Humanos', 1),
('Carlos Rodríguez Martínez', 'carlos.rodriguez@ejemplo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Director de Operaciones', 'Operaciones', 2);

-- =====================================================
-- VISTAS ÚTILES
-- =====================================================

-- Vista: Resumen de cuestionarios por empresa
CREATE VIEW vista_cuestionarios_empresa AS
SELECT 
    e.id AS empresa_id,
    e.nombre AS empresa_nombre,
    COUNT(DISTINCT c.id) AS total_cuestionarios,
    COUNT(DISTINCT CASE WHEN c.estado = 'completado' THEN c.id END) AS completados,
    COUNT(DISTINCT CASE WHEN c.estado = 'en_progreso' THEN c.id END) AS en_progreso,
    AVG(CASE WHEN c.estado = 'completado' THEN c.promedio_general END) AS promedio_empresa
FROM empresas e
LEFT JOIN cuestionarios c ON e.id = c.empresa_id
GROUP BY e.id, e.nombre;

-- Vista: Resumen de evaluaciones por usuario
CREATE VIEW vista_evaluaciones_usuario AS
SELECT 
    u.id AS usuario_id,
    u.nombre AS usuario_nombre,
    u.email,
    u.puesto,
    e.nombre AS empresa_nombre,
    COUNT(c.id) AS total_evaluaciones,
    MAX(c.fecha_completado) AS ultima_evaluacion,
    AVG(c.promedio_general) AS promedio_historico
FROM usuarios u
LEFT JOIN empresas e ON u.empresa_id = e.id
LEFT JOIN cuestionarios c ON u.id = c.usuario_id AND c.estado = 'completado'
GROUP BY u.id, u.nombre, u.email, u.puesto, e.nombre;

-- Vista: Estadísticas de habilidades por empresa
CREATE VIEW vista_habilidades_empresa AS
SELECT 
    e.id AS empresa_id,
    e.nombre AS empresa_nombre,
    rh.habilidad_id,
    rh.habilidad_nombre,
    COUNT(rh.id) AS total_evaluaciones,
    AVG(rh.promedio) AS promedio_habilidad,
    MIN(rh.promedio) AS minimo,
    MAX(rh.promedio) AS maximo
FROM empresas e
INNER JOIN cuestionarios c ON e.id = c.empresa_id AND c.estado = 'completado'
INNER JOIN resultados_habilidades rh ON c.id = rh.cuestionario_id
GROUP BY e.id, e.nombre, rh.habilidad_id, rh.habilidad_nombre;

-- =====================================================
-- PROCEDIMIENTOS ALMACENADOS
-- =====================================================

DELIMITER //

-- Procedimiento: Limpiar sesiones expiradas
CREATE PROCEDURE limpiar_sesiones_expiradas()
BEGIN
    DELETE FROM sesiones 
    WHERE fecha_expiracion < NOW() 
    OR (activa = 0 AND fecha_inicio < DATE_SUB(NOW(), INTERVAL 7 DAY));
END //

-- Procedimiento: Obtener estadísticas de empresa
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

-- =====================================================
-- TRIGGERS
-- =====================================================

DELIMITER //

-- Trigger: Actualizar fecha de acceso al iniciar sesión
CREATE TRIGGER actualizar_ultimo_acceso_usuario
AFTER INSERT ON sesiones
FOR EACH ROW
BEGIN
    IF NEW.tipo_usuario = 'usuario' AND NEW.usuario_id IS NOT NULL THEN
        UPDATE usuarios 
        SET ultimo_acceso = NEW.fecha_inicio 
        WHERE id = NEW.usuario_id;
    END IF;
    
    IF NEW.tipo_usuario = 'empresa' AND NEW.empresa_id IS NOT NULL THEN
        UPDATE empresas 
        SET ultimo_acceso = NEW.fecha_inicio 
        WHERE id = NEW.empresa_id;
    END IF;
END //

-- Trigger: Actualizar contador de preguntas respondidas
CREATE TRIGGER actualizar_progreso_respuesta
AFTER INSERT ON respuestas
FOR EACH ROW
BEGIN
    UPDATE cuestionarios
    SET preguntas_respondidas = (
        SELECT COUNT(*) 
        FROM respuestas 
        WHERE cuestionario_id = NEW.cuestionario_id
    )
    WHERE id = NEW.cuestionario_id;
END //

DELIMITER ;

-- =====================================================
-- ÍNDICES ADICIONALES PARA OPTIMIZACIÓN
-- =====================================================

-- Índice compuesto para búsquedas frecuentes
CREATE INDEX idx_cuestionario_estado_empresa ON cuestionarios(empresa_id, estado, fecha_completado);
CREATE INDEX idx_usuario_empresa_activo ON usuarios(empresa_id, activo);

-- =====================================================
-- COMENTARIOS Y DOCUMENTACIÓN
-- =====================================================

ALTER TABLE usuarios COMMENT = 'Tabla de usuarios que responden cuestionarios';
ALTER TABLE empresas COMMENT = 'Tabla de empresas que consultan resultados';
ALTER TABLE cuestionarios COMMENT = 'Instancias de cuestionarios (completos o en progreso)';
ALTER TABLE respuestas COMMENT = 'Respuestas individuales a preguntas del cuestionario';
ALTER TABLE resultados_habilidades COMMENT = 'Resultados calculados por habilidad';
ALTER TABLE sesiones COMMENT = 'Control de sesiones activas';
ALTER TABLE progreso_cuestionario COMMENT = 'Autoguardado del progreso del usuario';
ALTER TABLE logs_actividad COMMENT = 'Registro de auditoría del sistema';

-- =====================================================
-- FIN DEL SCRIPT
-- =====================================================
-- Para ejecutar este script:
-- 1. Abrir phpMyAdmin o MySQL Workbench
-- 2. Ejecutar todo el script
-- 3. Verificar que la base de datos 'habilidades_directivas' se creó correctamente
-- 4. Configurar las credenciales en config.php
-- =====================================================

<?php
// Configuración del sistema de evaluación de habilidades directivas

// Definición de las habilidades directivas y sus preguntas
$habilidades = [
    [
        'id' => 'tecnicas',
        'nombre' => 'Técnicas',
        'descripcion' => 'Desarrollar tareas específicas.',
        'preguntas' => [
            'Domino las herramientas y técnicas necesarias para mi área de trabajo',
            'Resuelvo problemas técnicos de manera eficiente',
            'Me mantengo actualizado en las técnicas de mi campo',
            'Aplico procedimientos técnicos con precisión'
        ]
    ],
    [
        'id' => 'interpersonales',
        'nombre' => 'Interpersonales',
        'descripcion' => 'Se refiere a la habilidad para trabajar en grupo, con espíritu de colaboración, cortesía y cooperación para resolver las necesidades de otras personas e, incluso, para obtener objetivos comunes.',
        'preguntas' => [
            'Trabajo efectivamente en equipo',
            'Muestro empatía y comprensión hacia los demás',
            'Colaboro activamente para alcanzar objetivos comunes',
            'Mantengo relaciones de trabajo positivas y respetuosas',
            'Resuelvo conflictos interpersonales de manera constructiva'
        ]
    ],
    [
        'id' => 'sociales',
        'nombre' => 'Sociales',
        'descripcion' => 'Son las acciones de uno con los demás y los demás con uno. Es donde se da el intercambio y la convivencia humana.',
        'preguntas' => [
            'Me relaciono fácilmente con diferentes tipos de personas',
            'Participo activamente en actividades sociales del trabajo',
            'Creo un ambiente de convivencia positivo',
            'Me adapto a diferentes contextos sociales'
        ]
    ],
    [
        'id' => 'academicas',
        'nombre' => 'Académicas',
        'descripcion' => 'Capacidad y habilidad para hacer análisis, comparación, contratación, evaluación, juicio o crítica.',
        'preguntas' => [
            'Analizo información de manera crítica y objetiva',
            'Comparo diferentes opciones antes de tomar decisiones',
            'Evalúo situaciones desde múltiples perspectivas',
            'Emito juicios fundamentados en evidencia',
            'Realizo análisis profundos de problemas complejos'
        ]
    ],
    [
        'id' => 'innovacion',
        'nombre' => 'De Innovación',
        'descripcion' => 'Invención, descubrimiento, suposición, formulación de hipótesis y teorización.',
        'preguntas' => [
            'Propongo ideas nuevas y creativas',
            'Busco constantemente formas de mejorar procesos',
            'Me siento cómodo experimentando con nuevos enfoques',
            'Formulo soluciones innovadoras a problemas existentes'
        ]
    ],
    [
        'id' => 'practicas',
        'nombre' => 'Prácticas',
        'descripcion' => 'Aplicación, empleo e implementación (hábito).',
        'preguntas' => [
            'Convierto ideas en acciones concretas',
            'Implemento soluciones de manera efectiva',
            'Mantengo buenos hábitos de trabajo',
            'Aplico conocimientos teóricos a situaciones reales'
        ]
    ],
    [
        'id' => 'fisicas',
        'nombre' => 'Físicas',
        'descripcion' => 'Autoeficiencia, flexibilidad, salud.',
        'preguntas' => [
            'Mantengo un buen estado de salud física',
            'Tengo la energía necesaria para cumplir mis responsabilidades',
            'Me adapto físicamente a las demandas de mi trabajo',
            'Cuido mi bienestar físico de manera consciente'
        ]
    ],
    [
        'id' => 'pensamiento',
        'nombre' => 'De Pensamiento',
        'descripcion' => 'Aprender a pensar y generar conocimiento.',
        'preguntas' => [
            'Reflexiono profundamente sobre los problemas',
            'Genero conocimiento a partir de mis experiencias',
            'Pienso de manera estratégica y a largo plazo',
            'Desarrollo mi capacidad de razonamiento constantemente',
            'Aprendo de mis errores y éxitos'
        ]
    ],
    [
        'id' => 'directivas',
        'nombre' => 'Directivas',
        'descripcion' => 'Saber dirigir, coordinar equipos de trabajo.',
        'preguntas' => [
            'Coordino eficientemente equipos de trabajo',
            'Delego tareas de manera apropiada',
            'Establezco objetivos claros para mi equipo',
            'Dirijo proyectos de principio a fin exitosamente',
            'Tomo decisiones directivas con confianza'
        ]
    ],
    [
        'id' => 'liderazgo',
        'nombre' => 'De Liderazgo',
        'descripcion' => 'Guiar, impulsar, motivar al equipo hacia un bien común.',
        'preguntas' => [
            'Inspiro y motivo a otros a alcanzar sus metas',
            'Guío a mi equipo hacia objetivos comunes',
            'Genero confianza y credibilidad en otros',
            'Impulso el desarrollo de las personas a mi alrededor',
            'Comunico una visión clara y motivadora'
        ]
    ],
    [
        'id' => 'empresariales',
        'nombre' => 'Empresariales',
        'descripcion' => 'Emprender una nueva idea, proyecto, empresa o negocio.',
        'preguntas' => [
            'Identifico oportunidades de negocio',
            'Tengo iniciativa para emprender nuevos proyectos',
            'Asumo riesgos calculados',
            'Desarrollo planes de negocio viables',
            'Tengo visión empresarial y estratégica'
        ]
    ]
];

// Escala Likert
$escala_likert = [
    1 => 'Totalmente en desacuerdo',
    2 => 'En desacuerdo',
    3 => 'Neutral',
    4 => 'De acuerdo',
    5 => 'Totalmente de acuerdo'
];

// Niveles de evaluación
function obtenerNivel($promedio) {
    if ($promedio >= 4.5) {
        return [
            'nivel' => 'Excelente',
            'clase' => 'success',
            'mensaje' => 'Tienes un dominio sobresaliente en esta habilidad.'
        ];
    } elseif ($promedio >= 3.5) {
        return [
            'nivel' => 'Bueno',
            'clase' => 'info',
            'mensaje' => 'Tienes un buen nivel en esta habilidad.'
        ];
    } elseif ($promedio >= 2.5) {
        return [
            'nivel' => 'Regular',
            'clase' => 'warning',
            'mensaje' => 'Tienes un nivel aceptable, pero hay áreas de mejora.'
        ];
    } else {
        return [
            'nivel' => 'Necesita mejorar',
            'clase' => 'danger',
            'mensaje' => 'Deberías trabajar en desarrollar esta habilidad.'
        ];
    }
}

// Configuración general
define('DATOS_DIR', __DIR__ . '/data');
define('TITULO_SISTEMA', 'Sistema de Evaluación de Habilidades Directivas');

// Crear directorio de datos si no existe
if (!file_exists(DATOS_DIR)) {
    mkdir(DATOS_DIR, 0777, true);
}

// =====================================================
// CONFIGURACIÓN DE BASE DE DATOS
// =====================================================

define('DB_HOST', 'localhost');
define('DB_NAME', 'habilidades_directivas');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// =====================================================
// FUNCIONES DE CONEXIÓN A BASE DE DATOS
// =====================================================

/**
 * Obtener conexión PDO a la base de datos
 * @return PDO
 */
function obtenerConexion() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $opciones = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
            ];
            
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $opciones);
        } catch (PDOException $e) {
            error_log("Error de conexión a BD: " . $e->getMessage());
            die("Error de conexión a la base de datos. Por favor, contacte al administrador.");
        }
    }
    
    return $pdo;
}

// =====================================================
// FUNCIONES DE SEGURIDAD
// =====================================================

/**
 * Iniciar sesión segura
 */
function iniciarSesionSegura() {
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_secure', 0); // Cambiar a 1 si usa HTTPS
        session_start();
    }
}

/**
 * Verificar si el usuario está autenticado
 * @return bool
 */
function estaAutenticado() {
    iniciarSesionSegura();
    return isset($_SESSION['usuario_id']) || isset($_SESSION['empresa_id']);
}

/**
 * Verificar si es un usuario (no empresa)
 * @return bool
 */
function esUsuario() {
    iniciarSesionSegura();
    return isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'usuario';
}

/**
 * Verificar si es una empresa
 * @return bool
 */
function esEmpresa() {
    iniciarSesionSegura();
    return isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'empresa';
}

/**
 * Obtener ID del usuario actual
 * @return int|null
 */
function obtenerUsuarioActual() {
    iniciarSesionSegura();
    return $_SESSION['usuario_id'] ?? null;
}

/**
 * Obtener ID de la empresa actual
 * @return int|null
 */
function obtenerEmpresaActual() {
    iniciarSesionSegura();
    return $_SESSION['empresa_id'] ?? null;
}

/**
 * Requerir autenticación de usuario
 */
function requerirUsuario() {
    if (!esUsuario()) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Requerir autenticación de empresa
 */
function requerirEmpresa() {
    if (!esEmpresa()) {
        header('Location: login.php?tipo=empresa');
        exit;
    }
}

/**
 * Cerrar sesión
 */
function cerrarSesion() {
    iniciarSesionSegura();
    
    // Registrar cierre de sesión
    if (isset($_SESSION['session_db_id'])) {
        try {
            $pdo = obtenerConexion();
            $stmt = $pdo->prepare("UPDATE sesiones SET activa = 0 WHERE id = ?");
            $stmt->execute([$_SESSION['session_db_id']]);
        } catch (Exception $e) {
            error_log("Error al cerrar sesión en BD: " . $e->getMessage());
        }
    }
    
    $_SESSION = [];
    
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    session_destroy();
}

/**
 * Sanitizar entrada de usuario
 * @param string $data
 * @return string
 */
function limpiarEntrada($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Generar token CSRF
 * @return string
 */
function generarTokenCSRF() {
    iniciarSesionSegura();
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verificar token CSRF
 * @param string $token
 * @return bool
 */
function verificarTokenCSRF($token) {
    iniciarSesionSegura();
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Registrar actividad en logs
 * @param string $tipo_usuario
 * @param int|null $usuario_id
 * @param int|null $empresa_id
 * @param string $accion
 * @param string|null $descripcion
 */
function registrarActividad($tipo_usuario, $usuario_id, $empresa_id, $accion, $descripcion = null) {
    try {
        $pdo = obtenerConexion();
        $stmt = $pdo->prepare("
            INSERT INTO logs_actividad (tipo_usuario, usuario_id, empresa_id, accion, descripcion, ip_address)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $stmt->execute([$tipo_usuario, $usuario_id, $empresa_id, $accion, $descripcion, $ip]);
    } catch (Exception $e) {
        error_log("Error al registrar actividad: " . $e->getMessage());
    }
}

// =====================================================
// FUNCIONES PARA CUESTIONARIOS
// =====================================================

/**
 * Obtener cuestionario en progreso del usuario
 * @param int $usuario_id
 * @return array|null
 */
function obtenerCuestionarioEnProgreso($usuario_id) {
    $pdo = obtenerConexion();
    $stmt = $pdo->prepare("
        SELECT * FROM cuestionarios 
        WHERE usuario_id = ? AND estado = 'en_progreso' 
        ORDER BY fecha_inicio DESC 
        LIMIT 1
    ");
    $stmt->execute([$usuario_id]);
    return $stmt->fetch();
}

/**
 * Crear nuevo cuestionario
 * @param int $usuario_id
 * @param int $empresa_id
 * @param int $total_preguntas
 * @return int ID del cuestionario creado
 */
function crearCuestionario($usuario_id, $empresa_id, $total_preguntas = 0) {
    $pdo = obtenerConexion();
    $stmt = $pdo->prepare("
        INSERT INTO cuestionarios (usuario_id, empresa_id, total_preguntas)
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$usuario_id, $empresa_id, $total_preguntas]);
    return $pdo->lastInsertId();
}

/**
 * Guardar progreso del cuestionario
 * @param int $usuario_id
 * @param int $cuestionario_id
 * @param int $paso_actual
 * @param array $respuestas
 */
function guardarProgreso($usuario_id, $cuestionario_id, $paso_actual, $respuestas) {
    $pdo = obtenerConexion();
    
    $respuestas_json = json_encode($respuestas, JSON_UNESCAPED_UNICODE);
    
    $stmt = $pdo->prepare("
        INSERT INTO progreso_cuestionario (usuario_id, cuestionario_id, paso_actual, respuestas_guardadas)
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            paso_actual = VALUES(paso_actual),
            respuestas_guardadas = VALUES(respuestas_guardadas)
    ");
    
    $stmt->execute([$usuario_id, $cuestionario_id, $paso_actual, $respuestas_json]);
}

// ============================================================================
// FUNCIONES DE ADMINISTRADOR
// ============================================================================

/**
 * Verificar si el usuario actual es administrador
 * @return bool
 */
function esAdmin() {
    return isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin';
}

/**
 * Obtener ID del administrador actual
 * @return int|null
 */
function obtenerAdminActual() {
    iniciarSesionSegura();
    return $_SESSION['admin_id'] ?? null;
}

/**
 * Requerir autenticación de administrador
 */
function requerirAdmin() {
    if (!esAdmin()) {
        header('Location: admin_login.php');
        exit;
    }
}

/**
 * Registrar actividad del administrador
 */
function registrarActividadAdmin($admin_id, $accion, $entidad_tipo, $entidad_id, $detalles) {
    try {
        $pdo = obtenerConexion();
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        
        $stmt = $pdo->prepare("
            INSERT INTO logs_admin (admin_id, accion, entidad_tipo, entidad_id, detalles, ip_address)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([$admin_id, $accion, $entidad_tipo, $entidad_id, $detalles, $ip]);
    } catch (Exception $e) {
        error_log("Error al registrar actividad admin: " . $e->getMessage());
    }
}

/**
 * Crear una nueva empresa (solo admin)
 */
function crearEmpresaAdmin($admin_id, $nombre, $email, $password, $rfc = null, $telefono = null) {
    $pdo = obtenerConexion();
    
    try {
        $pdo->beginTransaction();
        
        // Verificar si el email ya existe
        $stmt = $pdo->prepare("SELECT id FROM empresas WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            throw new Exception("El email ya está registrado");
        }
        
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("
            INSERT INTO empresas (nombre, email, password, rfc, telefono, creado_por_admin_id, activo)
            VALUES (?, ?, ?, ?, ?, ?, 1)
        ");
        
        $stmt->execute([$nombre, $email, $password_hash, $rfc, $telefono, $admin_id]);
        $empresa_id = $pdo->lastInsertId();
        
        // Registrar actividad
        registrarActividadAdmin($admin_id, 'crear_empresa', 'empresa', $empresa_id, 
            "Empresa creada: $nombre ($email)");
        
        $pdo->commit();
        return $empresa_id;
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

/**
 * Eliminar una empresa (solo admin)
 */
function eliminarEmpresa($admin_id, $empresa_id) {
    $pdo = obtenerConexion();
    
    try {
        // Obtener info de la empresa antes de eliminar
        $stmt = $pdo->prepare("SELECT nombre, email FROM empresas WHERE id = ?");
        $stmt->execute([$empresa_id]);
        $empresa = $stmt->fetch();
        
        if (!$empresa) {
            throw new Exception("Empresa no encontrada");
        }
        
        // Desactivar en lugar de eliminar (soft delete)
        $stmt = $pdo->prepare("UPDATE empresas SET activo = 0 WHERE id = ?");
        $stmt->execute([$empresa_id]);
        
        // Registrar actividad
        registrarActividadAdmin($admin_id, 'eliminar_empresa', 'empresa', $empresa_id,
            "Empresa desactivada: {$empresa['nombre']} ({$empresa['email']})");
        
        return true;
        
    } catch (Exception $e) {
        throw $e;
    }
}

/**
 * Eliminar un usuario (solo admin)
 */
function eliminarUsuario($admin_id, $usuario_id) {
    $pdo = obtenerConexion();
    
    try {
        // Obtener info del usuario antes de eliminar
        $stmt = $pdo->prepare("SELECT nombre, email FROM usuarios WHERE id = ?");
        $stmt->execute([$usuario_id]);
        $usuario = $stmt->fetch();
        
        if (!$usuario) {
            throw new Exception("Usuario no encontrado");
        }
        
        // Desactivar en lugar de eliminar (soft delete)
        $stmt = $pdo->prepare("UPDATE usuarios SET activo = 0 WHERE id = ?");
        $stmt->execute([$usuario_id]);
        
        // Registrar actividad
        registrarActividadAdmin($admin_id, 'eliminar_usuario', 'usuario', $usuario_id,
            "Usuario desactivado: {$usuario['nombre']} ({$usuario['email']})");
        
        return true;
        
    } catch (Exception $e) {
        throw $e;
    }
}

/**
 * Obtener todas las empresas para el panel admin
 */
function obtenerTodasEmpresas() {
    $pdo = obtenerConexion();
    $stmt = $pdo->query("
        SELECT 
            e.*,
            COUNT(DISTINCT u.id) as total_usuarios,
            COUNT(DISTINCT c.id) as total_cuestionarios,
            a.nombre as creado_por_admin
        FROM empresas e
        LEFT JOIN usuarios u ON e.id = u.empresa_id AND u.activo = 1
        LEFT JOIN cuestionarios c ON e.id = c.empresa_id AND c.estado = 'completado'
        LEFT JOIN administradores a ON e.creado_por_admin_id = a.id
        GROUP BY e.id
        ORDER BY e.activo DESC, e.fecha_registro DESC
    ");
    return $stmt->fetchAll();
}

/**
 * Obtener todos los usuarios para el panel admin
 */
function obtenerTodosUsuarios() {
    $pdo = obtenerConexion();
    $stmt = $pdo->query("
        SELECT 
            u.*,
            e.nombre as empresa_nombre,
            COUNT(c.id) as total_cuestionarios
        FROM usuarios u
        LEFT JOIN empresas e ON u.empresa_id = e.id
        LEFT JOIN cuestionarios c ON u.id = c.usuario_id AND c.estado = 'completado'
        GROUP BY u.id
        ORDER BY u.activo DESC, u.fecha_registro DESC
    ");
    return $stmt->fetchAll();
}

/**
 * Obtener estadísticas generales del sistema
 */
function obtenerEstadisticasSistema() {
    $pdo = obtenerConexion();
    $stmt = $pdo->query("SELECT * FROM vista_estadisticas_sistema");
    return $stmt->fetch();
}
?>

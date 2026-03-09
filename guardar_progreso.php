<?php
require_once 'config.php';
iniciarSesionSegura();
requerirUsuario();

header('Content-Type: application/json');

$usuario_id = obtenerUsuarioActual();
$cuestionario_id = intval($_POST['cuestionario_id'] ?? 0);
$paso_actual = intval($_POST['paso_actual'] ?? 0);

if (!$cuestionario_id) {
    echo json_encode(['success' => false, 'error' => 'Cuestionario no especificado']);
    exit;
}

try {
    $pdo = obtenerConexion();
    
    // Verificar que el cuestionario pertenece al usuario
    $stmt = $pdo->prepare("SELECT id FROM cuestionarios WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$cuestionario_id, $usuario_id]);
    
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'error' => 'Cuestionario no encontrado']);
        exit;
    }
    
    // Recopilar todas las respuestas del formulario
    $respuestas = [];
    foreach ($_POST as $key => $value) {
        if (strpos($key, '[') !== false && $key !== 'action') {
            $respuestas[$key] = $value;
        }
    }
    
    // Guardar progreso
    $respuestas_json = json_encode($respuestas, JSON_UNESCAPED_UNICODE);
    
    $stmt = $pdo->prepare("
        INSERT INTO progreso_cuestionario (usuario_id, cuestionario_id, paso_actual, respuestas_guardadas)
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            paso_actual = VALUES(paso_actual),
            respuestas_guardadas = VALUES(respuestas_guardadas),
            ultima_actualizacion = CURRENT_TIMESTAMP
    ");
    
    $stmt->execute([$usuario_id, $cuestionario_id, $paso_actual, $respuestas_json]);
    
    // Contar respuestas guardadas
    $total_respondidas = count($respuestas);
    
    // Actualizar contador en cuestionario
    $stmt = $pdo->prepare("UPDATE cuestionarios SET preguntas_respondidas = ? WHERE id = ?");
    $stmt->execute([$total_respondidas, $cuestionario_id]);
    
    echo json_encode([
        'success' => true,
        'respuestas_guardadas' => $total_respondidas,
        'paso_actual' => $paso_actual,
        'mensaje' => 'Progreso guardado correctamente'
    ]);
    
} catch (Exception $e) {
    error_log("Error al guardar progreso: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Error al guardar el progreso'
    ]);
}
?>

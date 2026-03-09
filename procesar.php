<?php
require_once 'config.php';
iniciarSesionSegura();
requerirUsuario();

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: formulario.php');
    exit;
}

$usuario_id = obtenerUsuarioActual();
$empresa_id = $_SESSION['empresa_id'];
$cuestionario_id = intval($_POST['cuestionario_id'] ?? 0);

if (!$cuestionario_id) {
    $_SESSION['error'] = 'Cuestionario no válido';
    header('Location: formulario.php');
    exit;
}

try {
    $pdo = obtenerConexion();
    
    // Verificar que el cuestionario pertenece al usuario
    $stmt = $pdo->prepare("SELECT * FROM cuestionarios WHERE id = ? AND usuario_id = ? AND estado = 'en_progreso'");
    $stmt->execute([$cuestionario_id, $usuario_id]);
    $cuestionario = $stmt->fetch();
    
    if (!$cuestionario) {
        $_SESSION['error'] = 'Cuestionario no encontrado o ya completado';
        header('Location: formulario.php');
        exit;
    }
    
    // Procesar respuestas por habilidad
    $resultados = [];
    $promedioGeneral = 0;
    $totalRespuestas = 0;
    
    // Iniciar transacción
    $pdo->beginTransaction();
    
    foreach ($habilidades as $habilidad) {
        $id_habilidad = $habilidad['id'];
        
        // Verificar que existan respuestas para esta habilidad
        if (!isset($_POST[$id_habilidad]) || !is_array($_POST[$id_habilidad])) {
            $pdo->rollBack();
            $_SESSION['error'] = 'Faltan respuestas para la habilidad: ' . $habilidad['nombre'];
            header('Location: formulario.php');
            exit;
        }
        
        $respuestas = $_POST[$id_habilidad];
        $suma = 0;
        $cantidad = count($respuestas);
        
        // Validar que todas las preguntas tengan respuesta
        if ($cantidad !== count($habilidad['preguntas'])) {
            $pdo->rollBack();
            $_SESSION['error'] = 'Debe responder todas las preguntas de: ' . $habilidad['nombre'];
            header('Location: formulario.php');
            exit;
        }
        
        // Guardar cada respuesta individual
        foreach ($respuestas as $idx => $respuesta) {
            $valor = intval($respuesta);
            if ($valor < 1 || $valor > 5) {
                $pdo->rollBack();
                $_SESSION['error'] = 'Valores de respuesta inválidos';
                header('Location: formulario.php');
                exit;
            }
            
            $suma += $valor;
            $totalRespuestas++;
            
            // Insertar respuesta en BD
            $stmt = $pdo->prepare("
                INSERT INTO respuestas (cuestionario_id, habilidad_id, habilidad_nombre, pregunta_index, pregunta_texto, valor_respuesta)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $cuestionario_id,
                $id_habilidad,
                $habilidad['nombre'],
                $idx,
                $habilidad['preguntas'][$idx],
                $valor
            ]);
        }
        
        // Calcular promedio de la habilidad
        $promedio = $suma / $cantidad;
        $promedioGeneral += $suma;
        
        // Obtener nivel de la habilidad
        $nivel = obtenerNivel($promedio);
        
        // Guardar resultado de habilidad
        $stmt = $pdo->prepare("
            INSERT INTO resultados_habilidades 
            (cuestionario_id, habilidad_id, habilidad_nombre, habilidad_descripcion, promedio, nivel, clase, mensaje)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $cuestionario_id,
            $id_habilidad,
            $habilidad['nombre'],
            $habilidad['descripcion'],
            round($promedio, 2),
            $nivel['nivel'],
            $nivel['clase'],
            $nivel['mensaje']
        ]);
        
        $resultados[$id_habilidad] = [
            'nombre' => $habilidad['nombre'],
            'descripcion' => $habilidad['descripcion'],
            'promedio' => round($promedio, 2),
            'nivel' => $nivel['nivel'],
            'clase' => $nivel['clase'],
            'mensaje' => $nivel['mensaje']
        ];
    }
    
    // Calcular promedio general
    $promedioGeneral = $promedioGeneral / $totalRespuestas;
    $nivelGeneral = obtenerNivel($promedioGeneral);
    
    // Actualizar cuestionario como completado
    $stmt = $pdo->prepare("
        UPDATE cuestionarios
        SET estado = 'completado',
            fecha_completado = NOW(),
            promedio_general = ?,
            nivel_general = ?,
            total_preguntas = ?,
            preguntas_respondidas = ?
        WHERE id = ?
    ");
    
    $stmt->execute([
        round($promedioGeneral, 2),
        $nivelGeneral['nivel'],
        $totalRespuestas,
        $totalRespuestas,
        $cuestionario_id
    ]);
    
    // Eliminar progreso temporal
    $stmt = $pdo->prepare("DELETE FROM progreso_cuestionario WHERE cuestionario_id = ?");
    $stmt->execute([$cuestionario_id]);
    
    // Commit transacción
    $pdo->commit();
    
    // Registrar actividad
    registrarActividad('usuario', $usuario_id, $empresa_id, 'cuestionario_completado', "Cuestionario #$cuestionario_id completado");
    
    // Guardar ID en sesión para confirmación
    $_SESSION['cuestionario_completado'] = $cuestionario_id;
    $_SESSION['success'] = 'Tu evaluación ha sido enviada exitosamente.';
    
    // Redirigir a página de confirmación (los usuarios NO ven resultados)
    header('Location: confirmacion.php');
    exit;
    
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    // Log detallado del error
    $errorMsg = "Error al procesar cuestionario: " . $e->getMessage();
    $errorMsg .= "\nArchivo: " . $e->getFile();
    $errorMsg .= "\nLínea: " . $e->getLine();
    $errorMsg .= "\nStack trace: " . $e->getTraceAsString();
    error_log($errorMsg);
    
    $_SESSION['error'] = 'Error al procesar el cuestionario: ' . $e->getMessage();
    header('Location: formulario.php');
    exit;
}
?>

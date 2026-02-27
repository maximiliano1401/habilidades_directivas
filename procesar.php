<?php
require_once 'config.php';
session_start();

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: formulario.php');
    exit;
}

// Obtener datos personales
$nombre = htmlspecialchars(trim($_POST['nombre'] ?? ''));
$email = htmlspecialchars(trim($_POST['email'] ?? ''));

// Validar datos personales
if (empty($nombre) || empty($email)) {
    $_SESSION['error'] = 'Debe completar nombre y correo electrónico';
    header('Location: formulario.php');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = 'El correo electrónico no es válido';
    header('Location: formulario.php');
    exit;
}

// Procesar respuestas por habilidad
$resultados = [];
$promedioGeneral = 0;
$totalRespuestas = 0;

foreach ($habilidades as $habilidad) {
    $id_habilidad = $habilidad['id'];
    
    // Verificar que existan respuestas para esta habilidad
    if (!isset($_POST[$id_habilidad]) || !is_array($_POST[$id_habilidad])) {
        $_SESSION['error'] = 'Faltan respuestas para la habilidad: ' . $habilidad['nombre'];
        header('Location: formulario.php');
        exit;
    }
    
    $respuestas = $_POST[$id_habilidad];
    $suma = 0;
    $cantidad = count($respuestas);
    
    // Validar que todas las preguntas tengan respuesta
    if ($cantidad !== count($habilidad['preguntas'])) {
        $_SESSION['error'] = 'Debe responder todas las preguntas de: ' . $habilidad['nombre'];
        header('Location: formulario.php');
        exit;
    }
    
    // Calcular promedio de la habilidad
    foreach ($respuestas as $respuesta) {
        $valor = intval($respuesta);
        if ($valor < 1 || $valor > 5) {
            $_SESSION['error'] = 'Valores de respuesta inválidos';
            header('Location: formulario.php');
            exit;
        }
        $suma += $valor;
        $totalRespuestas++;
    }
    
    $promedio = $suma / $cantidad;
    $promedioGeneral += $suma;
    
    // Obtener nivel de la habilidad
    $nivel = obtenerNivel($promedio);
    
    $resultados[$id_habilidad] = [
        'nombre' => $habilidad['nombre'],
        'descripcion' => $habilidad['descripcion'],
        'respuestas' => $respuestas,
        'promedio' => round($promedio, 2),
        'nivel' => $nivel['nivel'],
        'clase' => $nivel['clase'],
        'mensaje' => $nivel['mensaje']
    ];
}

// Calcular promedio general
$promedioGeneral = $promedioGeneral / $totalRespuestas;
$nivelGeneral = obtenerNivel($promedioGeneral);

// Preparar datos para guardar
$evaluacion = [
    'id' => uniqid(),
    'fecha' => date('Y-m-d H:i:s'),
    'nombre' => $nombre,
    'email' => $email,
    'resultados' => $resultados,
    'promedio_general' => round($promedioGeneral, 2),
    'nivel_general' => $nivelGeneral['nivel'],
    'total_preguntas' => $totalRespuestas
];

// Guardar en archivo JSON
$archivo = DATOS_DIR . '/evaluacion_' . $evaluacion['id'] . '.json';
file_put_contents($archivo, json_encode($evaluacion, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// Guardar ID en sesión para mostrar resultados
$_SESSION['evaluacion_id'] = $evaluacion['id'];
$_SESSION['evaluacion'] = $evaluacion;

// Redirigir a resultados
header('Location: resultados.php?id=' . $evaluacion['id']);
exit;
?>

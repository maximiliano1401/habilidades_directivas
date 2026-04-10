<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../vendor/autoload.php';

iniciarSesionSegura();
requerirEmpresa();

use Dompdf\Dompdf;
use Dompdf\Options;

$empresa_id = obtenerEmpresaActual();
$cuestionario_id = intval($_GET['id'] ?? 0);

if (!$cuestionario_id) {
    header('Location: dashboard.php');
    exit;
}

$pdo = obtenerConexion();

// Verificar que el cuestionario pertenece a la empresa
$stmt = $pdo->prepare("
    SELECT 
        c.*,
        u.nombre AS usuario_nombre,
        u.email AS usuario_email,
        u.puesto AS usuario_puesto,
        u.departamento AS usuario_departamento,
        e.nombre AS empresa_nombre
    FROM cuestionarios c
    INNER JOIN usuarios u ON c.usuario_id = u.id
    INNER JOIN empresas e ON c.empresa_id = e.id
    WHERE c.id = ? AND c.empresa_id = ? AND c.estado = 'completado'
");
$stmt->execute([$cuestionario_id, $empresa_id]);
$cuestionario = $stmt->fetch();

if (!$cuestionario) {
    header('Location: dashboard.php');
    exit;
}

// Obtener resultados por habilidad
$stmt = $pdo->prepare("
    SELECT * FROM resultados_habilidades 
    WHERE cuestionario_id = ? 
    ORDER BY promedio DESC
");
$stmt->execute([$cuestionario_id]);
$resultados = $stmt->fetchAll();

$fortalezas = array_filter($resultados, function($r) { return $r['promedio'] >= 4.0; });
$areas_mejora = array_filter($resultados, function($r) { return $r['promedio'] < 3.5; });

// Generar HTML del PDF
$html = generarHTMLPdf($cuestionario, $resultados, $fortalezas, $areas_mejora);

// Configurar DomPDF
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', false);
$options->set('defaultFont', 'Helvetica');

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Nombre del archivo
$nombre_archivo = 'evaluacion_' . preg_replace('/[^a-zA-Z0-9]/', '_', $cuestionario['usuario_nombre']) . '_' . date('Ymd') . '.pdf';

$dompdf->stream($nombre_archivo, ['Attachment' => true]);
exit;

function generarHTMLPdf($cuestionario, $resultados, $fortalezas, $areas_mejora) {
    $fecha_completado = date('d/m/Y H:i', strtotime($cuestionario['fecha_completado']));
    $fecha_generacion = date('d/m/Y H:i');
    $promedio = number_format($cuestionario['promedio_general'], 2);
    $nivel = htmlspecialchars($cuestionario['nivel_general']);
    
    // Colores para niveles
    $nivel_color = '#6B7280';
    if ($cuestionario['promedio_general'] >= 4.5) $nivel_color = '#10B981';
    elseif ($cuestionario['promedio_general'] >= 3.5) $nivel_color = '#3B82F6';
    elseif ($cuestionario['promedio_general'] >= 2.5) $nivel_color = '#F39C12';
    else $nivel_color = '#EF4444';

    $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Helvetica, Arial, sans-serif; color: #2C3E50; font-size: 11px; line-height: 1.5; }
        
        .header { background: #2C3E50; color: white; padding: 25px 30px; margin-bottom: 20px; }
        .header h1 { font-size: 20px; margin-bottom: 3px; color: #F39C12; }
        .header p { font-size: 11px; opacity: 0.9; }
        
        .container { padding: 0 30px; }
        
        .info-section { margin-bottom: 20px; }
        .info-row { display: table; width: 100%; margin-bottom: 15px; }
        .info-col { display: table-cell; vertical-align: top; }
        .info-col.left { width: 60%; }
        .info-col.right { width: 40%; text-align: right; }
        
        .info-label { font-weight: bold; color: #6B7280; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; }
        .info-value { font-size: 12px; margin-bottom: 6px; }
        
        .score-box { background: #2C3E50; color: white; padding: 15px 20px; border-radius: 6px; text-align: center; display: inline-block; }
        .score-number { font-size: 32px; font-weight: bold; color: #F39C12; }
        .score-label { font-size: 10px; opacity: 0.8; }
        .score-nivel { display: inline-block; background: ' . $nivel_color . '; color: white; padding: 3px 12px; border-radius: 12px; font-size: 11px; font-weight: bold; margin-top: 5px; }
        
        h2 { font-size: 15px; color: #2C3E50; border-bottom: 2px solid #F39C12; padding-bottom: 5px; margin: 20px 0 12px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th { background: #2C3E50; color: white; padding: 8px 10px; text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; }
        td { padding: 8px 10px; border-bottom: 1px solid #E5E7EB; font-size: 11px; }
        tr:nth-child(even) { background: #F8F9FA; }
        
        .nivel-excelente { color: #10B981; font-weight: bold; }
        .nivel-bueno { color: #3B82F6; font-weight: bold; }
        .nivel-regular { color: #F39C12; font-weight: bold; }
        .nivel-mejorar { color: #EF4444; font-weight: bold; }
        
        .promedio-bar { background: #E5E7EB; border-radius: 4px; height: 10px; width: 100px; display: inline-block; vertical-align: middle; margin-right: 8px; }
        .promedio-fill { height: 10px; border-radius: 4px; }
        
        .fortaleza-item { padding: 8px 12px; background: #ECFDF5; border-left: 3px solid #10B981; margin-bottom: 6px; border-radius: 0 4px 4px 0; }
        .mejora-item { padding: 8px 12px; background: #FEF3C7; border-left: 3px solid #F39C12; margin-bottom: 6px; border-radius: 0 4px 4px 0; }
        
        .footer { text-align: center; color: #9CA3AF; font-size: 9px; margin-top: 25px; padding-top: 10px; border-top: 1px solid #E5E7EB; }
        
        .page-break { page-break-before: always; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Evaluaci&oacute;n de Habilidades Directivas</h1>
        <p>' . htmlspecialchars($cuestionario['empresa_nombre']) . ' | Generado el ' . $fecha_generacion . '</p>
    </div>
    
    <div class="container">
        <div class="info-row">
            <div class="info-col left">
                <div class="info-label">Datos del Evaluado</div>
                <div class="info-value"><strong>' . htmlspecialchars($cuestionario['usuario_nombre']) . '</strong></div>
                <div class="info-value">' . htmlspecialchars($cuestionario['usuario_email']) . '</div>
                <div class="info-value">Puesto: ' . htmlspecialchars($cuestionario['usuario_puesto'] ?? 'No especificado') . '</div>
                <div class="info-value">Departamento: ' . htmlspecialchars($cuestionario['usuario_departamento'] ?? 'No especificado') . '</div>
                <div class="info-value">Fecha de evaluaci&oacute;n: ' . $fecha_completado . '</div>
            </div>
            <div class="info-col right">
                <div class="score-box">
                    <div class="score-label">CALIFICACI&Oacute;N GENERAL</div>
                    <div class="score-number">' . $promedio . '</div>
                    <div class="score-label">de 5.00</div>
                    <div class="score-nivel">' . $nivel . '</div>
                </div>
            </div>
        </div>

        <h2>Resultados por Habilidad</h2>
        <table>
            <thead>
                <tr>
                    <th style="width: 25%;">Habilidad</th>
                    <th style="width: 30%;">Descripci&oacute;n</th>
                    <th style="width: 20%;">Promedio</th>
                    <th style="width: 12%;">Nivel</th>
                    <th style="width: 13%;">Estado</th>
                </tr>
            </thead>
            <tbody>';
    
    foreach ($resultados as $r) {
        $porcentaje = ($r['promedio'] / 5) * 100;
        $color_barra = '#6B7280';
        $clase_nivel = 'nivel-mejorar';
        
        if ($r['promedio'] >= 4.5) { $color_barra = '#10B981'; $clase_nivel = 'nivel-excelente'; }
        elseif ($r['promedio'] >= 3.5) { $color_barra = '#3B82F6'; $clase_nivel = 'nivel-bueno'; }
        elseif ($r['promedio'] >= 2.5) { $color_barra = '#F39C12'; $clase_nivel = 'nivel-regular'; }
        else { $color_barra = '#EF4444'; $clase_nivel = 'nivel-mejorar'; }
        
        $html .= '
                <tr>
                    <td><strong>' . htmlspecialchars($r['habilidad_nombre']) . '</strong></td>
                    <td>' . htmlspecialchars($r['habilidad_descripcion']) . '</td>
                    <td>
                        <div class="promedio-bar">
                            <div class="promedio-fill" style="width: ' . $porcentaje . '%; background: ' . $color_barra . ';"></div>
                        </div>
                        ' . number_format($r['promedio'], 2) . '
                    </td>
                    <td><span class="' . $clase_nivel . '">' . htmlspecialchars($r['nivel']) . '</span></td>
                    <td>' . htmlspecialchars($r['mensaje']) . '</td>
                </tr>';
    }
    
    $html .= '
            </tbody>
        </table>';
    
    // Fortalezas
    if (count($fortalezas) > 0) {
        $html .= '
        <h2>Fortalezas Identificadas</h2>';
        foreach ($fortalezas as $f) {
            $html .= '
        <div class="fortaleza-item">
            <strong>' . htmlspecialchars($f['habilidad_nombre']) . '</strong> - ' . number_format($f['promedio'], 2) . '/5.00 (' . htmlspecialchars($f['nivel']) . ')
            <br><span style="color: #6B7280;">' . htmlspecialchars($f['habilidad_descripcion']) . '</span>
        </div>';
        }
    }
    
    // Áreas de mejora
    if (count($areas_mejora) > 0) {
        $html .= '
        <h2>&Aacute;reas de Oportunidad</h2>';
        foreach ($areas_mejora as $am) {
            $html .= '
        <div class="mejora-item">
            <strong>' . htmlspecialchars($am['habilidad_nombre']) . '</strong> - ' . number_format($am['promedio'], 2) . '/5.00 (' . htmlspecialchars($am['nivel']) . ')
            <br><span style="color: #6B7280;">' . htmlspecialchars($am['mensaje']) . '</span>
        </div>';
        }
    }
    
    $html .= '
        <div class="footer">
            <p>Este reporte fue generado autom&aacute;ticamente por el Sistema de Evaluaci&oacute;n de Habilidades Directivas.</p>
            <p>' . htmlspecialchars($cuestionario['empresa_nombre']) . ' | ' . $fecha_generacion . ' | Cuestionario #' . $cuestionario['id'] . '</p>
        </div>
    </div>
</body>
</html>';
    
    return $html;
}

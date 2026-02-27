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
?>

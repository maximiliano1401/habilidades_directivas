# Sistema de Evaluación de Habilidades Directivas

Sistema web para evaluar y analizar 11 habilidades directivas clave mediante un cuestionario con escala Likert.

## 🎯 Características

- **Landing Page** informativa sobre habilidades directivas
- **Formulario de evaluación** con 49 preguntas (escala Likert 1-5)
- **Análisis detallado** de resultados por habilidad
- **Gráfico de radar** para visualización del perfil
- **Recomendaciones personalizadas** según los resultados
- **Almacenamiento en JSON** de todas las evaluaciones
- **Diseño responsive** con Bootstrap 5
- **Interfaz moderna** y fácil de usar

## 📋 Habilidades Evaluadas

1. **Técnicas** - Desarrollar tareas específicas
2. **Interpersonales** - Trabajo en equipo y colaboración
3. **Sociales** - Convivencia e intercambio humano
4. **Académicas** - Análisis, evaluación y pensamiento crítico
5. **De Innovación** - Creatividad y nuevas ideas
6. **Prácticas** - Aplicación e implementación
7. **Físicas** - Salud y bienestar
8. **De Pensamiento** - Generar conocimiento
9. **Directivas** - Dirigir y coordinar equipos
10. **De Liderazgo** - Motivar e inspirar
11. **Empresariales** - Emprendimiento

## 🚀 Instalación

### Requisitos
- XAMPP (Apache + PHP 7.4 o superior)
- Navegador web moderno

### Pasos de instalación

1. Copia la carpeta `habilidades_directivas` en `C:\xampp\htdocs\`

2. Inicia XAMPP y activa Apache

3. Accede al sistema en tu navegador:
   ```
   http://localhost/habilidades_directivas/
   ```

## 📁 Estructura de Archivos

```
habilidades_directivas/
│
├── index.php              # Landing page principal
├── formulario.php         # Cuestionario de evaluación
├── procesar.php           # Procesa y guarda respuestas
├── resultados.php         # Muestra análisis y resultados
├── config.php             # Configuración y definiciones
├── README.md              # Este archivo
│
└── data/                  # Almacena evaluaciones en JSON
    └── evaluacion_*.json  # Archivos de evaluaciones
```

## 💻 Uso del Sistema

### Para el Usuario

1. **Inicio**: Accede a la página principal para conocer sobre las habilidades directivas

2. **Comenzar evaluación**: Click en "Comenzar Evaluación"

3. **Datos personales**: Completa nombre y correo electrónico

4. **Responder cuestionario**: Evalúa cada afirmación del 1 al 5:
   - 1 = Totalmente en desacuerdo
   - 2 = En desacuerdo
   - 3 = Neutral
   - 4 = De acuerdo
   - 5 = Totalmente de acuerdo

5. **Enviar**: Una vez completado, envía el formulario

6. **Ver resultados**: Analiza tu perfil de habilidades con:
   - Puntuación general
   - Gráfico de radar
   - Detalle por habilidad
   - Fortalezas identificadas
   - Áreas de mejora
   - Recomendaciones personalizadas

7. **Imprimir**: Opción para imprimir los resultados

## 📊 Sistema de Evaluación

### Escala de Calificación
- **Excelente** (4.5 - 5.0): Dominio sobresaliente
- **Bueno** (3.5 - 4.4): Buen nivel
- **Regular** (2.5 - 3.4): Nivel aceptable con áreas de mejora
- **Necesita mejorar** (1.0 - 2.4): Requiere desarrollo

### Cálculo de Resultados
- Se calcula el promedio de respuestas por cada habilidad
- El promedio general es la media de todas las respuestas
- Las fortalezas son habilidades con promedio ≥ 4.0
- Las áreas de mejora son habilidades con promedio < 3.5

## 🔧 Personalización

### Modificar preguntas
Edita el archivo `config.php` en la sección `$habilidades`:

```php
'preguntas' => [
    'Tu nueva pregunta aquí',
    // ... más preguntas
]
```

### Cambiar escala de evaluación
Modifica `$escala_likert` en `config.php`

### Ajustar niveles
Edita la función `obtenerNivel()` en `config.php`

## 📦 Almacenamiento de Datos

Los resultados se guardan en archivos JSON en la carpeta `data/`:
- Cada evaluación tiene un ID único
- Formato: `evaluacion_[ID].json`
- Incluye: datos personales, respuestas, promedios y niveles

## 🔜 Funcionalidades Futuras

- ✅ Generación de PDF con resultados
- ✅ Envío de resultados por correo electrónico
- ✅ Panel de administración
- ✅ Comparativa de resultados entre evaluaciones
- ✅ Estadísticas generales
- ✅ Base de datos MySQL (opcional)

## 🎨 Tecnologías Utilizadas

- **PHP 7.4+**: Backend y lógica del sistema
- **Bootstrap 5**: Framework CSS para diseño responsive
- **Chart.js**: Gráficos interactivos
- **Bootstrap Icons**: Iconografía moderna
- **JSON**: Almacenamiento de datos

## 📝 Notas Importantes

- Los datos se guardan localmente en archivos JSON
- No hay límite de evaluaciones (solo espacio en disco)
- Sistema pensado para uso individual o pequeños grupos
- Para uso empresarial, considerar implementar base de datos

## 🤝 Soporte

Para dudas o mejoras al sistema, puedes:
- Revisar el código fuente
- Modificar según necesidades específicas
- Extender funcionalidades

## 📄 Licencia

Sistema educativo de código abierto para evaluación de habilidades directivas.

---

**Versión**: 1.0  
**Fecha**: Febrero 2026  
**Desarrollado con**: PHP + Bootstrap

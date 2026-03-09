# 🎯 Sistema de Evaluación de Habilidades Directivas v2.0

Sistema web completo para evaluar y analizar 11 habilidades directivas clave mediante cuestionarios con escala Likert, ahora con autenticación de usuarios, base de datos MySQL y autoguardado de progreso.

> **⚠️ IMPORTANTE:** Los resultados de las evaluaciones **solo son visibles para las empresas**. Los usuarios que completan las evaluaciones reciben una confirmación de envío pero NO pueden ver sus propios resultados. Esto asegura que el departamento de Recursos Humanos o la Dirección revise los resultados antes de compartirlos.

---

## ✨ Características Principales

### �️ **Panel de Administración** (NUEVO)
- **Acceso exclusivo** para administradores del sistema
- **Gestión de empresas**: Crear y desactivar empresas
- **Gestión de usuarios**: Desactivar usuarios cuando sea necesario
- **Estadísticas globales**: Vista general de todo el sistema
- **Logs de auditoría**: Registro completo de todas las acciones administrativas
- **Seguridad reforzada**: Login separado, todas las acciones son monitoreadas
- 📖 **[Ver documentación completa del Admin Panel](ADMIN.md)**

### 🔐 **Sistema de Autenticación**
- Inicio de sesión obligatorio para usuarios y empresas
- Registro de usuarios (público)
- **Registro de empresas solo por administrador** (cambio de seguridad)
- Gestión segura de sesiones
- Roles diferenciados (Usuario/Empresa/Administrador)

### 📊 **Evaluación Completa**
- **Cuestionario interactivo** con 49 preguntas en escala Likert (1-5)
- **Autoguardado automático** del progreso cada 2 segundos
- **Continuación automática** desde donde se dejó
- **Confirmación de envío** al completar el cuestionario
- Los resultados **solo son visibles para las empresas** (no para los usuarios)

### 🏢 **Dashboard para Empresas**
- Panel de control con estadísticas generales
- Visualización de cuestionarios completados
- Monitoreo de evaluaciones en progreso
- **Análisis detallado de resultados** por habilidad
- **Gráficos interactivos** (radar y barras) con Chart.js
- **Vista detallada de resultados individuales** por empleado
- **Recomendaciones personalizadas** para cada evaluación

### 💾 **Base de Datos MySQL**
- Almacenamiento robusto y escalable
- Relaciones entre usuarios, empresas y cuestionarios
- Logs de auditoría completos
- Backups y recuperación de datos
- Consultas optimizadas con índices

### 🎨 **Interfaz Moderna**
- Diseño responsive con Bootstrap 5
- Compatible con todos los dispositivos
- Experiencia de usuario intuitiva
- Feedback visual en tiempo real

---

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

---

## 🚀 Instalación Rápida

### Requisitos Previos
- **XAMPP** (Apache + PHP 7.4+ + MySQL 5.7+)
- Navegador web moderno

### Pasos de Instalación

1. **Descargar e instalar XAMPP**
   ```
   https://www.apachefriends.org/
   ```

2. **Copiar archivos del sistema**
   ```
   Copiar carpeta a: C:\xampp\htdocs\alta_direccion\habilidades_directivas
   ```

3. **Crear la base de datos**
   - Abrir http://localhost/phpmyadmin
   - Ir a la pestaña "SQL"
   - Ejecutar el contenido de `database.sql`

4. **Configurar conexión** (si es necesario)
   - Editar `config.php` líneas 180-184
   - Ajustar credenciales de MySQL

5. **Acceder al sistema**
   ```
   http://localhost/alta_direccion/habilidades_directivas/
   ```

📖 **Para instrucciones detalladas, consulta [INSTALL.md](INSTALL.md)**

---

## 🔐 Credenciales de Prueba

### Administrador del Sistema 🛡️
```
URL: http://localhost/alta_direccion/habilidades_directivas/admin_login.php
Email: admin@sistema.com
Contraseña: admin123
⚠️ CAMBIA ESTA CONTRASEÑA en producción
```

### Usuarios
```
Email: juan.perez@demo.com
Contraseña: usuario123

Email: maria.gonzalez@demo.com
Contraseña: usuario123
```

### Empresas
```
Email: empresa@demo.com
Contraseña: empresa123

Email: contacto@ejemplo.com
Contraseña: empresa123
```

> **Nota:** Las empresas ya NO se pueden registrar desde el login público.  
> Solo el administrador puede crear nuevas empresas desde el panel de administración.

---

## 📁 Estructura del Proyecto

```
habilidades_directivas/
│
├── 📄 config.php                 # Configuración y funciones principales
├── 📄 database.sql               # Script de creación de BD
├── 📄 admin_setup.sql            # Script para sistema de administración
├── 📄 index.php                  # Página principal/landing
│
├── 🔐 AUTENTICACIÓN
│   ├── login.php                 # Inicio de sesión (usuarios y empresas)
│   ├── registro_usuario.php     # Registro de usuarios
│   └── logout.php                # Cierre de sesión
│
├── 🛡️ ADMINISTRACIÓN
│   ├── admin_login.php           # Login exclusivo para administradores
│   ├── admin_panel.php           # Panel de control administrativo
│   └── admin_acciones.php        # Procesamiento de acciones CRUD
│
├── 📝 CUESTIONARIO
│   ├── formulario.php           # Formulario de evaluación
│   ├── procesar.php             # Procesamiento de respuestas
│   ├── guardar_progreso.php     # Endpoint de autoguardado
│   └── confirmacion.php         # Confirmación de envío
│
├── 🏢 EMPRESA
│   ├── dashboard_empresa.php    # Panel de control empresa
│   ├── ver_resultado.php        # Vista detallada de resultado
│   └── mis_resultados.php       # Visualización de resultados (solo empresas)
│
├── 📁 data/                      # Carpeta de datos (legacy)
│
└── 📚 DOCUMENTACIÓN
    ├── README.md                 # Este archivo
    ├── INSTALL.md                # Guía de instalación
    ├── ADMIN.md                  # Guía del panel de administración
    ├── DOCUMENTACION.md          # Documentación técnica completa
    └── DEBUG.md                  # Guía de depuración
```

---

## 🔄 Flujo de Trabajo

### Para Usuarios (Empleados)

```
1. Registro/Login → 2. Formulario → 3. Responder preguntas
                                          ↓ (autoguardado cada 2s)
                                    4. Enviar evaluación
                                          ↓
                                    5. Confirmación de envío
                                          ↓
                                    ⚠️ Los resultados NO son visibles para el usuario
                                    ✅ Solo la empresa puede ver los resultados
```

### Para Empresas (RR.HH / Dirección)

```
1. Registro/Login → 2. Dashboard → 3. Ver cuestionarios completados
                                          ↓
                                    4. Ver resultados detallados
                                          ↓
                    ← 6. Gráficos ← 5. Análisis por habilidad
```

---

## 🗄️ Base de Datos

### Tablas Principales

- **usuarios** - Información de usuarios
- **empresas** - Información de empresas
- **cuestionarios** - Instancias de evaluaciones
- **respuestas** - Respuestas individuales
- **resultados_habilidades** - Resultados calculados
- **sesiones** - Control de sesiones
- **progreso_cuestionario** - Autoguardado
- **logs_actividad** - Auditoría del sistema

### Vistas y Procedimientos

- `vista_cuestionarios_empresa` - Resumen por empresa
- `vista_evaluaciones_usuario` - Resumen por usuario
- `obtener_estadisticas_empresa()` - Estadísticas completas
- `limpiar_sesiones_expiradas()` - Mantenimiento

📖 **Para más detalles, consulta [DOCUMENTACION.md](DOCUMENTACION.md)**

---

## 🔒 Seguridad

### Medidas Implementadas

- ✅ Autenticación obligatoria
- ✅ Contraseñas hasheadas con bcrypt
- ✅ Prepared statements (protección SQL Injection)
- ✅ Sanitización de entradas (protección XSS)
- ✅ Sesiones seguras con cookies HttpOnly
- ✅ Control de acceso por roles
- ✅ Logs de auditoría completos
- ✅ Validación de permisos en cada acción

---

## 🎨 Tecnologías Utilizadas

| Tecnología | Versión | Uso |
|------------|---------|-----|
| PHP | 7.4+ | Backend y lógica de negocio |
| MySQL | 5.7+ | Base de datos |
| Bootstrap | 5.3 | Framework CSS |
| Chart.js | 4.4 | Gráficos interactivos |
| JavaScript | ES6+ | Interactividad y AJAX |
| Bootstrap Icons | 1.11 | Iconografía |
| Google Fonts | - | Tipografía (Inter) |

---

## 📊 Funcionalidades Destacadas

### Autoguardado Inteligente

- Guarda automáticamente cada 2 segundos
- Evita pérdida de datos si se cierra el navegador
- Carga automática del progreso al volver
- Indicador visual del estado de guardado

### Dashboard Empresarial

- Estadísticas en tiempo real
- Gráficos de barras por habilidad
- Lista de evaluaciones completadas
- Monitoreo de progreso activo
- Filtros y búsquedas

### Análisis de Resultados

- Gráfico radar personalizado
- Identificación automática de fortalezas
- Detección de áreas de oportunidad
- Recomendaciones específicas
- Reporte imprimible

---

## 🆕 Novedades v2.0

### Migración de JSON a MySQL

**Antes (v1.0):**
- Datos guardados en archivos JSON
- Sin autenticación
- Sin autoguardado
- Sin roles

**Ahora (v2.0):**
- ✅ Base de datos MySQL
- ✅ Sistema de autenticación completo
- ✅ Autoguardado automático
- ✅ Roles de usuario y empresa
- ✅ Dashboard empresarial
- ✅ Logs de auditoría
- ✅ Sesiones seguras

---

## 🐛 Solución de Problemas

### Error de conexión a BD
```
Verificar:
1. MySQL está corriendo en XAMPP
2. Credenciales en config.php son correctas
3. Base de datos habilidades_directivas existe
```

### El autoguardado no funciona
```
Verificar:
1. Archivo guardar_progreso.php existe
2. Abrir consola del navegador (F12) y buscar errores
3. Tabla progreso_cuestionario existe en BD
```

### Sesiones se cierran rápido
```
Solución:
Ajustar tiempo de expiración en login.php línea 53:
$fecha_expiracion = date('Y-m-d H:i:s', time() + 3600 * 24); // 24 horas
```

📖 **Para más soluciones, consulta [INSTALL.md](INSTALL.md#-solución-de-problemas-comunes)**

---

## 📈 Estadísticas del Sistema

- **11** habilidades directivas evaluadas
- **49** criterios de evaluación
- **5** niveles en escala Likert
- **3** tipos de gráficos (radar, barras, progreso)
- **8** tablas en base de datos
- **3** procedimientos almacenados
- **2** roles de usuario (Usuario/Empresa)

---

## 🔐 Arquitectura de Seguridad

```
┌─────────────────────────────────────┐
│         CAPA DE PRESENTACIÓN         │ → Validación Frontend
├─────────────────────────────────────┤
│         CAPA DE AUTENTICACIÓN        │ → Verificación de Sesiones
├─────────────────────────────────────┤
│         CAPA DE NEGOCIO              │ → Validación y Sanitización
├─────────────────────────────────────┤
│         CAPA DE ACCESO A DATOS       │ → Prepared Statements
├─────────────────────────────────────┤
│         BASE DE DATOS                │ → Constraints y Triggers
└─────────────────────────────────────┘
```

---

## 📝 Mantenimiento

### Tareas Recomendadas

**Diarias:**
- Revisar logs de errores

**Semanales:**
- Backup de base de datos
- Limpiar sesiones expiradas

**Mensuales:**
- Optimizar tablas MySQL
- Analizar estadísticas

---

## 🤝 Contribuciones

Este sistema fue desarrollado como parte de un proyecto educativo/empresarial.

Para sugerencias o reportar problemas:
1. Revisa la documentación completa
2. Verifica que el problema no esté en la sección de solución de problemas
3. Contacta al administrador del sistema

---

## 📄 Licencia

Sistema desarrollado para fines educativos y empresariales.

---

## 📚 Documentación Adicional

- 📖 [INSTALL.md](INSTALL.md) - Guía completa de instalación
- 📖 [DOCUMENTACION.md](DOCUMENTACION.md) - Documentación técnica detallada

---

## 📞 Información del Sistema

- **Versión:** 2.0
- **Fecha:** Marzo 2026
- **PHP Min:** 7.4
- **MySQL Min:** 5.7

---

## ⚡ Quick Start

```bash
# 1. Instalar XAMPP
# 2. Copiar archivos a: C:\xampp\htdocs\alta_direccion\habilidades_directivas
# 3. Crear BD ejecutando database.sql en phpMyAdmin
# 4. Acceder a: http://localhost/alta_direccion/habilidades_directivas/
# 5. Login con: juan.perez@demo.com / usuario123
```

---

**¡Sistema listo para usar! 🎉**

Para dudas o problemas, consulta la [Documentación Completa](DOCUMENTACION.md) o la [Guía de Instalación](INSTALL.md).

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

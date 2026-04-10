# 📘 Documentación Técnica Completa
## Sistema de Evaluación de Habilidades Directivas v2.0

---

## 📝 Índice

1. [Resumen de Cambios](#resumen-de-cambios)
2. [Arquitectura del Sistema](#arquitectura-del-sistema)
3. [Base de Datos](#base-de-datos)
4. [Archivos del Sistema](#archivos-del-sistema)
5. [Flujo de Trabajo](#flujo-de-trabajo)
6. [Funciones Principales](#funciones-principales)
7. [Seguridad](#seguridad)
8. [API y Endpoints](#api-y-endpoints)

---

## 🔄 Resumen de Cambios

### Sistema Anterior (v1.0)
- ❌ Sin autenticación de usuarios
- ❌ Datos guardados en archivos JSON
- ❌ Sin autoguardado de progreso
- ❌ Sin panel de administración
- ❌ Sin roles de usuario

### Sistema Actual (v2.0)
- ✅ Sistema de autenticación completo (usuarios y empresas)
- ✅ Base de datos relacional MySQL
- ✅ Autoguardado automático del progreso
- ✅ Dashboard para empresas
- ✅ Sistema de roles y permisos
- ✅ Logs de auditoría
- ✅ Sesiones seguras
- ✅ Visualización de resultados con gráficos

---

## 🏗️ Arquitectura del Sistema

### Diagrama de Componentes

```
┌─────────────────────────────────────────────────────────┐
│                    CAPA DE PRESENTACIÓN                  │
├─────────────────────────────────────────────────────────┤
│  index.php │ login.php │ formulario.php │ dashboard.php │
└─────────────────────────────────────────────────────────┘
                           ↓
┌─────────────────────────────────────────────────────────┐
│                    CAPA DE NEGOCIO                       │
├─────────────────────────────────────────────────────────┤
│  config.php │ funciones de autenticación y validación   │
└─────────────────────────────────────────────────────────┘
                           ↓
┌─────────────────────────────────────────────────────────┐
│                    CAPA DE DATOS                         │
├─────────────────────────────────────────────────────────┤
│           MySQL Database (habilidades_directivas)        │
└─────────────────────────────────────────────────────────┘
```

### Tecnologías Utilizadas

- **Backend**: PHP 7.4+
- **Base de Datos**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Framework CSS**: Bootstrap 5.3
- **Gráficos**: Chart.js 4.4
- **Iconos**: Bootstrap Icons
- **Fuentes**: Google Fonts (Inter)

---

## 🗄️ Base de Datos

### Modelo Entidad-Relación

```
┌──────────────┐        ┌──────────────┐
│   EMPRESAS   │◄───────┤   USUARIOS   │
└──────────────┘        └──────────────┘
       │                       │
       │                       │
       │                       │
       ▼                       ▼
┌─────────────────────────────────┐
│        CUESTIONARIOS            │
└─────────────────────────────────┘
       │                       │
       │                       │
       ▼                       ▼
┌──────────────┐        ┌──────────────────────┐
│  RESPUESTAS  │        │ RESULTADOS_HABILIDADES│
└──────────────┘        └──────────────────────┘
```

### Tablas Principales

#### 1. **usuarios**
Almacena información de usuarios que responden cuestionarios.

```sql
Campos:
- id (PK, INT, Auto Increment)
- nombre (VARCHAR 255)
- email (UNIQUE, VARCHAR 255)
- password (VARCHAR 255) - Hash bcrypt
- telefono (VARCHAR 20)
- puesto (VARCHAR 100)
- departamento (VARCHAR 100)
- empresa_id (FK, INT)
- fecha_registro (DATETIME)
- ultimo_acceso (DATETIME)
- activo (TINYINT)
```

#### 2. **empresas**
Almacena información de empresas que consultan resultados.

```sql
Campos:
- id (PK, INT, Auto Increment)
- nombre (VARCHAR 255)
- email (UNIQUE, VARCHAR 255)
- password (VARCHAR 255) - Hash bcrypt
- rfc (VARCHAR 20)
- telefono (VARCHAR 20)
- direccion (TEXT)
- fecha_registro (DATETIME)
- ultimo_acceso (DATETIME)
- activo (TINYINT)
```

#### 3. **cuestionarios**
Instancias de cuestionarios (completos o en progreso).

```sql
Campos:
- id (PK, INT, Auto Increment)
- usuario_id (FK, INT)
- empresa_id (FK, INT)
- fecha_inicio (DATETIME)
- fecha_completado (DATETIME)
- promedio_general (DECIMAL 3,2)
- nivel_general (VARCHAR 50)
- estado (ENUM: 'en_progreso', 'completado', 'abandonado')
- total_preguntas (INT)
- preguntas_respondidas (INT)
```

#### 4. **respuestas**
Respuestas individuales a cada pregunta.

```sql
Campos:
- id (PK, INT, Auto Increment)
- cuestionario_id (FK, INT)
- habilidad_id (VARCHAR 50)
- habilidad_nombre (VARCHAR 100)
- pregunta_index (INT)
- pregunta_texto (TEXT)
- valor_respuesta (INT) - CHECK (1-5)
- fecha_respuesta (DATETIME)
```

#### 5. **resultados_habilidades**
Resultados calculados por habilidad.

```sql
Campos:
- id (PK, INT, Auto Increment)
- cuestionario_id (FK, INT)
- habilidad_id (VARCHAR 50)
- habilidad_nombre (VARCHAR 100)
- habilidad_descripcion (TEXT)
- promedio (DECIMAL 3,2)
- nivel (VARCHAR 50)
- clase (VARCHAR 20)
- mensaje (TEXT)
- fecha_calculo (DATETIME)
```

#### 6. **sesiones**
Control de sesiones activas.

```sql
Campos:
- id (PK, INT, Auto Increment)
- session_id (UNIQUE, VARCHAR 128)
- tipo_usuario (ENUM: 'usuario', 'empresa')
- usuario_id (INT)
- empresa_id (INT)
- ip_address (VARCHAR 45)
- user_agent (TEXT)
- fecha_inicio (DATETIME)
- fecha_expiracion (DATETIME)
- activa (TINYINT)
```

#### 7. **progreso_cuestionario**
Autoguardado del progreso del usuario.

```sql
Campos:
- id (PK, INT, Auto Increment)
- usuario_id (FK, INT)
- cuestionario_id (FK, INT)
- paso_actual (INT)
- datos_personales_completos (TINYINT)
- respuestas_guardadas (TEXT) - JSON
- ultima_actualizacion (TIMESTAMP)
```

#### 8. **logs_actividad**
Registro de auditoría del sistema.

```sql
Campos:
- id (PK, INT, Auto Increment)
- tipo_usuario (ENUM: 'usuario', 'empresa', 'sistema')
- usuario_id (INT)
- empresa_id (INT)
- accion (VARCHAR 100)
- descripcion (TEXT)
- ip_address (VARCHAR 45)
- fecha (DATETIME)
```

---

## 📁 Archivos del Sistema

### Archivos Nuevos Creados

| Archivo | Descripción |
|---------|-------------|
| `database.sql` | Script completo de creación de base de datos |
| `login.php` | Página de inicio de sesión (usuarios y empresas) |
| `registro_usuario.php` | Formulario de registro para usuarios |
| `registro_empresa.php` | Formulario de registro para empresas |
| `logout.php` | Script de cierre de sesión |
| `dashboard_empresa.php` | Panel de control para empresas |
| `ver_resultado.php` | Vista detallada de resultados (para empresas) |
| `mis_resultados.php` | Vista de resultados (para usuarios) |
| `guardar_progreso.php` | Endpoint AJAX para autoguardado |
| `INSTALL.md` | Guía de instalación completa |
| `DOCUMENTACION.md` | Documentación técnica (este archivo) |

### Archivos Modificados

| Archivo | Cambios Principales |
|---------|---------------------|
| `config.php` | + Conexión a BD, funciones de autenticación, funciones de seguridad |
| `formulario.php` | + Autenticación obligatoria, autoguardado, carga de progreso guardado |
| `procesar.php` | Migrado de JSON a base de datos, transacciones SQL |
| `index.php` | + Botones de login/registro condicionales según estado de sesión |
| `resultados.php` | Actualizado para trabajar con BD (aunque ahora usa `mis_resultados.php`) |

---

## 🔄 Flujo de Trabajo

### Flujo de Usuario (Responder Cuestionario)

```
1. Usuario → index.php
            ↓
2. Clic en "Iniciar Sesión"
            ↓
3. login.php → Ingresa credenciales
            ↓
4. Validación → Crea sesión en BD
            ↓
5. Redirige a formulario.php
            ↓
6. Verifica/crea cuestionario en progreso
            ↓
7. Carga progreso guardado (si existe)
            ↓
8. Usuario responde preguntas
            ↓
9. JavaScript → autoguardar cada 2 segundos
            ↓
10. guardar_progreso.php → Guarda en BD
            ↓
11. Usuario completa todas las preguntas
            ↓
12. Submit → procesar.php
            ↓
13. Guarda respuestas y calcula resultados
            ↓
14. Marca cuestionario como completado
            ↓
15. Redirige a mis_resultados.php
            ↓
16. Usuario ve resultados con gráfico radar
```

### Flujo de Empresa (Consultar Resultados)

```
1. Empresa → index.php
            ↓
2. Clic en "Iniciar Sesión"
            ↓
3. login.php?tipo=empresa → Ingresa credenciales
            ↓
4. Validación → Crea sesión en BD
            ↓
5. Redirige a dashboard_empresa.php
            ↓
6. Carga estadísticas de la empresa
            ↓
7. Muestra:
   - Total usuarios
   - Cuestionarios completados
   - Cuestionarios en progreso
   - Promedio general
   - Gráfico de habilidades
   - Lista de evaluaciones
            ↓
8. Empresa selecciona una evaluación
            ↓
9. ver_resultado.php?id=X
            ↓
10. Muestra resultados detallados con:
    - Información del usuario
    - Calificación general
    - Gráfico radar
    - Fortalezas
    - Áreas de oportunidad
    - Detalle por habilidad
```

---

## 🔧 Funciones Principales

### Funciones de Conexión (config.php)

#### `obtenerConexion()`
```php
/**
 * Obtener conexión PDO a la base de datos
 * @return PDO Objeto PDO conectado
 */
function obtenerConexion()
```

### Funciones de Seguridad (config.php)

#### `iniciarSesionSegura()`
```php
/**
 * Iniciar sesión segura con cookies HttpOnly
 */
function iniciarSesionSegura()
```

#### `estaAutenticado()`
```php
/**
 * Verificar si el usuario está autenticado
 * @return bool
 */
function estaAutenticado()
```

#### `esUsuario()` / `esEmpresa()`
```php
/**
 * Verificar tipo de usuario
 * @return bool
 */
function esUsuario()
function esEmpresa()
```

#### `requerirUsuario()` / `requerirEmpresa()`
```php
/**
 * Requerir autenticación y redirigir si no está autenticado
 */
function requerirUsuario()
function requerirEmpresa()
```

#### `limpiarEntrada($data)`
```php
/**
 * Sanitizar entrada de usuario
 * @param string $data Datos a sanitizar
 * @return string Datos sanitizados
 */
function limpiarEntrada($data)
```

### Funciones de Cuestionarios (config.php)

#### `obtenerCuestionarioEnProgreso($usuario_id)`
```php
/**
 * Obtener cuestionario en progreso del usuario
 * @param int $usuario_id ID del usuario
 * @return array|null Datos del cuestionario o null
 */
function obtenerCuestionarioEnProgreso($usuario_id)
```

#### `crearCuestionario($usuario_id, $empresa_id, $total_preguntas)`
```php
/**
 * Crear nuevo cuestionario
 * @param int $usuario_id ID del usuario
 * @param int $empresa_id ID de la empresa
 * @param int $total_preguntas Total de preguntas
 * @return int ID del cuestionario creado
 */
function crearCuestionario($usuario_id, $empresa_id, $total_preguntas)
```

#### `guardarProgreso($usuario_id, $cuestionario_id, $paso_actual, $respuestas)`
```php
/**
 * Guardar progreso del cuestionario
 * @param int $usuario_id ID del usuario
 * @param int $cuestionario_id ID del cuestionario
 * @param int $paso_actual Paso actual del formulario
 * @param array $respuestas Array de respuestas
 */
function guardarProgreso($usuario_id, $cuestionario_id, $paso_actual, $respuestas)
```

### Funciones de Auditoría (config.php)

#### `registrarActividad(...)`
```php
/**
 * Registrar actividad en logs
 * @param string $tipo_usuario Tipo de usuario
 * @param int|null $usuario_id ID del usuario
 * @param int|null $empresa_id ID de la empresa
 * @param string $accion Acción realizada
 * @param string|null $descripcion Descripción adicional
 */
function registrarActividad($tipo_usuario, $usuario_id, $empresa_id, $accion, $descripcion)
```

---

## 🔒 Seguridad

### Medidas Implementadas

#### 1. **Autenticación y Sesiones**
- ✅ Sesiones seguras con cookies HttpOnly
- ✅ Control de sesiones en base de datos
- ✅ Registro de IP y User Agent
- ✅ Expiración automática de sesiones (24 horas)
- ✅ Limpieza automática de sesiones expiradas (trigger)

#### 2. **Protección de Contraseñas**
- ✅ Hash con `password_hash()` (bcrypt)
- ✅ Verificación con `password_verify()`
- ✅ No se almacenan contraseñas en texto plano
- ✅ Contraseña mínima de 6 caracteres

#### 3. **Protección SQL Injection**
- ✅ Prepared statements en todas las consultas
- ✅ PDO con emulación desactivada
- ✅ Validación de tipos de datos
- ✅ Sanitización de entradas

#### 4. **Protección XSS**
- ✅ `htmlspecialchars()` en todos los outputs
- ✅ `ENT_QUOTES` para proteger comillas
- ✅ Validación de entradas de usuario

#### 5. **Control de Acceso**
- ✅ Verificación de permisos en cada página
- ✅ Funciones `requerirUsuario()` y `requerirEmpresa()`
- ✅ Validación de propiedad de datos (un usuario solo ve sus cuestionarios)
- ✅ Verificación de estado activo

#### 6. **Auditoría**
- ✅ Logs de todas las actividades importantes
- ✅ Registro de intentos de login fallidos
- ✅ Registro de IP y fecha de acciones
- ✅ Tabla `logs_actividad` para análisis

#### 7. **Validaciones**
- ✅ Validación de email con `filter_var()`
- ✅ Validación de valores numéricos con `intval()`
- ✅ Validación de rangos (respuestas 1-5)
- ✅ Verificación de existencia de registros antes de operar

---

## 🌐 API y Endpoints

### Endpoints AJAX

#### `guardar_progreso.php`

**Método**: POST  
**Autenticación**: Requerida (Usuario)  
**Descripción**: Guarda el progreso actual del cuestionario

**Parámetros**:
```javascript
{
  cuestionario_id: int,
  paso_actual: int,
  [habilidad_id][pregunta_index]: valor_respuesta
}
```

**Respuesta exitosa**:
```json
{
  "success": true,
  "respuestas_guardadas": 15,
  "paso_actual": 2,
  "mensaje": "Progreso guardado correctamente"
}
```

**Respuesta de error**:
```json
{
  "success": false,
  "error": "Descripción del error"
}
```

**Uso desde JavaScript**:
```javascript
fetch('guardar_progreso.php', {
    method: 'POST',
    body: formData
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        console.log('Guardado exitoso');
    }
});
```

---

## 📊 Procedimientos Almacenados

### `limpiar_sesiones_expiradas()`

Limpia sesiones expiradas y sesiones inactivas antiguas.

```sql
CALL limpiar_sesiones_expiradas();
```

### `obtener_estadisticas_empresa(empresa_id)`

Obtiene estadísticas completas de una empresa.

```sql
CALL obtener_estadisticas_empresa(1);
```

**Retorna**:
- `total_usuarios`: Total de usuarios de la empresa
- `total_cuestionarios`: Total de cuestionarios realizados
- `cuestionarios_completados`: Cuestionarios completados
- `cuestionarios_en_progreso`: Cuestionarios en progreso
- `promedio_general_empresa`: Promedio general de la empresa

### `calcular_resultados_cuestionario(cuestionario_id)`

Calcula y actualiza los resultados de un cuestionario.

```sql
CALL calcular_resultados_cuestionario(123);
```

---

## 🎨 Diseño y UI

### Colores del Sistema

```css
:root {
    --primary-dark: #2C3E50;    /* Azul oscuro principal */
    --secondary-dark: #34495E;   /* Azul oscuro secundario */
    --accent-gold: #F39C12;      /* Dorado de acento */
    --bg-light: #F8F9FA;         /* Fondo claro */
    --text-dark: #2C3E50;        /* Texto oscuro */
    --border-light: #E5E7EB;     /* Bordes claros */
}
```

### Clases de Badge

| Clase | Color | Uso |
|-------|-------|-----|
| `badge-excelente` | Verde (#10B981) | Promedio ≥ 4.5 |
| `badge-bueno` | Azul (#3B82F6) | Promedio ≥ 3.5 |
| `badge-regular` | Naranja (#F39C12) | Promedio ≥ 2.5 |
| `badge-mejora` | Rojo (#EF4444) | Promedio < 2.5 |

---

## 🧪 Testing

### Casos de Prueba Recomendados

#### Test de Autenticación
1. Login con credenciales válidas (usuario)
2. Login con credenciales inválidas
3. Login con credenciales válidas (empresa)
4. Registro de nuevo usuario
5. Registro de nueva empresa
6. Registro con email duplicado
7. Logout y verificación de sesión cerrada

#### Test de Cuestionario
1. Iniciar cuestionario nuevo
2. Responder algunas preguntas y cerrar navegador
3. Volver a entrar y verificar progreso guardado
4. Completar cuestionario
5. Ver resultados
6. Intentar acceder a cuestionario ya completado

#### Test de Dashboard Empresa
1. Login como empresa
2. Ver estadísticas generales
3. Ver lista de cuestionarios completados
4. Ver detalle de un cuestionario específico
5. Ver cuestionarios en progreso

#### Test de Seguridad
1. Intentar acceder a formulario.php sin login
2. Intentar acceder a dashboard_empresa.php como usuario
3. Intentar ver resultados de otro usuario
4. Intentar modificar cuestionario_id en URL
5. Verificar que contraseñas estén hasheadas en BD

---

## 📈 Mejoras Futuras Sugeridas

### Funcionalidades Adicionales

1. **Exportación de Reportes**
   - PDF individual
   - Excel con múltiples evaluaciones
   - Reportes personalizados

2. **Comparativas**
   - Comparar resultados entre usuarios
   - Ver evolución histórica
   - Benchmarking por industria

3. **Notificaciones**
   - Email al completar evaluación
   - Recordatorios de evaluaciones pendientes
   - Alertas a empresas de nuevas evaluaciones

4. **Multi-idioma**
   - Soporte para español e inglés
   - Configuración por usuario

5. **Personalización**
   - Logo de empresa en reportes
   - Colores personalizados
   - Preguntas personalizadas

6. **Análisis Avanzado**
   - Gráficos comparativos
   - Tendencias por departamento
   - Correlaciones entre habilidades

---

## 📝 Cambios en la Estructura

### Migración de JSON a MySQL

**Antes**:
```php
$evaluacion = ['id' => uniqid(), 'nombre' => $nombre, ...];
file_put_contents('data/evaluacion_' . $id . '.json', json_encode($evaluacion));
```

**Después**:
```php
$pdo = obtenerConexion();
$stmt = $pdo->prepare("INSERT INTO cuestionarios (...) VALUES (...)");
$stmt->execute([...]);
```

### Ventajas de la Migración

| Aspecto | JSON (Anterior) | MySQL (Actual) |
|---------|----------------|----------------|
| **Consultas** | Leer todos los archivos | Queries SQL eficientes |
| **Relaciones** | No existen | Foreign keys |
| **Integridad** | Sin validación | Constraints y triggers |
| **Escalabilidad** | Limitada | Alta |
| **Búsquedas** | Lentas | Indexadas y rápidas |
| **Concurrencia** | Problemas | Manejada por DBMS |
| **Backup** | Archivos individuales | Backup de BD |

---

## 🔧 Mantenimiento

### Tareas Periódicas

#### Diarias
- Revisar logs de errores PHP
- Verificar sesiones activas

#### Semanales
- Limpiar sesiones expiradas manualmente
- Backup de base de datos
- Revisar logs de actividad sospechosa

#### Mensuales
- Analizar estadísticas generales
- Optimizar tablas MySQL (`OPTIMIZE TABLE`)
- Actualizar dependencias (Chart.js, Bootstrap)

### Comandos Útiles MySQL

```sql
-- Ver tamaño de tablas
SELECT 
    table_name AS 'Tabla',
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Tamaño (MB)'
FROM information_schema.TABLES
WHERE table_schema = 'habilidades_directivas'
ORDER BY (data_length + index_length) DESC;

-- Ver estadísticas de cuestionarios
SELECT 
    estado,
    COUNT(*) as total,
    AVG(promedio_general) as promedio
FROM cuestionarios
GROUP BY estado;

-- Ver actividad por día
SELECT 
    DATE(fecha) as dia,
    COUNT(*) as acciones
FROM logs_actividad
GROUP BY DATE(fecha)
ORDER BY dia DESC
LIMIT 30;
```

---

## 📞 Información de Contacto

**Versión del Sistema:** 2.0  
**Fecha de Desarrollo:** Marzo 2026  
**Última Actualización:** 8 de marzo de 2026

---

## 📄 Licencia

Sistema desarrollado para fines educativos y empresariales.

---

**Fin del Documento**

# 📋 Guía de Instalación y Configuración
## Sistema de Evaluación de Habilidades Directivas v2.0

---

## 📦 Requisitos del Sistema

### Software Necesario
- **XAMPP** (Apache + PHP 7.4+ + MySQL 5.7+)
- **Navegador Web** moderno (Chrome, Firefox, Edge, Safari)
- **Editor de texto** (opcional, para configuración)

### Extensiones PHP Requeridas
- PDO
- PDO_MySQL
- JSON
- MBString

---

## 🚀 Instalación Paso a Paso

### Paso 1: Descarga e Instalación de XAMPP

1. Descarga XAMPP desde https://www.apachefriends.org/
2. Instala XAMPP en tu computadora
3. Verifica que Apache y MySQL estén funcionando

### Paso 2: Copiar Archivos del Sistema

1. Copia la carpeta `habilidades_directivas` completa en:
   ```
   C:\xampp\htdocs\alta_direccion\habilidades_directivas
   ```

2. Verifica que todos los archivos estén presentes:
   ```
   habilidades_directivas/
   ├── config.php
   ├── database.sql
   ├── index.php
   ├── login.php
   ├── registro_usuario.php
   ├── registro_empresa.php
   ├── logout.php
   ├── formulario.php
   ├── procesar.php
   ├── guardar_progreso.php
   ├── mis_resultados.php
   ├── dashboard_empresa.php
   ├── ver_resultado.php
   └── data/ (carpeta)
   ```

### Paso 3: Crear la Base de Datos

#### Opción A: Usando phpMyAdmin

1. Abre tu navegador y ve a: `http://localhost/phpmyadmin`
2. Inicia sesión (usuario: `root`, contraseña: en blanco por defecto)
3. Haz clic en la pestaña "SQL" en la parte superior
4. Abre el archivo `database.sql` con un editor de texto
5. Copia todo el contenido del archivo
6. Pégalo en el área de texto de phpMyAdmin
7. Haz clic en "Continuar" o "Go"
8. Verifica que la base de datos `habilidades_directivas` se haya creado correctamente

#### Opción B: Usando línea de comandos

```bash
# Navega a la carpeta de XAMPP
cd C:\xampp\mysql\bin

# Ejecuta el script SQL
mysql -u root -p < "C:\xampp\htdocs\alta_direccion\habilidades_directivas\database.sql"
```

### Paso 4: Configurar Conexión a la Base de Datos

1. Abre el archivo `config.php`
2. Verifica las siguientes constantes (líneas 180-184):

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'habilidades_directivas');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
```

3. **Si tu configuración de MySQL es diferente**, actualiza los valores:
   - `DB_HOST`: normalmente es `localhost`
   - `DB_NAME`: nombre de la base de datos (debe ser `habilidades_directivas`)
   - `DB_USER`: tu usuario de MySQL (por defecto es `root`)
   - `DB_PASS`: tu contraseña de MySQL (por defecto está vacía)

### Paso 5: Verificar Permisos

Asegúrate de que la carpeta `data/` tenga permisos de escritura:

```bash
# En Windows, hacer clic derecho en la carpeta > Propiedades > Seguridad
# Asegurarse de que "Usuarios" tiene permisos de escritura
```

### Paso 6: Iniciar el Sistema

1. Asegúrate de que Apache y MySQL estén corriendo en XAMPP
2. Abre tu navegador
3. Ve a: `http://localhost/alta_direccion/habilidades_directivas/`
4. Deberías ver la página principal del sistema

---

## 🔐 Credenciales de Prueba

El script SQL incluye datos de prueba. Puedes usar estas credenciales para probar el sistema:

### Cuentas de Usuario
```
Email: juan.perez@demo.com
Contraseña: usuario123

Email: maria.gonzalez@demo.com
Contraseña: usuario123

Email: carlos.rodriguez@ejemplo.com
Contraseña: usuario123
```

### Cuentas de Empresa
```
Email: empresa@demo.com
Contraseña: empresa123

Email: contacto@ejemplo.com
Contraseña: empresa123
```

---

## 🔧 Configuración Adicional

### Activar HTTPS (Opcional pero Recomendado)

Para mayor seguridad en producción, activa HTTPS:

1. En `config.php`, línea 201, cambia:
```php
ini_set('session.cookie_secure', 1); // Era 0
```

2. Configura un certificado SSL en tu servidor

### Configurar Zona Horaria

En `config.php`, agrega después de la línea 1:

```php
date_default_timezone_set('America/Mexico_City');
```

### Ajustar Duración de Sesiones

Para cambiar el tiempo de sesión (por defecto 24 horas):

En `login.php`, líneas 53 y 97, modifica:
```php
$fecha_expiracion = date('Y-m-d H:i:s', time() + 3600 * 24); // 24 horas
// Cambia 24 por el número de horas deseado
```

---

## ⚙️ Mantenimiento de la Base de Datos

### Limpiar Sesiones Expiradas

Ejecuta periódicamente (puedes crear un cron job):

```sql
CALL limpiar_sesiones_expiradas();
```

### Backup de la Base de Datos

#### Usando phpMyAdmin:
1. Ve a phpMyAdmin
2. Selecciona la base de datos `habilidades_directivas`
3. Haz clic en "Exportar"
4. Selecciona "SQL" y haz clic en "Continuar"

#### Usando línea de comandos:
```bash
mysqldump -u root -p habilidades_directivas > backup_$(date +%Y%m%d).sql
```

### Restaurar Backup

```bash
mysql -u root -p habilidades_directivas < backup_20260308.sql
```

---

## 🐛 Solución de Problemas Comunes

### Error: "No se puede conectar a la base de datos"

**Solución:**
1. Verifica que MySQL esté corriendo en XAMPP
2. Verifica las credenciales en `config.php`
3. Verifica que la base de datos `habilidades_directivas` exista

### Error: "Call to undefined function obtenerConexion()"

**Solución:**
1. Asegúrate de que `config.php` esté correctamente incluido al inicio de cada archivo
2. Verifica que no haya errores de sintaxis en `config.php`

### No se guarda el progreso del cuestionario

**Solución:**
1. Verifica que el archivo `guardar_progreso.php` exista
2. Abre la consola del navegador (F12) y busca errores JavaScript
3. Verifica que la tabla `progreso_cuestionario` exista en la BD

### Error 404 al intentar ver resultados

**Solución:**
1. Verifica que el archivo `ver_resultado.php` o `mis_resultados.php` exista
2. Verifica la ruta completa en la barra de direcciones del navegador

### Las sesiones se cierran muy rápido

**Solución:**
1. Aumenta el tiempo de expiración en `login.php` (ver Configuración Adicional)
2. Verifica la configuración de `session.gc_maxlifetime` en `php.ini`

---

## 📊 Estructura de la Base de Datos

### Tablas Principales

1. **usuarios**: Información de usuarios que responden cuestionarios
2. **empresas**: Información de empresas que consultan resultados
3. **cuestionarios**: Instancias de cuestionarios (completos o en progreso)
4. **respuestas**: Respuestas individuales a cada pregunta
5. **resultados_habilidades**: Resultados calculados por habilidad
6. **sesiones**: Control de sesiones activas
7. **progreso_cuestionario**: Autoguardado del progreso
8. **logs_actividad**: Registro de auditoría

### Vistas Disponibles

- `vista_cuestionarios_empresa`: Resumen de cuestionarios por empresa
- `vista_evaluaciones_usuario`: Resumen de evaluaciones por usuario
- `vista_habilidades_empresa`: Estadísticas de habilidades por empresa

### Procedimientos Almacenados

- `limpiar_sesiones_expiradas()`: Limpia sesiones antiguas
- `obtener_estadisticas_empresa(empresa_id)`: Obtiene estadísticas de una empresa
- `calcular_resultados_cuestionario(cuestionario_id)`: Calcula resultados de un cuestionario

---

## 🔒 Seguridad

### Medidas Implementadas

1. **Autenticación obligatoria**: No se puede acceder al formulario sin login
2. **Password hashing**: Contraseñas encriptadas con `password_hash()`
3. **Prepared statements**: Protección contra SQL Injection
4. **Validación de sesiones**: Control de sesiones en base de datos
5. **Sanitización de entradas**: Limpieza de datos del usuario
6. **Protección CSRF**: Tokens para formularios críticos
7. **Logs de auditoría**: Registro de actividades importantes

### Recomendaciones Adicionales

1. **Cambiar contraseñas de prueba** en producción
2. **Activar HTTPS** en servidor de producción
3. **Configurar firewall** para proteger MySQL
4. **Hacer backups regulares** de la base de datos
5. **Actualizar PHP y MySQL** regularmente
6. **Revisar logs** periódicamente para detectar actividad sospechosa

---

## 📞 Soporte

Si encuentras problemas durante la instalación:

1. Verifica que hayas seguido todos los pasos
2. Revisa los logs de errores de PHP: `C:\xampp\php\logs\php_error_log`
3. Revisa los logs de MySQL: `C:\xampp\mysql\data\mysql_error.log`
4. Revisa los logs de Apache: `C:\xampp\apache\logs\error.log`

---

## ✅ Verificación de Instalación

Para verificar que todo esté funcionando correctamente:

1. ✅ Accede a `http://localhost/alta_direccion/habilidades_directivas/`
2. ✅ Haz clic en "Iniciar Sesión"
3. ✅ Inicia sesión con una cuenta de prueba de usuario
4. ✅ Verifica que puedas acceder al formulario
5. ✅ Responde algunas preguntas y verifica el autoguardado
6. ✅ Cierra sesión
7. ✅ Inicia sesión con una cuenta de empresa
8. ✅ Verifica que puedas ver el dashboard

Si todos los pasos funcionan, ¡tu instalación es exitosa! 🎉

---

**Fecha de actualización:** 8 de marzo de 2026  
**Versión del documento:** 1.0

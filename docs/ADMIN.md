# 🛡️ Sistema de Administración
## Instalación y Configuración

---

## 📋 Cambios Implementados

### ✅ Funcionalidades del Administrador

1. **Panel de Administración** (`admin_panel.php`)
   - Vista general de estadísticas del sistema
   - Gestión completa de empresas (crear/desactivar)
   - Gestión completa de usuarios (desactivar)
   - Logs de todas las actividades administrativas

2. **Acceso Exclusivo para Administradores**
   - Login separado en `admin_login.php`
   - No se puede crear administradores desde el registro público
   - Todos los accesos son registrados y monitoreados

3. **Cambios en el Login Público**
   - ❌ Eliminado botón "Registrar Empresa"
   - ✅ Las empresas solo pueden ser creadas por el administrador
   - ✅ Enlace discreto al panel de administrador en el footer

---

## 🚀 Instalación del Sistema de Administración

### Paso 1: Ejecutar el Script SQL

Debes ejecutar el archivo `admin_setup.sql` **DESPUÉS** de haber ejecutado `database.sql`

#### Opción A: Usando phpMyAdmin
1. Abre `http://localhost/phpmyadmin`
2. Selecciona la base de datos `habilidades_directivas`
3. Ve a la pestaña "SQL"
4. Abre `admin_setup.sql` en un editor de texto
5. Copia todo el contenido
6. Pégalo en phpMyAdmin y ejecuta

#### Opción B: Usando línea de comandos
```bash
cd C:\xampp\mysql\bin
mysql -u root -p habilidades_directivas < "C:\xampp\htdocs\alta_direccion\habilidades_directivas\admin_setup.sql"
```

### Paso 2: Verificar la Instalación

El script creará:
- ✅ Tabla `administradores`
- ✅ Tabla `logs_admin`
- ✅ Vista `vista_estadisticas_sistema`
- ✅ Procedimientos almacenados para administración
- ✅ Un administrador por defecto

---

## 🔐 Credenciales del Administrador

### Acceso por Defecto
```
URL: http://localhost/alta_direccion/habilidades_directivas/admin_login.php
Email: admin@sistema.com
Contraseña: admin123
```

⚠️ **IMPORTANTE:** Cambia esta contraseña inmediatamente en un entorno de producción.

---

## 📊 Estructura de la Base de Datos

### Nuevas Tablas

#### `administradores`
```sql
- id (PRIMARY KEY)
- nombre
- email (UNIQUE)
- password (hasheado)
- fecha_creacion
- ultimo_acceso
- activo
```

#### `logs_admin`
```sql
- id (PRIMARY KEY)
- admin_id (FK a administradores)
- accion (crear_empresa, eliminar_empresa, etc.)
- entidad_tipo (usuario, empresa, cuestionario, sistema)
- entidad_id
- detalles (JSON con información adicional)
- ip_address
- fecha
```

### Modificaciones a Tablas Existentes

#### `empresas`
- ✅ Agregado campo `creado_por_admin_id` (FK a administradores)
- ✅ Permite rastrear qué admin creó cada empresa

---

## 🎯 Funcionalidades del Panel de Administración

### Dashboard Principal
- **Estadísticas Generales**
  - Total de empresas activas
  - Total de usuarios activos
  - Cuestionarios completados
  - Promedio general del sistema

### Gestión de Empresas
- ✅ **Crear nueva empresa**
  - Nombre comercial
  - Razón social
  - Email y contraseña temporal
  - Asignación automática del admin que la creó
  
- ✅ **Ver lista de empresas**
  - Información completa
  - Número de usuarios por empresa
  - Número de cuestionarios completados
  - Estado (activa/inactiva)
  
- ✅ **Desactivar empresa**
  - Soft delete (no elimina datos)
  - Preserva historial de cuestionarios
  - Registra en logs quién desactivó y cuándo

### Gestión de Usuarios
- ✅ **Ver lista de usuarios**
  - Información completa
  - Empresa a la que pertenecen
  - Número de cuestionarios completados
  - Estado (activo/inactivo)
  
- ✅ **Desactivar usuario**
  - Soft delete (no elimina datos)
  - Preserva cuestionarios completados
  - Registra en logs la acción

### Logs y Auditoría
- Todas las acciones del administrador son registradas
- Información guardada:
  - Qué acción realizó
  - Sobre qué entidad (empresa/usuario)
  - Cuándo lo hizo
  - Desde qué IP
  - Detalles adicionales

---

## 🔒 Seguridad

### Medidas Implementadas

1. **Acceso Restringido**
   - Login separado para administradores
   - No se puede acceder al panel sin autenticación
   - Sesiones separadas de usuarios/empresas

2. **Auditoría Completa**
   - Todos los accesos y acciones son registrados
   - Imposible realizar acciones sin dejar rastro
   - Logs permanentes para investigaciones

3. **Soft Delete**
   - Usuarios y empresas no se eliminan físicamente
   - Solo se desactivan
   - Permite recuperación si fue un error
   - Preserva integridad referencial

4. **Contraseñas Seguras**
   - Hasheadas con bcrypt
   - Validación de longitud mínima
   - Recomendación de cambio en primer acceso

---

## 📁 Archivos del Sistema de Administración

```
admin_setup.sql          # Script SQL para crear tablas y procedimientos
admin_login.php          # Login exclusivo para administradores
admin_panel.php          # Panel de control principal
admin_acciones.php       # Procesamiento de acciones CRUD
ADMIN.md                 # Esta documentación
```

### Modificaciones a Archivos Existentes

```
config.php               # Agregadas funciones de admin
login.php                # Eliminado botón "Registrar Empresa"
logout.php               # Manejo de logout de admin
```

---

## 🔧 Casos de Uso

### Caso 1: Empresa Nueva Solicita Acceso
1. Administrador accede a `admin_login.php`
2. Navega a "Gestión de Empresas"
3. Clic en "Nueva Empresa"
4. Llena el formulario:
   - Nombre: "Constructora ABC"
   - Razón Social: "ABC Construcciones S.A. de C.V."
   - Email: "rh@abc.com"
   - Contraseña temporal: "temporal123"
5. Se crea la empresa y se envía email con credenciales
6. La empresa puede iniciar sesión en el login público

### Caso 2: Usuario Reporta Problemas
1. Administrador busca al usuario en el panel
2. Ve el historial de cuestionarios
3. Identifica el problema
4. Si es necesario, desactiva la cuenta temporalmente
5. Resuelve el problema
6. Reactiva la cuenta cambiando `activo = 1` en BD

### Caso 3: Empresa Termina Contrato
1. Administrador desactiva la empresa
2. Automáticamente sus usuarios no pueden acceder
3. Los datos históricos se preservan
4. Los cuestionarios completados permanecen en BD
5. Se puede reactivar en el futuro si renuevan

---

## 🛠️ Mantenimiento

### Consultas Útiles SQL

#### Ver Logs Recientes del Admin
```sql
SELECT 
    a.nombre as admin,
    la.accion,
    la.entidad_tipo,
    la.detalles,
    la.fecha
FROM logs_admin la
JOIN administradores a ON la.admin_id = a.id
ORDER BY la.fecha DESC
LIMIT 50;
```

#### Ver Empresas Creadas por Admin
```sql
SELECT 
    e.nombre as empresa,
    e.email,
    a.nombre as creado_por,
    e.fecha_registro
FROM empresas e
LEFT JOIN administradores a ON e.creado_por_admin_id = a.id
ORDER BY e.fecha_registro DESC;
```

#### Ver Estadísticas Generales
```sql
SELECT * FROM vista_estadisticas_sistema;
```

### Crear Nuevo Administrador (Manual)
```sql
-- Contraseña: nuevopass123
INSERT INTO administradores (nombre, email, password) VALUES 
('Nuevo Admin', 'nuevo@admin.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
```

Para generar un nuevo hash de contraseña en PHP:
```php
echo password_hash('tu_contraseña', PASSWORD_DEFAULT);
```

---

## ⚠️ Advertencias Importantes

1. **No Compartas las Credenciales de Administrador**
   - Solo personal de confianza debe tener acceso
   - Cambia la contraseña por defecto inmediatamente

2. **Backups Regulares**
   - Haz respaldo de la tabla `administradores`
   - Haz respaldo de `logs_admin` regularmente

3. **Monitorea los Logs**
   - Revisa periódicamente las acciones administrativas
   - Detecta accesos no autorizados

4. **Entorno de Producción**
   - Usa HTTPS obligatorio para el panel de admin
   - Configura un firewall para proteger el acceso
   - Considera agregar autenticación de dos factores

---

## 📞 Solución de Problemas

### Error: "Tabla administradores no existe"
**Solución:** Ejecuta `admin_setup.sql`

### Error: "Call to undefined function esAdmin()"
**Solución:** Verifica que `config.php` tenga las funciones de admin

### No puedo acceder al panel de admin
**Solución:** 
1. Verifica que la URL sea correcta: `admin_login.php`
2. Verifica las credenciales
3. Revisa que el admin esté activo en BD

### La empresa creada no puede iniciar sesión
**Solución:**
1. Verifica que el email sea correcto
2. Verifica que la empresa esté activa (`activo = 1`)
3. Intenta resetear la contraseña en BD

---

## ✅ Checklist de Verificación

- [ ] Script `admin_setup.sql` ejecutado correctamente
- [ ] Tabla `administradores` existe en la BD
- [ ] Tabla `logs_admin` existe en la BD
- [ ] Vista `vista_estadisticas_sistema` funciona
- [ ] Puedes acceder a `admin_login.php`
- [ ] Puedes iniciar sesión con `admin@sistema.com` / `admin123`
- [ ] El panel de administración muestra estadísticas
- [ ] Puedes crear una empresa de prueba
- [ ] El botón "Registrar Empresa" YA NO aparece en `login.php`
- [ ] El logout del admin funciona correctamente

---

**Fecha de actualización:** 8 de marzo de 2026  
**Versión:** 1.0  
**Sistema:** Habilidades Directivas v2.0 + Admin Panel

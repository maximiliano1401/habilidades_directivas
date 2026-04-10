# 🚀 INSTRUCCIONES RÁPIDAS - Sistema de Administración

## ⚡ Pasos para Activar el Panel de Administrador

### 1️⃣ Ejecutar el Script SQL
Abre una terminal PowerShell y ejecuta:

```powershell
cd C:\xampp\mysql\bin
mysql -u root -p habilidades_directivas < "C:\xampp\htdocs\alta_direccion\habilidades_directivas\admin_setup.sql"
```

Cuando te pida la contraseña, presiona Enter (si no configuraste contraseña en MySQL).

### 2️⃣ Verificar que se Creó Correctamente
Abre phpMyAdmin (`http://localhost/phpmyadmin`) y verifica que existan:
- ✅ Tabla `administradores`
- ✅ Tabla `logs_admin`
- ✅ Vista `vista_estadisticas_sistema`

### 3️⃣ Acceder al Panel de Administrador
Abre tu navegador y ve a:
```
http://localhost/alta_direccion/habilidades_directivas/admin_login.php
```

**Credenciales:**
- Email: `admin@sistema.com`
- Contraseña: `admin123`

### 4️⃣ Probar la Funcionalidad
Una vez dentro del panel:
1. ✅ Verifica que veas las estadísticas del sistema
2. ✅ Crea una empresa de prueba desde el panel
3. ✅ Intenta desactivar un usuario de prueba
4. ✅ Verifica que el logout funciona

---

## 📋 Cambios Implementados

### ✅ En el Sistema
- Ya NO se puede registrar empresas desde el login público
- El botón "Registrar Empresa" fue eliminado
- Solo aparece "Registrar Usuario" en el login
- Enlace discreto "Acceso Administrador" en el footer del login

### ✅ Lo que Puede Hacer el Administrador
- ✓ Ver estadísticas globales del sistema
- ✓ Crear nuevas empresas
- ✓ Desactivar empresas existentes
- ✓ Desactivar usuarios problemáticos
- ✓ Ver logs de todas las actividades
- ✓ Monitorear el uso del sistema

### ✅ Seguridad
- Todas las acciones del admin son registradas
- Login separado para administradores
- No se eliminan datos, solo se desactivan (soft delete)
- Contraseñas hasheadas con bcrypt

---

## ❓ Si Algo No Funciona

### El script SQL da error
**Solución:** Asegúrate de haber ejecutado primero `database.sql`

### No puedo acceder a admin_login.php
**Solución:** Verifica que Apache esté corriendo en XAMPP

### Las credenciales no funcionan
**Solución:** Verifica que el script SQL se haya ejecutado correctamente

### Veo errores de "función no definida"
**Solución:** Verifica que `config.php` tenga las funciones de administrador agregadas

---

## 📖 Documentación Completa
Para más detalles, consulta: **[ADMIN.md](ADMIN.md)**

---

**Siguiente Paso:** Ejecuta el script SQL y accede al panel de administración 🚀

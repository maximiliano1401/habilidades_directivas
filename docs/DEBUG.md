# Guía de Depuración - Problema con Envío de Formulario

## Cambios Realizados

### 1. formulario.php
- ✅ Agregados console.log para rastrear el proceso de envío
- ✅ Corregida variable `autoguardadoTimer` → `autoguardadoTimeout`
- ✅ Agregado spinner de carga al botón de envío
- ✅ Agregado manejo visual de mensajes de error/éxito
- ✅ Detención explícita del autoguardado antes de enviar

### 2. procesar.php
- ✅ Eliminado código muerto del sistema antiguo JSON
- ✅ Mejorado logging de errores con detalles completos (archivo, línea, stack trace)
- ✅ Mensajes de error más descriptivos
- ✅ Redirect cambiado a confirmacion.php (los usuarios NO ven resultados)

### 3. confirmacion.php
- ✅ Nueva página de confirmación para usuarios
- ✅ Mensaje de éxito al completar el cuestionario
- ✅ Explica que solo la empresa puede ver los resultados

## Pasos para Depurar

### Paso 1: Verificar Consola del Navegador
1. Abre el formulario en tu navegador
2. Presiona F12 para abrir DevTools
3. Ve a la pestaña "Console"
4. Completa el formulario y haz clic en "Enviar Evaluación"

**Mensajes esperados en consola:**
```
Submit event triggered
Preguntas respondidas: X de X
Deteniendo autoguardado y enviando formulario...
Formulario enviándose a procesar.php
```

**Si no ves estos mensajes:** Hay un error JavaScript que impide el evento submit.

### Paso 2: Verificar Errores de PHP
1. Abre el archivo de errores de PHP
   - En XAMPP: `C:\xampp\php\logs\php_error_log`
   - O en Apache: `C:\xampp\apache\logs\error.log`

2. Busca errores recientes relacionados con:
   - `procesar.php`
   - `config.php`
   - Conexión a base de datos

### Paso 3: Verificar Estado del Cuestionario
Ejecuta en MySQL:
```sql
SELECT * FROM cuestionarios 
WHERE usuario_id = [TU_USUARIO_ID] 
ORDER BY fecha_inicio DESC 
LIMIT 5;
```

**Estado esperado:** `en_progreso`
**Si está `completado`:** Ya fue enviado y no se puede volver a enviar

### Paso 4: Verificar Datos POST
Si el formulario se envía pero no procesa, agrega temporalmente al inicio de procesar.php:
```php
error_log("POST Data: " . print_r($_POST, true));
error_log("Usuario ID: " . $usuario_id);
error_log("Cuestionario ID: " . $cuestionario_id);
```

### Paso 5: Verificar Autoguardado
En la consola del navegador, escribe:
```javascript
guardarProgreso();
```

**Si aparece error:** El problema está en guardar_progreso.php

## Problemas Comunes y Soluciones

### Problema 1: "Cuestionario no encontrado o ya completado"
**Causa:** El cuestionario ya fue enviado o no existe
**Solución:** 
```sql
-- Verificar estado
SELECT id, estado, fecha_inicio, fecha_completado 
FROM cuestionarios 
WHERE usuario_id = [TU_ID];

-- Si necesitas reiniciar un cuestionario para pruebas:
UPDATE cuestionarios 
SET estado = 'en_progreso', fecha_completado = NULL 
WHERE id = [CUESTIONARIO_ID];
```

### Problema 2: Botón no hace nada, sin errores en consola
**Causa:** Conflicto de JavaScript o evento submit no registrado
**Solución:** Verifica en consola:
```javascript
document.getElementById('evaluacionForm') // Debe retornar el formulario
```

### Problema 3: Se envía pero redirige a formulario.php con error
**Causa:** Error en procesar.php
**Solución:** 
1. Revisa los logs de PHP (ver Paso 2)
2. Verifica que todas las habilidades tengan respuestas
3. Verifica la conexión a base de datos

### Problema 4: "Faltan respuestas para la habilidad X"
**Causa:** No todas las preguntas fueron respondidas
**Solución:** 
- El sistema exige responder TODAS las preguntas
- Verifica que no haya preguntas sin marcar (en la barra de progreso debe decir 100%)

## Testing Manual

### Test 1: Validación de Preguntas Incompletas
1. Deja algunas preguntas sin responder
2. Intenta enviar
3. **Resultado esperado:** Alerta "Por favor, responde todas las preguntas antes de enviar."

### Test 2: Confirmación
1. Completa todas las preguntas
2. Haz clic en "Enviar Evaluación"
3. **Resultado esperado:** Dialog de confirmación
4. Haz clic en "Cancelar"
5. **Resultado esperado:** Formulario no se envía

### Test 3: Envío Exitoso
1. Completa todas las preguntas
2. Haz clic en "Enviar Evaluación"
3. Confirma
4. **Resultado esperado:** 
   - Botón muestra "Procesando..." con spinner
   - Redirige a `confirmacion.php` con mensaje de éxito
   - El usuario NO ve sus resultados
   - Solo la empresa puede ver los resultados en su dashboard

## Script de Diagnóstico SQL

Ejecuta esto en MySQL para obtener información completa:

```sql
-- Ver cuestionarios del usuario
SELECT 
    c.id,
    c.estado,
    c.fecha_inicio,
    c.fecha_completado,
    c.total_preguntas,
    c.preguntas_respondidas,
    u.nombre as usuario,
    e.nombre as empresa
FROM cuestionarios c
JOIN usuarios u ON c.usuario_id = u.id
JOIN empresas e ON c.empresa_id = e.id
WHERE u.email = '[TU_EMAIL]'
ORDER BY c.fecha_inicio DESC;

-- Ver progreso guardado
SELECT * FROM progreso_cuestionario
WHERE cuestionario_id = [CUESTIONARIO_ID];

-- Ver respuestas guardadas (si existe alguna)
SELECT * FROM respuestas
WHERE cuestionario_id = [CUESTIONARIO_ID];

-- Ver logs de actividad recientes
SELECT * FROM logs_actividad
WHERE usuario_id = [TU_USUARIO_ID]
ORDER BY fecha DESC
LIMIT 10;
```

## Contacto con el Desarrollador

Si nada de lo anterior funciona, proporciona:
1. Screenshot de la consola del navegador (F12)
2. Últimas 20 líneas del log de PHP
3. Resultado de las queries SQL de diagnóstico
4. Navegador y versión que estás usando

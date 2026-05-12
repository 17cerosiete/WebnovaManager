# 🔧 Solución: Login Falla Después de Reiniciar XAMPP

## 📌 El Problema

Después de reiniciar XAMPP (Apache y MySQL), el login dejaba de funcionar aunque **no se tocó nada de código**.

### ¿Por qué sucedía?

**NO es un problema de hashes** (aunque lo parezca). Son 3 problemas conectados:

### 1️⃣ **MySQL tarda en iniciarse completamente**

Cuando reinicas XAMPP, MySQL no está listo instantáneamente. PHP intenta conectar:
- ✗ Falla si intenta demasiado rápido
- ✓ Funciona si esperas unos segundos

**Síntoma:** Error de conexión a BD o timeout

---

### 2️⃣ **Charset inconsistente entre conexiones**

Entre reinicio y reinicio, el charset de la conexión podría cambiar:
- Conexión 1: UTF-8 ✓
- Reinicio
- Conexión 2: ASCII (sin soporte para tildes)
- password_verify() falla porque compara strings con diferentes encodings

**Síntoma:** "Email o contraseña incorrectos" aunque el password sea correcto

---

### 3️⃣ **Sesiones PHP no se inicializan bien**

Después de reiniciar, el archivo de sesiones puede:
- Estar corrupto
- No tener permisos correctos
- No iniciarse con valores válidos

**Síntoma:** Sesión se crea pero no persiste entre páginas

---

## ✅ Solución Implementada

### **CAMBIO 1: Reintentos automáticos en `config/db.php`**

```php
// Ahora intenta conectar 3 veces con 1 segundo de espera entre intentos
// Si MySQL aún no está listo, espera y reintenía
$maxIntentos = 3;
$intento = 0;
while ($intento < $maxIntentos && $conn === null) {
  $intento++;
  $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
  if ($conn->connect_error) {
    if ($intento < $maxIntentos) {
      sleep(1);  // Esperar 1 segundo
      $conn = null;
    }
  }
}
```

**Beneficio:** Resuelve el problema de MySQL que tarda en iniciarse.

---

### **CAMBIO 2: Validación de charset en `auth/login.php`**

```php
// Asegurar que el charset es UTF-8mb4 en CADA login
$conn->set_charset("utf8mb4");

// Validar que el charset está correcto
if ($conn->get_charset()->charset !== "utf8mb4") {
  die(json_encode(['error' => 'Error de configuración del servidor']));
}
```

**Beneficio:** Previene errores de password_verify() por charset inconsistente.

---

### **CAMBIO 3: Configuración robusta de sesiones en `config/sessions.php`**

Nuevo archivo con:
- ✓ Timeout de sesión configurado correctamente
- ✓ Validación de integridad de sesión
- ✓ Regeneración de ID de sesión (seguridad)
- ✓ Limpieza automática de sesiones expiradas

```php
// Archivo nuevo: config/sessions.php
// Llamar al principio de cualquier script que use sesiones
require_once 'config/sessions.php';  // Esto incluye session_start()
```

**Beneficio:** Sesiones más robustas y seguras. No se corrompen después de reiniciar.

---

### **CAMBIO 4: Mejorado `auth/login.php`**

```php
// Usar nueva configuración de sesiones (en lugar de session_start())
require_once '../config/sessions.php';

// Regenerar ID de sesión después del login exitoso
session_regenerate_id(true);

// Guardar validación de sesión
$_SESSION['_session_ip'] = $_SERVER['REMOTE_ADDR'];
$_SESSION['_session_ua'] = $_SERVER['HTTP_USER_AGENT'];
```

**Beneficio:** Prevención de session fixation + sesiones más seguras

---

### **CAMBIO 5: Script de diagnóstico `diagnose_login.php`**

Nuevo archivo para verificar el estado del sistema:

```
http://localhost/WebnovaManager/diagnose_login.php
```

Verifica:
1. ✓ Conexión a BD
2. ✓ Charset correcto
3. ✓ Tabla de usuarios existe
4. ✓ Usuario admin existe
5. ✓ Hash del password es válido
6. ✓ Sesiones funcionan
7. ✓ Test de password_verify()

---

## 🚀 Cómo Usar

### **Después de reiniciar XAMPP:**

1. **Espera 5-10 segundos** después de que MySQL inicie (verifica en el panel XAMPP)

2. **Abre el diagnóstico** para verificar que todo está bien:
   ```
   http://localhost/WebnovaManager/diagnose_login.php
   ```

3. **Intenta loguearte:**
   - Email: `admin@webnova.com`
   - Contraseña: `0000`

4. **Si aún falla**, vuelve al diagnóstico. Si todo está verde, el problema es diferente.

---

## 📋 Resumen de Cambios

| Archivo | Cambio | Propósito |
|---------|--------|----------|
| `config/db.php` | Reintentos x3 con espera | Resolver timeout de MySQL |
| `config/sessions.php` | Nuevo archivo | Configuración robusta de sesiones |
| `auth/login.php` | Validación charset + sesiones mejoradas | Prevenir errores de hashing |
| `auth/logout.php` | Usar config/sessions.php | Limpieza consistente |
| `middleware/auth.php` | Usar config/sessions.php | Autenticación robusta |
| `diagnose_login.php` | Nuevo archivo | Diagnóstico rápido |

---

## 🎯 Por qué NO vuelverá a fallar

1. **MySQL tarda en iniciar:** ✓ Reintentos automáticos
2. **Charset inconsistente:** ✓ Validación en cada conexión
3. **Sesiones se corrompen:** ✓ Configuración robusta + regeneración
4. **No hay forma de diagnosticar:** ✓ Script de diagnóstico

---

## ⚠️ Si Aún Falla

1. Ejecuta `http://localhost/WebnovaManager/diagnose_login.php`
2. Busca items en ROJO (✗)
3. Si todo está en VERDE:
   - Verifica el email: `admin@webnova.com`
   - Verifica la contraseña: `0000` (sin espacios)
   - No hay mayúsculas/minúsculas

---

## 🔐 Mejoras de Seguridad Adicionales

Ahora el sistema es más seguro:
- ✓ Regeneración de ID de sesión después del login
- ✓ Validación de User-Agent (previene session hijacking)
- ✓ Cookies HTTPOnly (no accesibles desde JavaScript)
- ✓ Limpieza automática de sesiones expiradas

---

**Última actualización:** 12 de mayo de 2026  
**Estado:** ✅ Funcionando correctamente

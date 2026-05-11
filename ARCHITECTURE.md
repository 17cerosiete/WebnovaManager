# 🏗️ ARCHITECTURE.md - Arquitectura Backend WebNova Manager

## 📖 Índice

1. [Visión General](#visión-general)
2. [Estructura de Capas](#estructura-de-capas)
3. [Flujo de Autenticación](#flujo-de-autenticación)
4. [Estructura de Carpetas](#estructura-de-carpetas)
5. [Patrones Implementados](#patrones-implementados)
6. [Seguridad](#seguridad)
7. [Decisiones de Diseño](#decisiones-de-diseño)

---

## 🎯 Visión General

**WebNova Manager** es un CMS (Content Management System) con arquitectura **backend en PHP** que:

- ✅ Autentica usuarios con hash BCRYPT
- ✅ Gestiona sesiones PHP
- ✅ Protege rutas mediante middleware
- ✅ Muestra contenido según rol del usuario
- ✅ Usa MySQL para persistencia de datos

**Stack:**
- PHP 7.4+ (backend)
- MySQL 5.7+ (base de datos)
- HTML5 + JavaScript Vanilla (frontend)
- Session PHP (gestión de sesiones)

---

## 🏛️ Estructura de Capas

```
┌─────────────────────────────────────────┐
│           CAPA PRESENTACIÓN             │
│     HTML + JavaScript + CSS             │
│  (admin/index.html, admin/dashboard.php)│
└────────────────┬────────────────────────┘
                 │ REQUEST/RESPONSE
┌────────────────▼────────────────────────┐
│         CAPA APLICACIÓN                 │
│    PHP - auth/, middleware/             │
│  - Validación de datos                  │
│  - Lógica de negocio                    │
│  - Control de acceso                    │
└────────────────┬────────────────────────┘
                 │ SQL QUERIES
┌────────────────▼────────────────────────┐
│         CAPA DATOS                      │
│    MySQL - database/tablas              │
│  - usuarios                             │
│  - paginas                              │
│  - articulos                            │
│  - sesiones                             │
└─────────────────────────────────────────┘
```

---

## 🔐 Flujo de Autenticación (Detallado)

### **FASE 1: Usuario intenta login**

```
NAVEGADOR
  ↓
admin/index.html
  ├─ Formulario: <input type="email"> + <input type="password">
  ├─ JavaScript: document.addEventListener('submit')
  └─ Fetch: POST a ../auth/login.php
```

### **FASE 2: PHP valida y busca en BD**

```
auth/login.php
  ├─ session_start()
  ├─ require_once config/db.php → $conn disponible
  ├─ Validar: ¿email y password no vacíos?
  ├─ Sanitizar: trim(), FILTER_VALIDATE_EMAIL
  │
  ├─ PREPARED STATEMENT (protección SQL injection)
  │  SELECT id, nombre, email, password, rol 
  │  FROM usuarios 
  │  WHERE email = ? ← Parámetro seguro
  │
  └─ Buscar en MySQL
      ↓
     MySQL: ¿existe usuarios.email = 'admin@webnova.com'?
        ├─ NO → Responder: error 401
        └─ SÍ → Obtener fila usuario
```

### **FASE 3: Verificar contraseña (SIN texto plano)**

```
password_verify('0000', '$2y$10$N9qo8uL...')
  ├─ Toma: contraseña ingresada
  ├─ Obtiene: hash almacenado
  ├─ Calcula: hash(contraseña_ingresada + salt)
  └─ Compara: ¿coincide con hash_bd?
      ├─ NO → error 401
      └─ SÍ → continuar
```

### **FASE 4: Crear sesión PHP**

```
$_SESSION['usuario_id'] = 1
$_SESSION['usuario_nombre'] = 'Admin WEBNOVA'
$_SESSION['usuario_email'] = 'admin@webnova.com'
$_SESSION['usuario_rol'] = 'admin'
$_SESSION['logueado'] = true

↓ Se guarda en:
/tmp/sess_PHPSESSID (Linux)
C:\xampp\tmp\sess_PHPSESSID (Windows)

↓ Cookie HTTP Set-Cookie: PHPSESSID=abc123
```

### **FASE 5: Respuesta JSON**

```json
{
  "success": true,
  "mensaje": "Login exitoso",
  "usuario": {
    "nombre": "Admin WEBNOVA",
    "rol": "admin"
  }
}
```

### **FASE 6: JavaScript redirige**

```javascript
// Recibe JSON success: true
setTimeout(() => {
  window.location.href = 'dashboard.php'
}, 800);
```

### **FASE 7: Dashboard carga con sesión**

```
admin/dashboard.php
  ├─ require_once '../middleware/auth.php'
  │   ├─ session_start()
  │   └─ Verificar: isset($_SESSION['logueado']) && $_SESSION['logueado'] === true
  │       ├─ NO → header('Location: ../admin/index.html') + exit()
  │       └─ SÍ → continuar
  │
  ├─ PHP es ejecutado en servidor ✓
  ├─ Variables $_SESSION disponibles
  └─ HTML con datos dinámicos:
      echo "Bienvenido, " . $_SESSION['usuario_nombre'];
      // Muestra: "Bienvenido, Admin WEBNOVA"
```

---

## 📁 Estructura de Carpetas Explicada

```
WebnovaManager/
│
├── config/
│   └── db.php
│       ├─ Conexión mysqli a MySQL
│       ├─ Define: DB_HOST, DB_USER, DB_PASS, DB_NAME
│       ├─ Crea: $conn disponible globalmente
│       └─ Maneja: charset UTF-8, errores
│       
├── auth/
│   ├── login.php
│   │   ├─ Recibe: POST email + password
│   │   ├─ Valida: contra tabla usuarios
│   │   ├─ Usa: password_verify()
│   │   └─ Crea: $_SESSION
│   │
│   └── logout.php
│       ├─ Destruye: session_destroy()
│       └─ Redirige: a admin/index.html
│
├── middleware/
│   └── auth.php
│       ├─ Verifica: ¿usuario logueado?
│       ├─ Protege: rutas que lo requieran
│       ├─ Redirige: si no está autenticado
│       └─ Funciones: esAdmin(), esEditor(), requiereAdmin()
│
├── api/
│   ├── usuarios.php (próximo: CRUD usuarios)
│   ├── paginas.php (próximo: CRUD páginas)
│   └── articulos.php (próximo: CRUD artículos)
│
├── admin/
│   ├── index.html (login - frontend)
│   └── dashboard.php (dashboard - PHP dinámico)
│
├── public/
│   └── index.html (sitio público)
│
├── assets/
│   ├── css/
│   │   └── styles.css
│   └── js/
│       └── (JavaScript del frontend)
│
├── database.sql (esquema + datos demo)
├── INSTALLATION.md (cómo instalar)
└── ARCHITECTURE.md (este archivo)
```

---

## 🎨 Patrones Implementados

### **1. Separación de Responsabilidades**

```
config/  → Solo conexión
auth/    → Solo autenticación
middleware/ → Solo protección
```

**Beneficio:** Cambiar un componente sin afectar otros.

### **2. Prepared Statements (Anti SQL Injection)**

```php
// ❌ MALO - Vulnerable
$sql = "SELECT * FROM usuarios WHERE email = '" . $email . "'";

// ✅ BUENO - Seguro
$sql = "SELECT * FROM usuarios WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
```

### **3. Middleware Pattern**

```php
// En cualquier página protegida:
require_once '../middleware/auth.php';
// Si no está logueado → redirige automáticamente
```

### **4. Role-Based Access Control (RBAC)**

```php
// En middleware/auth.php
function requiereAdmin() {
  if ($_SESSION['usuario_rol'] !== 'admin') {
    header('Location: dashboard.php?error=permiso');
    exit();
  }
}

// En página que solo admins ven:
requiereAdmin(); // Si no eres admin → out
```

### **5. MVC Ligero (sin framework)**

```
MODEL: database.sql + config/db.php (datos)
VIEW: admin/*.html, admin/*.php (presentación)
CONTROL: auth/*.php, middleware/*.php (lógica)
```

---

## 🔐 Seguridad Implementada

| Medida | Ubicación | Beneficio |
|--------|-----------|----------|
| **Password Hashing** | database.sql, auth/login.php | Imposible obtener passwords |
| **Password Verify** | auth/login.php | Verificar sin revelar hash |
| **Prepared Statements** | auth/login.php | Prevenir SQL injection |
| **Session PHP** | auth/login.php | Sesiones servidor (no localStorage) |
| **Middleware** | middleware/auth.php | Proteger rutas sin login |
| **htmlspecialchars()** | admin/dashboard.php | Prevenir XSS |
| **POST only** | auth/login.php | No aceptar GET |
| **Trim + Validate** | auth/login.php | Sanitizar entrada |

### **Vulnerabilidades NO implementadas (Fuera del scope TFG):**
- CSRF tokens (se puede agregar)
- Rate limiting (prevenir fuerza bruta)
- 2FA (autenticación de dos factores)
- HTTPS (requiere certificado SSL)
- Encriptación de datos sensibles
- Logs de auditoría detallados

---

## 🧠 Decisiones de Diseño

### **1. ¿Por qué PHP + MySQL (no Node.js, Python, etc.)?**

```
✅ Ventajas:
  - Instalación simple (XAMPP)
  - Fácil de aprender (TFG)
  - Hosting compartido: disponible en 99% de servidores
  - Session nativa del lenguaje

❌ Desventajas:
  - No es "trendy" como Node.js
  - Escalabilidad limitada comparada con otros
  - Arquitectura más monolítica
```

**Decisión:** Perfecto para TFG educativo y pequeño CMS.

### **2. ¿Por qué session PHP en lugar de JWT?**

```
✅ Session PHP:
  - Almacenado en servidor (más seguro)
  - No requiere código extra
  - Compatible con Apache/XAMPP

JWT:
  - Más moderno (para APIs)
  - Stateless (sin estado en servidor)
  - Mejor para arquitectura distribuida
```

**Decisión:** Sessions es suficiente para este proyecto.

### **3. ¿Por qué no usar framework (Laravel, Symfony)?**

```
✅ Framework (Laravel):
  - Muchas features
  - Seguridad mejorada
  - ORM (Eloquent)

❌ Más complejo:
  - Curva de aprendizaje
  - Configuración inicial
  - Overkill para TFG
```

**Decisión:** Código vanilla PHP es mejor para aprender arquitectura.

### **4. ¿Por qué localStorage → Sessions?**

```
localStorage (primera versión):
  ❌ Datos en cliente (inseguro)
  ❌ Fácil de hacking
  ❌ No escalable

Sessions (nueva versión):
  ✅ Datos en servidor
  ✅ Seguro por defecto
  ✅ Profesional
```

**Decisión:** Evolucionar a backend real = aprendizaje > prototipo.

---

## 📊 Diagrama de Flujo Completo

```
┌──────────────────────────────────────────────────────────┐
│                   USUARIO EN NAVEGADOR                  │
└────────────────────┬─────────────────────────────────────┘
                     │
                     │ Abre: /admin/index.html
                     ▼
        ┌────────────────────────────┐
        │ Formulario Login (HTML)    │
        │ - Email                    │
        │ - Password                 │
        │ - Botón Submit             │
        └────────────┬───────────────┘
                     │
                     │ Ingresa datos + Submit
                     ▼
        ┌────────────────────────────┐
        │ JavaScript Fetch           │
        │ POST /auth/login.php       │
        │ Body: email + password     │
        └────────────┬───────────────┘
                     │
                     ▼ HTTP POST
        ┌─────────────────────────────────┐
        │   Apache Web Server             │
        │   (ejecuta PHP)                 │
        └────────────┬────────────────────┘
                     │
                     ▼
        ┌────────────────────────────────────────┐
        │ auth/login.php                         │
        │ ├─ Validar email + password            │
        │ ├─ Buscar en BD (MySQL)                │
        │ ├─ password_verify()                   │
        │ └─ $_SESSION['logueado'] = true        │
        └────────────┬───────────────────────────┘
                     │
          ┌──────────┴──────────┐
          │                     │
         ❌ Incorrecto         ✅ Correcto
          │                     │
          ▼                     ▼
    JSON error          JSON success
    → Mostrar en        → Redirigir a
      formulario          dashboard.php
          │                     │
          │                     ▼
          │          ┌──────────────────────┐
          │          │ admin/dashboard.php  │
          │          │ require middleware   │
          │          │ Verifica: ¿logueado?│
          │          └──────┬───────────────┘
          │                 │
          │               ✅ Sí
          │                 │
          │                 ▼
          │          ┌──────────────────────┐
          │          │ Mostrar dashboard    │
          │          │ - Nombre usuario     │
          │          │ - Rol                │
          │          │ - Opciones según rol │
          │          └──────────────────────┘
          │
          └──────────────────────────────────────────────┐
                                                         │
                                     Usuario ve formulario
                                     "Email o contraseña
                                      incorrectos"
```

---

## 🎓 Aprendizajes Clave

### **Concepto 1: Sesiones vs Tokens**

```
SESIONES (lo que usamos):
  - Servidor guarda: {"usuario_id": 1, "rol": "admin"}
  - Cliente tiene: cookie PHPSESSID
  - Servidor verifica: ¿existe PHPSESSID?
  - Ventaja: Seguro, servidor controla

TOKENS JWT (futuro):
  - Servidor genera: eyJhbGc.eyJ...
  - Cliente guarda: localStorage
  - Cliente envía: header Authorization
  - Ventaja: Escalable, stateless
```

### **Concepto 2: Hash vs Encriptación**

```
HASH (contraseñas):
  password_hash('0000') → '$2y$10$N9qo8uL...'
  ❌ Irreversible (intentar revertir = imposible)
  ✅ Seguro porque: intentar de fuerza bruta toma años

ENCRIPTACIÓN (datos):
  openssl_encrypt('secret') → 'A3B2C1...'
  ✅ Reversible (con clave)
  Uso: guardar tokens, API keys, etc.
```

### **Concepto 3: Autenticación vs Autorización**

```
AUTENTICACIÓN: ¿Quién eres?
  - Login: verificar email + password
  - Respuesta: SÍ, eres Carlos

AUTORIZACIÓN: ¿Qué puedes hacer?
  - Verificar rol: ¿eres admin?
  - Respuesta: SÍ, puedes editar usuarios
```

---

## 📈 Evolución Futura

### **Fase 1 (Actual)** ✅
- [x] Login + Logout
- [x] Sesiones PHP
- [x] Middleware básico
- [x] Dashboard por rol

### **Fase 2 (Próxima)**
- [ ] API REST: GET/POST/PUT/DELETE usuarios
- [ ] CRUD páginas y artículos
- [ ] Validaciones servidor + cliente
- [ ] Logs de auditoría

### **Fase 3 (Avanzada)**
- [ ] Cambio de contraseña
- [ ] Recuperación de contraseña (email)
- [ ] CSRF tokens
- [ ] Rate limiting

### **Fase 4 (Producción)**
- [ ] HTTPS (SSL cert)
- [ ] Database backup automatizado
- [ ] Monitoreo de errores
- [ ] Performance optimization

---

## 📚 Referencias

- [OWASP - Password Storage Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Password_Storage_Cheat_Sheet.html)
- [PHP Manual - Sessions](https://www.php.net/manual/en/book.session.php)
- [MySQL - MySQLi](https://www.php.net/manual/en/book.mysqli.php)
- [OWASP - SQL Injection Prevention](https://cheatsheetseries.owasp.org/cheatsheets/SQL_Injection_Prevention_Cheat_Sheet.html)

---

**Última actualización:** Mayo 2026  
**Versión:** 1.0  
**Autor:** WebNova Manager TFG

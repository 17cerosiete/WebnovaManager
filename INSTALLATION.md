# 📖 INSTALLATION.md - WebNova Manager Backend Setup

## 📋 Requisitos Previos

Antes de instalar, asegúrate de tener:
- **XAMPP** instalado (Apache + MySQL + PHP)
- **VSCode** para editar código
- **phpMyAdmin** (viene con XAMPP)
- **GitHub** (para versionado)

---

## 🔧 INSTALACIÓN PASO A PASO

### **1. Verificar que XAMPP está funcionando**

```bash
# Abre XAMPP Control Panel
# Haz clic en "Start" para:
# ✓ Apache
# ✓ MySQL
```

Verifica en navegador:
- http://localhost/xampp/ → Debería funcionar
- http://localhost/phpmyadmin/ → Acceso a BD

---

### **2. El proyecto ya está en la carpeta correcta**

Tu proyecto está en:
```
C:\xampp\htdocs\WebnovaManager\
```

Esta es la ubicación **correcta**. No mover.

---

### **3. Importar database.sql**

#### **Opción A: Vía phpMyAdmin (Más fácil)** ✅

```
1. Abre: http://localhost/phpmyadmin/
2. En el menú izquierdo: haz clic en "Nuevo"
3. Ingresa el nombre: "webnova_db"
4. Haz clic en "Crear"
5. Verás la BD creada
6. Haz clic en "webnova_db" (izquierda)
7. Haz clic en tab "Importar"
8. Haz clic en "Seleccionar archivo"
9. Selecciona: C:\xampp\htdocs\WebnovaManager\database.sql
10. Haz clic en "Importar"
```

**✓ Listo!** La BD tiene tablas + usuarios demo.

#### **Opción B: Vía línea de comandos** (Avanzado)

```bash
# Abrir terminal en C:\xampp\mysql\bin\
cd C:\xampp\mysql\bin\

# Importar
mysql -u root < C:\xampp\htdocs\WebnovaManager\database.sql

# Verificar
mysql -u root
> USE webnova_db;
> SELECT * FROM usuarios;
```

---

### **4. Verificar datos en phpMyAdmin**

```
1. Abre: http://localhost/phpmyadmin/
2. Izquierda: selecciona "webnova_db"
3. Haz clic en tabla "usuarios"
4. Deberías ver 4 usuarios:
   - Carlos González
   - Sergio Martínez
   - Ester López
   - Admin WEBNOVA
```

---

### **5. Acceder al login**

```
Abre en navegador:
http://localhost/WebnovaManager/admin/index.html
```

Debería ver el formulario de login.

---

## 🔑 Credenciales de Demo

Todos los usuarios tienen la **misma contraseña**: `0000`

| Nombre | Email | Rol |
|--------|-------|-----|
| Carlos González | carlos@webnova.com | admin |
| Sergio Martínez | sergio@webnova.com | editor |
| Ester López | ester@webnova.com | usuario |
| Admin WEBNOVA | admin@webnova.com | admin |

---

## 🧪 Prueba el Login

1. Abre: http://localhost/WebnovaManager/admin/index.html
2. Ingresa:
   - Email: `admin@webnova.com`
   - Contraseña: `0000`
3. Haz clic en "Iniciar Sesión"
4. **✓ Deberías ver el dashboard**

---

## 📁 Estructura de Carpetas

```
WebnovaManager/
├── config/
│   └── db.php                    ← Conexión MySQL
├── auth/
│   ├── login.php                 ← Procesar login
│   └── logout.php                ← Cerrar sesión
├── middleware/
│   └── auth.php                  ← Protección de rutas
├── admin/
│   ├── index.html                ← Página login
│   └── dashboard.php             ← Dashboard con rol dinámico
├── public/
│   └── index.html                ← Sitio público
├── assets/
│   ├── css/
│   └── js/
├── database.sql                  ← Esquema de BD
└── INSTALLATION.md               ← Este archivo
```

---

## 🔍 Explicación del Flujo de Login (Paso a Paso)

### **Lo que ocurre cuando haces login:**

```
1. NAVEGADOR
   ↓ Usuario ingresa: admin@webnova.com + 0000
   ↓ Hace clic en "Iniciar Sesión"

2. JavaScript (admin/index.html)
   ↓ Intercepta el submit
   ↓ Hace POST a: ../auth/login.php
   ↓ Envía: email=admin@webnova.com&password=0000

3. PHP (auth/login.php)
   ↓ session_start() - Inicia sesión PHP
   ↓ require_once '../config/db.php' - Conecta BD
   ↓ Valida email + password no vacíos
   ↓ Sanitiza email (trim)

4. MYSQL QUERY (config/db.php → MySQL)
   ↓ $conn->prepare("SELECT ... FROM usuarios WHERE email = ?")
   ↓ Busca en BD: ¿existe admin@webnova.com?

5. VERIFICACIÓN (auth/login.php)
   ✓ Usuario encontrado
   ↓ password_verify('0000', hash_en_bd)
   ↓ ¿Coincide? → SÍ ✓

6. CREAR SESIÓN PHP
   ↓ $_SESSION['usuario_id'] = 1
   ↓ $_SESSION['usuario_nombre'] = 'Admin WEBNOVA'
   ↓ $_SESSION['usuario_rol'] = 'admin'
   ↓ $_SESSION['logueado'] = true

7. RESPUESTA JSON
   ↓ {'success': true, 'mensaje': 'Login exitoso'}

8. JAVASCRIPT (admin/index.html)
   ↓ Recibe respuesta success
   ↓ Muestra "✓ Acceso concedido"
   ↓ Después de 800ms: window.location.href = 'dashboard.php'

9. NAVEGADOR CARGA dashboard.php
   ↓ PHP ejecuta: require_once '../middleware/auth.php'
   ↓ Verifica: ¿$_SESSION['logueado'] === true?
   ↓ ✓ SÍ → Muestra contenido

10. DASHBOARD DINÁMICO
    ↓ <?php echo $_SESSION['usuario_nombre']; ?>
    ↓ Muestra: "¡Bienvenido, Admin WEBNOVA!"
    ↓ Según rol: muestra opciones de admin
```

---

## 🔐 Seguridad Básica Implementada

| Medida | Dónde | Por qué |
|--------|-------|---------|
| **password_hash()** | database.sql | Las contraseñas no se guardan en texto plano |
| **password_verify()** | auth/login.php | Verificar sin revelar el hash |
| **Prepared Statements** | auth/login.php | Protección contra SQL injection |
| **Session PHP** | auth/login.php | Sesiones del servidor, no localStorage |
| **Middleware** | middleware/auth.php | Verificar autenticación en páginas protegidas |
| **htmlspecialchars()** | admin/dashboard.php | Prevenir XSS al mostrar datos |
| **POST only** | auth/login.php | No procesar GET requests |

---

## ❌ Si algo no funciona

### **Error: "Error de conexión a BD"**
- Verificar que MySQL está iniciado en XAMPP
- Verificar que la BD `webnova_db` existe en phpMyAdmin

### **Error: "Email o contraseña incorrectos"**
- Verificar credenciales en tabla usuarios (phpMyAdmin)
- Intentar con: admin@webnova.com / 0000

### **Error: "Método no permitido"**
- Verificar que haces POST (no GET) a auth/login.php
- Mirar consola navegador (F12) para más detalles

### **Dashboard en blanco**
- Verificar que middleware/auth.php existe
- Verificar que la sesión se creó (cookie PHPSESSID)
- Mirar error en: Apache error log de XAMPP

---

## 🚀 Próximos Pasos

Una vez funcione todo:

1. **Crear API REST** (auth/*, api/usuarios.php, etc.)
2. **Gestión de Usuarios** (CRUD: crear, leer, actualizar, borrar)
3. **Gestión de Páginas/Artículos**
4. **Roles y Permisos** (más granular)
5. **Validaciones** (servidor + cliente)
6. **Logs de auditoría**

---

## 📚 Archivos Clave para Entender

| Archivo | Qué estudiar |
|---------|-------------|
| database.sql | Estructura de tablas, campos, relaciones |
| config/db.php | Cómo conectar a MySQL |
| auth/login.php | Flujo de autenticación, password_verify() |
| middleware/auth.php | Cómo proteger rutas |
| admin/dashboard.php | Cómo mostrar contenido dinámico según rol |
| admin/index.html | Formulario, AJAX, envío de datos |

---

## ✅ Checklist de Instalación

```
[ ] XAMPP instalado y funcionando
[ ] Apache iniciado en XAMPP
[ ] MySQL iniciado en XAMPP
[ ] database.sql importado en phpMyAdmin
[ ] Verificar tabla usuarios con 4 registros
[ ] http://localhost/WebnovaManager/admin/index.html funciona
[ ] Login con admin@webnova.com / 0000 funciona
[ ] Dashboard.php carga y muestra usuario logueado
[ ] Botón "Cerrar sesión" funciona
[ ] Al cerrar sesión → redirige a login
```

---

**¿Preguntas?** Revisar comentarios en código o en archivo HELP.md (próximamente).

# 📊 WebNova Manager - CMS Backend profesional

> Sistema de Gestión de Contenidos (CMS) tipo WordPress desarrollado como TFG de Desarrollo de Aplicaciones Web.  
> Con backend en PHP + MySQL y autenticación segura.

![Status](https://img.shields.io/badge/status-development-yellow)
![PHP](https://img.shields.io/badge/PHP-7.4+-blue)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-blue)
![License](https://img.shields.io/badge/license-TFG-green)

---

## 🎯 ¿Qué es WebNova Manager?

**WebNova Manager** es un CMS educativo que demuestra cómo construir un **backend profesional** para gestionar sitios web. 

Enfoque: **Aprender arquitectura real** (no solo features).

---

## ✨ Características Principales

### 🔐 **Autenticación Segura**
- Login con email + contraseña
- Password hashing con BCRYPT
- Protección contra SQL injection
- Sesiones PHP seguras

### 👥 **Sistema de Roles**
- **Admin** - acceso total, gestión de usuarios
- **Editor** - crear y editar contenido
- **Usuario** - acceso limitado

### 📊 **Dashboard Dinámico**
- Contenido diferente según rol
- Estadísticas en tiempo real
- Interfaz responsive

### 🛡️ **Seguridad Implementada**
- ✅ Prepared statements
- ✅ Password hashing (BCRYPT)
- ✅ Middleware de autenticación
- ✅ Protección XSS (htmlspecialchars)
- ✅ Validación de entrada
- ✅ POST-only para formularios

---

## 🚀 Quick Start (5 minutos)

### **1. Verificar XAMPP**
```bash
# Inicia Apache + MySQL en XAMPP Control Panel
```

### **2. Importar BD**
```
http://localhost/phpmyadmin
→ Nuevo → "webnova_db" → Crear
→ Importar → Selecciona: database.sql
```

### **3. Login**
```
http://localhost/WebnovaManager/admin/index.html

Email: admin@webnova.com
Contraseña: 0000
```

**✓ ¡Listo!** Deberías ver el dashboard.

---

## 📁 Estructura del Proyecto

```
WebnovaManager/
│
├── 🔌 config/
│   └── db.php                      ← Conexión MySQL
│
├── 🔐 auth/
│   ├── login.php                   ← Procesar login
│   └── logout.php                  ← Cerrar sesión
│
├── 🛡️ middleware/
│   └── auth.php                    ← Proteger rutas
│
├── 🎨 admin/
│   ├── index.html                  ← Formulario login
│   └── dashboard.php               ← Panel principal (dinámico)
│
├── 🌐 public/
│   └── index.html                  ← Sitio público
│
├── 📦 assets/
│   ├── css/
│   └── js/
│
├── 🗄️ database.sql                 ← Esquema + datos demo
├── 📖 INSTALLATION.md              ← Guía instalación
├── 🏗️ ARCHITECTURE.md              ← Explicación arquitectura
└── 📖 README.md                    ← Este archivo
```

---

## 🔑 Usuarios Demo

Todos tienen contraseña: **0000**

| Nombre | Email | Rol |
|--------|-------|-----|
| Carlos González | carlos@webnova.com | admin |
| Sergio Martínez | sergio@webnova.com | editor |
| Ester López | ester@webnova.com | usuario |
| Admin WEBNOVA | admin@webnova.com | admin |

---

## 📚 Documentación Completa

| Documento | Contenido |
|-----------|----------|
| **INSTALLATION.md** | Cómo instalar paso a paso |
| **ARCHITECTURE.md** | Explicación de arquitectura, patrones, seguridad |
| **database.sql** | Esquema de BD con comentarios |

---

## 🏗️ Flujo de Login (Paso a Paso)

```
USUARIO → Formula.login → POST auth/login.php
                              ↓
                         Valida email
                              ↓
                         Busca en MySQL
                              ↓
                         password_verify()
                              ↓
          ┌─────────────────────────────────┐
          ├──────────────────┬──────────────┤
         ❌ Error            ✅ Correcto     │
          │                  │              │
      JSON error         $_SESSION['logueado']
      Mostrar en         Redirige a
      formulario         dashboard.php
                         ↓
                    Muestra contenido
                    según rol
```

---

## 🔐 Conceptos de Seguridad Explicados

### **Password Hashing**

❌ **Mal** (Nunca hacer):
```php
INSERT INTO usuarios (password) VALUES ('0000');
// ¡Si alguien roba BD, ve la contraseña!
```

✅ **Bien** (Lo que hacemos):
```php
$hash = password_hash('0000', PASSWORD_BCRYPT);
// $2y$10$N9qo8uLOickgx2ZMRZoMye...
// Imposible revertir, único, lento
```

### **Por qué BCRYPT es seguro:**

1. **Irreversible** - No se puede obtener contraseña del hash
2. **Único** - Cada hash es diferente (por el "salt" aleatorio)
3. **Lento** - Tarda ms en calcularse (ralentiza ataques)
4. **Escalable** - Puedes aumentar dificultad sin tocar BD vieja

### **Verificación:**

```php
if (password_verify('0000', $hash_en_bd)) {
  // ✓ Contraseña correcta
} else {
  // ✗ Contraseña incorrecta
}
```

---

## 🛡️ Protección Contra Ataques

| Ataque | Prevención | Ubicación |
|--------|-----------|-----------|
| **SQL Injection** | Prepared statements (?) | auth/login.php |
| **XSS** | htmlspecialchars() | admin/dashboard.php |
| **Fuerza Bruta** | (A implementar: rate limiting) | - |
| **CSRF** | (A implementar: tokens) | - |
| **Session Hijacking** | Validación servidor | middleware/auth.php |

---

## 🧪 Probar el Proyecto

### **Test 1: Login correcto**
```
Email: admin@webnova.com
Password: 0000
Resultado: ✓ Dashboard carga
```

### **Test 2: Login incorrecto**
```
Email: admin@webnova.com
Password: xxxx
Resultado: ✓ Error mostrado
```

### **Test 3: Cerrar sesión**
```
Click: "Cerrar sesión"
Resultado: ✓ Redirige a login
```

### **Test 4: Rol admin vs editor**
```
Login como: admin@webnova.com → Ver opciones admin
Login como: sergio@webnova.com → Ver opciones editor
Resultado: ✓ Contenido diferente
```

---

## 🔧 Configuración

### **Database (config/db.php)**

```php
define('DB_HOST', 'localhost');    // MySQL host
define('DB_USER', 'root');         // User
define('DB_PASS', '');             // Password (XAMPP: vacío)
define('DB_NAME', 'webnova_db');   // Database name
```

### **Cambiar credenciales MySQL:**

Si tu MySQL no es `root:` (sin password):

```php
// ANTES
define('DB_USER', 'root');
define('DB_PASS', '');

// DESPUÉS (ejemplo)
define('DB_USER', 'webnova_user');
define('DB_PASS', 'tu_password_aqui');
```

Luego recrear BD con credenciales nuevas.

---

## 🐛 Troubleshooting

| Problema | Solución |
|----------|----------|
| "Error de conexión a BD" | Verificar MySQL iniciado en XAMPP |
| "Email o contraseña incorrectos" | Verificar usuarios en phpMyAdmin |
| "Método no permitido" | Usar POST (no GET) al login |
| "Página en blanco" | Ver error log Apache en XAMPP |
| "No redirecciona a dashboard" | Verificar middleware/auth.php existe |

---

## 📈 Próximos Pasos

### **Fase 2 - API REST**
- [ ] GET /api/usuarios - Listar usuarios
- [ ] POST /api/usuarios - Crear usuario
- [ ] PUT /api/usuarios/{id} - Editar usuario
- [ ] DELETE /api/usuarios/{id} - Borrar usuario

### **Fase 3 - Gestión de Contenido**
- [ ] CRUD de páginas
- [ ] CRUD de artículos
- [ ] CRUD de categorías
- [ ] Sistema de permisos granular

### **Fase 4 - Mejorar Seguridad**
- [ ] CSRF tokens
- [ ] Rate limiting
- [ ] 2FA (Autenticación de 2 factores)
- [ ] HTTPS (SSL certificate)
- [ ] Logs de auditoría

### **Fase 5 - Producción**
- [ ] Deploy a servidor real
- [ ] Database backup automatizado
- [ ] Monitoreo de errores (Sentry)
- [ ] CDN para assets estáticos

---

## 🎓 Recursos Educativos

### **Conceptos estudiados:**

1. **Autenticación**
   - Sessions PHP
   - Password hashing
   - JWT (tokens) [próximo]

2. **Seguridad**
   - SQL Injection prevention
   - XSS prevention
   - CSRF protection [próximo]

3. **Base de datos**
   - Diseño de tablas
   - Relaciones
   - Índices

4. **Arquitectura**
   - Separación de responsabilidades
   - Middleware pattern
   - MVC light

### **Libros recomendados:**

- OWASP Top 10
- "The Web Application Hacker's Handbook"
- PHP Manual (oficial)

---

## 👥 Estructura de Roles

### **Admin 👑**
```
- Ver todos los usuarios
- Crear/editar/borrar usuarios
- Cambiar roles
- Ver logs del sistema
```

### **Editor ✏️**
```
- Crear páginas y artículos
- Editar propios contenidos
- Editar contenidos de otros
- NO gestionar usuarios
```

### **Usuario 👤**
```
- Acceso muy limitado
- Solo ver dashboard
- NO puede crear contenido
```

---

## 📞 Soporte

Este es un proyecto educativo de TFG. 

**Preguntas frecuentes:**
1. ¿Cómo cambio la contraseña de un usuario? → En DB directamente (fase 2)
2. ¿Cómo agrego más usuarios? → Via API (fase 2)
3. ¿Cómo protejo rutas? → middleware/auth.php
4. ¿Es seguro para producción? → No aún (faltan CSRF, rate limit, HTTPS)

---

## 📄 Licencia

Proyecto educativo - Todos los derechos reservados para propósitos del TFG.

---

## ✅ Checklist de Funcionalidad

```
[ ] Login funciona
[ ] Dashboard muestra usuario logueado
[ ] Roles muestran contenido diferente
[ ] Logout redirige a login
[ ] Rutas protegidas no accesibles sin login
[ ] Password_verify funciona correctamente
[ ] Prepared statements previenen SQL injection
[ ] Sesión persiste entre páginas
[ ] Base de datos tiene 4 usuarios demo
```

---

**Última actualización:** Mayo 2026  
**Versión:** 1.0  
**Estado:** Development  
**Autor:** WebNova Manager TFG Team

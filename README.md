# 🚀 WebNova Manager - CMS

Sistema de Gestión de Contenidos simple y funcional.

## ⚡ Acceso Rápido

```
URL: http://localhost/WebnovaManager/admin/
```

**Modo Desarrollo:** Acceso automático sin contraseña.

### Usuarios de Prueba (pueden cambiarse en quick-login.html):
- 👤 **Carlos González** (Admin)
- ✏️ **Sergio Martínez** (Editor)  
- 👁️ **Ester López** (Usuario)

## 📁 Estructura

```
WebnovaManager/
├── admin/          → Panel de administración y dashboards
├── api/            → Endpoints de API
├── assets/         → CSS, JS, imágenes
├── auth/           → Lógica de autenticación
├── config/         → Configuración (BD, sesiones, dev-mode)
├── middleware/     → Middleware de autenticación
├── public/         → Sitio público
├── utils/          → Funciones auxiliares
├── uploads/        → Archivos subidos
└── database.sql    → Schema de BD
```

## 🔧 Instalación

1. Copiar proyecto a `c:\xampp\htdocs\WebnovaManager\`
2. Importar `database.sql` en phpMyAdmin
3. Acceder a `http://localhost/WebnovaManager/admin/`

## 🛠️ Componentes

- **Backend:** PHP 7.4+ con MySQLi
- **Frontend:** HTML5, CSS3, JavaScript vanilla
- **BD:** MySQL con UTF-8mb4
- **Sesiones:** PHP nativo

## 📝 Notas

- **DEV_MODE activo:** Permite acceso sin autenticación (config/dev-mode.php)
- Para producción: Desactivar DEV_MODE en config/dev-mode.php
- Base de datos: webnova_db
- Usuario MySQL: root (sin password por defecto en XAMPP)

---

**Status:** ✅ Limpio y listo para usar

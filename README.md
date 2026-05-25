# WebNova Manager - CMS

Sistema de gestion de contenidos para el TFG de WebNova Digital S.L.

## Acceso

URL local:

```text
http://localhost/WebnovaManager/admin/
```

El proyecto esta en modo desarrollo y permite acceso rapido desde `admin/quick-login.html`.

## Funciones principales

- Gestion de paginas con bloques editables.
- Widgets reutilizables con esquema de claves estandar.
- Vista previa de paginas antes de guardar.
- Publicacion de paginas mediante `public/page.php?slug=...`.
- Bootstrap automatico de base de datos para entorno XAMPP.

## Estructura

```text
admin/       Panel de administracion
api/         Endpoints PHP
assets/      CSS y JavaScript
config/      Conexion y configuracion
docs/        Documentacion tecnica del proyecto
public/      Sitio publico
utils/       Helpers de autenticacion y renderizado
```

## Widget schema

El contrato de claves para widgets esta documentado en:

```text
docs/WIDGET_SCHEMA.md
```

<?php
/**
 * admin/help/index.php
 *
 * Centro de ayuda integrado
 */

require_once '../../middleware/auth.php';

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centro de Ayuda - WebNova Manager</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto;
            background: #f3f4f6;
            color: #1f2937;
        }

        .navbar {
            background: white;
            border-bottom: 1px solid #e5e7eb;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar h1 {
            color: #2563eb;
            font-size: 1.5rem;
        }

        .container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .breadcrumb {
            margin-bottom: 2rem;
            font-size: 0.9rem;
            color: #6b7280;
        }

        .breadcrumb a {
            color: #2563eb;
            text-decoration: none;
        }

        .help-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .help-card h2 {
            color: #2563eb;
            margin-bottom: 1rem;
            font-size: 1.3rem;
        }

        .help-card h3 {
            color: #1f2937;
            margin-top: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .help-card p {
            line-height: 1.6;
            color: #6b7280;
            margin-bottom: 1rem;
        }

        .help-card ul {
            margin-left: 1.5rem;
            margin-bottom: 1rem;
        }

        .help-card li {
            margin-bottom: 0.5rem;
            color: #6b7280;
        }

        .code-block {
            background: #f9fafb;
            padding: 1rem;
            border-radius: 0.5rem;
            border-left: 4px solid #2563eb;
            margin: 1rem 0;
            font-family: monospace;
            overflow-x: auto;
        }

        .tip {
            background: #dbeafe;
            border-left: 4px solid #0284c7;
            padding: 1rem;
            border-radius: 0.5rem;
            margin: 1rem 0;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .grid-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 1.5rem;
            text-decoration: none;
            color: inherit;
            transition: all 250ms;
        }

        .grid-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .grid-card h3 {
            color: #2563eb;
            margin-bottom: 0.5rem;
        }

        .grid-card p {
            color: #6b7280;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<nav class="navbar">
    <h1>📊 WebNova Manager</h1>
    <a href="../../auth/logout.php" style="color: #ef4444; text-decoration: none; font-weight: 600;">Salir</a>
</nav>

<div class="container">

    <div class="breadcrumb">
        <a href="../dashboard.php">Dashboard</a> / Ayuda
    </div>

    <div class="help-card">
        <h2>❓ Centro de Ayuda - WebNova Manager</h2>
        <p>
            Bienvenido al centro de ayuda. Aquí encontrarás tutoriales y explicaciones
            sobre cómo usar cada función del CMS.
        </p>
    </div>

    <!-- ATAJOS RÁPIDOS -->
    <h2 style="margin-bottom: 1rem; color: #1f2937;">🚀 Acceso Rápido</h2>
    <div class="grid">
        <div class="grid-card">
            <h3>📄 Crear Página</h3>
            <p>Aprende a crear páginas estáticas para tu sitio web</p>
            <a href="#pages" style="color: #2563eb; text-decoration: none;">Leer más →</a>
        </div>
        <div class="grid-card">
            <h3>📝 Escribir Artículo</h3>
            <p>Publica contenido en tu blog</p>
            <a href="#posts" style="color: #2563eb; text-decoration: none;">Leer más →</a>
        </div>
        <div class="grid-card">
            <h3>👥 Gestionar Usuarios</h3>
            <p>Crea y edita usuarios del sistema</p>
            <a href="#users" style="color: #2563eb; text-decoration: none;">Leer más →</a>
        </div>
        <div class="grid-card">
            <h3>⚙️ Configuración</h3>
            <p>Personaliza tu sitio web</p>
            <a href="#settings" style="color: #2563eb; text-decoration: none;">Leer más →</a>
        </div>
    </div>

    <!-- TUTORIALES -->
    <div class="help-card" id="pages">
        <h2>📄 Crear y Gestionar Páginas</h2>

        <h3>¿Qué son las páginas?</h3>
        <p>Las páginas son contenido estático de tu sitio web. Ejemplos: Quiénes somos, Contacto, Servicios, etc.</p>

        <h3>Crear una página</h3>
        <ol style="margin-left: 1.5rem; color: #6b7280;">
            <li>Ve a <strong>Gestionar Páginas</strong></li>
            <li>Haz clic en <strong>➕ Nueva Página</strong></li>
            <li>Completa el título y slug (URL amigable)</li>
            <li>Escribe el contenido usando el editor</li>
            <li>Añade SEO (meta description, palabras clave)</li>
            <li>Haz clic en <strong>Publicar</strong></li>
        </ol>

        <div class="tip">
            💡 <strong>Tip:</strong> El slug se genera automáticamente desde el título, pero puedes personalizarlo.
        </div>

        <h3>Editar página</h3>
        <p>Ve a la lista de páginas y haz clic en "✎ Editar" para modificar cualquier página existente.</p>
    </div>

    <div class="help-card" id="posts">
        <h2>📝 Blog - Crear Artículos</h2>

        <h3>¿Qué son los artículos?</h3>
        <p>Los artículos son posts del blog. Aparecen ordenados por fecha (más recientes primero).</p>

        <h3>Crear un artículo</h3>
        <ol style="margin-left: 1.5rem; color: #6b7280;">
            <li>Ve a <strong>Gestionar Artículos</strong></li>
            <li>Haz clic en <strong>➕ Nuevo Artículo</strong></li>
            <li>Escribe título y contenido</li>
            <li>Elige categoría (opcional)</li>
            <li>Haz clic en <strong>Publicar</strong></li>
        </ol>

        <div class="tip">
            💡 <strong>Tip:</strong> Los borradores no aparecen en el sitio público hasta que los publiques.
        </div>
    </div>

    <div class="help-card" id="users">
        <h2>👥 Gestión de Usuarios</h2>

        <h3>Roles disponibles</h3>
        <ul>
            <li><strong>Admin:</strong> Acceso total. Puede crear usuarios, eliminar contenido, cambiar configuración.</li>
            <li><strong>Editor:</strong> Puede crear y editar páginas y artículos, pero no gestionar usuarios.</li>
            <li><strong>Usuario:</strong> Acceso limitado. Solo lectura.</li>
        </ul>

        <h3>Crear usuario</h3>
        <ol style="margin-left: 1.5rem; color: #6b7280;">
            <li>Ve a <strong>Gestionar Usuarios</strong> (solo admin)</li>
            <li>Haz clic en <strong>➕ Nuevo Usuario</strong></li>
            <li>Completa nombre, email y contraseña</li>
            <li>Asigna rol</li>
            <li>Haz clic en <strong>Crear</strong></li>
        </ol>

        <div class="tip">
            ⚠️ <strong>Importante:</strong> Las contraseñas se guardan encriptadas con BCRYPT. No se pueden recuperar directamente.
        </div>
    </div>

    <div class="help-card" id="settings">
        <h2>⚙️ Configuración del Sitio</h2>

        <h3>¿Qué configuraciones hay?</h3>
        <ul>
            <li><strong>Título del sitio:</strong> Aparece en el navegador y header</li>
            <li><strong>Descripción:</strong> Meta description para buscadores (SEO)</li>
            <li><strong>URL:</strong> Dirección principal del sitio</li>
            <li><strong>Email de contacto:</strong> Para formularios</li>
            <li><strong>Email de soporte:</strong> Para notificaciones</li>
        </ul>

        <h3>Cambiar configuración</h3>
        <ol style="margin-left: 1.5rem; color: #6b7280;">
            <li>Ve a <strong>Configuración</strong> (solo admin)</li>
            <li>Modifica los campos que desees</li>
            <li>Haz clic en <strong>Guardar Configuración</strong></li>
        </ol>
    </div>

    <!-- PREGUNTAS FRECUENTES -->
    <div class="help-card">
        <h2>❓ Preguntas Frecuentes</h2>

        <h3>¿Cuál es la diferencia entre página y artículo?</h3>
        <p>
            Las <strong>páginas</strong> son estáticas (no cambian: Quiénes somos, Contacto).
            Los <strong>artículos</strong> son dinámicos y aparecen en un blog (noticias, tutoriales).
        </p>

        <h3>¿Cómo cambio la contraseña?</h3>
        <p>Solicita al administrador que cambien tu contraseña (Gestionar Usuarios).</p>

        <h3>¿Puedo programar la publicación?</h3>
        <p>Actualmente no, pero en futuras versiones habrá programación de publicaciones.</p>

        <h3>¿Cómo hago SEO?</h3>
        <p>
            Cada página y artículo tiene campos SEO:
        </p>
        <ul>
            <li>Meta description (lo que ves en Google)</li>
            <li>Palabras clave (keywords)</li>
            <li>Slug optimizado (URL amigable)</li>
        </ul>

        <h3>¿Es segura mi contraseña?</h3>
        <p>
            Sí. Las contraseñas se guardan usando BCRYPT, un algoritmo de encriptación
            de nivel empresarial. No es posible acceder a ellas ni siquiera para los admins.
        </p>
    </div>

    <!-- CONTACTO SOPORTE -->
    <div class="help-card" style="background: #eff6ff; border-left: 4px solid #2563eb;">
        <h2 style="color: #2563eb;">📞 ¿Necesitas ayuda?</h2>
        <p>Si no encuentras respuesta a tu pregunta, contacta con soporte:</p>
        <p style="margin-top: 1rem;">
            <strong>Email:</strong> <a href="mailto:soporte@webnova.com" style="color: #2563eb;">soporte@webnova.com</a>
        </p>
    </div>

</div>

</body>
</html>

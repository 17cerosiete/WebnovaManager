<?php
/**
 * admin/settings/general.php
 *
 * Configuración general del CMS
 */

require_once '../../middleware/auth.php';
require_once '../../config/db.php';

if (!esAdmin()) {
    header('Location: ../dashboard.php?error=permiso');
    exit();
}

// Obtener configuración
$config = [];
$result = $conn->query("SELECT clave, valor FROM configuracion");
while ($row = $result->fetch_assoc()) {
    $config[$row['clave']] = $row['valor'];
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración - WebNova Manager</title>
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
            max-width: 900px;
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

        .form-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        input[type="text"],
        input[type="email"],
        textarea {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 1rem;
            font-family: inherit;
        }

        input[type="text"]:focus,
        textarea:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .btn {
            padding: 0.75rem 1.5rem;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            font-weight: 600;
        }

        .btn:hover {
            background: #1d4ed8;
        }

        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border-left: 4px solid #10b981;
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
        <a href="../dashboard.php">Dashboard</a> / Configuración
    </div>

    <div class="form-card">
        <h2 style="margin-bottom: 1.5rem;">⚙️ Configuración General</h2>

        <form method="POST" action="../../api/settings.php">

            <div class="form-group">
                <label for="sitio_titulo">Título del Sitio</label>
                <input type="text" id="sitio_titulo" name="sitio_titulo" value="<?php echo htmlspecialchars($config['sitio_titulo'] ?? ''); ?>" required>
                <small style="color: #6b7280;">Aparecerá en el navegador y header</small>
            </div>

            <div class="form-group">
                <label for="sitio_descripcion">Descripción del Sitio</label>
                <textarea id="sitio_descripcion" name="sitio_descripcion" rows="3"><?php echo htmlspecialchars($config['sitio_descripcion'] ?? ''); ?></textarea>
                <small style="color: #6b7280;">Meta description para buscadores</small>
            </div>

            <div class="form-group">
                <label for="sitio_url">URL del Sitio</label>
                <input type="text" id="sitio_url" name="sitio_url" value="<?php echo htmlspecialchars($config['sitio_url'] ?? ''); ?>" required>
                <small style="color: #6b7280;">Ej: https://tudominio.com</small>
            </div>

            <div class="form-group">
                <label for="email_contacto">Email de Contacto</label>
                <input type="email" id="email_contacto" name="email_contacto" value="<?php echo htmlspecialchars($config['email_contacto'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="email_soporte">Email de Soporte</label>
                <input type="email" id="email_soporte" name="email_soporte" value="<?php echo htmlspecialchars($config['email_soporte'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="items_por_pagina">Items por Página</label>
                <input type="text" id="items_por_pagina" name="items_por_pagina" value="<?php echo htmlspecialchars($config['items_por_pagina'] ?? '10'); ?>">
                <small style="color: #6b7280;">En listados de páginas, posts, etc.</small>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="permitir_comentarios" value="1" <?php echo ($config['permitir_comentarios'] == 1) ? 'checked' : ''; ?>>
                    Permitir comentarios en posts
                </label>
            </div>

            <button type="submit" class="btn">💾 Guardar Configuración</button>

        </form>
    </div>

    <div class="form-card" style="background: #f0f9ff; border-left: 4px solid #2563eb;">
        <h3 style="color: #2563eb; margin-bottom: 1rem;">ℹ️ Información del Sistema</h3>
        <ul style="line-height: 1.8;">
            <li><strong>BD:</strong> webnova_db</li>
            <li><strong>Usuario BD:</strong> root</li>
            <li><strong>Versión PHP:</strong> <?php echo phpversion(); ?></li>
            <li><strong>Usuarios registrados:</strong>
                <?php
                $count = $conn->query("SELECT COUNT(*) as total FROM usuarios");
                $c = $count->fetch_assoc();
                echo $c['total'];
                ?>
            </li>
        </ul>
    </div>

</div>

</body>
</html>

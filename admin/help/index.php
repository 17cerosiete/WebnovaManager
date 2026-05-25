<?php
require_once '../../middleware/auth.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayuda - WebNova Manager</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <style>
        body { background: #f3f4f6; }
        .navbar { background: white; border-bottom: 1px solid #e5e7eb; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; }
        .container { max-width: 920px; margin: 2rem auto; padding: 0 1rem; }
        .card { background: white; border: 1px solid #dbe3ef; border-radius: 8px; padding: 1.5rem; margin-bottom: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
        li { margin-bottom: .5rem; }
    </style>
</head>
<body>
    <nav class="navbar">
        <h1>WebNova Manager</h1>
        <a href="../dashboard.php">Dashboard</a>
    </nav>
    <main class="container">
        <section class="card">
            <h2>Flujo recomendado</h2>
            <ol>
                <li>Crea o edita widgets desde el apartado Widgets.</li>
                <li>Crea una pagina y anade bloques o widgets reutilizables.</li>
                <li>Usa Vista previa para revisar el resultado antes de guardar.</li>
                <li>Marca la pagina como publicada para verla en el sitio publico.</li>
            </ol>
        </section>
        <section class="card">
            <h2>Widgets</h2>
            <p>Los widgets usan un esquema de claves definido en <code>docs/WIDGET_SCHEMA.md</code>. El HTML se genera automaticamente desde el tipo y su configuracion.</p>
        </section>
        <section class="card">
            <h2>Contexto TFG</h2>
            <p>El prototipo responde al comunicado de WebNova Digital: mejora de usabilidad, responsive, gestion de contenidos, SEO basico y autonomia del cliente.</p>
        </section>
    </main>
</body>
</html>

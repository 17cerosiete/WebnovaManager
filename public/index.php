<?php
require_once '../config/db.php';

$result = $conn->query('SELECT id, titulo, slug, contenido, fecha_actualizacion FROM paginas WHERE publicada = 1 ORDER BY fecha_actualizacion DESC');
$pages = [];
while ($row = $result->fetch_assoc()) {
    $pages[] = $row;
}

function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="WebNova Manager - CMS para crear y publicar paginas web">
    <title>WebNova Manager</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        body { background: #f8fafc; }
        .site-header { background: white; border-bottom: 1px solid #dbe3ef; }
        .site-nav { max-width: 1120px; margin: 0 auto; padding: 1rem; display: flex; justify-content: space-between; align-items: center; gap: 1rem; }
        .brand { font-weight: 800; color: #0f172a; font-size: 1.2rem; }
        .hero { background: #0f172a; color: white; padding: 4rem 1rem; }
        .hero-inner { max-width: 1120px; margin: 0 auto; }
        .hero h1 { max-width: 760px; color: white; font-size: 2.8rem; }
        .hero p { max-width: 720px; color: #cbd5e1; font-size: 1.15rem; }
        .section { max-width: 1120px; margin: 0 auto; padding: 2.5rem 1rem; }
        .page-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1rem; }
        .page-card { background: white; border: 1px solid #dbe3ef; border-radius: 8px; padding: 1.25rem; box-shadow: 0 10px 24px rgba(15, 23, 42, 0.05); }
        .page-card h3 { margin-bottom: .5rem; }
        .empty-state { background: white; border: 1px dashed #94a3b8; border-radius: 8px; padding: 2rem; color: #64748b; }
    </style>
</head>
<body>
    <header class="site-header">
        <nav class="site-nav">
            <a class="brand" href="index.php">WebNova Manager</a>
            <a class="btn btn-primary" href="../admin/index.html">Admin</a>
        </nav>
    </header>

    <section class="hero">
        <div class="hero-inner">
            <h1>CMS responsive para crear paginas web reutilizables</h1>
            <p>Prototipo TFG orientado a mejorar usabilidad, gestion de contenidos, publicacion y mantenimiento para WebNova Digital S.L.</p>
        </div>
    </section>

    <main class="section">
        <h2>Paginas publicadas</h2>
        <?php if (count($pages) > 0): ?>
            <div class="page-grid">
                <?php foreach ($pages as $page): ?>
                    <article class="page-card">
                        <h3><?php echo h($page['titulo']); ?></h3>
                        <p class="text-muted">Actualizada el <?php echo date('d/m/Y H:i', strtotime($page['fecha_actualizacion'])); ?></p>
                        <a class="btn btn-secondary" href="page.php?slug=<?php echo h($page['slug']); ?>">Ver pagina</a>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <p>No hay paginas publicadas todavia. Crea una pagina desde el panel y marcala como publicada.</p>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>

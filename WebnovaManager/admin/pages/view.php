<?php
/**
 * admin/pages/view.php
 *
 * Previsualizar una página guardada.
 */

require_once '../../middleware/auth.php';
require_once '../../config/db.php';

if (!esEditor()) {
    header('Location: ../dashboard.php?error=permiso');
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    header('Location: list.php?error=missing_id');
    exit();
}

$stmt = $conn->prepare("SELECT p.titulo, p.slug, p.contenido, p.publicada, u.nombre as autor, p.fecha_creacion, p.fecha_actualizacion FROM paginas p LEFT JOIN usuarios u ON p.autor_id = u.id WHERE p.id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    header('Location: list.php?error=not_found');
    exit();
}
$page = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vista previa: <?php echo htmlspecialchars($page['titulo']); ?> - WebNova Manager</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background: #f3f4f6; color: #111827; margin: 0; }
        .navbar { background: white; border-bottom: 1px solid #e5e7eb; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; }
        .navbar a { color: #2563eb; text-decoration: none; font-weight: 600; }
        .container { max-width: 960px; margin: 2rem auto; padding: 0 1rem; }
        .header { margin-bottom: 2rem; }
        .header h1 { margin-bottom: 0.5rem; font-size: 2.2rem; }
        .header p { color: #6b7280; }
        .page-status { display: inline-flex; gap: 0.5rem; align-items: center; margin-top: 0.5rem; }
        .badge { padding: 0.35rem 0.8rem; border-radius: 999px; font-size: 0.8rem; font-weight: 700; }
        .badge-published { background: #d1fae5; color: #065f46; }
        .badge-draft { background: #fef3c7; color: #92400e; }
        .page-content { background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
        .page-content img { max-width: 100%; height: auto; border-radius: 0.75rem; }
    </style>
</head>
<body>
<nav class="navbar">
    <div><strong>Vista previa</strong></div>
    <a href="list.php">Volver a la lista</a>
</nav>
<div class="container">
    <div class="header">
        <h1><?php echo htmlspecialchars($page['titulo']); ?></h1>
        <p>URL: /pagina/<?php echo htmlspecialchars($page['slug']); ?> | Autor: <?php echo htmlspecialchars($page['autor'] ?? 'Sistema'); ?></p>
        <div class="page-status">
            <span class="badge <?php echo $page['publicada'] ? 'badge-published' : 'badge-draft'; ?>">
                <?php echo $page['publicada'] ? 'Publicado' : 'Borrador'; ?>
            </span>
            <span>Actualizado: <?php echo date('d/m/Y H:i', strtotime($page['fecha_actualizacion'])); ?></span>
        </div>
    </div>
    <div class="page-content">
        <?php echo $page['contenido']; ?>
    </div>
</div>
</body>
</html>

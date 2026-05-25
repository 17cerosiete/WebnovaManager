<?php
require_once '../config/db.php';
require_once '../utils/render_helpers.php';

$slug = trim($_GET['slug'] ?? '');
if ($slug === '') {
    http_response_code(404);
    die('Pagina no encontrada');
}

$stmt = $conn->prepare('SELECT * FROM paginas WHERE slug = ? AND publicada = 1 LIMIT 1');
$stmt->bind_param('s', $slug);
$stmt->execute();
$page = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$page) {
    http_response_code(404);
    die('Pagina no encontrada o no publicada');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo webnova_h($page['titulo']); ?> - WebNova Manager</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/rendered-page.css">
</head>
<body>
    <main class="rendered-page">
        <?php echo webnova_render_blocks($page['contenido']); ?>
    </main>
</body>
</html>

<?php
require_once '../../middleware/auth.php';
require_once '../../config/db.php';
require_once '../../utils/render_helpers.php';

if (!esEditor()) {
    header('Location: ../dashboard.php?error=permiso');
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $conn->prepare('SELECT p.*, u.nombre AS autor FROM paginas p LEFT JOIN usuarios u ON p.autor_id = u.id WHERE p.id = ? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$page = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$page) {
    header('Location: list.php?error=notfound');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo webnova_h($page['titulo']); ?> - Vista previa</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <link rel="stylesheet" href="../../assets/css/rendered-page.css">
</head>
<body>
    <header class="admin-preview-bar">
        <div>
            <strong><?php echo webnova_h($page['titulo']); ?></strong>
            <span><?php echo $page['publicada'] ? 'Publicada' : 'Borrador'; ?> · /<?php echo webnova_h($page['slug']); ?></span>
        </div>
        <nav>
            <a class="btn btn-secondary" href="list.php">Volver</a>
            <a class="btn btn-primary" href="edit.php?id=<?php echo (int)$page['id']; ?>">Editar</a>
            <a class="btn btn-secondary" href="../../public/page.php?slug=<?php echo webnova_h($page['slug']); ?>" target="_blank">Vista publica</a>
        </nav>
    </header>

    <main class="rendered-page">
        <?php echo webnova_render_blocks($page['contenido']); ?>
    </main>
</body>
</html>

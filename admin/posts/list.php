<?php
/**
 * admin/posts/list.php
 *
 * Listar todos los artículos del blog
 * Con opciones: crear, editar, eliminar, categoría
 */

require_once '../../middleware/auth.php';
require_once '../../config/db.php';

if (!esEditor()) {
    header('Location: ../dashboard.php?error=permiso');
    exit();
}

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$items_por_pagina = 10;
$offset = ($page - 1) * $items_por_pagina;

// Total posts
$total_result = $conn->query("SELECT COUNT(*) as total FROM articulos");
$total_row = $total_result->fetch_assoc();
$total_paginas = ceil($total_row['total'] / $items_por_pagina);

// Obtener posts
$query = "
    SELECT a.id, a.titulo, a.slug, a.publicado, a.fecha_creacion, a.fecha_actualizacion, u.nombre as autor
    FROM articulos a
    LEFT JOIN usuarios u ON a.autor_id = u.id
    ORDER BY a.fecha_actualizacion DESC
    LIMIT $items_por_pagina OFFSET $offset
";
$result = $conn->query($query);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artículos - WebNova Manager</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
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
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .navbar h1 {
            color: #2563eb;
            font-size: 1.5rem;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 600;
            background: #dbeafe;
            color: #0c4a6e;
        }

        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
            transition: all 250ms;
        }

        .btn-primary {
            background: #2563eb;
            color: white;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }

        .btn-secondary {
            background: #e5e7eb;
            color: #1f2937;
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .header h2 {
            color: #1f2937;
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

        .table-responsive {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f9fafb;
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: #6b7280;
            border-bottom: 1px solid #e5e7eb;
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .status {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-published {
            background: #d1fae5;
            color: #065f46;
        }

        .status-draft {
            background: #fef3c7;
            color: #92400e;
        }

        .actions {
            display: flex;
            gap: 0.5rem;
        }

        .actions a, .actions button {
            padding: 0.25rem 0.75rem;
            font-size: 0.85rem;
            border: 1px solid #d1d5db;
            background: white;
            color: #2563eb;
            border-radius: 0.375rem;
            cursor: pointer;
            text-decoration: none;
            transition: all 250ms;
        }

        .actions a:hover {
            background: #eff6ff;
            border-color: #2563eb;
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: all 250ms;
        }

        .card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transform: translateY(-2px);
        }

        .card-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.5rem;
            display: block;
        }

        .card-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.9rem;
            color: #6b7280;
        }

        .card-content {
            padding: 1.5rem;
        }

        .card-content p {
            color: #4b5563;
            line-height: 1.5;
            margin: 0;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .card-footer {
            padding: 1rem 1.5rem;
            background: #f9fafb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .status {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-published {
            background: #d1fae5;
            color: #065f46;
        }

        .status-draft {
            background: #fef3c7;
            color: #92400e;
        }

        .actions {
            display: flex;
            gap: 0.5rem;
        }

        .actions a {
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
            border: 1px solid #d1d5db;
            background: white;
            color: #2563eb;
            border-radius: 0.5rem;
            text-decoration: none;
            transition: all 250ms;
        }

        .actions a:hover {
            background: #eff6ff;
            border-color: #2563eb;
        }

        .actions a.danger {
            color: #dc2626;
            border-color: #dc2626;
        }

        .actions a.danger:hover {
            background: #fef2f2;
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
    <h1>📊 WebNova Manager</h1>
    <div class="user-info">
        <div>
            <p><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></p>
            <span class="badge"><?php echo htmlspecialchars($_SESSION['usuario_rol']); ?></span>
        </div>
        <a href="../../auth/logout.php" class="btn btn-secondary">Salir</a>
    </div>
</nav>

<!-- CONTENIDO -->
<div class="container">

    <div class="breadcrumb">
        <a href="../dashboard.php">Dashboard</a> / Artículos
    </div>

    <div class="header">
        <h2>📝 Gestión de Artículos</h2>
        <a href="create.php" class="btn btn-primary">➕ Nuevo Artículo</a>
    </div>

    <div class="cards-grid">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($post = $result->fetch_assoc()): ?>
                <div class="card">
                    <div class="card-header">
                        <a href="edit.php?id=<?php echo $post['id']; ?>" class="card-title">
                            <?php echo htmlspecialchars($post['titulo']); ?>
                        </a>
                        <div class="card-meta">
                            <span><?php echo htmlspecialchars($post['autor'] ?? 'Sistema'); ?></span>
                            <span><?php echo date('d/m/Y', strtotime($post['fecha_creacion'])); ?></span>
                        </div>
                    </div>
                    <div class="card-content">
                        <p><?php 
                            $contenido = $post['contenido'] ?? '';
                            echo strip_tags(substr($contenido, 0, 150)) . (strlen(strip_tags($contenido)) > 150 ? '...' : ''); 
                        ?></p>
                    </div>
                    <div class="card-footer">
                        <span class="status <?php echo $post['publicado'] ? 'status-published' : 'status-draft'; ?>">
                            <?php echo $post['publicado'] ? '✓ Publicado' : '📝 Borrador'; ?>
                        </span>
                        <div class="actions">
                            <a href="edit.php?id=<?php echo $post['id']; ?>">✎ Editar</a>
                            <a href="delete.php?id=<?php echo $post['id']; ?>" class="danger" onclick="return confirm('¿Eliminar artículo?')">🗑️ Eliminar</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state" style="grid-column: 1 / -1;">
                <p>📭 No hay artículos aún</p>
                <a href="create.php" class="btn btn-primary">Crear primer artículo</a>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($total_paginas > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                <a href="?page=<?php echo $i; ?>" <?php echo $page === $i ? 'class="active"' : ''; ?>>
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>

</div>

</body>
</html>

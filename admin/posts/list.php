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

// Total widgets
$total_result = $conn->query("SELECT COUNT(*) as total FROM widgets");
$total_row = $total_result->fetch_assoc();
$total_paginas = ceil($total_row['total'] / $items_por_pagina);

// Obtener widgets
$query = "
    SELECT id, nombre, tipo, fecha_creacion, fecha_actualizacion
    FROM widgets
    ORDER BY fecha_actualizacion DESC
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

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #6b7280;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 2rem;
        }

        .pagination a, .pagination span {
            padding: 0.5rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            text-decoration: none;
            color: #2563eb;
        }

        .pagination .active {
            background: #2563eb;
            color: white;
            border-color: #2563eb;
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
        <a href="../dashboard.php">Dashboard</a> / Widgets
    </div>

    <div class="header">
        <h2>📦 Gestión de Widgets</h2>
        <a href="create.php" class="btn btn-primary">➕ Nuevo Widget</a>
    </div>

    <div class="table-responsive">
        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Nombre del Widget</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Creado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($post = $result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($post['nombre']); ?></strong>
                            </td>
                            <td><?php echo htmlspecialchars($post['tipo']); ?></td>
                            <td>
                                <span class="status status-published">
                                    📦 Módulo
                                </span>
                            </td>
                            <td><?php echo date('d/m/Y H:i', strtotime($post['fecha_creacion'])); ?></td>
                            <td>
                                <div class="actions">
                                    <a href="edit.php?id=<?php echo $post['id']; ?>">✎ Editar</a>
                                    <a href="delete.php?id=<?php echo $post['id']; ?>" onclick="return confirm('¿Eliminar?')">🗑️ Eliminar</a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <p>📭 No hay widgets aún</p>
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

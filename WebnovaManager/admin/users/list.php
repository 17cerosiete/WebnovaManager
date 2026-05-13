<?php
/**
 * admin/users/list.php
 *
 * Gestión de usuarios - solo para ADMIN
 */

require_once '../../middleware/auth.php';
require_once '../../config/db.php';

// Solo admins
if (!esAdmin()) {
    header('Location: ../dashboard.php?error=permiso');
    exit();
}

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$items_por_pagina = 10;
$offset = ($page - 1) * $items_por_pagina;

// Total usuarios
$total_result = $conn->query("SELECT COUNT(*) as total FROM usuarios");
$total_row = $total_result->fetch_assoc();
$total_paginas = ceil($total_row['total'] / $items_por_pagina);

// Obtener usuarios
$query = "
    SELECT id, nombre, email, rol, fecha_creacion
    FROM usuarios
    ORDER BY fecha_creacion DESC
    LIMIT $items_por_pagina OFFSET $offset
";
$result = $conn->query($query);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios - WebNova Manager</title>
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
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .navbar h1 {
            color: #2563eb;
            font-size: 1.5rem;
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

        .btn {
            padding: 0.5rem 1rem;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .btn:hover {
            background: #1d4ed8;
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

        .badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .badge-admin {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-editor {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-usuario {
            background: #d1fae5;
            color: #065f46;
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
        }

        .actions a:hover {
            background: #eff6ff;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #6b7280;
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
        <a href="../dashboard.php">Dashboard</a> / Usuarios
    </div>

    <div class="header">
        <h2>👥 Gestión de Usuarios</h2>
        <a href="create.php" class="btn">➕ Nuevo Usuario</a>
    </div>

    <div class="table-responsive">
        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Creado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = $result->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($user['nombre']); ?></strong></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <span class="badge badge-<?php echo $user['rol']; ?>">
                                    <?php echo htmlspecialchars($user['rol']); ?>
                                </span>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($user['fecha_creacion'])); ?></td>
                            <td>
                                <div class="actions">
                                    <a href="edit.php?id=<?php echo $user['id']; ?>">✎ Editar</a>
                                    <?php if ($user['id'] !== $_SESSION['usuario_id']): ?>
                                        <a href="delete.php?id=<?php echo $user['id']; ?>" onclick="return confirm('¿Eliminar usuario?')">🗑️ Eliminar</a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <p>📭 No hay usuarios</p>
            </div>
        <?php endif; ?>
    </div>

</div>

</body>
</html>

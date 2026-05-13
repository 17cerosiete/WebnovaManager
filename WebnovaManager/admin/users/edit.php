<?php
/**
 * admin/users/edit.php
 *
 * Editar usuario existente.
 */

require_once '../../middleware/auth.php';
require_once '../../config/db.php';

if (!esAdmin()) {
    header('Location: ../dashboard.php?error=permiso');
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    header('Location: list.php?error=missing_id');
    exit();
}

$stmt = $conn->prepare("SELECT id, nombre, email, rol FROM usuarios WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    header('Location: list.php?error=not_found');
    exit();
}
$user = $result->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $rol = $_POST['rol'] ?? 'usuario';
    $password = trim($_POST['password'] ?? '');

    if (!$nombre || !$email) {
        $error = 'Nombre y email son obligatorios';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email no válido';
    } else {
        // Verificar email único
        $check = $conn->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
        $check->bind_param('si', $email, $id);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $error = 'Este email ya está en uso';
        }
        $check->close();

        if (!isset($error)) {
            if ($password) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE usuarios SET nombre = ?, email = ?, rol = ?, password = ? WHERE id = ?");
                $stmt->bind_param('ssssi', $nombre, $email, $rol, $hash, $id);
            } else {
                $stmt = $conn->prepare("UPDATE usuarios SET nombre = ?, email = ?, rol = ? WHERE id = ?");
                $stmt->bind_param('sssi', $nombre, $email, $rol, $id);
            }
            $stmt->execute();
            $stmt->close();

            header('Location: list.php?success=updated');
            exit();
        }
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario - WebNova Manager</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background: #f3f4f6; color: #1f2937; }
        .navbar { background: white; border-bottom: 1px solid #e5e7eb; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .navbar h1 { color: #2563eb; font-size: 1.5rem; }
        .container { max-width: 600px; margin: 2rem auto; padding: 0 1rem; }
        .breadcrumb { margin-bottom: 2rem; font-size: 0.9rem; color: #6b7280; }
        .breadcrumb a { color: #2563eb; text-decoration: none; }
        .form-card { background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 2rem; }
        .form-group { margin-bottom: 1.5rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: 600; color: #1f2937; }
        input[type="text"], input[type="email"], input[type="password"], select { width: 100%; padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 1rem; }
        .form-actions { display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem; }
        .btn { padding: 0.75rem 1.5rem; border: none; border-radius: 0.5rem; font-weight: 600; cursor: pointer; transition: all 250ms; text-decoration: none; display: inline-block; }
        .btn-primary { background: #2563eb; color: white; }
        .btn-primary:hover { background: #1d4ed8; }
        .btn-secondary { background: #e5e7eb; color: #1f2937; }
        .btn-secondary:hover { background: #d1d5db; }
        .alert { padding: 1rem 1.25rem; border-radius: 0.75rem; margin-bottom: 1rem; }
        .alert.error { background: #fee2e2; color: #991b1b; }
        .alert.success { background: #d1fae5; color: #065f46; }
    </style>
</head>
<body>
<nav class="navbar">
    <h1>👤 Editar Usuario</h1>
    <a href="list.php" class="btn btn-secondary">Volver a usuarios</a>
</nav>

<div class="container">
    <div class="breadcrumb">
        <a href="../dashboard.php">Dashboard</a> / <a href="list.php">Usuarios</a> / Editar
    </div>

    <div class="form-card">
        <?php if (isset($error)): ?>
            <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="nombre">Nombre completo</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($user['nombre']); ?>" required />
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required />
            </div>

            <div class="form-group">
                <label for="rol">Rol</label>
                <select id="rol" name="rol">
                    <option value="usuario" <?php echo $user['rol'] === 'usuario' ? 'selected' : ''; ?>>Usuario</option>
                    <option value="editor" <?php echo $user['rol'] === 'editor' ? 'selected' : ''; ?>>Editor</option>
                    <option value="admin" <?php echo $user['rol'] === 'admin' ? 'selected' : ''; ?>>Administrador</option>
                </select>
            </div>

            <div class="form-group">
                <label for="password">Nueva contraseña (opcional)</label>
                <input type="password" id="password" name="password" placeholder="Dejar vacío para mantener actual" />
                <small style="color: #6b7280; margin-top: 0.25rem; display: block;">Solo si deseas cambiar la contraseña</small>
            </div>

            <div class="form-actions">
                <a href="list.php" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar cambios</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>

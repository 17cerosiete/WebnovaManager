<?php
/**
 * admin/users/delete.php
 *
 * Eliminar usuario.
 */

require_once '../../middleware/auth.php';
require_once '../../config/db.php';

if (!esAdmin()) {
    header('Location: ../dashboard.php?error=permiso');
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id || $id === $_SESSION['usuario_id']) {
    header('Location: list.php?error=cannot_delete_self');
    exit();
}

$stmt = $conn->prepare("SELECT nombre FROM usuarios WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    header('Location: list.php?error=not_found');
    exit();
}
$user = $result->fetch_assoc();
$stmt->close();

// Eliminar usuario
$stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->close();

header('Location: list.php?success=deleted');
exit();

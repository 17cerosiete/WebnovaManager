<?php
/**
 * admin/posts/delete.php
 *
 * Eliminar artículo.
 */

require_once '../../middleware/auth.php';
require_once '../../config/db.php';

if (!esAdmin()) {
    header('Location: ../dashboard.php?error=permiso');
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    header('Location: list.php?error=id');
    exit();
}

$stmt = $conn->prepare("SELECT titulo FROM articulos WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();
$stmt->close();

if ($post) {
    $stmt = $conn->prepare("DELETE FROM articulos WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
}

header('Location: list.php');
exit();

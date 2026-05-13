<?php
/**
 * admin/pages/delete.php
 *
 * Eliminar una página por ID.
 */

require_once '../../middleware/auth.php';
require_once '../../config/db.php';

if (!esAdmin()) {
    header('Location: list.php?error=permiso');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: list.php?error=invalid_method');
    exit();
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if (!$id) {
    header('Location: list.php?error=missing_id');
    exit();
}

$stmt = $conn->prepare("DELETE FROM paginas WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->close();

header('Location: list.php?success=deleted');
exit();

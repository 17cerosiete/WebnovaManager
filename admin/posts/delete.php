<?php
require_once '../../middleware/auth.php';
require_once '../../config/db.php';

if (!esEditor()) {
    header('Location: ../dashboard.php?error=permiso');
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id === 0) {
    header('Location: list.php?error=invalidid');
    exit();
}

$stmt = $conn->prepare('DELETE FROM widgets WHERE id = ?');
$stmt->bind_param('i', $id);
if ($stmt->execute()) {
    header('Location: list.php?success=deleted');
} else {
    header('Location: list.php?error=dbfail');
}
$stmt->close();
?>

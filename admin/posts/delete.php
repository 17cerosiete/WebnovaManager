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

$query = "DELETE FROM widgets WHERE id = $id";
if ($conn->query($query)) {
    header('Location: list.php?success=deleted');
} else {
    header('Location: list.php?error=dbfail');
}
?>

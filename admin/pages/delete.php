<?php
require_once '../../middleware/auth.php';
require_once '../../config/db.php';

if (!esEditor()) {
    header('Location: ../dashboard.php?error=permiso');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: list.php?error=method');
    exit();
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id <= 0) {
    header('Location: list.php?error=invalidid');
    exit();
}

$stmt = $conn->prepare('DELETE FROM paginas WHERE id = ?');
$stmt->bind_param('i', $id);
$ok = $stmt->execute();
$stmt->close();

header('Location: list.php?' . ($ok ? 'success=deleted' : 'error=dbfail'));
exit();
?>

<?php
/**
 * Acceso rapido para desarrollo.
 *
 * No valida password, pero si sincroniza el usuario con la base de datos.
 * La sesion resultante contiene un usuario_id real.
 */

require_once '../config/sessions.php';
require_once '../config/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Metodo no permitido']);
    exit();
}

$email = trim($_POST['email'] ?? '');
$nombre = trim($_POST['nombre'] ?? '');
$rol = $_POST['rol'] ?? 'usuario';
$rolesPermitidos = ['admin', 'editor', 'usuario'];

if ($email === '' || $nombre === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Datos de usuario invalidos']);
    exit();
}

if (!in_array($rol, $rolesPermitidos, true)) {
    $rol = 'usuario';
}

$usuario = webnova_ensure_user($conn, $email, $nombre, $rol);

session_regenerate_id(true);

$_SESSION['usuario_id'] = (int)$usuario['id'];
$_SESSION['usuario_nombre'] = $usuario['nombre'];
$_SESSION['usuario_email'] = $usuario['email'];
$_SESSION['usuario_rol'] = $usuario['rol'];
$_SESSION['logueado'] = true;
$_SESSION['login_tiempo'] = time();
$_SESSION['_session_ip'] = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
$_SESSION['_session_ua'] = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

echo json_encode([
    'success' => true,
    'mensaje' => 'Acceso concedido',
    'usuario' => [
        'id' => (int)$usuario['id'],
        'nombre' => $usuario['nombre'],
        'rol' => $usuario['rol'],
    ],
]);
?>

<?php
/**
 * Modo desarrollo.
 *
 * Mantiene el acceso simple, pero usando siempre un usuario real de la BD.
 * Asi evitamos sesiones con usuario_id inventado que luego rompen inserts,
 * logs o claves foraneas despues de reiniciar XAMPP.
 */

define('DEV_MODE', true);
define('DEV_DEFAULT_EMAIL', 'admin@webnova.com');

if (DEV_MODE && session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (DEV_MODE && empty($_SESSION['logueado'])) {
    require_once __DIR__ . '/db.php';

    $usuario = webnova_ensure_user($conn, DEV_DEFAULT_EMAIL, 'Admin WEBNOVA', 'admin');

    $_SESSION['usuario_id'] = (int)$usuario['id'];
    $_SESSION['usuario_nombre'] = $usuario['nombre'];
    $_SESSION['usuario_email'] = $usuario['email'];
    $_SESSION['usuario_rol'] = $usuario['rol'];
    $_SESSION['logueado'] = true;
    $_SESSION['login_tiempo'] = time();
    $_SESSION['_session_ip'] = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $_SESSION['_session_ua'] = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
}
?>

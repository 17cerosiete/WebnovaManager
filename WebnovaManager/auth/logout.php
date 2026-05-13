<?php
/**
 * auth/logout.php
 *
 * Cierra la sesión del usuario
 * Se llama desde el botón "Cerrar sesión" en el dashboard
 */

// Usar configuración robusta de sesiones
require_once '../config/sessions.php';

// Limpiar todos los datos de sesión
$_SESSION = array();

// Destruir TODA la sesión
session_destroy();

// Eliminar cookies de sesión
setcookie('PHPSESSID', '', array(
  'expires' => time() - 3600,
  'path' => '/',
  'httponly' => true,
  'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'
));

// Redirigir a login - usando URL robusta
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$baseUrl = dirname(dirname($_SERVER['SCRIPT_NAME']));
if ($baseUrl === '\\' || $baseUrl === '/' || $baseUrl === '.') {
    $baseUrl = '';
}

$loginUrl = $protocol . '://' . $host . $baseUrl . '/admin/index.html';
header('Location: ' . $loginUrl);
exit();
?>

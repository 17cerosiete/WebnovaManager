<?php
/**
 * auth/logout.php
 *
 * Cierra la sesión del usuario
 * Se llama desde el botón "Cerrar sesión" en el dashboard
 */

// Iniciar sesión para poder acceder a ella
session_start();

// Destruir TODA la sesión
session_destroy();

// Eliminar cookies de sesión
setcookie('PHPSESSID', '', time() - 3600, '/');

// Redirigir a login
header('Location: ../admin/index.html');
exit();

?>

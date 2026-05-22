<?php
/**
 * api/settings.php
 *
 * API para guardar configuración
 */

require_once '../middleware/auth.php';
require_once '../config/db.php';

if (!esAdmin()) {
    die(json_encode(['error' => 'Permiso denegado']));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['error' => 'Solo POST permitido']));
}

$usuario_id = $_SESSION['usuario_id'];

// Campos permitidos
$campos_permitidos = [
    'sitio_titulo',
    'sitio_descripcion',
    'sitio_url',
    'email_contacto',
    'email_soporte',
    'items_por_pagina',
    'permitir_comentarios'
];

foreach ($campos_permitidos as $campo) {
    if (isset($_POST[$campo])) {
        $valor = trim($_POST[$campo]);

        $sql = "UPDATE configuracion SET valor = ? WHERE clave = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $valor, $campo);
        $stmt->execute();
        $stmt->close();
    }
}

// Registrar auditoría
$sql = "INSERT INTO logs_auditoria (usuario_id, accion, tabla, descripcion)
        VALUES (?, 'ACTUALIZAR_CONFIG', 'configuracion', 'Configuración general actualizada')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$stmt->close();

// Redirigir
header('Location: ../admin/settings/general.php?success=1');
exit();

?>

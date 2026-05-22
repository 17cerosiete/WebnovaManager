<?php
/**
 * api/widgets.php
 * API para la gestión de widgets reutilizables.
 */

require_once '../config/db.php';
require_once '../middleware/auth.php';

// Solo editores y administradores pueden gestionar widgets
if (!esEditor()) {
    header('Content-Type: application/json');
    http_response_code(403);
    echo json_encode(['error' => 'No tienes permisos para acceder a esta API']);
    exit();
}

// Evitar que cualquier Warning de PHP se cuele en el JSON
ob_start();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Listar widgets
        $result = $conn->query("SELECT * FROM widgets ORDER BY nombre ASC");
        $widgets = [];
        while ($row = $result->fetch_assoc()) {
            $widgets[] = $row;
        }
        ob_end_clean(); // Limpiar cualquier basura de PHP antes del JSON
        header('Content-Type: application/json');
        echo json_encode($widgets);
        exit();

    case 'POST':
        // Crear o actualizar widget
        $input = json_decode(file_get_contents('php://input'), true);

        if (!isset($input['nombre']) || !isset($input['tipo']) || !isset($input['config'])) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'Faltan campos obligatorios: nombre, tipo, config']);
            exit();
        }

        $nombre = $conn->real_escape_string($input['nombre']);
        $tipo = $conn->real_escape_string($input['tipo']);
        $config = $conn->real_escape_string(json_encode($input['config']));
        $preview = isset($input['preview_html']) ? $conn->real_escape_string($input['preview_html']) : null;
        $id = isset($input['id']) ? (int)$input['id'] : null;

        if ($id) {
            // Actualizar
            $query = "UPDATE widgets SET nombre='$nombre', tipo='$tipo', config='$config', preview_html='$preview' WHERE id=$id";
        } else {
            // Crear
            $query = "INSERT INTO widgets (nombre, tipo, config, preview_html) VALUES ('$nombre', '$tipo', '$config', '$preview')";
        }

        if ($conn->query($query)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'id' => $id ?: $conn->insert_id]);
        } else {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => 'Error al guardar el widget: ' . $conn->error]);
        }
        break;

    case 'DELETE':
        // Eliminar widget
        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
        if (!$id) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'ID de widget no proporcionado']);
            exit();
        }

        $query = "DELETE FROM widgets WHERE id=$id";
        if ($conn->query($query)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
        } else {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => 'Error al eliminar el widget: ' . $conn->error]);
        }
        break;

    default:
        header('HTTP/1.1 405 Method Not Allowed');
        break;
}
?>
EOF

<?php
/**
 * api/pages.php
 * API para la gestión de páginas del CMS.
 */

require_once '../config/db.php';
require_once '../middleware/auth.php';

if (!esEditor()) {
    header('Content-Type: application/json');
    http_response_code(403);
    echo json_encode(['error' => 'No tienes permisos']);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
        if ($id) {
            $result = $conn->query("SELECT * FROM paginas WHERE id = $id");
            $page = $result->fetch_assoc();
            header('Content-Type: application/json');
            echo json_encode($page);
        } else {
            $result = $conn->query("SELECT * FROM paginas ORDER BY fecha_creacion DESC");
            $pages = [];
            while ($row = $result->fetch_assoc()) {
                $pages[] = $row;
            }
            header('Content-Type: application/json');
            echo json_encode($pages);
        }
        break;

    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        if (!isset($input['title']) || !isset($input['blocks'])) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'Faltan datos obligatorios: title, blocks']);
            exit();
        }

        $title = $conn->real_escape_string($input['title']);
        $slug = $conn->real_escape_string(strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title))));
        $blocks = $conn->real_escape_string(json_encode($input['blocks']));
        $autor_id = $_SESSION['usuario_id'];

        $id = isset($input['id']) ? (int)$input['id'] : null;

        if ($id) {
            $query = "UPDATE paginas SET titulo='$title', slug='$slug', contenido='$blocks' WHERE id=$id";
        } else {
            $query = "INSERT INTO paginas (titulo, slug, contenido, autor_id) VALUES ('$title', '$slug', '$blocks', $autor_id)";
        }

        if ($conn->query($query)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'id' => $id ?: $conn->insert_id]);
        } else {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => 'Error al guardar página: ' . $conn->error]);
        }
        break;
}
?>

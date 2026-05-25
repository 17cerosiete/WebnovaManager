<?php
require_once '../config/db.php';
require_once '../middleware/auth.php';
require_once '../utils/render_helpers.php';

header('Content-Type: application/json');

if (!esEditor()) {
    http_response_code(403);
    echo json_encode(['error' => 'No tienes permisos para acceder a esta API']);
    exit();
}

function webnova_widget_config($value) {
    if (is_array($value)) {
        return $value;
    }

    if (is_string($value)) {
        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : null;
    }

    return null;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : null;

    if ($id) {
        $stmt = $conn->prepare('SELECT * FROM widgets WHERE id = ? LIMIT 1');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $widget = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        echo json_encode($widget ?: null);
        exit();
    }

    $result = $conn->query('SELECT * FROM widgets ORDER BY nombre ASC');
    $widgets = [];
    while ($row = $result->fetch_assoc()) {
        $widgets[] = $row;
    }

    echo json_encode($widgets);
    exit();
}

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!is_array($input) || empty($input['nombre']) || empty($input['tipo']) || !isset($input['config'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Faltan campos obligatorios: nombre, tipo, config']);
        exit();
    }

    $config = webnova_widget_config($input['config']);
    if ($config === null) {
        http_response_code(400);
        echo json_encode(['error' => 'La configuracion debe ser JSON valido']);
        exit();
    }

    $id = isset($input['id']) && $input['id'] !== '' ? (int)$input['id'] : null;
    $nombre = trim($input['nombre']);
    $tipo = trim($input['tipo']);
    $configJson = json_encode($config, JSON_UNESCAPED_UNICODE);
    $preview = webnova_render_widget($tipo, $config);

    if ($id) {
        $stmt = $conn->prepare('UPDATE widgets SET nombre = ?, tipo = ?, config = ?, preview_html = ? WHERE id = ?');
        $stmt->bind_param('ssssi', $nombre, $tipo, $configJson, $preview, $id);
    } else {
        $stmt = $conn->prepare('INSERT INTO widgets (nombre, tipo, config, preview_html) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('ssss', $nombre, $tipo, $configJson, $preview);
    }

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'id' => $id ?: $stmt->insert_id]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Error al guardar el widget: ' . $stmt->error]);
    }
    $stmt->close();
    exit();
}

if ($method === 'DELETE') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'ID de widget no valido']);
        exit();
    }

    $stmt = $conn->prepare('DELETE FROM widgets WHERE id = ?');
    $stmt->bind_param('i', $id);
    $ok = $stmt->execute();
    $stmt->close();

    echo json_encode(['success' => $ok]);
    exit();
}

http_response_code(405);
echo json_encode(['error' => 'Metodo no permitido']);
?>

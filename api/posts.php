<?php
/**
 * api/posts.php
 *
 * API REST para CRUD de artículos.
 */

require_once '../middleware/auth.php';
require_once '../config/db.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '');

function respuesta_json($success, $message = '', $data = null) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => $success,
        'error' => !$success ? $message : '',
        'data' => $data
    ]);
    exit();
}

if ($action === 'create' && $method === 'POST') {
    if (!esEditor()) {
        respuesta_json(false, 'Permiso denegado');
    }

    $titulo = trim($_POST['titulo'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $contenido = $_POST['contenido'] ?? '';
    $meta_description = trim($_POST['meta_description'] ?? '');
    $keywords = trim($_POST['keywords'] ?? '');
    $publicado = isset($_POST['publicado']) ? 1 : 0;
    $usuario_id = $_SESSION['usuario_id'];

    if (!$titulo || strlen($titulo) < 3) {
        respuesta_json(false, 'Título debe tener al menos 3 caracteres');
    }
    if (!$slug || strlen($slug) < 3) {
        respuesta_json(false, 'Slug debe tener al menos 3 caracteres');
    }
    if (!$contenido || strlen(strip_tags($contenido)) < 20) {
        respuesta_json(false, 'Contenido debe tener al menos 20 caracteres');
    }

    $check = $conn->prepare("SELECT id FROM articulos WHERE slug = ?");
    $check->bind_param('s', $slug);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        respuesta_json(false, 'Este slug ya está usado por otro artículo');
    }
    $check->close();

    $sql = "INSERT INTO articulos (titulo, slug, contenido, meta_description, keywords, autor_id, publicado) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssssii', $titulo, $slug, $contenido, $meta_description, $keywords, $usuario_id, $publicado);

    if ($stmt->execute()) {
        $post_id = $conn->insert_id;
        respuesta_json(true, 'Artículo creado', ['id' => $post_id]);
    }
    respuesta_json(false, 'Error al crear artículo: ' . $conn->error);
}

elseif ($action === 'update' && $method === 'POST') {
    if (!esEditor()) {
        respuesta_json(false, 'Permiso denegado');
    }
    $id = (int)($_POST['id'] ?? 0);
    $titulo = trim($_POST['titulo'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $contenido = $_POST['contenido'] ?? '';
    $meta_description = trim($_POST['meta_description'] ?? '');
    $keywords = trim($_POST['keywords'] ?? '');
    $publicado = isset($_POST['publicado']) ? 1 : 0;
    $usuario_id = $_SESSION['usuario_id'];

    if (!$id) {
        respuesta_json(false, 'ID de artículo requerido');
    }
    if (!$titulo || strlen($titulo) < 3) {
        respuesta_json(false, 'Título debe tener al menos 3 caracteres');
    }
    if (!$slug || strlen($slug) < 3) {
        respuesta_json(false, 'Slug debe tener al menos 3 caracteres');
    }
    if (!$contenido || strlen(strip_tags($contenido)) < 20) {
        respuesta_json(false, 'Contenido debe tener al menos 20 caracteres');
    }

    $check = $conn->prepare("SELECT autor_id FROM articulos WHERE id = ?");
    $check->bind_param('i', $id);
    $check->execute();
    $result = $check->get_result();
    if ($result->num_rows === 0) {
        respuesta_json(false, 'Artículo no encontrado');
    }
    $row = $result->fetch_assoc();
    if ($row['autor_id'] !== $usuario_id && !esAdmin()) {
        respuesta_json(false, 'Solo el autor o un admin puede editar este artículo');
    }
    $check->close();

    $sql = "UPDATE articulos SET titulo = ?, slug = ?, contenido = ?, meta_description = ?, keywords = ?, publicado = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssssii', $titulo, $slug, $contenido, $meta_description, $keywords, $publicado, $id);

    if ($stmt->execute()) {
        respuesta_json(true, 'Artículo actualizado');
    }
    respuesta_json(false, 'Error al actualizar artículo: ' . $conn->error);
}

elseif ($action === 'delete' && $method === 'POST') {
    if (!esAdmin()) {
        respuesta_json(false, 'Solo admins pueden eliminar artículos');
    }
    $id = (int)($_POST['id'] ?? 0);
    $usuario_id = $_SESSION['usuario_id'];
    if (!$id) {
        respuesta_json(false, 'ID requerido');
    }

    $check = $conn->prepare("SELECT titulo FROM articulos WHERE id = ?");
    $check->bind_param('i', $id);
    $check->execute();
    $result = $check->get_result();
    if ($result->num_rows === 0) {
        respuesta_json(false, 'Artículo no encontrado');
    }

    $row = $result->fetch_assoc();
    $titulo = $row['titulo'];
    $check->close();

    $sql = "DELETE FROM articulos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        respuesta_json(true, 'Artículo eliminado');
    }
    respuesta_json(false, 'Error al eliminar artículo: ' . $conn->error);
}

elseif ($action === 'get' && $method === 'GET') {
    if (!esEditor()) {
        respuesta_json(false, 'Permiso denegado');
    }
    $id = (int)($_GET['id'] ?? 0);
    if (!$id) {
        respuesta_json(false, 'ID requerido');
    }
    $sql = "SELECT * FROM articulos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        respuesta_json(false, 'Artículo no encontrado');
    }
    $post = $result->fetch_assoc();
    respuesta_json(true, '', $post);
}

else {
    http_response_code(400);
    respuesta_json(false, 'Acción no válida');
}

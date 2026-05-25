<?php
require_once '../config/db.php';
require_once '../middleware/auth.php';

header('Content-Type: application/json');

if (!esEditor()) {
    http_response_code(403);
    echo json_encode(['error' => 'No tienes permisos']);
    exit();
}

function webnova_slug($title) {
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));
    return $slug !== '' ? $slug : 'pagina';
}

function webnova_unique_slug($conn, $title, $ignoreId = null) {
    $base = webnova_slug($title);
    $slug = $base;
    $counter = 2;

    while (true) {
        if ($ignoreId) {
            $stmt = $conn->prepare('SELECT id FROM paginas WHERE slug = ? AND id <> ? LIMIT 1');
            $stmt->bind_param('si', $slug, $ignoreId);
        } else {
            $stmt = $conn->prepare('SELECT id FROM paginas WHERE slug = ? LIMIT 1');
            $stmt->bind_param('s', $slug);
        }

        $stmt->execute();
        $exists = $stmt->get_result()->num_rows > 0;
        $stmt->close();

        if (!$exists) {
            return $slug;
        }

        $slug = $base . '-' . $counter;
        $counter++;
    }
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : null;

    if ($id) {
        $stmt = $conn->prepare('SELECT * FROM paginas WHERE id = ? LIMIT 1');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $page = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        echo json_encode($page ?: null);
        exit();
    }

    $result = $conn->query('SELECT * FROM paginas ORDER BY fecha_actualizacion DESC');
    $pages = [];
    while ($row = $result->fetch_assoc()) {
        $pages[] = $row;
    }

    echo json_encode($pages);
    exit();
}

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!is_array($input) || empty($input['title']) || !isset($input['blocks'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Faltan datos obligatorios: title, blocks']);
        exit();
    }

    $id = isset($input['id']) && $input['id'] !== '' ? (int)$input['id'] : null;
    $title = trim($input['title']);
    $slug = webnova_unique_slug($conn, $title, $id);
    $blocks = json_encode($input['blocks'], JSON_UNESCAPED_UNICODE);
    $published = !empty($input['publicada']) ? 1 : 0;

    if ($id) {
        $stmt = $conn->prepare('UPDATE paginas SET titulo = ?, slug = ?, contenido = ?, publicada = ? WHERE id = ?');
        $stmt->bind_param('sssii', $title, $slug, $blocks, $published, $id);
    } else {
        $authorId = (int)$_SESSION['usuario_id'];
        $stmt = $conn->prepare('INSERT INTO paginas (titulo, slug, contenido, autor_id, publicada) VALUES (?, ?, ?, ?, ?)');
        $stmt->bind_param('sssii', $title, $slug, $blocks, $authorId, $published);
    }

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'id' => $id ?: $stmt->insert_id, 'slug' => $slug]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Error al guardar pagina: ' . $stmt->error]);
    }
    $stmt->close();
    exit();
}

if ($method === 'DELETE') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'ID de pagina no valido']);
        exit();
    }

    $stmt = $conn->prepare('DELETE FROM paginas WHERE id = ?');
    $stmt->bind_param('i', $id);
    $ok = $stmt->execute();
    $stmt->close();

    echo json_encode(['success' => $ok]);
    exit();
}

http_response_code(405);
echo json_encode(['error' => 'Metodo no permitido']);
?>

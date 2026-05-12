<?php
/**
 * api/pages.php
 *
 * API REST para CRUD de páginas
 * Endpoints:
 * POST   /api/pages.php?action=create  - Crear página
 * GET    /api/pages.php?action=get     - Obtener página
 * POST   /api/pages.php?action=update  - Actualizar página
 * POST   /api/pages.php?action=delete  - Eliminar página
 */

require_once '../middleware/auth.php';
require_once '../config/db.php';

// Solo acepta POST o GET
$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '');

// Funciones auxiliares para respuestas JSON
function respuesta_json($success, $message = '', $data = null) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'error' => !$success ? $message : '',
        'data' => $data
    ]);
    exit();
}

function registrar_auditoria($usuario_id, $accion, $tabla, $registro_id, $descripcion) {
    global $conn;
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'desconocida';

    $sql = "INSERT INTO logs_auditoria (usuario_id, accion, tabla, registro_id, descripcion, ip_address)
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ississ", $usuario_id, $accion, $tabla, $registro_id, $descripcion, $ip);
    $stmt->execute();
    $stmt->close();
}

// =====================================================
// CREATE - Crear nueva página
// =====================================================
if ($action === 'create' && $method === 'POST') {
    if (!esEditor()) {
        respuesta_json(false, 'Permiso denegado', null);
    }

    $titulo = trim($_POST['titulo'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $contenido = $_POST['contenido'] ?? '';
    $publicada = isset($_POST['publicada']) ? 1 : 0;
    $meta_description = trim($_POST['meta_description'] ?? '');
    $keywords = trim($_POST['keywords'] ?? '');
    $usuario_id = $_SESSION['usuario_id'];

    // Validaciones
    if (!$titulo || strlen($titulo) < 3) {
        respuesta_json(false, 'Título debe tener al menos 3 caracteres', null);
    }

    if (!$slug || strlen($slug) < 3) {
        respuesta_json(false, 'Slug debe tener al menos 3 caracteres', null);
    }

    if (!$contenido || strlen(strip_tags($contenido)) < 10) {
        respuesta_json(false, 'Contenido debe tener al menos 10 caracteres', null);
    }

    // Verificar que slug no existe
    $check = $conn->prepare("SELECT id FROM paginas WHERE slug = ?");
    $check->bind_param("s", $slug);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        respuesta_json(false, 'Este slug ya existe', null);
    }
    $check->close();

    // Insertar página
    $sql = "INSERT INTO paginas (titulo, slug, contenido, autor_id, publicada)
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssii", $titulo, $slug, $contenido, $usuario_id, $publicada);

    if ($stmt->execute()) {
        $pagina_id = $conn->insert_id;
        $accion = $publicada ? 'CREAR_PUBLICADA' : 'CREAR_BORRADOR';
        registrar_auditoria($usuario_id, $accion, 'paginas', $pagina_id, "Página: $titulo");
        respuesta_json(true, 'Página creada', ['id' => $pagina_id]);
    } else {
        respuesta_json(false, 'Error al crear página: ' . $conn->error, null);
    }

    $stmt->close();
}

// =====================================================
// UPDATE - Actualizar página
// =====================================================
elseif ($action === 'update' && $method === 'POST') {
    if (!esEditor()) {
        respuesta_json(false, 'Permiso denegado', null);
    }

    $id = (int)($_POST['id'] ?? 0);
    $titulo = trim($_POST['titulo'] ?? '');
    $contenido = $_POST['contenido'] ?? '';
    $publicada = isset($_POST['publicada']) ? 1 : 0;
    $usuario_id = $_SESSION['usuario_id'];

    if (!$id) {
        respuesta_json(false, 'ID de página requerido', null);
    }

    // Verificar que la página existe y el usuario es propietario (o admin)
    $check = $conn->prepare("SELECT autor_id FROM paginas WHERE id = ?");
    $check->bind_param("i", $id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows === 0) {
        respuesta_json(false, 'Página no encontrada', null);
    }

    $row = $result->fetch_assoc();
    if ($row['autor_id'] !== $usuario_id && !esAdmin()) {
        respuesta_json(false, 'Solo el propietario puede editar esta página', null);
    }

    $check->close();

    // Actualizar
    $sql = "UPDATE paginas SET titulo = ?, contenido = ?, publicada = ?
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $titulo, $contenido, $publicada, $id);

    if ($stmt->execute()) {
        registrar_auditoria($usuario_id, 'ACTUALIZAR', 'paginas', $id, "Página: $titulo");
        respuesta_json(true, 'Página actualizada', null);
    } else {
        respuesta_json(false, 'Error al actualizar', null);
    }

    $stmt->close();
}

// =====================================================
// DELETE - Eliminar página
// =====================================================
elseif ($action === 'delete' && $method === 'POST') {
    if (!esAdmin()) {
        respuesta_json(false, 'Solo admins pueden eliminar', null);
    }

    $id = (int)($_POST['id'] ?? 0);
    $usuario_id = $_SESSION['usuario_id'];

    if (!$id) {
        respuesta_json(false, 'ID requerido', null);
    }

    // Obtener nombre para auditoría
    $check = $conn->prepare("SELECT titulo FROM paginas WHERE id = ?");
    $check->bind_param("i", $id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows === 0) {
        respuesta_json(false, 'Página no encontrada', null);
    }

    $row = $result->fetch_assoc();
    $titulo = $row['titulo'];
    $check->close();

    // Eliminar
    $sql = "DELETE FROM paginas WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        registrar_auditoria($usuario_id, 'ELIMINAR', 'paginas', $id, "Página: $titulo");
        respuesta_json(true, 'Página eliminada', null);
    } else {
        respuesta_json(false, 'Error al eliminar', null);
    }

    $stmt->close();
}

// =====================================================
// GET - Obtener página
// =====================================================
elseif ($action === 'get' && $method === 'GET') {
    if (!esEditor()) {
        respuesta_json(false, 'Permiso denegado', null);
    }

    $id = (int)($_GET['id'] ?? 0);

    if (!$id) {
        respuesta_json(false, 'ID requerido', null);
    }

    $sql = "SELECT * FROM paginas WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        respuesta_json(false, 'Página no encontrada', null);
    }

    $pagina = $result->fetch_assoc();
    respuesta_json(true, '', $pagina);
    $stmt->close();
}

else {
    http_response_code(400);
    respuesta_json(false, 'Acción no válida', null);
}

?>

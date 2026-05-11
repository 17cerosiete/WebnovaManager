<?php
/**
 * auth/login.php
 *
 * Procesa el login del usuario.
 * Recibe email + password del formulario HTML
 * Verifica en la BD usando password_verify()
 * Crea sesión si es correcto
 *
 * FLUJO COMPLETO:
 * 1. Usuario ingresa email + password en formulario HTML
 * 2. Formulario hace POST a este archivo
 * 3. Aquí se valida contra MySQL
 * 4. Si es correcto, crear sesión
 * 5. Si es incorrecto, mostrar error
 */

// =====================================================
// 1. INICIAR SESIÓN PHP
// =====================================================

// session_start() debe ser ANTES de cualquier output
// Carga la sesión del usuario (o crea una nueva si no existe)
session_start();

// =====================================================
// 2. REQUERIR CONEXIÓN A BD
// =====================================================

// Importar la conexión de config/db.php
require_once '../config/db.php';

// Ahora disponemos de: $conn (conexión MySQLi)

// =====================================================
// 3. PROCESAR SOLO REQUESTS POST
// =====================================================

// Solo procesamos si es formulario POST
// (Protección contra acceso directo)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  // Si acceden directamente: error
  http_response_code(405); // 405 Method Not Allowed
  die(json_encode(['error' => 'Método no permitido']));
}

// =====================================================
// 4. VALIDAR DATOS RECIBIDOS
// =====================================================

// Verificar que email y password llegaron
if (empty($_POST['email']) || empty($_POST['password'])) {
  // Responder con error JSON
  http_response_code(400); // 400 Bad Request
  die(json_encode(['error' => 'Email y contraseña requeridos']));
}

// =====================================================
// 5. SANITIZAR DATOS
// =====================================================

// Obtener valores del formulario
$email = trim($_POST['email']); // trim = eliminar espacios
$password = $_POST['password'];

// Validar que email sea válido
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  http_response_code(400);
  die(json_encode(['error' => 'Email inválido']));
}

// =====================================================
// 6. BUSCAR USUARIO EN BD
// =====================================================

// Usar prepared statement (protección contra SQL injection)
// ? es placeholder: se reemplaza de forma segura
$sql = "SELECT id, nombre, email, password, rol FROM usuarios WHERE email = ?";

// $stmt = prepared statement
$stmt = $conn->prepare($sql);

if (!$stmt) {
  // Error en la query
  http_response_code(500);
  die(json_encode(['error' => 'Error en servidor: ' . $conn->error]));
}

// Bind: reemplazar ? con $email (de forma segura)
// "s" significa: string
$stmt->bind_param("s", $email);

// Ejecutar query
$stmt->execute();

// Obtener resultado
$result = $stmt->get_result();

// =====================================================
// 7. VERIFICAR SI USUARIO EXISTE
// =====================================================

if ($result->num_rows === 0) {
  // No existe usuario con ese email
  http_response_code(401); // 401 Unauthorized
  die(json_encode(['error' => 'Email o contraseña incorrectos']));
}

// Obtener datos del usuario
$usuario = $result->fetch_assoc();

// $usuario = [
//   'id' => 1,
//   'nombre' => 'Carlos González',
//   'email' => 'carlos@webnova.com',
//   'password' => '$2y$10$N9qo8uL...',  // Hash BCRYPT
//   'rol' => 'admin'
// ]

// =====================================================
// 8. VERIFICAR CONTRASEÑA CON password_verify()
// =====================================================

// password_verify() compara:
// - $password: lo que ingresó el usuario ("0000")
// - $usuario['password']: el hash almacenado en BD
// Retorna: true o false
if (!password_verify($password, $usuario['password'])) {
  // Contraseña no coincide
  http_response_code(401);
  die(json_encode(['error' => 'Email o contraseña incorrectos']));
}

// =====================================================
// 9. CREAR SESIÓN PHP
// =====================================================

// Si llegamos aquí: email + contraseña son correctos ✓

// Guardar datos del usuario EN LA SESIÓN
// $_SESSION es un array global que persiste entre páginas
$_SESSION['usuario_id'] = $usuario['id'];
$_SESSION['usuario_nombre'] = $usuario['nombre'];
$_SESSION['usuario_email'] = $usuario['email'];
$_SESSION['usuario_rol'] = $usuario['rol'];
$_SESSION['logueado'] = true;

// Opcional: guardar hora de login
$_SESSION['login_tiempo'] = time();

// =====================================================
// 10. RESPONDER ÉXITO
// =====================================================

// Responder con JSON
http_response_code(200); // 200 OK
echo json_encode([
  'success' => true,
  'mensaje' => 'Login exitoso',
  'usuario' => [
    'nombre' => $usuario['nombre'],
    'rol' => $usuario['rol'],
  ]
]);

// =====================================================
// FLUJO RESUMIDO:
// =====================================================
// Usuario → HTML form POST → auth/login.php
//         ↓
// Validar email existe en BD
//         ↓
// password_verify(password_ingresado, hash_en_bd)
//         ↓
// ¿Correcto? → SI → Guardar en $_SESSION → JSON: éxito
//           → NO → JSON: error
// =====================================================

// Cerrar statement
$stmt->close();

// Cerrar conexión (no obligatorio, PHP lo hace al terminar)
// $conn->close();

?>

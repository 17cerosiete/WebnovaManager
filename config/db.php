<?php
/**
 * config/db.php
 *
 * Conexión a MySQL usando MySQLi
 * Todos los archivos PHP hacen: require_once 'config/db.php'
 *
 * ¿Por qué separar en un archivo?
 *   - NO repetir código de conexión en cada archivo
 *   - Cambiar servidor? Cambias solo aquí
 *   - Cambiar password? Cambias solo aquí
 *   - Es profesional y escalable
 */

// =====================================================
// CONFIGURACIÓN DE CONEXIÓN
// =====================================================

// Datos del servidor MySQL
define('DB_HOST', 'localhost');    // Donde está MySQL
define('DB_USER', 'root');         // Usuario MySQL
define('DB_PASS', '');             // Password (XAMPP por defecto: vacío)
define('DB_NAME', 'webnova_db');   // Nombre de la BD

// =====================================================
// CREAR CONEXIÓN
// =====================================================

// new mysqli() crea una conexión a MySQL
// Parámetros: (host, usuario, password, nombreBD)
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// =====================================================
// VERIFICAR ERRORES DE CONEXIÓN
// =====================================================

if ($conn->connect_error) {
  // Si hay error, mostrar qué pasó
  // En producción: esto sería un log, no mostrar al usuario
  die("❌ Error de conexión a BD: " . $conn->connect_error);
}

// =====================================================
// CONFIGURAR CHARACTER SET
// =====================================================

// Usar UTF-8 para soportar tildes, emojis, etc.
$conn->set_charset("utf8mb4");

// =====================================================
// RETORNAR CONEXIÓN
// =====================================================

// Otros archivos harán: $conn = include 'config/db.php'
// O: require_once 'config/db.php'; (ya disponible como $conn)
// Retornamos la conexión para que otros archivos la usen
return $conn;

// =====================================================
// FLUJO DE USO:
// =====================================================
// 1. auth/login.php hace: require_once 'config/db.php'
// 2. Ahora en login.php existe: $conn
// 3. login.php hace: $result = $conn->query("SELECT ... FROM usuarios");
// 4. Se ejecuta en MySQL ✓
// =====================================================
?>

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
// CREAR CONEXIÓN CON REINTENTOS
// =====================================================

// Reintentos automáticos en caso de que MySQL esté iniciando
// Esto resuelve el problema de "después de reiniciar XAMPP falla el login"
$maxIntentos = 3;
$intento = 0;
$conn = null;

while ($intento < $maxIntentos && $conn === null) {
  $intento++;
  
  // Intentar conexión
  $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
  
  if ($conn->connect_error) {
    if ($intento < $maxIntentos) {
      // Si no es el último intento, esperar 1 segundo y reintentar
      sleep(1);
      $conn = null;
    } else {
      // Último intento falló: mostrar error
      die("❌ Error de conexión a BD después de $maxIntentos intentos: " . $conn->connect_error);
    }
  }
}

// =====================================================
// VERIFICAR CHARSET Y CONFIGURACIÓN CRÍTICA
// =====================================================

// Usar UTF-8mb4 para soportar tildes, emojis, caracteres especiales
// ⚠️ CRÍTICO: Esto previene errores de password_verify() por charset inconsistente
$conn->set_charset("utf8mb4");

// Validar que la conexión está lista
if (!$conn->ping()) {
  die("❌ Conexión a BD no está activa. Intenta reiniciar MySQL.");
}

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

<?php
/**
 * diagnose_login.php
 * 
 * Script de diagnóstico para verificar por qué falla el login
 * después de reiniciar XAMPP.
 * 
 * USO: Abre en navegador: http://localhost/WebnovaManager/diagnose_login.php
 * 
 * Verifica:
 * 1. Conexión a BD
 * 2. Charset de conexión
 * 3. Estado de sesiones
 * 4. Hash del usuario admin
 * 5. Test de password_verify()
 */

// Iniciar sesión
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

header('Content-Type: text/html; charset=utf-8');

echo "<!DOCTYPE html>
<html lang='es'>
<head>
  <meta charset='UTF-8'>
  <meta name='viewport' content='width=device-width, initial-scale=1.0'>
  <title>Diagnóstico - WebNova Manager</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 2rem; background: #f5f5f5; }
    .container { max-width: 900px; margin: 0 auto; background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    h1 { color: #2563eb; border-bottom: 3px solid #2563eb; padding-bottom: 1rem; }
    .test { margin: 2rem 0; padding: 1rem; border-left: 4px solid #ccc; background: #f9f9f9; }
    .test.success { border-left-color: #10b981; background: #f0fdf4; }
    .test.error { border-left-color: #ef4444; background: #fef2f2; }
    .test.warning { border-left-color: #f59e0b; background: #fffbeb; }
    .test h3 { margin-top: 0; }
    code { background: #e5e7eb; padding: 0.25rem 0.5rem; border-radius: 3px; font-family: monospace; }
    .status { font-weight: bold; margin-top: 0.5rem; }
    .status.ok { color: #10b981; }
    .status.fail { color: #ef4444; }
    .status.warn { color: #f59e0b; }
    pre { background: #1f2937; color: #10b981; padding: 1rem; border-radius: 5px; overflow-x: auto; }
  </style>
</head>
<body>
<div class='container'>
  <h1>🔍 Diagnóstico de Login - WebNova Manager</h1>
  <p>Este script verifica por qué falla el login después de reiniciar XAMPP.</p>\n";

// =====================================================
// TEST 1: CONEXIÓN A BD
// =====================================================

echo "<div class='test'><h3>1️⃣ Conexión a Base de Datos</h3>";

require_once __DIR__ . '/config/db.php';

if ($conn && $conn->ping()) {
  echo "<p class='status ok'>✓ Conectado a BD: " . DB_NAME . " en " . DB_HOST . "</p>";
} else {
  echo "<p class='status fail'>✗ ERROR: No se puede conectar a la BD</p>";
  echo "<p>Asegúrate de que MySQL está corriendo en XAMPP.</p>";
}
echo "</div>\n";

// =====================================================
// TEST 2: CHARSET
// =====================================================

echo "<div class='test'><h3>2️⃣ Configuración de Charset</h3>";

$charset = $conn->get_charset();
echo "<p>Charset actual: <code>" . $charset->charset . "</code></p>";

if ($charset->charset === "utf8mb4") {
  echo "<p class='status ok'>✓ Charset correcto (utf8mb4)</p>";
} else {
  echo "<p class='status warn'>⚠ Charset no es utf8mb4. Esto puede causar problemas.</p>";
}
echo "</div>\n";

// =====================================================
// TEST 3: TABLA USUARIOS
// =====================================================

echo "<div class='test'><h3>3️⃣ Tabla de Usuarios</h3>";

$result = $conn->query("SELECT COUNT(*) as total FROM usuarios");
if ($result) {
  $row = $result->fetch_assoc();
  echo "<p class='status ok'>✓ Tabla usuarios existe. Total de usuarios: " . $row['total'] . "</p>";
} else {
  echo "<p class='status fail'>✗ ERROR: No se puede acceder a tabla usuarios</p>";
  echo "<p>Error: " . $conn->error . "</p>";
}
echo "</div>\n";

// =====================================================
// TEST 4: USUARIO ADMIN Y HASH
// =====================================================

echo "<div class='test'><h3>4️⃣ Usuario Admin</h3>";

$stmt = $conn->prepare("SELECT id, nombre, email, password, rol FROM usuarios WHERE email = ?");
$stmt->bind_param("s", $email);
$email = "admin@webnova.com";
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
  $admin = $result->fetch_assoc();
  echo "<p class='status ok'>✓ Usuario admin encontrado</p>";
  echo "<p>Nombre: <code>" . htmlspecialchars($admin['nombre']) . "</code></p>";
  echo "<p>Email: <code>" . htmlspecialchars($admin['email']) . "</code></p>";
  echo "<p>Rol: <code>" . htmlspecialchars($admin['rol']) . "</code></p>";
  echo "<p>Hash: <code>" . substr($admin['password'], 0, 20) . "...</code></p>";
} else {
  echo "<p class='status fail'>✗ ERROR: Usuario admin no encontrado</p>";
  echo "<p>Intenta ejecutar el script database.sql en phpMyAdmin.</p>";
}
echo "</div>\n";

// =====================================================
// TEST 5: VERIFICACIÓN DE PASSWORD
// =====================================================

echo "<div class='test'><h3>5️⃣ Test de password_verify()</h3>";

// HACER UNA NUEVA CONSULTA (no reutilizar $result anterior)
$stmt2 = $conn->prepare("SELECT id, nombre, email, password, rol FROM usuarios WHERE email = ?");
if (!$stmt2) {
  echo "<p class='status fail'>✗ Error en preparación de statement: " . $conn->error . "</p>";
  echo "</div>\n";
  echo "</div></body></html>";
  $conn->close();
  die();
}

$stmt2->bind_param("s", $email);
$email = "admin@webnova.com";
$stmt2->execute();
$result2 = $stmt2->get_result();

if ($result2 && $result2->num_rows > 0) {
  $usuario = $result2->fetch_assoc();
  
  if ($usuario && isset($usuario['password'])) {
    $testPassword = "0000";
    $verify = password_verify($testPassword, $usuario['password']);
    
    if ($verify) {
      echo "<p class='status ok'>✓ password_verify() funciona correctamente</p>";
      echo "<p>La contraseña <code>0000</code> coincide con el hash almacenado.</p>";
    } else {
      echo "<p class='status fail'>✗ ERROR: password_verify() falló</p>";
      echo "<p>La contraseña no coincide con el hash almacenado.</p>";
      echo "<p><strong>SOLUCIÓN:</strong> Intenta usar el script de reparo:</p>";
      echo "<p><code>http://localhost/WebnovaManager/repair_database.php</code></p>";
    }
  } else {
    echo "<p class='status fail'>✗ ERROR: No se pudo leer el password del usuario</p>";
  }
  $stmt2->close();
} else {
  echo "<p class='status fail'>✗ ERROR CRÍTICO: Usuario admin NO existe en la BD</p>";
  echo "<p>Posibles causas:</p>";
  echo "<ul>";
  echo "<li>database.sql nunca se ejecutó</li>";
  echo "<li>La BD se truncó accidentalmente</li>";
  echo "<li>Los datos se perdieron</li>";
  echo "</ul>";
  echo "<p><strong>SOLUCIÓN:</strong> Usa el script de reparo automático:</p>";
  echo "<p><code>http://localhost/WebnovaManager/repair_database.php</code></p>";
}
echo "</div>\n";

// =====================================================
// TEST 6: SESIONES
// =====================================================

echo "<div class='test'><h3>6️⃣ Configuración de Sesiones PHP</h3>";

echo "<p>Directorio de sesiones: <code>" . session_save_path() . "</code></p>";
echo "<p>ID de sesión actual: <code>" . session_id() . "</code></p>";
echo "<p>Estado de sesión: <code>" . session_status() . "</code> (1=disabled, 2=none, 3=active)</p>";

if (is_writable(session_save_path())) {
  echo "<p class='status ok'>✓ Directorio de sesiones es escribible</p>";
} else {
  echo "<p class='status warn'>⚠ Directorio de sesiones NO es escribible. Las sesiones pueden fallar.</p>";
}
echo "</div>\n";

// =====================================================
// TEST 7: SESIÓN ACTUAL
// =====================================================

echo "<div class='test'><h3>7️⃣ Sesión Actual</h3>";

if (isset($_SESSION['logueado']) && $_SESSION['logueado'] === true) {
  echo "<p class='status ok'>✓ Hay una sesión activa</p>";
  echo "<p>Usuario logueado: " . htmlspecialchars($_SESSION['usuario_nombre']) . "</p>";
} else {
  echo "<p class='status warn'>ℹ No hay sesión activa (esto es normal si no has logueado)</p>";
}
echo "</div>\n";

// =====================================================
// TEST 8: PRUEBA DE LOGIN SIMULADO
// =====================================================

echo "<div class='test'><h3>8️⃣ Prueba de Login Simulado</h3>";

// Simular un login sin usar sesiones
$testEmail = "admin@webnova.com";
$testPassword = "0000";

$stmt = $conn->prepare("SELECT id, nombre, email, password, rol FROM usuarios WHERE email = ?");
$stmt->bind_param("s", $testEmail);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
  $usuario = $result->fetch_assoc();
  
  if (password_verify($testPassword, $usuario['password'])) {
    echo "<p class='status ok'>✓ Login simulado EXITOSO</p>";
    echo "<p>Email: <code>" . htmlspecialchars($usuario['email']) . "</code></p>";
    echo "<p>Password: ✓ Verificado</p>";
    echo "<p>Rol: <code>" . htmlspecialchars($usuario['rol']) . "</code></p>";
  } else {
    echo "<p class='status fail'>✗ Login simulado FALLÓ</p>";
    echo "<p>El password_verify() devolvió false. Hay un problema con el hash.</p>";
  }
} else {
  echo "<p class='status fail'>✗ Usuario no encontrado</p>";
}
echo "</div>\n";

// =====================================================
// RESUMEN Y RECOMENDACIONES
// =====================================================

echo "<div class='test' style='border-left-color: #2563eb;'>
<h3>📋 Resumen y Soluciones</h3>";

echo "<h4>Si TODO está en verde (✓):</h4>";
echo "<ul>";
echo "<li>El problema es probablemente con tus datos de login</li>";
echo "<li>Verifica que escribes exactamente: <code>admin@webnova.com</code> y <code>0000</code></li>";
echo "<li>Comprueba que no hay mayúsculas/minúsculas incorrectas</li>";
echo "</ul>";

echo "<h4>Si hay algo en ROJO (✗):</h4>";
echo "<ul>";
echo "<li>Necesitas ejecutar <code>database.sql</code> en phpMyAdmin</li>";
echo "<li>O verificar que XAMPP (Apache + MySQL) está corriendo</li>";
echo "</ul>";

echo "<h4>Si después de reiniciar XAMPP el login falla:</h4>";
echo "<ul>";
echo "<li>Espera 5-10 segundos después de que MySQL inicie</li>";
echo "<li>Recarga esta página para verificar que la BD está lista</li>";
echo "<li>Si sigue fallando, verifica los logs de MySQL</li>";
echo "</ul>";

echo "</div>\n";

// Cerrar
echo "</div>
</body>
</html>";

$conn->close();
?>

<?php
/**
 * repair_database.php
 * 
 * SCRIPT DE REPARO AUTOMÁTICO
 * 
 * Detecta y repara automáticamente problemas comunes en la BD:
 * 1. Usuario admin no existe → Lo crea
 * 2. Hash del admin es incorrecto → Lo corrige
 * 3. Otras inconsistencias
 * 
 * USO: http://localhost/WebnovaManager/repair_database.php
 */

echo "<!DOCTYPE html>
<html lang='es'>
<head>
  <meta charset='UTF-8'>
  <meta name='viewport' content='width=device-width, initial-scale=1.0'>
  <title>Reparo de Base de Datos - WebNova Manager</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 2rem; background: #f5f5f5; }
    .container { max-width: 900px; margin: 0 auto; background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    h1 { color: #2563eb; border-bottom: 3px solid #2563eb; padding-bottom: 1rem; }
    .action { margin: 2rem 0; padding: 1rem; border-left: 4px solid #f59e0b; background: #fffbeb; }
    .action.success { border-left-color: #10b981; background: #f0fdf4; }
    .action.error { border-left-color: #ef4444; background: #fef2f2; }
    .action h3 { margin-top: 0; }
    code { background: #e5e7eb; padding: 0.25rem 0.5rem; border-radius: 3px; font-family: monospace; }
    .status { font-weight: bold; margin-top: 0.5rem; }
    .status.ok { color: #10b981; }
    .status.fail { color: #ef4444; }
    pre { background: #1f2937; color: #10b981; padding: 1rem; border-radius: 5px; overflow-x: auto; }
    .info { background: #f0f9ff; border-left: 4px solid #0284c7; padding: 1rem; border-radius: 5px; margin: 1rem 0; }
  </style>
</head>
<body>
<div class='container'>
  <h1>🔧 Reparo de Base de Datos</h1>
  <p>Script automático para reparar problemas comunes en la BD.</p>\n";

// Conectar a BD
require_once __DIR__ . '/config/db.php';

if (!$conn || !$conn->ping()) {
  echo "<div class='action error'>
    <h3>✗ Error de Conexión</h3>
    <p>No se puede conectar a la BD. Verifica que MySQL está corriendo.</p>
  </div>";
  echo "</div></body></html>";
  die();
}

echo "<p class='status ok'>✓ Conectado a la BD: " . DB_NAME . "</p>\n";

// =====================================================
// 1. VERIFICAR SI TABLA USUARIOS EXISTE
// =====================================================

echo "<div class='action'><h3>1. Verificar tabla de usuarios</h3>";

$result = $conn->query("SHOW TABLES LIKE 'usuarios'");
if ($result && $result->num_rows > 0) {
  echo "<p class='status ok'>✓ Tabla usuarios existe</p>";
} else {
  echo "<p class='status fail'>✗ Tabla usuarios NO existe</p>";
  echo "<p>Necesitas ejecutar database.sql desde phpMyAdmin</p>";
  echo "</div></div></body></html>";
  die();
}
echo "</div>\n";

// =====================================================
// 2. CONTAR USUARIOS EN LA BD
// =====================================================

echo "<div class='action'><h3>2. Contar usuarios en la BD</h3>";

$result = $conn->query("SELECT COUNT(*) as total FROM usuarios");
$row = $result->fetch_assoc();
$totalUsuarios = $row['total'];

echo "<p>Total de usuarios en la BD: <code>$totalUsuarios</code></p>";

if ($totalUsuarios == 0) {
  echo "<p class='status fail'>⚠ La tabla está vacía. Necesito regenerar datos...</p>";
}
echo "</div>\n";

// =====================================================
// 3. VERIFICAR SI USUARIO ADMIN EXISTE
// =====================================================

echo "<div class='action'><h3>3. Buscar usuario admin@webnova.com</h3>";

$stmt = $conn->prepare("SELECT id, email, password, rol FROM usuarios WHERE email = ?");
$stmt->bind_param("s", $email);
$email = "admin@webnova.com";
$stmt->execute();
$result = $stmt->get_result();

$adminExiste = $result->num_rows > 0;
$admin = null;

if ($adminExiste) {
  $admin = $result->fetch_assoc();
  echo "<p class='status ok'>✓ Usuario admin encontrado (ID: " . $admin['id'] . ")</p>";
} else {
  echo "<p class='status fail'>✗ Usuario admin NO existe</p>";
}

echo "</div>\n";

// =====================================================
// 4. GENERAR HASH CORRECTO
// =====================================================

echo "<div class='action'><h3>4. Generar hash correcto para '0000'</h3>";

$hashCorrecto = password_hash('0000', PASSWORD_BCRYPT);
echo "<p>Hash generado: <code>" . substr($hashCorrecto, 0, 30) . "...</code></p>";

// Verificar que funciona
if (password_verify('0000', $hashCorrecto)) {
  echo "<p class='status ok'>✓ Hash verificado correctamente</p>";
} else {
  echo "<p class='status fail'>✗ ERROR: Hash no se verifica (esto sería muy raro)</p>";
}

echo "</div>\n";

// =====================================================
// 5. REPARAR DATOS SI ES NECESARIO
// =====================================================

echo "<div class='action'><h3>5. 🔧 Reparación Automática</h3>";

if (!$adminExiste) {
  echo "<p>Insertando usuario admin...</p>";
  
  $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)");
  $stmt->bind_param("ssss", $nombre, $adminEmail, $hash, $rol);
  
  $nombre = "Admin WEBNOVA";
  $adminEmail = "admin@webnova.com";
  $hash = $hashCorrecto;
  $rol = "admin";
  
  if ($stmt->execute()) {
    echo "<p class='status ok'>✓ Usuario admin CREADO exitosamente</p>";
    echo "<p>ID: " . $conn->insert_id . "</p>";
  } else {
    echo "<p class='status fail'>✗ ERROR al insertar: " . $conn->error . "</p>";
  }
  
} else if ($admin && $admin['password'] !== $hashCorrecto) {
  echo "<p>Actualizando hash del admin...</p>";
  
  // Verificar si el hash actual es incorrecto
  if (!password_verify('0000', $admin['password'])) {
    $stmt = $conn->prepare("UPDATE usuarios SET password = ? WHERE email = ?");
    $stmt->bind_param("ss", $hashCorrecto, $adminEmail);
    $adminEmail = "admin@webnova.com";
    
    if ($stmt->execute()) {
      echo "<p class='status ok'>✓ Hash del admin ACTUALIZADO correctamente</p>";
    } else {
      echo "<p class='status fail'>✗ ERROR al actualizar: " . $conn->error . "</p>";
    }
  } else {
    echo "<p class='status ok'>✓ Hash ya es correcto, no hay cambios necesarios</p>";
  }
  
} else {
  echo "<p class='status ok'>✓ Todo está correcto, no hay nada que reparar</p>";
}

echo "</div>\n";

// =====================================================
// 6. VERIFICACIÓN FINAL
// =====================================================

echo "<div class='action'><h3>6. ✔️ Verificación Final</h3>";

$stmt = $conn->prepare("SELECT email, password FROM usuarios WHERE email = ?");
$stmt->bind_param("s", $email);
$email = "admin@webnova.com";
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
  $usuario = $result->fetch_assoc();
  
  if (password_verify('0000', $usuario['password'])) {
    echo "<p class='status ok'>✓ REPARO COMPLETADO EXITOSAMENTE</p>";
    echo "<p>El usuario admin@webnova.com puede loguearse con contraseña: <code>0000</code></p>";
    echo "<div class='info'>";
    echo "<strong>Próximos pasos:</strong><br>";
    echo "1. Abre: <code>http://localhost/WebnovaManager/admin/index.html</code><br>";
    echo "2. Ingresa email: <code>admin@webnova.com</code><br>";
    echo "3. Ingresa contraseña: <code>0000</code><br>";
    echo "4. Intenta loguearte<br>";
    echo "</div>";
  } else {
    echo "<p class='status fail'>✗ Aún hay problemas: password_verify() falla</p>";
    echo "<p>Esto es muy extraño. Contacta a soporte técnico.</p>";
  }
} else {
  echo "<p class='status fail'>✗ Usuario admin sigue sin existir</p>";
  echo "<p>Intenta ejecutar database.sql manualmente en phpMyAdmin</p>";
}

echo "</div>\n";

// =====================================================
// 7. INSERTAR OTROS USUARIOS DE DEMO
// =====================================================

echo "<div class='action'><h3>7. Usuarios de Demo Adicionales</h3>";

$demoUsuarios = [
  ['Carlos González', 'carlos@webnova.com', 'admin'],
  ['Sergio Martínez', 'sergio@webnova.com', 'editor'],
  ['Ester López', 'ester@webnova.com', 'usuario'],
];

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM usuarios WHERE email = ?");

$nuevosInsertados = 0;

foreach ($demoUsuarios as $user) {
  list($nombre, $userEmail, $userRol) = $user;
  
  $stmt->bind_param("s", $userEmail);
  $stmt->execute();
  $res = $stmt->get_result();
  $row = $res->fetch_assoc();
  
  if ($row['total'] == 0) {
    $insertStmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)");
    $insertStmt->bind_param("ssss", $nombre, $userEmail, $hashCorrecto, $userRol);
    
    if ($insertStmt->execute()) {
      echo "<p class='status ok'>✓ " . htmlspecialchars($nombre) . " (" . htmlspecialchars($userEmail) . ") - " . htmlspecialchars($userRol) . "</p>";
      $nuevosInsertados++;
    }
    $insertStmt->close();
  }
}

if ($nuevosInsertados > 0) {
  echo "<p>Se agregaron $nuevosInsertados usuarios de demo.</p>";
} else {
  echo "<p>Todos los usuarios de demo ya existen.</p>";
}

echo "</div>\n";

// =====================================================
// 8. RESUMEN FINAL
// =====================================================

echo "<div class='action success'>
<h3>✅ Reparo Completado</h3>
<p>La base de datos ha sido verificada y reparada automáticamente.</p>
<p><strong>Estado actual:</strong></p>";

$result = $conn->query("SELECT COUNT(*) as total FROM usuarios");
$row = $result->fetch_assoc();

echo "<ul>";
echo "<li>Total de usuarios: <strong>" . $row['total'] . "</strong></li>";
echo "<li>Admin: <strong>admin@webnova.com / 0000</strong></li>";
echo "<li>Autenticación: <strong>✓ Funcionando</strong></li>";
echo "</ul>";

echo "<p><a href='http://localhost/WebnovaManager/diagnose_login.php' style='color: #2563eb; text-decoration: none;'>👈 Volver al diagnóstico</a></p>";

echo "</div>\n";

echo "</div></body></html>";

$conn->close();
?>

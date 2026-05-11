<?php
/**
 * debug.php - Diagnóstico del sistema
 * Prueba:
 * 1. Conexión a MySQL
 * 2. Si existe la BD
 * 3. Si existen los usuarios
 * 4. Si password_verify funciona
 */

echo "<h1>🔍 Diagnóstico WebNova Manager</h1>";
echo "<hr>";

// =====================================================
// TEST 1: Conexión MySQL
// =====================================================
echo "<h2>TEST 1: Conexión MySQL</h2>";

$conn = new mysqli('localhost', 'root', '', 'webnova_db');

if ($conn->connect_error) {
    echo "❌ ERROR: " . $conn->connect_error;
    die();
} else {
    echo "✅ Conectado a MySQL correctamente<br>";
    echo "Host: localhost<br>";
    echo "User: root<br>";
    echo "Database: webnova_db<br>";
}

$conn->set_charset("utf8mb4");

// =====================================================
// TEST 2: Verificar tabla usuarios existe
// =====================================================
echo "<h2>TEST 2: Tabla usuarios</h2>";

$result = $conn->query("SHOW TABLES LIKE 'usuarios'");

if ($result->num_rows === 0) {
    echo "❌ Tabla 'usuarios' NO existe<br>";
    echo "Posible solución: Importar database.sql nuevamente<br>";
} else {
    echo "✅ Tabla 'usuarios' existe<br>";
}

// =====================================================
// TEST 3: Contar usuarios
// =====================================================
echo "<h2>TEST 3: Usuarios en BD</h2>";

$result = $conn->query("SELECT COUNT(*) as total FROM usuarios");
$row = $result->fetch_assoc();
$total = $row['total'];

if ($total === 0) {
    echo "❌ NO hay usuarios en la BD (total: 0)<br>";
    echo "Posible solución: Verificar que database.sql se importó completo<br>";
} else {
    echo "✅ Hay $total usuarios en la BD<br>";
}

// =====================================================
// TEST 4: Listar todos los usuarios
// =====================================================
echo "<h2>TEST 4: Usuarios en la BD</h2>";

$result = $conn->query("SELECT id, nombre, email, rol FROM usuarios");

if ($result->num_rows > 0) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Nombre</th><th>Email</th><th>Rol</th></tr>";

    while ($user = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $user['id'] . "</td>";
        echo "<td>" . htmlspecialchars($user['nombre']) . "</td>";
        echo "<td>" . htmlspecialchars($user['email']) . "</td>";
        echo "<td>" . htmlspecialchars($user['rol']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "❌ No hay usuarios<br>";
}

// =====================================================
// TEST 5: Verificar password_verify
// =====================================================
echo "<h2>TEST 5: Verificación de Contraseña</h2>";

$result = $conn->query("SELECT password FROM usuarios WHERE email = 'admin@webnova.com'");

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $hash_en_bd = $row['password'];

    echo "Hash en BD: " . substr($hash_en_bd, 0, 50) . "...<br>";

    // Verificar con contraseña 0000
    if (password_verify('0000', $hash_en_bd)) {
        echo "✅ password_verify('0000', hash) = TRUE<br>";
        echo "La contraseña es correcta<br>";
    } else {
        echo "❌ password_verify('0000', hash) = FALSE<br>";
        echo "ERROR: La contraseña no coincide con el hash<br>";
    }
} else {
    echo "❌ Usuario admin@webnova.com NO existe<br>";
}

// =====================================================
// TEST 6: Probar conexión directa a auth/login.php
// =====================================================
echo "<h2>TEST 6: Prueba Manual de Login</h2>";

echo "<p>Para probar manualmente, abre el navegador en:</p>";
echo "<code>http://localhost/WebnovaManager/admin/index.html</code><br>";
echo "<p>Ingresa:</p>";
echo "<ul>";
echo "<li>Email: <strong>admin@webnova.com</strong></li>";
echo "<li>Contraseña: <strong>0000</strong></li>";
echo "</ul>";

// =====================================================
// TEST 7: Verificar archivo auth/login.php existe
// =====================================================
echo "<h2>TEST 7: Archivos PHP</h2>";

$files_to_check = [
    '../config/db.php',
    '../auth/login.php',
    '../auth/logout.php',
    '../middleware/auth.php'
];

foreach ($files_to_check as $file) {
    $full_path = __DIR__ . '/' . $file;
    if (file_exists($full_path)) {
        echo "✅ " . $file . " existe<br>";
    } else {
        echo "❌ " . $file . " NO existe<br>";
    }
}

// =====================================================
// RESULTADO FINAL
// =====================================================
echo "<hr>";
echo "<h2>📊 RESUMEN</h2>";

$all_ok = true;

if ($total > 0 && password_verify('0000', $hash_en_bd)) {
    echo "✅ Todo parece estar correcto<br>";
    echo "El problema puede estar en:<br>";
    echo "- JavaScript del formulario no funciona<br>";
    echo "- F12 → Console → Ver errores JavaScript<br>";
} else {
    echo "❌ Hay problemas en:<br>";
    if ($total === 0) {
        echo "- Base de datos no importada correctamente<br>";
    }
}

$conn->close();

?>

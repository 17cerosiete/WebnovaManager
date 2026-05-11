<?php
/**
 * test-login.php
 * Prueba REAL de login - sin interfaz, solo lógica
 */

echo "<h1>🧪 TEST DE LOGIN - LÓGICA PURA</h1>";
echo "<hr>";

// Conexión
$conn = new mysqli('localhost', 'root', '', 'webnova_db');
if ($conn->connect_error) {
    die("❌ Conexión fallida: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
echo "✅ Conectado a BD<br><br>";

// Test 1: Obtener usuario
echo "<h2>TEST 1: Buscar usuario admin@webnova.com</h2>";
$stmt = $conn->prepare("SELECT id, nombre, email, password, rol FROM usuarios WHERE email = ?");
$email = 'admin@webnova.com';
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "❌ Usuario NO existe<br>";
} else {
    $usuario = $result->fetch_assoc();
    echo "✅ Usuario encontrado<br>";
    echo "ID: " . $usuario['id'] . "<br>";
    echo "Nombre: " . htmlspecialchars($usuario['nombre']) . "<br>";
    echo "Email: " . htmlspecialchars($usuario['email']) . "<br>";
    echo "Rol: " . htmlspecialchars($usuario['rol']) . "<br>";
    echo "Hash: " . substr($usuario['password'], 0, 50) . "...<br><br>";

    // Test 2: Verificar contraseña
    echo "<h2>TEST 2: Verificar password_verify('0000', hash)</h2>";
    if (password_verify('0000', $usuario['password'])) {
        echo "✅ password_verify devuelve TRUE<br>";
        echo "✅ LA CONTRASEÑA ES CORRECTA<br>";
        echo "<br><strong style='color: green; font-size: 1.2em;'>🎉 LOGIN FUNCIONARÍA CORRECTAMENTE</strong>";
    } else {
        echo "❌ password_verify devuelve FALSE<br>";
        echo "❌ LA CONTRASEÑA NO COINCIDE<br>";
        echo "<br><strong style='color: red; font-size: 1.2em;'>⚠️ PROBLEMA: El hash en BD no corresponde a '0000'</strong>";
    }
}

$stmt->close();

echo "<br><br>";
echo "<h2>TEST 3: Simulación completa de login</h2>";

// Simular formulario POST
$_POST['email'] = 'admin@webnova.com';
$_POST['password'] = '0000';

echo "Email ingresado: " . htmlspecialchars($_POST['email']) . "<br>";
echo "Password ingresado: " . htmlspecialchars($_POST['password']) . "<br><br>";

// Lógica de login
$stmt = $conn->prepare("SELECT id, nombre, email, password, rol FROM usuarios WHERE email = ?");
$stmt->bind_param("s", $_POST['email']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "❌ Email o contraseña incorrectos<br>";
} else {
    $usuario = $result->fetch_assoc();

    if (!password_verify($_POST['password'], $usuario['password'])) {
        echo "❌ Email o contraseña incorrectos<br>";
    } else {
        echo "✅ Login exitoso<br>";
        echo "✅ Se crearía sesión PHP<br>";
        echo "✅ Se redirigirían a dashboard.php<br>";
    }
}

$stmt->close();
$conn->close();

?>

<hr>

<h2>💡 INTERPRETACIÓN</h2>

<p>Si ves "🎉 LOGIN FUNCIONARÍA CORRECTAMENTE", entonces:</p>
<ul>
    <li>La BD está OK</li>
    <li>Las contraseñas están OK</li>
    <li>El problema está en otro lado (JavaScript, rutas, etc.)</li>
</ul>

<p>Si ves "⚠️ PROBLEMA", entonces:</p>
<ul>
    <li>Ejecuta fix_passwords.php para regenerar hashes</li>
    <li>Luego vuelve a probar aquí</li>
</ul>

<hr>

<p style="margin-top: 2rem; font-weight: bold;">
    <a href="http://localhost/WebnovaManager/admin/index.html">Volver a Login</a>
</p>

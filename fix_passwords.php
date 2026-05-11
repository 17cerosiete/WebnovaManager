<?php
/**
 * fix_passwords.php
 * Arreglador: regenera hashes válidos para todos los usuarios
 */

echo "<h1>🔧 Arreglador de Contraseñas</h1>";
echo "<hr>";

// Conectar a BD
$conn = new mysqli('localhost', 'root', '', 'webnova_db');

if ($conn->connect_error) {
    die("❌ Error de conexión: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

echo "✅ Conectado a BD<br><br>";

// Generar hash válido para contraseña: 0000
$password = '0000';
$hash_correcto = password_hash($password, PASSWORD_BCRYPT);

echo "Contraseña original: <strong>0000</strong><br>";
echo "Hash BCRYPT generado: <strong>" . $hash_correcto . "</strong><br>";
echo "Verificación local: " . (password_verify('0000', $hash_correcto) ? "✅ OK" : "❌ FALLA") . "<br>";
echo "<hr>";

// Actualizar TODOS los usuarios con el hash correcto
echo "<h2>Actualizando usuarios en BD...</h2>";

$usuarios = [
    ['email' => 'carlos@webnova.com', 'nombre' => 'Carlos González'],
    ['email' => 'sergio@webnova.com', 'nombre' => 'Sergio Martínez'],
    ['email' => 'ester@webnova.com', 'nombre' => 'Ester López'],
    ['email' => 'admin@webnova.com', 'nombre' => 'Admin WEBNOVA']
];

foreach ($usuarios as $usuario) {
    $email = $usuario['email'];
    $nombre = $usuario['nombre'];

    // UPDATE: cambiar password
    $sql = "UPDATE usuarios SET password = ? WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $hash_correcto, $email);

    if ($stmt->execute()) {
        echo "✅ " . htmlspecialchars($nombre) . " (" . htmlspecialchars($email) . ")<br>";
    } else {
        echo "❌ Error al actualizar " . htmlspecialchars($email) . "<br>";
    }

    $stmt->close();
}

echo "<hr>";
echo "<h2>✅ ¡HECHO!</h2>";
echo "<p>Todos los usuarios ahora tienen la contraseña: <strong>0000</strong></p>";
echo "<p>Puedes proceder a probar el login.</p>";

$conn->close();

?>

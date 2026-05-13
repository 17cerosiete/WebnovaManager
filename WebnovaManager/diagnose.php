<?php
/**
 * diagnose.php - Diagnóstico rápido de por qué el login no funciona
 */
session_start();

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Diagnóstico - WebNova Manager</title>
    <style>
        body { font-family: monospace; background: #1e1e1e; color: #00ff00; padding: 20px; }
        .ok { color: #00ff00; } .error { color: #ff4444; } .warning { color: #ffaa00; }
        pre { background: #000; padding: 10px; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>🔍 Diagnóstico de Login</h1>

    <h2>1️⃣ Estado de Sesión</h2>
    <?php
    echo 'Session ID: <span class="ok">' . session_id() . '</span><br>';
    echo 'Session Dir: <span class="' . (is_writable(sys_get_temp_dir()) ? 'ok' : 'error') . '">' . session_save_path() . '</span><br>';
    echo 'Session Status: ' . (session_status() === PHP_SESSION_ACTIVE ? '<span class="ok">ACTIVA</span>' : '<span class="error">NO ACTIVA</span>') . '<br>';
    ?>

    <h2>2️⃣ Conexión a MySQL</h2>
    <?php
    require_once 'config/db.php';
    
    if ($conn->connect_error) {
        echo '<span class="error">❌ Error: ' . $conn->connect_error . '</span>';
    } else {
        echo '<span class="ok">✓ Conectado a: ' . $conn->server_info . '</span><br>';
        
        $result = $conn->query("SELECT COUNT(*) as count FROM usuarios");
        if ($result) {
            $row = $result->fetch_assoc();
            echo '<span class="ok">✓ Tabla usuarios tiene: ' . $row['count'] . ' usuarios</span><br>';
            
            $result = $conn->query("SELECT email, password FROM usuarios LIMIT 1");
            $user = $result->fetch_assoc();
            echo '<span class="ok">✓ Usuario demo: ' . $user['email'] . '</span><br>';
            
            // Probar password_verify
            if (password_verify('0000', $user['password'])) {
                echo '<span class="ok">✓ Password verification funciona</span><br>';
            } else {
                echo '<span class="error">❌ Password verification FALLA</span><br>';
            }
        }
    }
    ?>

    <h2>3️⃣ Cookies</h2>
    <?php
    echo 'Cookies enviadas: ';
    if (empty($_COOKIE)) {
        echo '<span class="warning">Ninguna (normal en nuevo request)</span>';
    } else {
        echo '<pre>';
        var_dump($_COOKIE);
        echo '</pre>';
    }
    ?>

    <h2>4️⃣ Archivos Temp de Sesión</h2>
    <?php
    $sessionPath = sys_get_temp_dir();
    $files = glob($sessionPath . '/sess_*');
    echo 'Total de archivos de sesión: ' . count($files) . '<br>';
    if (count($files) > 100) {
        echo '<span class="warning">⚠️ Hay muchas sesiones! Probablemente necesitas limpiar:</span><br>';
        echo '<code>rm ' . $sessionPath . '/sess_*</code>';
    }
    ?>

    <h2>5️⃣ Test de Login Simulado</h2>
    <?php
    require_once 'config/db.php';
    
    $email = 'admin@webnova.com';
    $password = '0000';
    
    $sql = "SELECT id, nombre, email, password, rol FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo '<span class="error">❌ No existe usuario: ' . $email . '</span>';
    } else {
        $usuario = $result->fetch_assoc();
        
        if (!password_verify($password, $usuario['password'])) {
            echo '<span class="error">❌ Contraseña incorrecta</span>';
        } else {
            echo '<span class="ok">✓ Credenciales correctas!</span><br>';
            
            // Crear sesión de prueba
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['usuario_email'] = $usuario['email'];
            $_SESSION['usuario_rol'] = $usuario['rol'];
            $_SESSION['logueado'] = true;
            
            echo '<span class="ok">✓ Sesión creada - Recarga esta página para verificar que persiste</span>';
        }
    }
    
    echo '<br><br>Contenido de $_SESSION:<br><pre>';
    var_dump($_SESSION);
    echo '</pre>';
    ?>

    <hr>
    <p><strong>Si ves errores, es el problema. Soluciones:</strong></p>
    <ul>
        <li>❌ "Error: SQLSTATE" → Reinicia MySQL en XAMPP</li>
        <li>❌ "No existe usuario" → Reimporta database.sql</li>
        <li>❌ "Contraseña incorrecta" → Los hashes pueden estar corruptos</li>
        <li>⚠️ Muchas sesiones → Limpia tmp de Windows</li>
    </ul>
</body>
</html>

<?php
/**
 * Conexion y bootstrap de base de datos.
 *
 * Este archivo es intencionadamente autocurable para desarrollo con XAMPP:
 * si MySQL acaba de arrancar, reintenta; si la BD o tablas minimas faltan,
 * las crea; si faltan usuarios demo, los repone.
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'webnova_db');

function webnova_connect_server($maxIntentos = 10) {
    $conn = null;

    for ($intento = 1; $intento <= $maxIntentos; $intento++) {
        $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS);

        if (!$conn->connect_error) {
            $conn->set_charset('utf8mb4');
            return $conn;
        }

        if ($intento < $maxIntentos) {
            sleep(1);
        }
    }

    http_response_code(503);
    die('MySQL no esta disponible. Inicia MySQL en XAMPP y recarga la pagina.');
}

function webnova_exec(mysqli $conn, string $sql) {
    if (!$conn->query($sql)) {
        http_response_code(500);
        die('Error preparando la base de datos: ' . $conn->error);
    }
}

function webnova_table_exists(mysqli $conn, string $table): bool {
    $stmt = $conn->prepare(
        "SELECT COUNT(*) AS total
         FROM information_schema.tables
         WHERE table_schema = ? AND table_name = ?"
    );
    $dbName = DB_NAME;
    $stmt->bind_param('ss', $dbName, $table);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return (int)($row['total'] ?? 0) > 0;
}

function webnova_ensure_user(mysqli $conn, string $email, string $nombre, string $rol): array {
    $stmt = $conn->prepare('SELECT id, nombre, email, rol FROM usuarios WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        $stmt->close();
        return $user;
    }
    $stmt->close();

    $password = password_hash('0000', PASSWORD_BCRYPT);
    $stmt = $conn->prepare('INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)');
    $stmt->bind_param('ssss', $nombre, $email, $password, $rol);
    $stmt->execute();
    $id = $stmt->insert_id;
    $stmt->close();

    return [
        'id' => $id,
        'nombre' => $nombre,
        'email' => $email,
        'rol' => $rol,
    ];
}

function webnova_bootstrap_database(mysqli $conn) {
    webnova_exec(
        $conn,
        'CREATE DATABASE IF NOT EXISTS ' . DB_NAME . ' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
    );

    if (!$conn->select_db(DB_NAME)) {
        http_response_code(500);
        die('No se pudo seleccionar la base de datos ' . DB_NAME . ': ' . $conn->error);
    }

    $tables = [
        "CREATE TABLE IF NOT EXISTS usuarios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(100) NOT NULL,
            email VARCHAR(120) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            rol ENUM('admin', 'editor', 'usuario') DEFAULT 'usuario',
            fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_email (email)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS paginas (
            id INT AUTO_INCREMENT PRIMARY KEY,
            titulo VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL UNIQUE,
            contenido LONGTEXT NOT NULL,
            autor_id INT NOT NULL,
            publicada BOOLEAN DEFAULT FALSE,
            fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (autor_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            INDEX idx_slug (slug),
            INDEX idx_publicada (publicada)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS articulos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            titulo VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL UNIQUE,
            contenido LONGTEXT NOT NULL,
            autor_id INT NOT NULL,
            publicado BOOLEAN DEFAULT FALSE,
            fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (autor_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            INDEX idx_slug (slug),
            INDEX idx_publicado (publicado)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS widgets (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(100) NOT NULL,
            tipo VARCHAR(50) NOT NULL,
            config LONGTEXT NOT NULL,
            preview_html TEXT,
            fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_tipo (tipo)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS sesiones (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NOT NULL,
            token VARCHAR(255) NOT NULL UNIQUE,
            ip_address VARCHAR(45),
            user_agent TEXT,
            creada_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            expira_en TIMESTAMP NULL,
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            INDEX idx_token (token),
            INDEX idx_usuario_id (usuario_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS categorias (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(100) NOT NULL UNIQUE,
            slug VARCHAR(100) NOT NULL UNIQUE,
            descripcion TEXT,
            fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_slug (slug)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS comentarios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            articulo_id INT NOT NULL,
            usuario_id INT,
            nombre VARCHAR(100) NOT NULL,
            email VARCHAR(120),
            contenido TEXT NOT NULL,
            aprobado BOOLEAN DEFAULT FALSE,
            fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (articulo_id) REFERENCES articulos(id) ON DELETE CASCADE,
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
            INDEX idx_articulo_id (articulo_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS logs_auditoria (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NOT NULL,
            accion VARCHAR(100) NOT NULL,
            tabla VARCHAR(100),
            registro_id INT,
            descripcion TEXT,
            ip_address VARCHAR(45),
            fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            INDEX idx_usuario_id (usuario_id),
            INDEX idx_fecha (fecha)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS configuracion (
            id INT AUTO_INCREMENT PRIMARY KEY,
            clave VARCHAR(100) NOT NULL UNIQUE,
            valor LONGTEXT,
            tipo VARCHAR(50) DEFAULT 'string',
            descripcion TEXT,
            actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS media (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre_original VARCHAR(255) NOT NULL,
            nombre_archivo VARCHAR(255) NOT NULL UNIQUE,
            ruta VARCHAR(255) NOT NULL,
            tipo_archivo VARCHAR(100),
            tamano INT,
            usuario_id INT NOT NULL,
            fecha_subida TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            INDEX idx_usuario_id (usuario_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    ];

    foreach ($tables as $sql) {
        webnova_exec($conn, $sql);
    }

    webnova_ensure_user($conn, 'admin@webnova.com', 'Admin WEBNOVA', 'admin');
    webnova_ensure_user($conn, 'carlos@webnova.com', 'Carlos Gonzalez', 'admin');
    webnova_ensure_user($conn, 'sergio@webnova.com', 'Sergio Martinez', 'editor');
    webnova_ensure_user($conn, 'ester@webnova.com', 'Ester Lopez', 'usuario');

    $settings = [
        ['sitio_titulo', 'WebNova Manager', 'string', 'Titulo del sitio'],
        ['sitio_descripcion', 'Sistema de Gestion de Contenidos profesional', 'string', 'Meta description'],
        ['sitio_url', 'http://localhost/WebnovaManager', 'string', 'URL principal del sitio'],
        ['email_contacto', 'contacto@webnova.com', 'string', 'Email de contacto'],
        ['email_soporte', 'soporte@webnova.com', 'string', 'Email de soporte tecnico'],
        ['items_por_pagina', '10', 'integer', 'Items por pagina en listados'],
        ['permitir_comentarios', '1', 'boolean', 'Permitir comentarios en posts'],
    ];

    $stmt = $conn->prepare(
        'INSERT IGNORE INTO configuracion (clave, valor, tipo, descripcion) VALUES (?, ?, ?, ?)'
    );

    foreach ($settings as $setting) {
        $stmt->bind_param('ssss', $setting[0], $setting[1], $setting[2], $setting[3]);
        $stmt->execute();
    }

    $stmt->close();

    $result = $conn->query('SELECT COUNT(*) AS total FROM widgets');
    $row = $result ? $result->fetch_assoc() : ['total' => 0];

    if ((int)$row['total'] === 0) {
        webnova_exec(
            $conn,
            "INSERT INTO widgets (nombre, tipo, config, preview_html) VALUES
            ('Hero Principal', 'hero', '{\"title\":\"Bienvenidos a WebNova\",\"subtitle\":\"Creamos tu web ideal\",\"buttonText\":\"Empezar ahora\",\"bgImage\":\"hero.jpg\"}', '<div class=\"hero\"><h1>Bienvenidos...</h1></div>'),
            ('CTA Contacto', 'cta', '{\"text\":\"Contacta con nosotros hoy mismo\",\"buttonText\":\"Enviar Mensaje\",\"color\":\"blue\"}', '<div class=\"cta\">Contacta con nosotros...</div>'),
            ('FAQ Servicios', 'faq', '{\"items\":[{\"q\":\"Que hacemos?\",\"a\":\"Diseno web profesional\"}]}', '<div class=\"faq\">FAQ...</div>')"
        );
    }
}

$conn = webnova_connect_server();
webnova_bootstrap_database($conn);

if (!$conn->ping()) {
    http_response_code(503);
    die('La conexion a MySQL no esta activa. Reinicia MySQL en XAMPP.');
}

return $conn;
?>

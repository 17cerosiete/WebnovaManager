<?php
/**
 * utils/user_management.php
 * 
 * Módulo dedicado a la gestión de usuarios y la seguridad de contraseñas.
 * Contiene funciones para crear, actualizar y verificar usuarios.
 * 
 * USO:
 *   require_once '../utils/user_management.php';
 *   
 *   // Ahora tienes disponibles:
 *   - manejar_creacion_usuario()
 */

/**
 * Maneja la creación de un nuevo usuario en la base de datos.
 * @param mysqli $conn La conexión activa a la base de datos.
 * @param string $email Email del usuario.
 * @param string $nombre Nombre completo del usuario.
 * @param string $password Contraseña en texto plano.
 * @param string $rol Rol del usuario ('admin', 'editor', 'usuario').
 * @return array Resultado de la operación (success/message).
 */
function manejar_creacion_usuario($conn, $email, $nombre, $password, $rol) {
    // 1. VALIDACIÓN BÁSICA
    if (empty($email) || empty($nombre) || empty($password) || empty($rol)) {
        return ['success' => false, 'message' => 'Todos los campos son obligatorios.'];
    }

    // 2. GENERACIÓN DEL HASH SEGURO
    // Usamos PASSWORD_BCRYPT, que es el estándar recomendado por PHP.
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // 3. INSERCIÓN EN LA BASE DE DATOS
    $sql = "INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssss", $nombre, $email, $hashed_password, $rol);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Usuario creado exitosamente.'];
        } else {
            // Manejo de errores de BD (ej: email ya existe)
            if (strpos($stmt->error, 'Duplicate entry') !== false) {
                 return ['success' => false, 'message' => 'Este email ya está registrado.'];
            }
            return ['success' => false, 'message' => 'Error al crear usuario: ' . $stmt->error];
        }
    } else {
        return ['success' => false, 'message' => 'Error de preparación de la consulta SQL.'];
    }
}

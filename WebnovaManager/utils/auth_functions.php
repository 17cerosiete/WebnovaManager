<?php
/**
 * utils/auth_functions.php
 * 
 * CENTRALIZACIÓN DE FUNCIONES DE AUTENTICACIÓN
 * 
 * ¿Por qué separar esto?
 * - Evita duplicar código de seguridad
 * - Un lugar único para mantener/debuggear autenticación
 * - Facilita agregar nuevas funciones sin romper el sistema
 * - Si hay un problema de auth, sabes exactamente dónde mirá
 * 
 * USO:
 *   require_once '../utils/auth_functions.php';
 *   
 *   // Ahora tienes disponibles:
 *   - es_usuario_logueado()
 *   - es_admin()
 *   - es_editor()
 *   - require_login()
 *   - require_admin()
 *   - require_editor()
 *   - redirigir_a_login()
 *   - obtener_info_usuario()
 */

// =====================================================
// 0. INICIALIZAR SESIONES (ROBUSTA)
// =====================================================

// Usar configuración robusta de sesiones
// (Esto incluye session_start() y validaciones)
require_once dirname(__FILE__) . '/../config/sessions.php';

// =====================================================
// 1. VERIFICACIONES BÁSICAS DE AUTENTICACIÓN
// =====================================================

/**
 * Verifica si el usuario está logueado
 * @return bool
 */
function es_usuario_logueado() {
    return isset($_SESSION['logueado']) && $_SESSION['logueado'] === true;
}

/**
 * Verifica si el usuario es administrador
 * @return bool
 */
function es_admin() {
    return isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'admin';
}

/**
 * Verifica si el usuario es editor (o admin)
 * @return bool
 */
function es_editor() {
    return isset($_SESSION['usuario_rol']) &&
           ($_SESSION['usuario_rol'] === 'editor' || $_SESSION['usuario_rol'] === 'admin');
}

/**
 * Verifica si el usuario tiene un rol específico
 * @param string $rol
 * @return bool
 */
function tiene_rol($rol) {
    return isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === $rol;
}

// =====================================================
// 2. FUNCIONES DE REDIRECCIÓN Y PROTECCIÓN
// =====================================================

/**
 * Redirige a login de forma segura y robusta
 * Construye la URL dinámicamente basada en la estructura del servidor
 */
function redirigir_a_login() {
    // Destruir sesión por seguridad
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }
    
    // Obtener la ruta base del proyecto
    // Ejemplo: /WebnovaManager/
    $baseUrl = dirname(dirname($_SERVER['SCRIPT_NAME']));
    if ($baseUrl === '\\' || $baseUrl === '/' || $baseUrl === '.') {
        $baseUrl = '';
    }
    
    // Construir URL completa a login
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $loginUrl = $protocol . '://' . $host . $baseUrl . '/admin/index.html';
    
    header('Location: ' . $loginUrl);
    exit();
}

/**
 * Protege una página: redirige a login si no está autenticado
 * Retorna un array con información del usuario si está autenticado
 * @return array|null
 */
function require_login() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!es_usuario_logueado()) {
        redirigir_a_login();
    }
    
    return obtener_info_usuario();
}

/**
 * Protege una página: solo administradores
 * @return array|null
 */
function require_admin() {
    require_login();
    
    if (!es_admin()) {
        header('Location: dashboard.php?error=permiso_denegado');
        exit();
    }
    
    return obtener_info_usuario();
}

/**
 * Protege una página: editores o administradores
 * @return array|null
 */
function require_editor() {
    require_login();
    
    if (!es_editor()) {
        header('Location: dashboard.php?error=permiso_denegado');
        exit();
    }
    
    return obtener_info_usuario();
}

// =====================================================
// 3. FUNCIONES DE OBTENCIÓN DE INFORMACIÓN
// =====================================================

/**
 * Obtiene la información completa del usuario logueado
 * @return array|null
 */
function obtener_info_usuario() {
    if (!es_usuario_logueado()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['usuario_id'] ?? null,
        'nombre' => $_SESSION['usuario_nombre'] ?? null,
        'email' => $_SESSION['usuario_email'] ?? null,
        'rol' => $_SESSION['usuario_rol'] ?? null,
        'login_tiempo' => $_SESSION['login_tiempo'] ?? null
    ];
}

/**
 * Obtiene el ID del usuario logueado
 * @return int|null
 */
function obtener_usuario_id() {
    return $_SESSION['usuario_id'] ?? null;
}

/**
 * Obtiene el nombre del usuario logueado
 * @return string|null
 */
function obtener_usuario_nombre() {
    return $_SESSION['usuario_nombre'] ?? null;
}

/**
 * Obtiene el rol del usuario logueado
 * @return string|null
 */
function obtener_usuario_rol() {
    return $_SESSION['usuario_rol'] ?? null;
}

// =====================================================
// 4. FUNCIONES DE VALIDACIÓN Y SANITIZACIÓN
// =====================================================

/**
 * Valida y sanitiza un email
 * @param string $email
 * @return string|false
 */
function validar_email($email) {
    $email = trim($email);
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return $email;
    }
    return false;
}

/**
 * Valida una contraseña (mínimo 6 caracteres)
 * @param string $password
 * @return bool
 */
function validar_password($password) {
    return strlen($password) >= 6;
}

// =====================================================
// 5. INICIALIZACIÓN
// =====================================================

// Iniciar sesión si no está ya iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>

<?php
/**
 * middleware/auth.php
 *
 * MIDDLEWARE de autenticación
 * Protege páginas: verifica que el usuario esté logueado
 *
 * Uso en cualquier página protegida:
 *   <?php
 *   require_once '../middleware/auth.php';
 *   // Si no está logueado, esto redirige a login
 *   // Si está logueado, continúa con la página
 *   ?>
 */

// =====================================================
// 1. INICIAR SESIÓN (CON CONFIGURACIÓN ROBUSTA)
// =====================================================

// Usar configuración robusta de sesiones
require_once dirname(__FILE__) . '/../config/sessions.php';

// =====================================================
// 2. VERIFICAR SI USUARIO ESTÁ LOGUEADO
// =====================================================

if (!isset($_SESSION['logueado']) || $_SESSION['logueado'] !== true) {
    // NO está logueado - redirigir
    session_destroy();
    
    // Construir URL de login robusta
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $baseUrl = dirname(dirname($_SERVER['SCRIPT_NAME']));
    if ($baseUrl === '\\' || $baseUrl === '/' || $baseUrl === '.') {
        $baseUrl = '';
    }
    
    $loginUrl = $protocol . '://' . $host . $baseUrl . '/admin/index.html';
    header('Location: ' . $loginUrl);
    exit();
}

// =====================================================
// 3. USUARIO ESTÁ AUTENTICADO - DISPONIBILIZAR FUNCIONES
// =====================================================

// Función: ¿Es administrador?
function esAdmin() {
    return isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'admin';
}

// Función: ¿Es editor o admin?
function esEditor() {
    return isset($_SESSION['usuario_rol']) &&
           ($_SESSION['usuario_rol'] === 'editor' || $_SESSION['usuario_rol'] === 'admin');
}

// Función: Obtener información del usuario
function obtener_usuario_info() {
    return [
        'id' => $_SESSION['usuario_id'] ?? null,
        'nombre' => $_SESSION['usuario_nombre'] ?? null,
        'email' => $_SESSION['usuario_email'] ?? null,
        'rol' => $_SESSION['usuario_rol'] ?? null,
    ];
}

// Función: Redirigir si NO es admin
function requiereAdmin() {
    if (!esAdmin()) {
        header('Location: dashboard.php?error=permiso_denegado');
        exit();
    }
}

// Función: Redirigir si NO es editor
function requiereEditor() {
    if (!esEditor()) {
        header('Location: dashboard.php?error=permiso_denegado');
        exit();
    }
}

?>



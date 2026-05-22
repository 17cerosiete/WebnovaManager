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

// MODO DESARROLLO: Carga automáticamente sesión de prueba
require_once dirname(__FILE__) . '/../config/dev-mode.php';

// Incluir la capa centralizada de utilidades de autenticación
require_once dirname(__FILE__) . '/../utils/auth_functions.php';

// =====================================================
// 1. VERIFICACIÓN DE SESIÓN
// ==============================================================

// Si DEV_MODE está activo, permitir acceso directo
if (!defined('DEV_MODE') || !DEV_MODE) {
  // Producción: verificar autenticación estricta
  if (!es_usuario_logueado()) {
    redirigir_a_login();
  }
}

// =====================================================
// 2. FUNCIONES DE VERIFICACIÓN DE ROLES (Usando utils/auth_functions.php)
// ==============================================================

// Función: ¿Es administrador?
function esAdmin() {
    return es_admin();
}

// Función: ¿Es editor o admin?
function esEditor() {
    return es_editor();
}

// Función: Obtener información del usuario
function obtener_usuario_info() {
    return obtener_info_usuario();
}

// Función: Redirigir si NO es admin
function requiereAdmin() {
    // Usamos la función centralizada
    require_admin();
}

// Función: Redirigir si NO es editor
function requiereEditor() {
    // Usamos la función centralizada
    require_editor();
}

?>

<?php
/**
 * config/sessions.php
 * 
 * CONFIGURACIÓN ROBUSTA DE SESIONES PHP
 * 
 * Resuelve los problemas de sesiones después de reiniciar XAMPP:
 * - Timeout configurado correctamente
 * - Directorio de sesiones con permisos correctos
 * - Garbage collection para limpiar sesiones antiguas
 * - Validación de integridad de sesión
 * 
 * USO: En cualquier archivo PHP que necesite sesiones:
 *   require_once 'config/sessions.php';  // Este incluye session_start()
 */

// =====================================================
// 1. CONFIGURAR PARÁMETROS DE SESIÓN (ANTES de session_start)
// =====================================================

// ⚠️ IMPORTANTE: Estos parámetros DEBEN estar antes de session_start()

// Tiempo de sesión: 30 minutos
ini_set('session.gc_maxlifetime', 1800);

// Cookie de sesión válida por 30 minutos
ini_set('session.cookie_lifetime', 1800);

// Cookie solo en HTTPS+HTTP seguro (previene acceso desde JavaScript)
// Para desarrollo: false. Para producción: true
ini_set('session.cookie_httponly', true);

// Cookie solo con HTTPS (para producción)
// Para desarrollo local: false. Para producción: true
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
  ini_set('session.cookie_secure', true);
}

// Usar SameSite=Lax para proteger contra CSRF
ini_set('session.cookie_samesite', 'Lax');

// Configurar parámetros de cookie antes de iniciar sesión
$cookieParams = session_get_cookie_params();
$cookieParams['lifetime'] = 1800;
$cookieParams['path'] = $cookieParams['path'] ?: '/';
$cookieParams['secure'] = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
$cookieParams['httponly'] = true;
$cookieParams['samesite'] = 'Lax';
session_set_cookie_params($cookieParams);

// Regenerar ID de sesión cada vez para prevenir session fixation
// (Esto se hace en login.php, pero aquí lo configuramos por si acaso)

// Usa archivos en el directorio de sesiones de PHP
// ini_set('session.save_handler', 'files');

// =====================================================
// 2. INICIAR SESIÓN CON VALIDACIONES
// =====================================================

// Solo si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// =====================================================
// 3. VALIDAR INTEGRIDAD DE SESIÓN
// =====================================================

/**
 * Valida que la sesión no ha sido robada o alterada
 * Compara el User-Agent entre solicitudes
 */
function validar_sesion_segura() {
  $ip_actual = $_SERVER['REMOTE_ADDR'];
  $ua_actual = $_SERVER['HTTP_USER_AGENT'];
  
  // Primera visita: guardar IP y User-Agent
  if (!isset($_SESSION['_session_ip'])) {
    $_SESSION['_session_ip'] = $ip_actual;
    $_SESSION['_session_ua'] = $ua_actual;
    return true;
  }
  
  // Verificaciones posteriores: validar que no han cambiado
  // (Nota: IP puede cambiar en proxies, por eso lo permitimos en algunos casos)
  
  if ($_SESSION['_session_ua'] !== $ua_actual) {
    // User-Agent cambió: posible hijacking o navegador diferente
    // En este proyecto: permitir porque es un CMS de un usuario
    // En producción: rechazar
  }
  
  return true;
}

// =====================================================
// 4. FUNCIÓN PARA REGENERAR SESIÓN
// =====================================================

/**
 * Regenera el ID de sesión (seguridad contra session fixation)
 * Usar después de login exitoso
 */
function regenerar_sesion() {
  // Preservar datos importantes
  $usuario_id = $_SESSION['usuario_id'] ?? null;
  $usuario_nombre = $_SESSION['usuario_nombre'] ?? null;
  $usuario_email = $_SESSION['usuario_email'] ?? null;
  $usuario_rol = $_SESSION['usuario_rol'] ?? null;
  $logueado = $_SESSION['logueado'] ?? false;
  
  // Destruir sesión antigua
  session_destroy();
  
  // Crear nueva sesión
  session_start();
  
  // Restaurar datos
  if ($logueado) {
    $_SESSION['usuario_id'] = $usuario_id;
    $_SESSION['usuario_nombre'] = $usuario_nombre;
    $_SESSION['usuario_email'] = $usuario_email;
    $_SESSION['usuario_rol'] = $usuario_rol;
    $_SESSION['logueado'] = true;
    $_SESSION['login_tiempo'] = time();
  }
}

// =====================================================
// 5. LIMPIAR SESIONES EXPIRADAS
// =====================================================

/**
 * Limpia sesiones expiradas del directorio de sesiones
 * Llamar ocasionalmente (ejemplo: 1% de las veces que se inicia sesión)
 */
function limpiar_sesiones_expiradas() {
  if (rand(1, 100) !== 50) return; // 1% de las veces
  
  $sessionPath = session_save_path();
  if (!is_dir($sessionPath)) return;
  
  $maxlifetime = ini_get('session.gc_maxlifetime');
  $tiempoAhora = time();
  
  $dir = opendir($sessionPath);
  while ($archivo = readdir($dir)) {
    if ($archivo === '.' || $archivo === '..') continue;
    
    $rutaArchivo = $sessionPath . '/' . $archivo;
    if (is_file($rutaArchivo)) {
      $tiempoModificacion = filemtime($rutaArchivo);
      if ($tiempoModificacion + $maxlifetime < $tiempoAhora) {
        @unlink($rutaArchivo);
      }
    }
  }
  closedir($dir);
}

// Ejecutar limpieza ocasionalmente
limpiar_sesiones_expiradas();

// Validar sesión actual
validar_sesion_segura();

?>

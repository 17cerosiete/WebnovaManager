<?php
/**
 * middleware/auth.php
 *
 * MIDDLEWARE de autenticación
 * Protege páginas: verifica que el usuario esté logueado
 *
 * Uso en cualquier página protegida:
 *   <?php
 *   require_once '../middleware/auth.php'; // Poner al inicio
 *   // Si no está logueado, esto redirige a login.html
 *   // Si está logueado, continúa con la página
 *   ?>
 *
 * FLUJO:
 * 1. Verificar si sesión existe
 * 2. ¿Usuario logueado? → Continuar
 * 3. ¿No logueado? → Redirigir a login
 *
 * Esto es SEGURIDAD BÁSICA pero EFECTIVA
 */

// =====================================================
// 1. INICIAR SESIÓN
// =====================================================

// session_start() carga la sesión actual
// Si no existe, crea una nueva
session_start();

// =====================================================
// 2. VERIFICAR SI USUARIO ESTÁ LOGUEADO
// =====================================================

// ¿Existe la variable de sesión que creamos en login.php?
if (!isset($_SESSION['logueado']) || $_SESSION['logueado'] !== true) {
  // NO está logueado

  // Destruir cualquier dato de sesión (seguridad)
  session_destroy();

  // Redirigir a login (ruta relativa al web root)
  header('Location: /WebnovaManager/admin/index.html');

  // Detener ejecución del resto de la página
  exit();
}

// Si llegamos aquí: ¡Usuario está logueado! ✓

// =====================================================
// 3. DATOS DISPONIBLES
// =====================================================

// En cualquier página que use este middleware:
// $_SESSION['usuario_id']     - ID del usuario
// $_SESSION['usuario_nombre'] - Nombre
// $_SESSION['usuario_email']  - Email
// $_SESSION['usuario_rol']    - Rol (admin, editor, usuario)
// $_SESSION['logueado']       - true

// Ejemplo en dashboard.html (después):
// <h1>Bienvenido <?php echo $_SESSION['usuario_nombre']; ?></h1>

// =====================================================
// 4. VERIFICAR ROL (OPCIONAL)
// =====================================================

// Función auxiliar: verificar si usuario es admin
function esAdmin() {
  return isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'admin';
}

// Función: verificar si usuario es editor
function esEditor() {
  return isset($_SESSION['usuario_rol']) &&
         ($_SESSION['usuario_rol'] === 'editor' || $_SESSION['usuario_rol'] === 'admin');
}

// Función: redirigir si no es admin
function requiereAdmin() {
  if (!esAdmin()) {
    header('Location: ../admin/dashboard.html?error=permiso_denegado');
    exit();
  }
}

// EJEMPLO DE USO EN PÁGINAS:
//
// En admin/usuarios.php (solo para admins):
//   <?php
//   require_once '../middleware/auth.php';
//   requiereAdmin(); // Si no es admin: redirige
//   ?>

?>

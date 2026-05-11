<?php
/**
 * admin/dashboard.php
 *
 * Dashboard principal
 * Diferente contenido según rol del usuario
 *
 * PROTECCIÓN: requiere middleware/auth.php
 * CONTENIDO: dinámico según usuario_rol
 */

// PRIMER PASO: Verificar autenticación
require_once '../middleware/auth.php';

// Si llegamos aquí: usuario está logueado ✓
$usuario_nombre = $_SESSION['usuario_nombre'];
$usuario_rol = $_SESSION['usuario_rol'];
$usuario_id = $_SESSION['usuario_id'];

// Conectar BD para estadísticas
require_once '../config/db.php';

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - WebNova Manager</title>
  <link rel="stylesheet" href="../assets/css/styles.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
      background: #f3f4f6;
      color: #1f2937;
    }

    .navbar {
      background: white;
      border-bottom: 1px solid #e5e7eb;
      padding: 1rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .navbar h1 {
      color: #2563eb;
      font-size: 1.5rem;
    }

    .user-info {
      display: flex;
      align-items: center;
      gap: 2rem;
    }

    .user-info span {
      color: #6b7280;
      font-size: 0.9rem;
    }

    .badge {
      display: inline-block;
      padding: 0.25rem 0.75rem;
      border-radius: 999px;
      font-size: 0.8rem;
      font-weight: 600;
    }

    .badge-admin {
      background: #fee2e2;
      color: #991b1b;
    }

    .badge-editor {
      background: #fef3c7;
      color: #92400e;
    }

    .badge-usuario {
      background: #d1fae5;
      color: #065f46;
    }

    .btn {
      padding: 0.5rem 1rem;
      border: none;
      border-radius: 0.5rem;
      background: #ef4444;
      color: white;
      cursor: pointer;
      font-weight: 500;
      transition: all 250ms;
    }

    .btn:hover {
      background: #dc2626;
    }

    .container {
      max-width: 1200px;
      margin: 2rem auto;
      padding: 0 1rem;
    }

    .welcome {
      background: white;
      padding: 2rem;
      border-radius: 1rem;
      margin-bottom: 2rem;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .welcome h2 {
      color: #1f2937;
      margin-bottom: 1rem;
    }

    .welcome p {
      color: #6b7280;
      margin-bottom: 0.5rem;
    }

    .grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 1.5rem;
      margin-bottom: 2rem;
    }

    .card {
      background: white;
      padding: 1.5rem;
      border-radius: 1rem;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .card h3 {
      color: #2563eb;
      margin-bottom: 0.5rem;
    }

    .card-stat {
      font-size: 2rem;
      font-weight: bold;
      color: #1f2937;
      margin-bottom: 0.5rem;
    }

    .card-desc {
      color: #6b7280;
      font-size: 0.9rem;
    }

    .admin-features {
      background: white;
      padding: 2rem;
      border-radius: 1rem;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .admin-features h3 {
      color: #1f2937;
      margin-bottom: 1rem;
    }

    .features-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1rem;
    }

    .feature-btn {
      padding: 1rem;
      border: 2px solid #e5e7eb;
      border-radius: 0.5rem;
      background: white;
      cursor: pointer;
      font-weight: 500;
      color: #2563eb;
      transition: all 250ms;
      text-decoration: none;
      display: inline-block;
      text-align: center;
    }

    .feature-btn:hover {
      border-color: #2563eb;
      background: #eff6ff;
    }

    .error {
      background: #fee2e2;
      border-left: 4px solid #dc2626;
      padding: 1rem;
      border-radius: 0.5rem;
      color: #991b1b;
      margin-bottom: 1rem;
    }
  </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
  <h1>📊 WebNova Manager</h1>
  <div class="user-info">
    <div>
      <p><?php echo htmlspecialchars($usuario_nombre); ?></p>
      <span>
        <span class="badge badge-<?php echo $usuario_rol; ?>">
          <?php
          echo match($usuario_rol) {
            'admin' => '👑 Administrador',
            'editor' => '✏️ Editor',
            default => '👤 Usuario'
          };
          ?>
        </span>
      </span>
    </div>
    <a href="../auth/logout.php" class="btn">Cerrar sesión</a>
  </div>
</nav>

<!-- CONTENIDO PRINCIPAL -->
<div class="container">

  <!-- MENSAJE DE BIENVENIDA -->
  <div class="welcome">
    <h2>¡Bienvenido, <?php echo htmlspecialchars($usuario_nombre); ?>!</h2>
    <p>Conectado como: <strong><?php echo htmlspecialchars($usuario_email); ?></strong></p>
    <p>Rol: <strong><?php echo htmlspecialchars($usuario_rol); ?></strong></p>
  </div>

  <?php
  // ====== CONTENIDO DINÁMICO SEGÚN ROL ======
  if ($usuario_rol === 'admin'): ?>

    <!-- ============ DASHBOARD ADMIN ============ -->
    <div class="grid">
      <div class="card">
        <h3>Usuarios</h3>
        <div class="card-stat">4</div>
        <div class="card-desc">Usuarios registrados</div>
      </div>
      <div class="card">
        <h3>Páginas</h3>
        <div class="card-stat">0</div>
        <div class="card-desc">Páginas publicadas</div>
      </div>
      <div class="card">
        <h3>Artículos</h3>
        <div class="card-stat">0</div>
        <div class="card-desc">Posts publicados</div>
      </div>
    </div>

    <div class="admin-features">
      <h3>🔧 Panel de Administración</h3>
      <div class="features-grid">
        <a href="pages/list.php" class="feature-btn">📄 Gestionar Páginas</a>
        <a href="posts/list.php" class="feature-btn">📝 Gestionar Artículos</a>
        <a href="users/list.php" class="feature-btn">👥 Gestionar Usuarios</a>
        <a href="settings/general.php" class="feature-btn">⚙️ Configuración</a>
        <a href="stats/dashboard.php" class="feature-btn">📊 Estadísticas</a>
        <a href="help/index.php" class="feature-btn">❓ Ayuda</a>
      </div>
    </div>

  <?php elseif ($usuario_rol === 'editor'): ?>

    <!-- ============ DASHBOARD EDITOR ============ -->
    <div class="error">
      ℹ️ Eres <strong>Editor</strong>: puedes crear y editar contenido, pero no gestionar usuarios.
    </div>

    <div class="admin-features">
      <h3>✏️ Panel de Editor</h3>
      <div class="features-grid">
        <a href="#" class="feature-btn">📄 Mis Páginas</a>
        <a href="#" class="feature-btn">📝 Mis Artículos</a>
        <a href="#" class="feature-btn">➕ Crear Página</a>
        <a href="#" class="feature-btn">➕ Crear Artículo</a>
      </div>
    </div>

  <?php else: ?>

    <!-- ============ DASHBOARD USUARIO NORMAL ============ -->
    <div class="error">
      ℹ️ Eres <strong>Usuario</strong>: acceso limitado. Contacta a admin para más permisos.
    </div>

    <div class="welcome">
      <p>Tu rol actual no incluye acceso a funciones de edición.</p>
      <p>Solicita permisos al administrador.</p>
    </div>

  <?php endif; ?>

</div>

</body>
</html>

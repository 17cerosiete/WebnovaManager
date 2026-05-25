<?php
require_once '../middleware/auth.php';
require_once '../config/db.php';

$usuario_nombre = $_SESSION['usuario_nombre'];
$usuario_email = $_SESSION['usuario_email'];
$usuario_rol = $_SESSION['usuario_rol'];

$usuarios_count = $conn->query("SELECT COUNT(*) AS total FROM usuarios")->fetch_assoc()['total'] ?? 0;
$paginas_count = $conn->query("SELECT COUNT(*) AS total FROM paginas")->fetch_assoc()['total'] ?? 0;
$widgets_count = $conn->query("SELECT COUNT(*) AS total FROM widgets")->fetch_assoc()['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - WebNova Manager</title>
  <link rel="stylesheet" href="../assets/css/styles.css">
  <style>
    body { background: #f3f4f6; color: #1f2937; }
    .navbar { background: white; border-bottom: 1px solid #e5e7eb; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
    .navbar h1 { color: #2563eb; font-size: 1.5rem; margin: 0; }
    .user-info { display: flex; align-items: center; gap: 1rem; }
    .user-info p { margin: 0; }
    .badge { display: inline-block; padding: .25rem .75rem; border-radius: 999px; font-size: .8rem; font-weight: 700; background: #dbeafe; color: #0c4a6e; }
    .btn-exit { padding: .5rem 1rem; border-radius: .5rem; background: #ef4444; color: white; text-decoration: none; font-weight: 600; }
    .container { max-width: 1200px; margin: 2rem auto; padding: 0 1rem; }
    .welcome, .admin-features, .notice { background: white; padding: 2rem; border-radius: .75rem; margin-bottom: 2rem; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
    .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem; margin-bottom: 2rem; }
    .card { background: white; padding: 1.5rem; border-radius: .75rem; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
    .card h3 { color: #2563eb; margin-bottom: .5rem; }
    .card-stat { font-size: 2rem; font-weight: 800; color: #1f2937; }
    .card-desc { color: #6b7280; font-size: .9rem; }
    .features-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 1rem; }
    .feature-btn { padding: 1rem; border: 1px solid #e5e7eb; border-radius: .5rem; background: white; color: #2563eb; text-decoration: none; text-align: center; font-weight: 700; }
    .feature-btn:hover { border-color: #2563eb; background: #eff6ff; text-decoration: none; }
    .notice { border-left: 4px solid #2563eb; color: #334155; }
    @media (max-width: 760px) { .navbar, .user-info { align-items: flex-start; flex-direction: column; } }
  </style>
</head>
<body>
<nav class="navbar">
  <h1>WebNova Manager</h1>
  <div class="user-info">
    <div>
      <p><?php echo htmlspecialchars($usuario_nombre); ?></p>
      <span class="badge"><?php echo htmlspecialchars($usuario_rol); ?></span>
    </div>
    <a href="../auth/logout.php" class="btn-exit">Cerrar sesion</a>
  </div>
</nav>

<main class="container">
  <section class="welcome">
    <h2>Bienvenido, <?php echo htmlspecialchars($usuario_nombre); ?></h2>
    <p>Conectado como: <strong><?php echo htmlspecialchars($usuario_email); ?></strong></p>
    <p>Rol: <strong><?php echo htmlspecialchars($usuario_rol); ?></strong></p>
  </section>

  <?php if ($usuario_rol === 'admin'): ?>
    <section class="grid">
      <article class="card">
        <h3>Usuarios</h3>
        <div class="card-stat"><?php echo (int)$usuarios_count; ?></div>
        <div class="card-desc">Usuarios registrados</div>
      </article>
      <article class="card">
        <h3>Paginas</h3>
        <div class="card-stat"><?php echo (int)$paginas_count; ?></div>
        <div class="card-desc">Paginas creadas</div>
      </article>
      <article class="card">
        <h3>Widgets</h3>
        <div class="card-stat"><?php echo (int)$widgets_count; ?></div>
        <div class="card-desc">Modulos reutilizables</div>
      </article>
    </section>

    <section class="admin-features">
      <h3>Panel de administracion</h3>
      <div class="features-grid">
        <a href="pages/list.php" class="feature-btn">Gestionar paginas</a>
        <a href="posts/list.php" class="feature-btn">Gestionar widgets</a>
        <a href="users/list.php" class="feature-btn">Gestionar usuarios</a>
        <a href="settings/general.php" class="feature-btn">Configuracion</a>
        <a href="help/index.php" class="feature-btn">Ayuda</a>
        <a href="../public/index.php" class="feature-btn" target="_blank">Ver sitio publico</a>
      </div>
    </section>
  <?php elseif ($usuario_rol === 'editor'): ?>
    <section class="notice">
      <p>Eres editor: puedes crear y editar contenido, pero no gestionar usuarios.</p>
    </section>
    <section class="admin-features">
      <h3>Panel de editor</h3>
      <div class="features-grid">
        <a href="pages/list.php" class="feature-btn">Mis paginas</a>
        <a href="pages/create.php" class="feature-btn">Crear pagina</a>
        <a href="posts/list.php" class="feature-btn">Widgets</a>
        <a href="posts/create.php" class="feature-btn">Crear widget</a>
      </div>
    </section>
  <?php else: ?>
    <section class="notice">
      <p>Tu rol actual no incluye acceso a funciones de edicion. Contacta con un administrador para ampliar permisos.</p>
    </section>
  <?php endif; ?>
</main>
</body>
</html>

<?php
/**
 * admin/stats/dashboard.php
 * 
 * Dashboard de estadísticas avanzadas
 */

require_once '../../middleware/auth.php';
require_once '../../config/db.php';

if (!esAdmin()) {
    header('Location: ../dashboard.php?error=permiso');
    exit();
}

// Obtener estadísticas
$stats = [
    'usuarios_total' => 0,
    'usuarios_admin' => 0,
    'usuarios_editor' => 0,
    'paginas_total' => 0,
    'paginas_publicadas' => 0,
    'paginas_borradores' => 0,
    'articulos_total' => 0,
    'articulos_publicados' => 0,
    'articulos_borradores' => 0,
];

// Usuarios
$result = $conn->query("SELECT COUNT(*) as total FROM usuarios");
$stats['usuarios_total'] = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE rol = 'admin'");
$stats['usuarios_admin'] = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE rol = 'editor'");
$stats['usuarios_editor'] = $result->fetch_assoc()['total'];

// Páginas
$result = $conn->query("SELECT COUNT(*) as total FROM paginas");
$stats['paginas_total'] = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(*) as total FROM paginas WHERE publicada = 1");
$stats['paginas_publicadas'] = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(*) as total FROM paginas WHERE publicada = 0");
$stats['paginas_borradores'] = $result->fetch_assoc()['total'];

// Artículos
$result = $conn->query("SELECT COUNT(*) as total FROM articulos");
$stats['articulos_total'] = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(*) as total FROM articulos WHERE publicado = 1");
$stats['articulos_publicados'] = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(*) as total FROM articulos WHERE publicado = 0");
$stats['articulos_borradores'] = $result->fetch_assoc()['total'];

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas - WebNova Manager</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background: #f3f4f6; color: #1f2937; }
        .navbar { background: white; border-bottom: 1px solid #e5e7eb; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .navbar h1 { color: #2563eb; font-size: 1.5rem; }
        .container { max-width: 1200px; margin: 2rem auto; padding: 0 1rem; }
        .breadcrumb { margin-bottom: 2rem; font-size: 0.9rem; color: #6b7280; }
        .breadcrumb a { color: #2563eb; text-decoration: none; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
        .card { background: white; padding: 1.5rem; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .card h3 { color: #2563eb; margin-bottom: 0.5rem; font-size: 1rem; }
        .card-stat { font-size: 2.5rem; font-weight: bold; color: #1f2937; margin: 1rem 0; }
        .card-desc { color: #6b7280; font-size: 0.9rem; }
        .card-detail { display: flex; justify-content: space-between; font-size: 0.9rem; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e5e7eb; }
        .card-detail span { color: #6b7280; }
        .btn { padding: 0.5rem 1.25rem; border: none; border-radius: 0.5rem; background: #2563eb; color: white; cursor: pointer; font-weight: 600; transition: all 250ms; text-decoration: none; display: inline-block; }
        .btn:hover { background: #1d4ed8; }
        .btn-secondary { background: #e5e7eb; color: #1f2937; }
        .btn-secondary:hover { background: #d1d5db; }
        .chart-container { background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 2rem; }
        .stat-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1rem; padding: 1rem; background: #f9fafb; border-radius: 0.5rem; }
        .stat-item { text-align: center; }
        .stat-item-label { font-size: 0.85rem; color: #6b7280; margin-bottom: 0.5rem; }
        .stat-item-value { font-size: 1.5rem; font-weight: bold; color: #2563eb; }
    </style>
</head>
<body>

<nav class="navbar">
    <h1>📊 WebNova Manager</h1>
    <a href="../../auth/logout.php" style="color: #ef4444; text-decoration: none; font-weight: 600;">Salir</a>
</nav>

<div class="container">
    <div class="breadcrumb">
        <a href="../dashboard.php">Dashboard</a> / Estadísticas
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1 style="color: #1f2937;">Estadísticas del Sistema</h1>
        <a href="../dashboard.php" class="btn btn-secondary">Volver al dashboard</a>
    </div>

    <!-- ESTADÍSTICAS DE USUARIOS -->
    <div class="chart-container">
        <h2 style="margin-bottom: 1.5rem; color: #1f2937;">👥 Usuarios</h2>
        <div class="grid">
            <div class="card">
                <h3>Total de Usuarios</h3>
                <div class="card-stat"><?php echo $stats['usuarios_total']; ?></div>
                <div class="card-detail">
                    <span>Administradores: <strong><?php echo $stats['usuarios_admin']; ?></strong></span>
                    <span>Editores: <strong><?php echo $stats['usuarios_editor']; ?></strong></span>
                </div>
            </div>
        </div>
    </div>

    <!-- ESTADÍSTICAS DE PÁGINAS -->
    <div class="chart-container">
        <h2 style="margin-bottom: 1.5rem; color: #1f2937;">📄 Páginas</h2>
        <div class="grid">
            <div class="card">
                <h3>Total de Páginas</h3>
                <div class="card-stat"><?php echo $stats['paginas_total']; ?></div>
                <div class="stat-row">
                    <div class="stat-item">
                        <div class="stat-item-label">Publicadas</div>
                        <div class="stat-item-value"><?php echo $stats['paginas_publicadas']; ?></div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-item-label">Borradores</div>
                        <div class="stat-item-value"><?php echo $stats['paginas_borradores']; ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ESTADÍSTICAS DE ARTÍCULOS -->
    <div class="chart-container">
        <h2 style="margin-bottom: 1.5rem; color: #1f2937;">📝 Artículos</h2>
        <div class="grid">
            <div class="card">
                <h3>Total de Artículos</h3>
                <div class="card-stat"><?php echo $stats['articulos_total']; ?></div>
                <div class="stat-row">
                    <div class="stat-item">
                        <div class="stat-item-label">Publicados</div>
                        <div class="stat-item-value"><?php echo $stats['articulos_publicados']; ?></div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-item-label">Borradores</div>
                        <div class="stat-item-value"><?php echo $stats['articulos_borradores']; ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- RESUMEN GENERAL -->
    <div class="chart-container">
        <h2 style="margin-bottom: 1.5rem; color: #1f2937;">📊 Resumen General</h2>
        <div class="stat-row">
            <div class="stat-item">
                <div class="stat-item-label">Contenido Total</div>
                <div class="stat-item-value"><?php echo $stats['paginas_total'] + $stats['articulos_total']; ?></div>
            </div>
            <div class="stat-item">
                <div class="stat-item-label">Contenido Publicado</div>
                <div class="stat-item-value"><?php echo $stats['paginas_publicadas'] + $stats['articulos_publicados']; ?></div>
            </div>
            <div class="stat-item">
                <div class="stat-item-label">Borradores</div>
                <div class="stat-item-value"><?php echo $stats['paginas_borradores'] + $stats['articulos_borradores']; ?></div>
            </div>
            <div class="stat-item">
                <div class="stat-item-label">Tasa de Publicación</div>
                <div class="stat-item-value">
                    <?php 
                    $total = $stats['paginas_total'] + $stats['articulos_total'];
                    $published = $stats['paginas_publicadas'] + $stats['articulos_publicados'];
                    echo $total > 0 ? round(($published / $total) * 100) : 0; 
                    ?>%
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>

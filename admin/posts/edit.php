<?php
require_once '../../middleware/auth.php';
require_once '../../config/db.php';

if (!esEditor()) {
    header('Location: ../dashboard.php?error=permiso');
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $conn->prepare('SELECT * FROM widgets WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$widget = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$widget) {
    header('Location: list.php?error=notfound');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar widget - WebNova Manager</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <style>
        body { background: #eef2f7; padding: 2rem; }
        .layout { max-width: 1180px; margin: 0 auto; display: grid; grid-template-columns: minmax(0, 0.95fr) minmax(320px, 1.05fr); gap: 1.5rem; }
        .card { background: white; padding: 1.5rem; border: 1px solid #dbe3ef; border-radius: 8px; box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08); }
        label { display: block; margin-bottom: 1rem; font-weight: 700; }
        input, select, textarea { width: 100%; margin-top: .35rem; }
        .actions { display: flex; gap: .75rem; flex-wrap: wrap; margin-top: 1rem; }
        .schema-note { background: #f8fafc; border: 1px solid #dbe3ef; border-radius: 8px; padding: 1rem; color: #475569; }
        .preview-frame { min-height: 320px; border: 1px solid #dbe3ef; border-radius: 8px; padding: 1rem; background: #f8fafc; overflow: hidden; }
        .widget-preview-hero { display: grid; grid-template-columns: minmax(0, 1fr) minmax(180px, .75fr); gap: 1rem; align-items: center; background: #0f172a; color: white; border-radius: 8px; padding: 2rem; }
        .widget-preview-hero h1 { color: white; }
        .widget-preview-hero img, .widget-preview-gallery img { width: 100%; border-radius: 8px; object-fit: cover; }
        .widget-preview-hero img { max-height: 260px; }
        .widget-preview-hero span, .widget-preview-cta span { display: inline-block; background: #2563eb; color: white; padding: .65rem 1rem; border-radius: 6px; font-weight: 700; }
        .widget-preview-cta, .widget-preview-card { background: white; border: 1px solid #dbe3ef; border-radius: 8px; padding: 1.25rem; }
        .widget-preview-grid > div, .widget-preview-gallery > div, .widget-preview-metrics { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: .75rem; }
        .widget-preview-grid article, .widget-preview-metrics article { background: white; border: 1px solid #dbe3ef; border-radius: 8px; padding: 1rem; }
        .widget-preview-metrics strong { display: block; color: #1d4ed8; font-size: 1.75rem; }
        .widget-preview-faq details { background: white; border: 1px solid #dbe3ef; border-radius: 8px; padding: .75rem; margin-bottom: .5rem; }
        @media (max-width: 900px) { .layout { grid-template-columns: 1fr; } .widget-preview-hero { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <main class="layout">
        <section class="card">
            <h1>Editar widget</h1>
            <p class="schema-note">Edita los campos estandar. El HTML de previsualizacion se genera al guardar desde estos datos.</p>
            <form id="widgetForm">
                <input type="hidden" name="id" value="<?php echo (int)$widget['id']; ?>">
                <label>Nombre del widget
                    <input type="text" name="nombre" value="<?php echo htmlspecialchars($widget['nombre'], ENT_QUOTES, 'UTF-8'); ?>" required>
                </label>
                <label>Tipo de widget
                    <select name="tipo" id="tipo">
                        <?php foreach (['hero' => 'Hero', 'cta' => 'CTA', 'features' => 'Ventajas', 'testimonial' => 'Testimonio', 'metrics' => 'Metricas', 'gallery' => 'Galeria', 'faq' => 'FAQ', 'text' => 'Texto'] as $value => $label): ?>
                            <option value="<?php echo $value; ?>" <?php echo $widget['tipo'] === $value ? 'selected' : ''; ?>><?php echo $label; ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <div id="schemaFields"></div>
                <label>JSON generado
                    <textarea name="config" id="config" rows="8" readonly></textarea>
                </label>
                <input type="hidden" name="preview_html" value="">
                <div class="actions">
                    <button type="submit" class="btn btn-primary">Actualizar widget</button>
                    <a href="list.php" class="btn btn-secondary">Cancelar</a>
                    <a href="../../docs/WIDGET_SCHEMA.md" class="btn btn-secondary">Ver estandar</a>
                </div>
            </form>
        </section>
        <aside class="card">
            <h2>Previsualizacion</h2>
            <div class="preview-frame" id="widgetPreview"></div>
        </aside>
    </main>
    <script>
        window.WEBNOVA_WIDGET = <?php echo json_encode($widget, JSON_UNESCAPED_UNICODE); ?>;
    </script>
    <script src="../../assets/js/widget-admin.js"></script>
</body>
</html>

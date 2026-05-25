<?php
require_once '../../middleware/auth.php';
require_once '../../config/db.php';

if (!esEditor()) {
    header('Location: ../dashboard.php?error=permiso');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo widget - WebNova Manager</title>
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
            <h1>Nuevo widget</h1>
            <p class="schema-note">El HTML se genera automaticamente desde el tipo de widget y sus claves estandar. El JSON avanzado queda visible para aprender y revisar lo que se guarda.</p>
            <form id="widgetForm">
                <label>Nombre del widget
                    <input type="text" name="nombre" placeholder="Ej: Hero principal" required>
                </label>
                <label>Tipo de widget
                    <select name="tipo" id="tipo">
                        <option value="hero">Hero</option>
                        <option value="cta">CTA</option>
                        <option value="features">Ventajas</option>
                        <option value="testimonial">Testimonio</option>
                        <option value="metrics">Metricas</option>
                        <option value="gallery">Galeria</option>
                        <option value="faq">FAQ</option>
                        <option value="text">Texto</option>
                    </select>
                </label>
                <div id="schemaFields"></div>
                <label>JSON generado
                    <textarea name="config" id="config" rows="8" readonly></textarea>
                </label>
                <input type="hidden" name="preview_html" value="">
                <div class="actions">
                    <button type="submit" class="btn btn-primary">Guardar widget</button>
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
        window.WEBNOVA_WIDGET = null;
    </script>
    <script src="../../assets/js/widget-admin.js"></script>
</body>
</html>

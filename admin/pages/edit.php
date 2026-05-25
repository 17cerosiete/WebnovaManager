<?php
require_once '../../middleware/auth.php';
require_once '../../config/db.php';

if (!esEditor()) {
    header('Location: ../dashboard.php?error=permiso');
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $conn->prepare('SELECT * FROM paginas WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$page = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$page) {
    header('Location: list.php?error=notfound');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar pagina - WebNova Manager</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <style>
        body { background: #eef2f7; }
        .builder-shell { min-height: 100vh; display: grid; grid-template-columns: 260px 1fr 320px; }
        .panel { background: white; border-right: 1px solid #dbe3ef; padding: 1rem; overflow-y: auto; }
        .panel-right { border-right: 0; border-left: 1px solid #dbe3ef; }
        .workspace { display: flex; flex-direction: column; min-width: 0; }
        .topbar { height: 64px; display: flex; justify-content: space-between; align-items: center; gap: 1rem; background: white; border-bottom: 1px solid #dbe3ef; padding: 0 1.25rem; }
        .topbar input[type="text"] { width: min(520px, 100%); font-size: 1.1rem; font-weight: 700; }
        .canvas-wrap { padding: 2rem; overflow: auto; }
        .canvas { max-width: 920px; min-height: 70vh; margin: 0 auto; background: white; border: 1px solid #dbe3ef; border-radius: 8px; padding: 2rem; box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08); }
        .tool-item { width: 100%; border: 1px solid #dbe3ef; background: #f8fafc; border-radius: 8px; padding: .75rem; margin-bottom: .5rem; cursor: pointer; text-align: left; font-weight: 600; }
        .tool-item small { display: block; color: #64748b; font-weight: 500; margin-top: .2rem; }
        .canvas-block { position: relative; border: 1px solid transparent; border-radius: 8px; padding: 1rem; margin-bottom: 1rem; }
        .canvas-block:hover, .canvas-block.is-active { border-color: #2563eb; background: #f8fbff; }
        .block-tools { position: absolute; right: .5rem; top: .5rem; display: flex; gap: .25rem; }
        .block-tools button { width: 28px; height: 28px; border: 1px solid #cbd5e1; background: white; border-radius: 6px; cursor: pointer; }
        .canvas-block img { max-width: 100%; border-radius: 8px; }
        .preview-hero { display: grid; grid-template-columns: minmax(0, 1fr) minmax(220px, .8fr); gap: 1rem; align-items: center; background: #0f172a; color: white; padding: 3rem 2rem; border-radius: 8px; }
        .preview-hero h1 { color: white; }
        .preview-hero img, .canvas-block img { width: 100%; height: auto; max-height: 420px; object-fit: contain; border-radius: 8px; }
        .preview-hero span, .preview-cta span { display: inline-block; background: #2563eb; color: white; padding: .6rem 1rem; border-radius: 6px; }
        .preview-cta { display: flex; justify-content: space-between; align-items: center; gap: 1rem; background: #ecfeff; padding: 1.25rem; border-radius: 8px; }
        .preview-faq details { border: 1px solid #dbe3ef; padding: .75rem; border-radius: 6px; margin-bottom: .5rem; }
        .preview-grid > div, .preview-gallery > div, .preview-metrics { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: .75rem; }
        .preview-grid article, .preview-card, .preview-metrics article { border: 1px solid #dbe3ef; border-radius: 8px; padding: 1rem; background: #f8fafc; }
        .preview-gallery img { width: 100%; aspect-ratio: 4 / 3; object-fit: cover; border-radius: 8px; }
        .muted, .empty { color: #64748b; }
        #blockEditor label { display: block; margin-bottom: 1rem; }
        #blockEditor input, #blockEditor textarea { width: 100%; margin-top: .35rem; }
        @media (max-width: 980px) { .builder-shell { grid-template-columns: 1fr; } .panel, .panel-right { border: 0; border-bottom: 1px solid #dbe3ef; } }
    </style>
</head>
<body>
    <div class="builder-shell">
        <aside class="panel">
            <h2>Bloques</h2>
            <button type="button" class="tool-item" data-add="text">Texto <small>Parrafo editable</small></button>
            <button type="button" class="tool-item" data-add="image">Imagen <small>URL y texto alternativo</small></button>
            <button type="button" class="tool-item" data-add="container">Seccion <small>Titulo + contenido</small></button>
            <h2 class="mt-lg">Widgets</h2>
            <div id="widgetList"><p class="muted">Cargando widgets...</p></div>
        </aside>

        <main class="workspace">
            <div class="topbar">
                <input type="text" id="pageTitle" value="<?php echo htmlspecialchars($page['titulo']); ?>" aria-label="Titulo de pagina">
                <label style="display:flex;align-items:center;gap:.5rem;margin:0;"><input type="checkbox" id="pagePublished" <?php echo $page['publicada'] ? 'checked' : ''; ?>> Publicada</label>
                <div style="display:flex;gap:.5rem;">
                    <a class="btn btn-secondary" href="list.php">Volver</a>
                    <a class="btn btn-secondary" href="view.php?id=<?php echo (int)$page['id']; ?>">Ver guardada</a>
                    <button type="button" class="btn btn-secondary" id="previewPage">Vista previa</button>
                    <button type="button" class="btn btn-primary" id="savePage">Guardar</button>
                </div>
            </div>
            <div class="canvas-wrap">
                <section class="canvas" id="canvas"></section>
            </div>
        </main>

        <aside class="panel panel-right">
            <h2>Editor</h2>
            <div id="blockEditor"></div>
        </aside>
    </div>

    <script>
        window.WEBNOVA_INITIAL_PAGE = <?php echo json_encode($page, JSON_UNESCAPED_UNICODE); ?>;
    </script>
    <script src="../../assets/js/page-builder-admin.js"></script>
</body>
</html>

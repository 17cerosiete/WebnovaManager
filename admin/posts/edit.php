<?php
/**
 * admin/posts/edit.php
 *
 * Editar artículo existente.
 */

require_once '../../middleware/auth.php';
require_once '../../config/db.php';

if (!esEditor()) {
    header('Location: ../dashboard.php?error=permiso');
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    header('Location: list.php?error=id');
    exit();
}

$stmt = $conn->prepare("SELECT * FROM articulos WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();
$stmt->close();

if (!$post) {
    header('Location: list.php?error=no_encontrado');
    exit();
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Artículo - WebNova Manager</title>
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
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

        .container {
            max-width: 960px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .breadcrumb {
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
            color: #6b7280;
        }

        .breadcrumb a {
            color: #2563eb;
            text-decoration: none;
        }

        .form-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            padding: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #111827;
        }

        input[type="text"], input[type="email"], select {
            width: 100%;
            padding: 0.85rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: 0.75rem;
            font-size: 1rem;
        }

        .editor-container {
            border: 1px solid #d1d5db;
            border-radius: 0.75rem;
            overflow: hidden;
        }

        .ql-toolbar.ql-snow {
            background: #f9fafb;
            border-bottom: 1px solid #d1d5db;
        }

        .ql-container.ql-snow {
            min-height: 320px;
            border: none;
        }

        .builder-panel {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .builder-panel span {
            font-weight: 600;
            color: #111827;
        }

        .builder-panel button {
            background: #eef2ff;
            color: #4338ca;
            border: none;
            padding: 0.65rem 0.95rem;
            border-radius: 0.75rem;
            cursor: pointer;
            transition: all 200ms;
        }

        .builder-panel button:hover {
            background: #e0e7ff;
        }

        .form-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn {
            padding: 0.85rem 1.4rem;
            border-radius: 0.75rem;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 200ms;
        }

        .btn-primary {
            background: #2563eb;
            color: white;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }

        .btn-secondary {
            background: #e5e7eb;
            color: #1f2937;
        }

        .btn-secondary:hover {
            background: #d1d5db;
        }

        .status-toggle {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .status-toggle input {
            width: auto;
        }

        .alert {
            padding: 1rem 1.25rem;
            border-radius: 0.75rem;
            margin-bottom: 1rem;
            display: none;
        }

        .alert.success {
            background: #d1fae5;
            color: #065f46;
        }

        .alert.error {
            background: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>
<body>
<nav class="navbar">
    <h1>✏️ Editar Artículo</h1>
    <a href="list.php" class="btn btn-secondary">Volver a artículos</a>
</nav>

<div class="container">
    <div class="breadcrumb">
        <a href="../dashboard.php">Dashboard</a> / <a href="list.php">Artículos</a> / Editar
    </div>

    <div class="form-card">
        <div class="alert" id="alertBox"></div>

        <div class="form-group">
            <label for="titulo">Título del artículo</label>
            <input type="text" id="titulo" value="<?php echo htmlspecialchars($post['titulo']); ?>" />
        </div>

        <div class="form-group">
            <label for="slug">Slug</label>
            <input type="text" id="slug" value="<?php echo htmlspecialchars($post['slug']); ?>" />
        </div>

        <div class="seo-section">
            <h3>🔍 Optimización SEO</h3>
            <div class="form-group">
                <label for="meta_description">Meta Descripción</label>
                <textarea id="meta_description" name="meta_description" rows="2" maxlength="160" placeholder="Descripción para motores de búsqueda"><?php echo htmlspecialchars($post['meta_description'] ?? ''); ?></textarea>
                <small style="color: #6b7280; margin-top: 0.25rem; display: block;">
                    <span id="meta-count">0</span>/160 caracteres
                </small>
            </div>
            <div class="form-group">
                <label for="keywords">Palabras clave</label>
                <input type="text" id="keywords" name="keywords" value="<?php echo htmlspecialchars($post['keywords'] ?? ''); ?>">
            </div>
        </div>

        <div class="form-group">
            <div class="builder-panel">
                <span>Insertar módulo:</span>
                <button type="button" onclick="insertModule('hero')">Hero</button>
                <button type="button" onclick="insertModule('feature')">Características</button>
                <button type="button" onclick="insertModule('testimonial')">Testimonial</button>
                <button type="button" onclick="insertModule('cta')">Llamada a la acción</button>
            </div>
            <div class="editor-container" id="editor"></div>
        </div>

        <div class="form-group status-toggle">
            <input type="checkbox" id="publicado" <?php echo $post['publicado'] ? 'checked' : ''; ?> />
            <label for="publicado">Publicar artículo</label>
        </div>

        <div class="form-actions">
            <button type="button" class="btn btn-secondary" onclick="window.location.href='list.php'">Cancelar</button>
            <button type="button" class="btn btn-primary" onclick="submitPost()">Actualizar artículo</button>
        </div>
    </div>
</div>

<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script>
    const editor = new Quill('#editor', {
        theme: 'snow'
    });
    editor.clipboard.dangerouslyPasteHTML(0, <?php echo json_encode($post['contenido'] ?? ''); ?>);

    function insertModule(type) {
        let html = '';
        switch (type) {
            case 'hero':
                html = `
                    <section style="padding:24px;background:#2563eb;color:white;border-radius:16px;margin-bottom:18px;">
                        <h2 style="margin:0 0 12px;font-size:28px;">Encabezado impactante</h2>
                        <p style="margin:0;font-size:16px;">Introduce tu artículo con un mensaje claro y atractivo.</p>
                    </section>
                `;
                break;
            case 'feature':
                html = `
                    <section style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;margin-bottom:18px;">
                        <article style="background:#ffffff;padding:18px;border-radius:16px;box-shadow:0 8px 24px rgba(15,23,42,0.06);">
                            <h3>Funcionalidad</h3>
                            <p>Explica los beneficios de tu producto o servicio.</p>
                        </article>
                        <article style="background:#ffffff;padding:18px;border-radius:16px;box-shadow:0 8px 24px rgba(15,23,42,0.06);">
                            <h3>Resultados</h3>
                            <p>Muestra cómo se transforma la experiencia del usuario.</p>
                        </article>
                    </section>
                `;
                break;
            case 'testimonial':
                html = `
                    <section style="background:#f8fafc;padding:20px;border-radius:16px;margin-bottom:18px;">
                        <p style="font-style:italic;">"Esta solución mejoró el sitio de nuestros clientes en pocas horas."</p>
                        <strong>— Cliente satisfecho</strong>
                    </section>
                `;
                break;
            case 'cta':
                html = `
                    <section style="background:#eff6ff;padding:20px;border-radius:16px;margin-bottom:18px;text-align:center;">
                        <h3 style="margin-bottom:8px;">¿Listo para avanzar?</h3>
                        <p style="margin-bottom:16px;">Agrega un llamado a la acción poderoso para convertir visitas en clientes.</p>
                        <button style="background:#2563eb;color:white;border:none;padding:12px 24px;border-radius:999px;cursor:pointer;">Contáctanos</button>
                    </section>
                `;
                break;
        }
        const range = editor.getSelection(true);
        editor.clipboard.dangerouslyPasteHTML(range.index, html);
        editor.setSelection(range.index + 1);
    }

    function showAlert(message, type = 'success') {
        const alertBox = document.getElementById('alertBox');
        alertBox.textContent = message;
        alertBox.className = `alert ${type}`;
        alertBox.style.display = 'block';
    }

    document.getElementById('meta_description').addEventListener('input', function() {
        const count = this.value.length;
        document.getElementById('meta-count').textContent = count;
    });
    document.getElementById('meta_description').dispatchEvent(new Event('input'));

    function submitPost() {
        const titulo = document.getElementById('titulo').value.trim();
        const slug = document.getElementById('slug').value.trim();
        const meta_description = document.getElementById('meta_description').value.trim();
        const keywords = document.getElementById('keywords').value.trim();
        const publicado = document.getElementById('publicado').checked;
        const contenido = editor.root.innerHTML;

        if (!titulo || !slug) {
            showAlert('Título y slug son obligatorios.', 'error');
            return;
        }

        const formData = new FormData();
        formData.append('action', 'update');
        formData.append('id', <?php echo $id; ?>);
        formData.append('titulo', titulo);
        formData.append('slug', slug);
        formData.append('contenido', contenido);
        formData.append('meta_description', meta_description);
        formData.append('keywords', keywords);
        if (publicado) {
            formData.append('publicado', '1');
        }

        fetch('../../api/posts.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Artículo actualizado con éxito. Redirigiendo...', 'success');
                setTimeout(() => {
                    window.location.href = 'list.php';
                }, 1200);
            } else {
                showAlert(data.error || 'Error al actualizar el artículo.', 'error');
            }
        })
        .catch(() => showAlert('No se pudo conectar con el servidor.', 'error'));
    }
</script>
</body>
</html>

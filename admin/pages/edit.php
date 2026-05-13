<?php
/**
 * admin/pages/edit.php
 *
 * Editar una página existente con el mismo constructor de página.
 */

require_once '../../middleware/auth.php';
require_once '../../config/db.php';

if (!esEditor()) {
    header('Location: ../dashboard.php?error=permiso');
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    header('Location: list.php?error=missing_id');
    exit();
}

$stmt = $conn->prepare("SELECT p.id, p.titulo, p.slug, p.contenido, p.meta_description, p.keywords, p.publicada, u.nombre as autor FROM paginas p LEFT JOIN usuarios u ON p.autor_id = u.id WHERE p.id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    header('Location: list.php?error=not_found');
    exit();
}
$page = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Página - WebNova Manager</title>
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background: #f3f4f6; color: #1f2937; }
        .navbar { background: white; border-bottom: 1px solid #e5e7eb; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .navbar h1 { color: #2563eb; font-size: 1.5rem; }
        .container { max-width: 900px; margin: 2rem auto; padding: 0 1rem; }
        .breadcrumb { margin-bottom: 2rem; font-size: 0.9rem; color: #6b7280; }
        .breadcrumb a { color: #2563eb; text-decoration: none; }
        .form-card { background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 2rem; margin-bottom: 2rem; }
        .form-group { margin-bottom: 1.5rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: 600; color: #1f2937; }
        input[type="text"], textarea, select { width: 100%; padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 1rem; font-family: inherit; transition: all 250ms; }
        input[type="text"]:focus, textarea:focus, select:focus { outline: none; border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); }
        .form-row { display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; }
        .editor-container { background: white; border-radius: 0.5rem; border: 1px solid #d1d5db; overflow: hidden; }
        .ql-toolbar.ql-snow { border-bottom: 1px solid #d1d5db; background: #f9fafb; }
        .ql-container.ql-snow { border: none; min-height: 300px; }
        .seo-section { background: #f0f9ff; padding: 1.5rem; border-radius: 0.5rem; border-left: 4px solid #2563eb; }
        .seo-section h3 { color: #2563eb; margin-bottom: 1rem; }
        .char-count { font-size: 0.85rem; color: #6b7280; margin-top: 0.25rem; }
        .char-count.warning { color: #f59e0b; }
        .char-count.error { color: #ef4444; }
        .form-actions { display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #e5e7eb; }
        .btn { padding: 0.75rem 1.5rem; border: none; border-radius: 0.5rem; font-weight: 600; cursor: pointer; transition: all 250ms; text-decoration: none; display: inline-block; }
        .btn-primary { background: #2563eb; color: white; }
        .btn-primary:hover { background: #1d4ed8; }
        .btn-secondary { background: #e5e7eb; color: #1f2937; }
        .btn-secondary:hover { background: #d1d5db; }
        .builder-panel { display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: center; margin-bottom: 1rem; }
        .builder-panel span { font-weight: 600; color: #1f2937; }
        .builder-panel .btn-secondary { font-size: 0.9rem; padding: 0.5rem 0.9rem; }
        .module-hero { background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%); color: white; padding: 2rem; border-radius: 1rem; }
        .module-hero h1, .module-hero p { margin: 0; }
        .module-feature-list { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; background: #f8fafc; padding: 1rem; border-radius: 1rem; }
        .module-feature-item { padding: 1rem; background: white; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06); }
        .module-cta { background: #eff6ff; padding: 1.5rem; border-left: 4px solid #2563eb; border-radius: 0.75rem; }
        .module-testimonial { background: #f8fafc; padding: 1.5rem; border-radius: 0.75rem; border: 1px solid #e2e8f0; }
        .module-image-text { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; align-items: center; background: #ffffff; padding: 1rem; border-radius: 1rem; }
        .module-image-text img { width: 100%; border-radius: 1rem; }
        @media (max-width: 768px) { .form-row { grid-template-columns: 1fr; } .module-image-text { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

<nav class="navbar">
    <h1>📊 WebNova Manager</h1>
    <a href="../../auth/logout.php" style="color: #ef4444; text-decoration: none; font-weight: 600;">Salir</a>
</nav>

<div class="container">
    <div class="breadcrumb">
        <a href="../dashboard.php">Dashboard</a> / <a href="list.php">Páginas</a> / Editar página
    </div>

    <div class="form-card">
        <h2 style="margin-bottom: 1.5rem; color: #1f2937;">✏️ Editar Página</h2>

        <form id="pageForm" method="POST" action="../../api/pages.php?action=update">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($page['id']); ?>">

            <div class="form-row">
                <div class="form-group">
                    <label for="titulo">Título *</label>
                    <input type="text" id="titulo" name="titulo" required placeholder="Ej: Quiénes somos" value="<?php echo htmlspecialchars($page['titulo']); ?>">
                </div>
                <div class="form-group">
                    <label for="slug">URL Amigable (slug) *</label>
                    <input type="text" id="slug" name="slug" required placeholder="ej-titulo" value="<?php echo htmlspecialchars($page['slug']); ?>">
                    <small style="color: #6b7280; margin-top: 0.25rem; display: block;">
                        URL: /pagina/<span id="slug-preview"><?php echo htmlspecialchars($page['slug']); ?></span>
                    </small>
                </div>
            </div>

            <div class="form-group">
                <label for="editor">Contenido *</label>
                <div class="builder-panel">
                    <span>Insertar módulo:</span>
                    <button type="button" class="btn btn-secondary" onclick="insertModule('hero')">Hero</button>
                    <button type="button" class="btn btn-secondary" onclick="insertModule('feature')">Características</button>
                    <button type="button" class="btn btn-secondary" onclick="insertModule('imageText')">Imagen + Texto</button>
                    <button type="button" class="btn btn-secondary" onclick="insertModule('testimonial')">Testimonial</button>
                    <button type="button" class="btn btn-secondary" onclick="insertModule('cta')">Llamada a la acción</button>
                </div>
                <div class="editor-container">
                    <div id="editor"></div>
                </div>
                <small style="color: #6b7280; margin-top: 0.5rem; display: block;">
                    Editor WYSIWYG con bloques modulares para páginas web.
                </small>
            </div>

            <div class="seo-section" style="margin-top: 2rem; margin-bottom: 2rem;">
                <h3>🔍 Optimización SEO</h3>
                <div class="form-group">
                    <label for="meta_description">Meta Descripción (160 caracteres máx)</label>
                    <textarea id="meta_description" name="meta_description" rows="2" maxlength="160" placeholder="Descripción breve para motores de búsqueda"><?php echo htmlspecialchars($page['meta_description'] ?? ''); ?></textarea>
                    <small style="color: #6b7280; margin-top: 0.25rem; display: block;">
                        <span id="meta-count">0</span>/160 caracteres
                    </small>
                </div>
                <div class="form-group">
                    <label for="keywords">Palabras clave (separadas por comas)</label>
                    <input type="text" id="keywords" name="keywords" placeholder="palabra1, palabra2, palabra3" value="<?php echo htmlspecialchars($page['keywords'] ?? ''); ?>">
                    <small style="color: #6b7280; margin-top: 0.25rem; display: block;">
                        Mejora la indexación en buscadores
                    </small>
                </div>
            </div>

            <div class="form-group" style="margin-top: 2rem;">
                <label style="margin-bottom: 1rem;">Opciones de publicación</label>
                <div class="checkbox-group">
                    <input type="checkbox" id="publicada" name="publicada" value="1" <?php echo $page['publicada'] ? 'checked' : ''; ?> />
                    <label for="publicada"><strong>Publicar ahora</strong> (visible en sitio público)</label>
                </div>
            </div>

            <div class="form-actions">
                <a href="list.php" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar cambios</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
    var quill = new Quill('#editor', {
        theme: 'snow',
        placeholder: 'Edita tu contenido aquí...',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                ['blockquote', 'code-block'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'align': [] }],
                ['link', 'image', 'video'],
                ['clean']
            ]
        }
    });

    quill.root.innerHTML = <?php echo json_encode($page['contenido']); ?>;

    document.getElementById('titulo').addEventListener('input', function() {
        let slug = this.value
            .toLowerCase()
            .trim()
            .replace(/[^ -\s-]/g, '')
            .replace(/[\s_-]+/g, '-')
            .replace(/^-+|-+$/g, '');
        document.getElementById('slug').value = slug;
        document.getElementById('slug-preview').textContent = slug || '<?php echo htmlspecialchars($page['slug']); ?>';
    });

    document.getElementById('slug').addEventListener('input', function() {
        document.getElementById('slug-preview').textContent = this.value || '<?php echo htmlspecialchars($page['slug']); ?>';
    });

    function insertModule(type) {
        const cursorIndex = quill.getSelection(true)?.index || quill.getLength();
        const modules = {
            hero: `
                <section class="module-hero">
                    <h1>Diseña una página web profesional</h1>
                    <p>Convierte tu contenido en secciones claras, modernas y con impacto visual.</p>
                </section>
            `,
            feature: `
                <section class="module-feature-list">
                    <div class="module-feature-item"><strong>Velocidad</strong><p>Diseño optimizado para carga rápida.</p></div>
                    <div class="module-feature-item"><strong>Responsive</strong><p>Se adapta a cualquier dispositivo.</p></div>
                    <div class="module-feature-item"><strong>Fácil edición</strong><p>Gestiona tu contenido sin necesidad de código.</p></div>
                </section>
            `,
            imageText: `
                <section class="module-image-text">
                    <div>
                        <h2>Imagen + Texto</h2>
                        <p>Presenta tu servicio con imagen descriptiva y texto claro.</p>
                    </div>
                    <div><img src="https://via.placeholder.com/600x400" alt="Ejemplo de página"></div>
                </section>
            `,
            testimonial: `
                <section class="module-testimonial">
                    <blockquote>"Gracias a esta plataforma, mi web se ve profesional y se actualiza en segundos."</blockquote>
                    <p><strong>Cliente satisfecho</strong> - Empresa X</p>
                </section>
            `,
            cta: `
                <section class="module-cta">
                    <h2>¿Listo para lanzar tu página?</h2>
                    <p>Añade una llamada a la acción clara para convertir visitantes en clientes.</p>
                </section>
            `
        };
        quill.clipboard.dangerouslyPasteHTML(cursorIndex, modules[type] || '');
        quill.setSelection(cursorIndex + 1);
    }

    document.getElementById('meta_description').addEventListener('input', function() {
        const count = this.value.length;
        document.getElementById('meta-count').textContent = count;
        document.getElementById('meta-count').style.color = count > 150 ? '#ef4444' : count > 120 ? '#f59e0b' : '#6b7280';
    });
    document.getElementById('meta_description').dispatchEvent(new Event('input'));

    // Submit del formulario
    document.getElementById('pageForm').addEventListener('submit', function(e) {
        e.preventDefault();

        // Obtener contenido del editor
        const contenido = quill.root.innerHTML;

        // Validaciones
        const titulo = document.getElementById('titulo').value.trim();
        const slug = document.getElementById('slug').value.trim();

        if (!titulo || !slug) {
            alert('❌ Título y slug son requeridos');
            return;
        }

        if (contenido.trim().length < 10) {
            alert('❌ El contenido debe tener al menos 10 caracteres');
            return;
        }

        // Crear FormData
        const formData = new FormData();
        formData.append('id', document.querySelector('input[name="id"]').value);
        formData.append('titulo', titulo);
        formData.append('slug', slug);
        formData.append('contenido', contenido);
        formData.append('meta_description', document.getElementById('meta_description').value);
        formData.append('keywords', document.getElementById('keywords').value);
        formData.append('publicada', document.getElementById('publicada').checked ? 1 : 0);

        // POST a API
        fetch('../../api/pages.php?action=update', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✅ Página actualizada correctamente');
                window.location.href = 'list.php';
            } else {
                alert('❌ Error: ' + (data.error || 'Error desconocido'));
                console.error('Respuesta API:', data);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('❌ Error de conexión');
        });
    });
</script>
</body>
</html>

<?php
/**
 * admin/pages/create.php
 *
 * Crear nueva página con editor WYSIWYG
 * Usa Quill.js para edición de contenido
 */

require_once '../../middleware/auth.php';
require_once '../../config/db.php';

if (!esEditor()) {
    header('Location: ../dashboard.php?error=permiso');
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Página - WebNova Manager</title>

    <!-- Quill Editor CSS -->
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
            max-width: 900px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .breadcrumb {
            margin-bottom: 2rem;
            font-size: 0.9rem;
            color: #6b7280;
        }

        .breadcrumb a {
            color: #2563eb;
            text-decoration: none;
        }

        .form-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #1f2937;
        }

        input[type="text"],
        input[type="email"],
        textarea,
        select {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 1rem;
            font-family: inherit;
            transition: all 250ms;
        }

        input[type="text"]:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
        }

        .editor-container {
            background: white;
            border-radius: 0.5rem;
            border: 1px solid #d1d5db;
            overflow: hidden;
        }

        .ql-toolbar.ql-snow {
            border-bottom: 1px solid #d1d5db;
            background: #f9fafb;
        }

        .ql-container.ql-snow {
            border: none;
            min-height: 300px;
        }

        .seo-section {
            background: #f0f9ff;
            padding: 1.5rem;
            border-radius: 0.5rem;
            border-left: 4px solid #2563eb;
        }

        .seo-section h3 {
            color: #2563eb;
            margin-bottom: 1rem;
        }

        .char-count {
            font-size: 0.85rem;
            color: #6b7280;
            margin-top: 0.25rem;
        }

        .char-count.warning {
            color: #f59e0b;
        }

        .char-count.error {
            color: #ef4444;
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #e5e7eb;
        }

        .builder-panel {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            align-items: center;
            margin-bottom: 1rem;
        }

        .builder-panel span {
            font-weight: 600;
            color: #1f2937;
        }

        .builder-panel .btn-secondary {
            font-size: 0.9rem;
            padding: 0.5rem 0.9rem;
        }

        .module-hero {
            background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
            color: white;
            padding: 2rem;
            border-radius: 1rem;
        }

        .module-hero h1,
        .module-hero p {
            margin: 0;
        }

        .module-feature-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
            background: #f8fafc;
            padding: 1rem;
            border-radius: 1rem;
        }

        .module-feature-item {
            padding: 1rem;
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
        }

        .module-cta {
            background: #eff6ff;
            padding: 1.5rem;
            border-left: 4px solid #2563eb;
            border-radius: 0.75rem;
        }

        .module-testimonial {
            background: #f8fafc;
            padding: 1.5rem;
            border-radius: 0.75rem;
            border: 1px solid #e2e8f0;
        }

        .module-image-text {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            align-items: center;
            background: #ffffff;
            padding: 1rem;
            border-radius: 1rem;
        }

        .module-image-text img {
            width: 100%;
            border-radius: 1rem;
        }

        @media (max-width: 768px) {
            .module-image-text {
                grid-template-columns: 1fr;
            }
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 250ms;
            text-decoration: none;
            display: inline-block;
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

        .checkbox-group {
            display: flex;
            gap: 1rem;
            align-items: center;
            margin-top: 0.75rem;
        }

        .checkbox-group input[type="checkbox"] {
            width: auto;
            cursor: pointer;
        }

        .checkbox-group label {
            margin-bottom: 0;
            font-weight: normal;
            cursor: pointer;
        }

        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border-left: 4px solid #10b981;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
    <h1>📊 WebNova Manager</h1>
    <a href="../../auth/logout.php" style="color: #ef4444; text-decoration: none; font-weight: 600;">Salir</a>
</nav>

<!-- CONTENIDO -->
<div class="container">

    <!-- BREADCRUMB -->
    <div class="breadcrumb">
        <a href="../dashboard.php">Dashboard</a> /
        <a href="list.php">Páginas</a> /
        Nueva página
    </div>

    <!-- FORMULARIO -->
    <div class="form-card">
        <h2 style="margin-bottom: 1.5rem; color: #1f2937;">📄 Crear Nueva Página</h2>

        <form id="pageForm" method="POST" action="../../api/pages.php?action=create">

            <!-- TÍTULO Y SLUG -->
            <div class="form-row">
                <div class="form-group">
                    <label for="titulo">Título *</label>
                    <input type="text" id="titulo" name="titulo" required placeholder="Ej: Quiénes somos">
                </div>
                <div class="form-group">
                    <label for="slug">URL Amigable (slug) *</label>
                    <input type="text" id="slug" name="slug" required placeholder="ej-titulo">
                    <small style="color: #6b7280; margin-top: 0.25rem; display: block;">
                        URL: /pagina/<span id="slug-preview">ej-titulo</span>
                    </small>
                </div>
            </div>

            <!-- CONTENIDO PRINCIPAL -->
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
                    Editor WYSIWYG con soporte para texto, imágenes, listas, códigos y bloques modulares.
                </small>
            </div>

            <!-- SEO -->
            <div class="seo-section">
                <h3>🔍 Optimización SEO</h3>

                <div class="form-group">
                    <label for="meta_description">Meta Description</label>
                    <textarea id="meta_description" name="meta_description" rows="2" maxlength="160" placeholder="Descripción que aparecerá en buscadores..."></textarea>
                    <div class="char-count" id="charCount">0/160 caracteres</div>
                </div>

                <div class="form-group">
                    <label for="keywords">Palabras clave (separadas por comas)</label>
                    <input type="text" id="keywords" name="keywords" placeholder="palabra1, palabra2, palabra3">
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" id="permitir_indexacion" name="permitir_indexacion" value="1" checked>
                    <label for="permitir_indexacion">Permitir indexación en buscadores (SEO)</label>
                </div>
            </div>

            <!-- OPCIONES DE PUBLICACIÓN -->
            <div class="form-group" style="margin-top: 2rem;">
                <label style="margin-bottom: 1rem;">Opciones de publicación</label>
                <div class="checkbox-group">
                    <input type="checkbox" id="publicada" name="publicada" value="1">
                    <label for="publicada"><strong>Publicar ahora</strong> (visible en sitio público)</label>
                </div>
                <div class="checkbox-group" style="margin-top: 0.75rem;">
                    <input type="checkbox" id="comentarios_habilitados" name="comentarios_habilitados" value="1" checked>
                    <label for="comentarios_habilitados">Permitir comentarios</label>
                </div>
            </div>

            <!-- BOTONES DE ACCIÓN -->
            <div class="form-actions">
                <a href="list.php" class="btn btn-secondary">Cancelar</a>
                <button type="submit" name="action" value="draft" class="btn btn-secondary">💾 Guardar como borrador</button>
                <button type="submit" name="action" value="publish" class="btn btn-primary">📤 Publicar ahora</button>
            </div>

        </form>
    </div>

</div>

<!-- Quill Editor JS -->
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

<script>
    // Inicializar editor Quill
    var quill = new Quill('#editor', {
        theme: 'snow',
        placeholder: 'Escribe tu contenido aquí...',
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

    // Slug automático desde título
    document.getElementById('titulo').addEventListener('input', function() {
        let slug = this.value
            .toLowerCase()
            .trim()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_-]+/g, '-')
            .replace(/^-+|-+$/g, '');

        document.getElementById('slug').value = slug;
        document.getElementById('slug-preview').textContent = slug || 'url-amigable';
    });

    // Contar caracteres meta description
    document.getElementById('meta_description').addEventListener('input', function() {
        const count = this.value.length;
        const counter = document.getElementById('charCount');
        counter.textContent = count + '/160 caracteres';

        if (count > 160) {
            counter.classList.add('error');
        } else if (count > 140) {
            counter.classList.add('warning');
            counter.classList.remove('error');
        } else {
            counter.classList.remove('warning', 'error');
        }
    });

    // Insertar módulos predefinidos en el editor
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
                        <p>Este bloque es perfecto para presentar servicios o casos de éxito con una imagen descriptiva.</p>
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
                    <p>Agrega un botón de llamada a la acción y convierte visitantes en clientes.</p>
                </section>
            `
        };

        const html = modules[type] || '';
        quill.clipboard.dangerouslyPasteHTML(cursorIndex, html);
        quill.setSelection(cursorIndex + 1);
    }

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
        formData.append('action', 'create');
        formData.append('titulo', titulo);
        formData.append('slug', slug);
        formData.append('contenido', contenido);
        formData.append('meta_description', document.getElementById('meta_description').value);
        formData.append('keywords', document.getElementById('keywords').value);
        formData.append('publicada', document.getElementById('publicada').checked ? 1 : 0);
        formData.append('comentarios_habilitados', document.getElementById('comentarios_habilitados').checked ? 1 : 0);
        formData.append('permitir_indexacion', document.getElementById('permitir_indexacion').checked ? 1 : 0);

        // POST a API
        fetch('../../api/pages.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✅ Página creada correctamente');
                window.location.href = 'list.php';
            } else {
                alert('❌ Error: ' + (data.error || 'Error desconocido'));
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

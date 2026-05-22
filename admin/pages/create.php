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
    <title>Constructor de Páginas - WebNova Manager</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <style>
        body { font-family: 'Segoe UI', Roboto, sans-serif; background: #f3f4f6; margin: 0; display: flex; height: 100vh; overflow: hidden; }

        /* SIDEBAR IZQUIERDA: Herramientas */
        .sidebar { width: 280px; background: white; border-right: 1px solid #e5e7eb; display: flex; flex-direction: column; padding: 1rem; z-index: 10; }

        /* SIDEBAR DERECHA: Editor de Bloques */
        .editor-panel { width: 320px; background: white; border-left: 1px solid #e5e7eb; display: flex; flex-direction: column; padding: 1rem; z-index: 10; }

        .canvas-container { flex: 1; display: flex; flex-direction: column; position: relative; overflow: hidden; }

        .canvas { flex: 1; padding: 4rem 2rem; overflow-y: auto; background: #d1d5db; }
        .canvas-inner { background: white; min-height: 100%; max-width: 900px; margin: 0 auto; padding: 3rem; box-shadow: 0 10px 25px rgba(0,0,0,0.1); border-radius: 0.5rem; }

        .section-title { font-size: 0.75rem; font-weight: bold; color: #9ca3af; margin: 1.5rem 0 0.5rem 0; text-transform: uppercase; letter-spacing: 0.05em; }
        .toolbox-item {
            padding: 0.75rem; background: #f9fafb; border: 1px solid #e5e7eb;
            border-radius: 0.5rem; margin-bottom: 0.5rem; cursor: pointer;
            transition: all 0.2s; font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem;
        }
        .toolbox-item:hover { background: #eff6ff; border-color: #2563eb; color: #2563eb; transform: translateY(-1px); }

        .top-bar {
            height: 60px; background: white; border-bottom: 1px solid #e5e7eb;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 2rem; z-index: 100;
        }

        /* Estilos para bloques seleccionados */
        .block-selected { outline: 2px solid #2563eb; outline-offset: 2px; cursor: pointer; }
        .block { position: relative; cursor: pointer; transition: outline 0.1s; }

        /* Controles rápidos flotantes */
        .block-actions {
            position: absolute; top: -25px; right: 0; display: none;
            gap: 4px; background: #2563eb; padding: 2px; border-radius: 4px;
        }
        .block-selected > .block-actions { display: flex; }
        .btn-action {
            background: white; border: none; font-size: 10px; padding: 2px 6px;
            cursor: pointer; border-radius: 2px; font-weight: bold;
        }

        /* Formulario de edición */
        .edit-field { margin-bottom: 1rem; }
        .edit-field label { display: block; font-size: 0.85rem; color: #4b5563; margin-bottom: 0.25rem; font-weight: 500; }
        .edit-field input, .edit-field textarea, .edit-field select {
            width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-family: inherit;
        }

        .btn { padding: 0.5rem 1rem; border-radius: 0.5rem; cursor: pointer; font-weight: 500; border: none; transition: opacity 0.2s; }
        .btn-primary { background: #2563eb; color: white; }
        .btn-secondary { background: #e5e7eb; color: #1f2937; }
        .btn:hover { opacity: 0.9; }
        .btn-danger { background: #ef4444; color: white; }
    </style>
</head>
<body>

    <!-- SIDEBAR IZQUIERDA: HERRAMIENTAS -->
    <div class="sidebar">
        <h2 style="margin-bottom: 1rem;">🏗️ Builder</h2>

        <div class="section-title">Bloques Básicos</div>
        <div class="toolbox-item" onclick="builder.addBlock('text')">📝 Texto</div>
        <div class="toolbox-item" onclick="builder.addBlock('image')">🖼️ Imagen</div>
        <div class="toolbox-item" onclick="builder.addBlock('container')">📦 Contenedor</div>

        <div class="section-title">Widgets Reutilizables</div>
        <div id="widget-list">
            <p style="font-size: 0.8rem; color: #9ca3af;">Cargando widgets...</p>
        </div>
    </div>

    <!-- AREA CENTRAL: LIENZO -->
    <div class="canvas-container">
        <div class="top-bar">
            <div>
                <input type="text" id="page-title" placeholder="Título de la página..."
                       style="font-size: 1.2rem; font-weight: bold; border: none; outline: none; width: 300px;"
                       value="Nueva Página">
            </div>
            <div style="display: flex; gap: 1rem;">
                <button class="btn btn-secondary" onclick="alert('Modo vista previa: El HTML se genera automáticamente en el lienzo')">👁️ Vista Previa</button>
                <button class="btn btn-primary" onclick="saveCurrentPage()">💾 Guardar Página</button>
            </div>
        </div>

        <div class="canvas">
            <div class="canvas-inner" id="builder-canvas">
                <!-- Bloques renderizados aquí -->
            </div>
        </div>
    </div>

    <!-- SIDEBAR DERECHA: EDITOR -->
    <div class="editor-panel" id="editor-panel">
        <div id="editor-empty" style="text-align: center; color: #9ca3af; margin-top: 2rem;">
            <p>Selecciona un bloque para editarlo</p>
        </div>
        <div id="editor-content" style="display: none;">
            <h3 id="editor-title" style="margin-bottom: 1rem; font-size: 1.1rem;">Editar Bloque</h3>
            <div id="editor-fields">
                <!-- Campos generados dinámicamente -->
            </div>
            <div style="margin-top: 2rem; display: flex; flex-direction: column; gap: 0.5rem;">
                <button class="btn btn-secondary" onclick="moveSelected('up')">⬆️ Subir Bloque</button>
                <button class="btn btn-secondary" onclick="moveSelected('down')">⬇️ Bajar Bloque</button>
                <button class="btn btn-danger" onclick="deleteSelected()">🗑️ Eliminar Bloque</button>
            </div>
        </div>
    </div>

    <script type="module">
        import PageBuilderEditor from '../../components/page-builder/PageBuilderEditor.js';
        import WidgetService from '../../services/widgetService/WidgetService.js';

        window.builder = new PageBuilderEditor("Nueva Página");
        let selectedBlockId = null;

        async function loadWidgets() {
            const widgetListDiv = document.getElementById('widget-list');
            try {
                const response = await fetch('../../api/widgets.php');
                const widgets = await response.json();
                widgetListDiv.innerHTML = '';
                widgets.forEach(w => {
                    const item = document.createElement('div');
                    item.className = 'toolbox-item';
                    item.innerText = `🧩 ${w.nombre}`;
                    item.onclick = () => builder.addBlock('widget', { widgetId: w.id });
                    widgetListDiv.appendChild(item);
                });
            } catch (e) {
                widgetListDiv.innerHTML = '<p style="color:red">Error cargando widgets</p>';
            }
        }

        function renderCanvas() {
            const canvas = document.getElementById('builder-canvas');
            const html = builder.getPreviewHtml();
            canvas.innerHTML = html;

            // Añadimos los controles de acción a cada bloque
            const blocks = canvas.querySelectorAll('[data-id]');
            blocks.forEach(blockEl => {
                const id = blockEl.getAttribute('data-id');

                // Añadir controles flotantes
                const controls = document.createElement('div');
                controls.className = 'block-actions';
                controls.innerHTML = `
                    <button class="btn-action" onclick="event.stopPropagation(); moveSelected('up')">↑</button>
                    <button class="btn-action" onclick="event.stopPropagation(); moveSelected('down')">↓</button>
                    <button class="btn-action" onclick="event.stopPropagation(); deleteSelected()">X</button>
                `;
                blockEl.appendChild(controls);

                // Marcar como seleccionado
                if (id === selectedBlockId) {
                    blockEl.classList.add('block-selected');
                }

                // Evento de selección
                blockEl.onclick = (e) => {
                    e.stopPropagation();
                    selectBlock(id);
                };
            });
        }

        function selectBlock(id) {
            selectedBlockId = id;
            renderCanvas();
            updateEditorPanel();
        }

        function updateEditorPanel() {
            const empty = document.getElementById('editor-empty');
            const content = document.getElementById('editor-content');
            const fieldsDiv = document.getElementById('editor-fields');

            if (!selectedBlockId) {
                empty.style.display = 'block';
                content.style.display = 'none';
                return;
            }

            empty.style.display = 'none';
            content.style.display = 'block';
            fieldsDiv.innerHTML = '';

            const block = builder.page.blocks.find(b => b.id === selectedBlockId);
            if (!block) return;

            // El contenido puede estar en .content o en .config (para widgets)
            const data = block.config || block.content || {};

            Object.keys(data).forEach(key => {
                const field = document.createElement('div');
                field.className = 'edit-field';

                const label = document.createElement('label');
                label.innerText = key.charAt(0).toUpperCase() + key.slice(1);

                const input = document.createElement('input');
                input.type = 'text';
                input.value = data[key];
                input.oninput = (e) => {
                    builder.updateBlockContent(selectedBlockId, key, e.target.value);
                    renderCanvas();
                };

                field.appendChild(label);
                field.appendChild(input);
                fieldsDiv.appendChild(field);
            });
        }

        window.moveSelected = (direction) => {
            if (!selectedBlockId) return;
            builder.page.moveBlock(selectedBlockId, direction);
            renderCanvas();
        };

        window.deleteSelected = () => {
            if (!selectedBlockId) return;
            if (confirm('¿Eliminar este bloque?')) {
                builder.removeBlock(selectedBlockId);
                selectedBlockId = null;
                updateEditorPanel();
                renderCanvas();
            }
        };

        window.saveCurrentPage = async () => {
            const title = document.getElementById('page-title').value || "Sin Título";
            builder.page.title = title;
            try {
                const jsonData = await builder.savePage();
                const response = await fetch('../../api/pages.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ title: title, blocks: jsonData.blocks })
                });
                const result = await response.json();
                if (result.success) alert('✅ Página guardada exitosamente');
            } catch (e) {
                alert('❌ Error al guardar: ' + e.message);
            }
        };

        loadWidgets();

        const originalAddBlock = builder.addBlock.bind(builder);
        builder.addBlock = function(type, content, styles) {
            const block = originalAddBlock(type, content, styles);
            if (block) selectBlock(block.id);
            renderCanvas();
        };

        renderCanvas();
    </script>
</body>
</html>

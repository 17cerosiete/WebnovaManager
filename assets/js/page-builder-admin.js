(function () {
  const state = {
    id: window.WEBNOVA_INITIAL_PAGE?.id || null,
    title: window.WEBNOVA_INITIAL_PAGE?.titulo || 'Nueva pagina',
    publicada: Boolean(Number(window.WEBNOVA_INITIAL_PAGE?.publicada || 0)),
    blocks: parseBlocks(window.WEBNOVA_INITIAL_PAGE?.contenido || []),
    selectedId: null,
    widgets: []
  };

  const $ = (selector) => document.querySelector(selector);

  function parseBlocks(value) {
    if (Array.isArray(value)) return value;
    try {
      const parsed = JSON.parse(value || '[]');
      return Array.isArray(parsed) ? parsed : [];
    } catch (_) {
      return [];
    }
  }

  function parseConfig(value) {
    if (!value) return {};
    if (typeof value === 'object') return value;
    try {
      return JSON.parse(value);
    } catch (_) {
      return {};
    }
  }

  function escapeHtml(value) {
    return String(value ?? '')
      .replaceAll('&', '&amp;')
      .replaceAll('<', '&lt;')
      .replaceAll('>', '&gt;')
      .replaceAll('"', '&quot;')
      .replaceAll("'", '&#039;');
  }

  function uid() {
    return Math.random().toString(36).slice(2, 10);
  }

  function addBlock(type, content = {}) {
    state.blocks.push({ id: uid(), type, content });
    state.selectedId = state.blocks[state.blocks.length - 1].id;
    render();
  }

  function selectedBlock() {
    return state.blocks.find((block) => block.id === state.selectedId) || null;
  }

  function renderWidget(widget) {
    const config = parseConfig(widget.config || widget.content?.config);
    const type = widget.tipo || widget.type || widget.content?.tipo || 'text';

    if (type === 'hero') {
      const image = config.imageUrl ? `<img src="${escapeHtml(config.imageUrl)}" alt="${escapeHtml(config.imageAlt || '')}">` : '';
      return `<section class="preview-hero"><div><h1>${escapeHtml(config.title || 'Hero destacado')}</h1><p>${escapeHtml(config.subtitle || 'Subtitulo del hero')}</p><span>${escapeHtml(config.buttonText || 'Saber mas')}</span></div>${image}</section>`;
    }

    if (type === 'cta') {
      return `<section class="preview-cta"><div><h2>${escapeHtml(config.title || 'Llamada a la accion')}</h2><p>${escapeHtml(config.text || 'Texto de conversion')}</p></div><span>${escapeHtml(config.buttonText || 'Contactar')}</span></section>`;
    }

    if (type === 'faq') {
      const items = Array.isArray(config.items) ? config.items : [];
      return `<section class="preview-faq">${items.map((item) => `<details open><summary>${escapeHtml(item.q || 'Pregunta')}</summary><p>${escapeHtml(item.a || '')}</p></details>`).join('') || '<p>FAQ sin preguntas</p>'}</section>`;
    }

    if (type === 'features') {
      const items = Array.isArray(config.items) ? config.items : [];
      return `<section class="preview-grid"><h2>${escapeHtml(config.title || 'Servicios destacados')}</h2><div>${items.map((item) => `<article><h3>${escapeHtml(item.title || 'Ventaja')}</h3><p>${escapeHtml(item.text || '')}</p></article>`).join('')}</div></section>`;
    }

    if (type === 'testimonial') {
      return `<section class="preview-card"><blockquote>${escapeHtml(config.quote || 'Testimonio del cliente')}</blockquote><p>${escapeHtml(config.author || 'Cliente')}</p><span>${escapeHtml(config.role || '')}</span></section>`;
    }

    if (type === 'metrics') {
      const items = Array.isArray(config.items) ? config.items : [];
      return `<section class="preview-metrics">${items.map((item) => `<article><strong>${escapeHtml(item.value || '')}</strong><span>${escapeHtml(item.label || '')}</span></article>`).join('')}</section>`;
    }

    if (type === 'gallery') {
      const items = Array.isArray(config.items) ? config.items : [];
      return `<section class="preview-gallery"><h2>${escapeHtml(config.title || 'Galeria')}</h2><div>${items.map((item) => `<figure><img src="${escapeHtml(item.src || '')}" alt="${escapeHtml(item.alt || '')}"><figcaption>${escapeHtml(item.alt || '')}</figcaption></figure>`).join('')}</div></section>`;
    }

    return `<section class="preview-text"><p>${escapeHtml(config.content || config.text || 'Texto del widget')}</p></section>`;
  }

  function renderBlock(block) {
    const active = block.id === state.selectedId ? ' is-active' : '';
    let inner = '';

    if (block.type === 'text') {
      inner = `<p>${escapeHtml(block.content?.content || 'Texto vacio')}</p>`;
    } else if (block.type === 'image') {
      inner = block.content?.src
        ? `<img src="${escapeHtml(block.content.src)}" alt="${escapeHtml(block.content.alt || '')}"><small>${escapeHtml(block.content.alt || '')}</small>`
        : '<p class="muted">Imagen sin URL</p>';
    } else if (block.type === 'container') {
      inner = `<h2>${escapeHtml(block.content?.title || 'Seccion')}</h2><p>${escapeHtml(block.content?.content || 'Contenido de la seccion')}</p>`;
    } else if (block.type === 'widget') {
      inner = renderWidget(block.content || {});
    }

    return `<article class="canvas-block${active}" data-id="${block.id}">
      <div class="block-tools">
        <button type="button" data-action="up" title="Subir">↑</button>
        <button type="button" data-action="down" title="Bajar">↓</button>
        <button type="button" data-action="remove" title="Eliminar">×</button>
      </div>
      ${inner}
    </article>`;
  }

  function renderCanvas() {
    $('#canvas').innerHTML = state.blocks.map(renderBlock).join('') || '<p class="empty">Anade bloques desde la barra lateral.</p>';
  }

  function field(label, name, value, multiline = false) {
    if (multiline) {
      return `<label>${label}<textarea rows="7" data-field="${name}">${escapeHtml(value)}</textarea></label>`;
    }

    return `<label>${label}<input type="text" data-field="${name}" value="${escapeHtml(value)}"></label>`;
  }

  function renderEditor() {
    const block = selectedBlock();
    const panel = $('#blockEditor');

    if (!block) {
      panel.innerHTML = '<p class="muted">Selecciona un bloque para editarlo.</p>';
      return;
    }

    if (block.type === 'text') {
      panel.innerHTML = field('Texto', 'content', block.content?.content || '', true);
    } else if (block.type === 'image') {
      panel.innerHTML = field('URL de imagen', 'src', block.content?.src || '') + field('Texto alternativo', 'alt', block.content?.alt || '');
    } else if (block.type === 'container') {
      panel.innerHTML = field('Titulo', 'title', block.content?.title || '') + field('Contenido', 'content', block.content?.content || '', true);
    } else {
      panel.innerHTML = field('Configuracion JSON', 'config', JSON.stringify(parseConfig(block.content?.config), null, 2), true);
    }
  }

  function renderWidgets() {
    const list = $('#widgetList');
    list.innerHTML = state.widgets.map((widget) => `
      <button type="button" class="tool-item" data-widget="${widget.id}">
        ${escapeHtml(widget.nombre)} <small>${escapeHtml(widget.tipo)}</small>
      </button>
    `).join('') || '<p class="muted">No hay widgets guardados.</p>';
  }

  function render() {
    $('#pageTitle').value = state.title;
    $('#pagePublished').checked = state.publicada;
    renderCanvas();
    renderEditor();
  }

  async function loadWidgets() {
    const response = await fetch('../../api/widgets.php');
    state.widgets = await response.json();
    renderWidgets();
  }

  async function savePage() {
    state.title = $('#pageTitle').value.trim() || 'Pagina sin titulo';
    state.publicada = $('#pagePublished').checked;

    const response = await fetch('../../api/pages.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        id: state.id,
        title: state.title,
        publicada: state.publicada ? 1 : 0,
        blocks: state.blocks
      })
    });

    const result = await response.json();
    if (!result.success) {
      throw new Error(result.error || 'No se pudo guardar');
    }

    window.location.href = `list.php?success=saved&id=${result.id}`;
  }

  function openPreview() {
    const content = state.blocks.map(renderBlock).join('') || '<p>Pagina sin bloques.</p>';
    const preview = window.open('', '_blank');
    if (!preview) {
      alert('El navegador ha bloqueado la vista previa.');
      return;
    }

    preview.document.write(`<!DOCTYPE html>
      <html lang="es">
      <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>${escapeHtml(state.title || 'Vista previa')}</title>
        <link rel="stylesheet" href="../../assets/css/styles.css">
        <style>
          body { background: #f8fafc; padding: 2rem 1rem; }
          main { max-width: 1040px; margin: 0 auto; background: white; border: 1px solid #dbe3ef; border-radius: 8px; padding: 2rem; }
          .block-tools { display: none; }
          .canvas-block { border: 0; padding: 0; margin-bottom: 1.25rem; }
          img { max-width: 100%; height: auto; object-fit: contain; border-radius: 8px; }
          .preview-hero { display: grid; grid-template-columns: minmax(0,1fr) minmax(240px,.8fr); gap: 1rem; align-items: center; background: #0f172a; color: white; padding: 2.5rem; border-radius: 8px; }
          .preview-hero h1 { color: white; }
          .preview-cta, .preview-card, .preview-grid article, .preview-metrics article, .preview-faq details { border: 1px solid #dbe3ef; border-radius: 8px; padding: 1rem; background: #f8fafc; }
          .preview-grid > div, .preview-gallery > div, .preview-metrics { display: grid; grid-template-columns: repeat(auto-fit,minmax(180px,1fr)); gap: 1rem; }
          .preview-gallery img { width: 100%; aspect-ratio: 4 / 3; object-fit: cover; }
          @media (max-width: 760px) { .preview-hero { display: flex; flex-direction: column; align-items: stretch; } }
        </style>
      </head>
      <body><main>${content}</main></body></html>`);
    preview.document.close();
  }

  document.addEventListener('click', async (event) => {
    const add = event.target.closest('[data-add]');
    if (add) {
      const type = add.dataset.add;
      const defaults = {
        text: { content: 'Nuevo bloque de texto editable.' },
        image: { src: '', alt: '' },
        container: { title: 'Nueva seccion', content: 'Describe aqui esta seccion.' }
      };
      addBlock(type, defaults[type] || {});
    }

    const widgetButton = event.target.closest('[data-widget]');
    if (widgetButton) {
      const widget = state.widgets.find((item) => String(item.id) === String(widgetButton.dataset.widget));
      if (widget) {
        addBlock('widget', {
          widgetId: widget.id,
          nombre: widget.nombre,
          tipo: widget.tipo,
          config: parseConfig(widget.config)
        });
      }
    }

    const blockEl = event.target.closest('.canvas-block');
    if (blockEl && !event.target.closest('.block-tools')) {
      state.selectedId = blockEl.dataset.id;
      render();
    }

    const action = event.target.closest('[data-action]');
    if (action) {
      const id = action.closest('.canvas-block')?.dataset.id;
      const index = state.blocks.findIndex((block) => block.id === id);
      if (index === -1) return;

      if (action.dataset.action === 'remove') {
        state.blocks.splice(index, 1);
        state.selectedId = null;
      }
      if (action.dataset.action === 'up' && index > 0) {
        [state.blocks[index - 1], state.blocks[index]] = [state.blocks[index], state.blocks[index - 1]];
      }
      if (action.dataset.action === 'down' && index < state.blocks.length - 1) {
        [state.blocks[index + 1], state.blocks[index]] = [state.blocks[index], state.blocks[index + 1]];
      }
      render();
    }

    if (event.target.matches('#savePage')) {
      event.target.disabled = true;
      try {
        await savePage();
      } catch (error) {
        alert(error.message);
        event.target.disabled = false;
      }
    }

    if (event.target.matches('#previewPage')) {
      openPreview();
    }
  });

  document.addEventListener('input', (event) => {
    const block = selectedBlock();
    if (!block || !event.target.matches('[data-field]')) return;

    const fieldName = event.target.dataset.field;
    if (fieldName === 'config') {
      try {
        block.content.config = JSON.parse(event.target.value || '{}');
      } catch (_) {
        return;
      }
    } else {
      block.content[fieldName] = event.target.value;
    }
    renderCanvas();
  });

  $('#pageTitle').addEventListener('input', (event) => {
    state.title = event.target.value;
  });

  $('#pagePublished').addEventListener('change', (event) => {
    state.publicada = event.target.checked;
  });

  loadWidgets().catch(() => {
    $('#widgetList').innerHTML = '<p class="muted">No se pudieron cargar widgets.</p>';
  });
  render();
})();

(function () {
  const schemas = {
    hero: {
      title: 'Hero',
      fields: [
        ['title', 'Titulo principal'],
        ['subtitle', 'Subtitulo'],
        ['buttonText', 'Texto del boton'],
        ['buttonUrl', 'URL del boton'],
        ['imageUrl', 'URL de imagen'],
        ['imageAlt', 'Texto alternativo'],
        ['theme', 'Tema (dark, blue, light, green)']
      ],
      defaults: {
        title: 'WebNova Digital',
        subtitle: 'Soluciones web responsive para pequenas y medianas empresas.',
        buttonText: 'Solicitar propuesta',
        buttonUrl: '#',
        imageUrl: '',
        imageAlt: '',
        theme: 'dark'
      }
    },
    cta: {
      title: 'CTA',
      fields: [
        ['title', 'Titulo'],
        ['text', 'Texto'],
        ['buttonText', 'Texto del boton'],
        ['buttonUrl', 'URL del boton'],
        ['theme', 'Tema (blue, light, green)']
      ],
      defaults: {
        title: 'Convierte visitas en clientes',
        text: 'Publica contenidos claros, rapidos y orientados a SEO.',
        buttonText: 'Contactar',
        buttonUrl: '#',
        theme: 'blue'
      }
    },
    features: {
      title: 'Ventajas',
      fields: [
        ['title', 'Titulo'],
        ['items', 'Items JSON']
      ],
      defaults: {
        title: 'Servicios digitales integrales',
        items: [
          { title: 'UX y accesibilidad', text: 'Interfaces claras y faciles de administrar.' },
          { title: 'Responsive', text: 'Paginas adaptadas a movil y tablet.' },
          { title: 'SEO tecnico', text: 'Estructura preparada para mejorar visibilidad.' }
        ]
      }
    },
    testimonial: {
      title: 'Testimonio',
      fields: [
        ['quote', 'Cita'],
        ['author', 'Autor'],
        ['role', 'Cargo o empresa']
      ],
      defaults: {
        quote: 'Ahora podemos actualizar nuestra web sin depender de soporte para cada cambio pequeno.',
        author: 'Cliente WebNova',
        role: 'PYME de servicios'
      }
    },
    metrics: {
      title: 'Metricas',
      fields: [
        ['items', 'Metricas JSON']
      ],
      defaults: {
        items: [
          { value: '70%', label: 'trafico movil esperado' },
          { value: '3x', label: 'bloques reutilizables' },
          { value: 'SEO', label: 'estructura de publicacion' }
        ]
      }
    },
    gallery: {
      title: 'Galeria',
      fields: [
        ['title', 'Titulo'],
        ['items', 'Imagenes JSON']
      ],
      defaults: {
        title: 'Galeria del proyecto',
        items: [
          { src: 'https://images.unsplash.com/photo-1497366754035-f200968a6e72?auto=format&fit=crop&w=900&q=80', alt: 'Equipo trabajando en una web' },
          { src: 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=900&q=80', alt: 'Panel de analitica digital' }
        ]
      }
    },
    faq: {
      title: 'FAQ',
      fields: [
        ['items', 'Preguntas JSON']
      ],
      defaults: {
        items: [
          { q: 'Que incluye el CMS?', a: 'Gestion de paginas, widgets reutilizables y publicacion responsive.' },
          { q: 'Esta pensado para movil?', a: 'Si, los bloques se adaptan a tablet y smartphone.' }
        ]
      }
    },
    text: {
      title: 'Texto',
      fields: [
        ['content', 'Contenido']
      ],
      defaults: {
        content: 'Texto reutilizable para una seccion informativa.'
      }
    }
  };

  const initial = window.WEBNOVA_WIDGET || null;
  const form = document.getElementById('widgetForm');
  const typeSelect = document.getElementById('tipo');
  const fieldsBox = document.getElementById('schemaFields');
  const configBox = document.getElementById('config');
  const previewBox = document.getElementById('widgetPreview');

  function escapeHtml(value) {
    return String(value ?? '')
      .replaceAll('&', '&amp;')
      .replaceAll('<', '&lt;')
      .replaceAll('>', '&gt;')
      .replaceAll('"', '&quot;')
      .replaceAll("'", '&#039;');
  }

  function parseConfig(value) {
    try {
      return JSON.parse(value || '{}');
    } catch (_) {
      return {};
    }
  }

  function currentSchema() {
    return schemas[typeSelect.value] || schemas.text;
  }

  function fieldValue(name, config) {
    const value = config[name] ?? currentSchema().defaults[name] ?? '';
    return typeof value === 'object' ? JSON.stringify(value, null, 2) : value;
  }

  function renderFields(config = null) {
    const schema = currentSchema();
    const data = config || schema.defaults;
    fieldsBox.innerHTML = schema.fields.map(([name, label]) => {
      const value = fieldValue(name, data);
      const multiline = name === 'items' || name === 'content' || name === 'quote' || name === 'text' || name === 'subtitle';
      if (multiline) {
        return `<label>${label}<textarea rows="4" data-config-field="${name}">${escapeHtml(value)}</textarea></label>`;
      }
      return `<label>${label}<input type="text" data-config-field="${name}" value="${escapeHtml(value)}"></label>`;
    }).join('');
    syncConfig();
  }

  function readConfigFromFields() {
    const config = {};
    fieldsBox.querySelectorAll('[data-config-field]').forEach((input) => {
      const key = input.dataset.configField;
      if (key === 'items') {
        try {
          config[key] = JSON.parse(input.value || '[]');
        } catch (_) {
          config[key] = [];
        }
      } else {
        config[key] = input.value;
      }
    });
    return config;
  }

  function syncConfig() {
    const config = readConfigFromFields();
    configBox.value = JSON.stringify(config, null, 2);
    renderPreview(typeSelect.value, config);
  }

  function renderPreview(type, config) {
    if (type === 'hero') {
      const image = config.imageUrl ? `<img src="${escapeHtml(config.imageUrl)}" alt="${escapeHtml(config.imageAlt || '')}">` : '';
      previewBox.innerHTML = `<section class="widget-preview-hero"><div><h1>${escapeHtml(config.title)}</h1><p>${escapeHtml(config.subtitle)}</p><span>${escapeHtml(config.buttonText)}</span></div>${image}</section>`;
      return;
    }

    if (type === 'cta') {
      previewBox.innerHTML = `<section class="widget-preview-cta"><div><h2>${escapeHtml(config.title)}</h2><p>${escapeHtml(config.text)}</p></div><span>${escapeHtml(config.buttonText)}</span></section>`;
      return;
    }

    if (type === 'features') {
      const items = Array.isArray(config.items) ? config.items : [];
      previewBox.innerHTML = `<section class="widget-preview-grid"><h2>${escapeHtml(config.title)}</h2><div>${items.map((item) => `<article><h3>${escapeHtml(item.title)}</h3><p>${escapeHtml(item.text)}</p></article>`).join('')}</div></section>`;
      return;
    }

    if (type === 'testimonial') {
      previewBox.innerHTML = `<section class="widget-preview-card"><blockquote>${escapeHtml(config.quote)}</blockquote><p>${escapeHtml(config.author)}</p><span>${escapeHtml(config.role)}</span></section>`;
      return;
    }

    if (type === 'metrics') {
      const items = Array.isArray(config.items) ? config.items : [];
      previewBox.innerHTML = `<section class="widget-preview-metrics">${items.map((item) => `<article><strong>${escapeHtml(item.value)}</strong><span>${escapeHtml(item.label)}</span></article>`).join('')}</section>`;
      return;
    }

    if (type === 'gallery') {
      const items = Array.isArray(config.items) ? config.items : [];
      previewBox.innerHTML = `<section class="widget-preview-gallery"><h2>${escapeHtml(config.title)}</h2><div>${items.map((item) => `<figure><img src="${escapeHtml(item.src)}" alt="${escapeHtml(item.alt)}"><figcaption>${escapeHtml(item.alt)}</figcaption></figure>`).join('')}</div></section>`;
      return;
    }

    if (type === 'faq') {
      const items = Array.isArray(config.items) ? config.items : [];
      previewBox.innerHTML = `<section class="widget-preview-faq">${items.map((item) => `<details open><summary>${escapeHtml(item.q)}</summary><p>${escapeHtml(item.a)}</p></details>`).join('')}</section>`;
      return;
    }

    previewBox.innerHTML = `<section class="widget-preview-card"><p>${escapeHtml(config.content || '')}</p></section>`;
  }

  typeSelect.addEventListener('change', () => renderFields());
  fieldsBox.addEventListener('input', syncConfig);

  form.addEventListener('submit', async (event) => {
    event.preventDefault();
    syncConfig();

    const payload = Object.fromEntries(new FormData(form).entries());
    payload.config = parseConfig(configBox.value);

    const response = await fetch('../../api/widgets.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });

    const result = await response.json();
    if (result.success) {
      window.location.href = 'list.php?success=saved';
    } else {
      alert(result.error || 'No se pudo guardar el widget');
    }
  });

  if (initial) {
    typeSelect.value = initial.tipo || 'text';
    renderFields(parseConfig(initial.config));
  } else {
    renderFields();
  }
})();

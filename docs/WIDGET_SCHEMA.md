# Estandar de widgets WebNova

Los widgets se guardan en MySQL en la tabla `widgets`.

Cada widget tiene:

- `nombre`: nombre visible en el panel.
- `tipo`: plantilla de renderizado.
- `config`: JSON con las claves que interpreta el renderizador.
- `preview_html`: HTML generado automaticamente desde `tipo + config`.

## Tipos y claves

### `hero`

```json
{
  "title": "Titulo principal",
  "subtitle": "Texto de apoyo",
  "buttonText": "Llamada a la accion",
  "buttonUrl": "#",
  "imageUrl": "",
  "imageAlt": "",
  "theme": "dark"
}
```

### `cta`

```json
{
  "title": "Mensaje breve",
  "text": "Texto de conversion",
  "buttonText": "Contactar",
  "buttonUrl": "#",
  "theme": "blue"
}
```

### `features`

```json
{
  "title": "Ventajas",
  "items": [
    { "title": "Responsive", "text": "Adaptado a movil y tablet" }
  ]
}
```

### `testimonial`

```json
{
  "quote": "Texto del cliente",
  "author": "Nombre",
  "role": "Cargo o empresa"
}
```

### `metrics`

```json
{
  "items": [
    { "value": "70%", "label": "Accesos desde movil" }
  ]
}
```

### `gallery`

```json
{
  "title": "Galeria",
  "items": [
    { "src": "https://...", "alt": "Descripcion" }
  ]
}
```

### `faq`

```json
{
  "items": [
    { "q": "Pregunta", "a": "Respuesta" }
  ]
}
```

### `text`

```json
{
  "content": "Texto enriquecido simple"
}
```

## Regla importante

Las claves son parte del contrato del proyecto. Si se cambia `buttonText` por `textoBoton`, el JSON sigue siendo valido, pero WebNova no lo interpretara salvo que el renderizador tambien se actualice.

# WebNova Manager - Documentación Técnica

## 🏗️ Arquitectura

### Stack Tecnológico

```
FRONTEND LAYER
├── HTML5 (Estructura semántica)
├── CSS3 (Diseño responsive mobile-first)
└── JavaScript ES6+ (Lógica cliente)

ALMACENAMIENTO
├── localStorage (5-10MB por dominio)
└── JSON structures (simulan modelos de BD)

NAVEGADOR (Single Page Application)
└── Sin dependencias externas (Vanilla JS)
```

### Arquitectura de Carpetas

```
WebNovaManager-CMS/
├── /admin                 # Panel de administración
│   ├── index.html        # Autenticación
│   ├── dashboard.html    # Resumen ejecutivo
│   ├── pages.html        # CRUD de páginas
│   ├── posts.html        # CRUD de artículos
│   ├── categories.html   # CRUD de categorías (TODO)
│   └── settings.html     # Configuración (TODO)
│
├── /public               # Sitio público (frontend)
│   ├── index.html        # Homepage
│   ├── blog.html         # Listado de blog (TODO)
│   └── page.html         # Template de página (TODO)
│
├── /assets
│   ├── /css
│   │   ├── styles.css    # Estilos globales
│   │   ├── admin.css     # Estilos admin (TODO)
│   │   └── public.css    # Estilos público (TODO)
│   ├── /js
│   │   ├── db.js         # Gestor de localStorage
│   │   ├── admin.js      # Lógica admin (TODO)
│   │   ├── public.js     # Lógica público (TODO)
│   │   └── core.js       # Funciones compartidas (TODO)
│   └── /images           # Assets gráficos
│
└── /docs
    ├── API.md            # Este archivo
    ├── MANUAL.md         # Guía de usuario
    ├── ROADMAP.md        # Plan futuro
    └── ARCHITECTURE.md   # Documentación técnica
```

---

## 📊 Modelos de Datos

### User Model
```javascript
{
  id: Number,
  email: String,
  password: String, // En producción: bcrypt hash
  name: String,
  role: String, // 'admin', 'editor', 'viewer'
  createdAt: ISO8601DateTime
}
```

### Page Model
```javascript
{
  id: Number,
  title: String,
  slug: String,              // URL amigable
  content: String,           // HTML content
  meta_description: String,  // SEO
  meta_keywords: String,     // SEO
  status: String,            // 'published', 'draft'
  order: Number,             // Orden en menú
  createdAt: ISO8601DateTime,
  updatedAt: ISO8601DateTime
}
```

### Post Model
```javascript
{
  id: Number,
  title: String,
  slug: String,
  excerpt: String,           // Resumen corto
  content: String,           // Contenido completo
  categoryId: Number,        // FK a Category
  author: String,            // Email del autor
  status: String,            // 'published', 'draft'
  views: Number,             // Contador de vistas
  comments: Number,          // Contador de comentarios
  createdAt: ISO8601DateTime,
  updatedAt: ISO8601DateTime
}
```

### Category Model
```javascript
{
  id: Number,
  name: String,
  slug: String,
  description: String
}
```

### Settings Model
```javascript
{
  siteName: String,
  siteDescription: String,
  siteUrl: String,
  logo: String,              // URL de imagen
  favicon: String,           // URL de icono
  theme: {
    primaryColor: String,
    secondaryColor: String,
    accentColor: String
  },
  timezone: String,
  language: String           // 'es', 'en', etc.
}
```

### Session Model
```javascript
{
  user: User | null,
  token: String | null,      // JWT en futuro
  lastActivity: ISO8601DateTime | null
}
```

---

## 🔌 API localStorage (Internal)

### Autenticación

#### `DB.login(email, password)`
Autentica un usuario.

**Parámetros:**
```javascript
email: String    // Email del usuario
password: String // Contraseña (hash en producción)
```

**Retorna:**
```javascript
{
  user: { id, email, name, role },
  token: "token_timestamp",
  lastActivity: "2026-05-05T..."
}
```

**Ejemplo:**
```javascript
const session = DB.login('admin@webnova.com', 'admin123');
if (session) {
  console.log('Login exitoso:', session.user.name);
}
```

#### `DB.logout()`
Cierra la sesión actual.

```javascript
DB.logout();
// Limpia la sesión del usuario
```

#### `DB.isAuthenticated()`
Verifica si hay una sesión activa.

**Retorna:** `Boolean`

```javascript
if (DB.isAuthenticated()) {
  // Usuario logado
} else {
  // Redirigir a login
}
```

#### `DB.getSession()`
Obtiene los datos de sesión actual.

**Retorna:** `Session | null`

```javascript
const session = DB.getSession();
console.log(session.user.email); // admin@webnova.com
```

---

### Páginas CRUD

#### `DB.getPages(status?)`
Obtiene todas las páginas, opcionalmente filtradas por estado.

**Parámetros:**
```javascript
status: String // Opcional: 'published', 'draft'
```

**Retorna:** `Array<Page>`

```javascript
const allPages = DB.getPages();
const publishedPages = DB.getPages('published');
```

#### `DB.getPageById(id)`
Obtiene una página por su ID.

**Parámetros:**
```javascript
id: Number
```

**Retorna:** `Page | undefined`

```javascript
const page = DB.getPageById(1);
console.log(page.title); // 'Inicio'
```

#### `DB.getPageBySlug(slug)`
Obtiene una página por su slug (URL amigable).

**Parámetros:**
```javascript
slug: String // ej: 'acerca-de'
```

**Retorna:** `Page | undefined`

```javascript
const page = DB.getPageBySlug('acerca-de');
```

#### `DB.createPage(pageData)`
Crea una nueva página.

**Parámetros:**
```javascript
{
  title: String,
  slug: String,
  content: String,
  meta_description?: String,
  status: String,
  order?: Number
}
```

**Retorna:** `Page` (con ID generado)

```javascript
const newPage = DB.createPage({
  title: 'Mi Nueva Página',
  slug: 'mi-nueva-pagina',
  content: '<h1>Contenido</h1>',
  status: 'draft'
});
console.log(newPage.id); // ID asignado automáticamente
```

#### `DB.updatePage(id, updates)`
Actualiza una página existente.

**Parámetros:**
```javascript
id: Number,
updates: Partial<Page>  // Solo los campos a actualizar
```

**Retorna:** `Page | null`

```javascript
const updated = DB.updatePage(1, {
  title: 'Título Actualizado',
  status: 'published'
});
```

#### `DB.deletePage(id)`
Elimina una página.

**Parámetros:**
```javascript
id: Number
```

**Retorna:** `Boolean`

```javascript
const success = DB.deletePage(1);
```

---

### Artículos CRUD

#### `DB.getPosts(status?)`
Obtiene todos los artículos.

```javascript
const allPosts = DB.getPosts();
const publishedPosts = DB.getPosts('published');
```

#### `DB.getPostsByCategory(categoryId)`
Obtiene artículos de una categoría específica.

**Parámetros:**
```javascript
categoryId: Number
```

**Retorna:** `Array<Post>`

```javascript
const techPosts = DB.getPostsByCategory(1);
```

#### `DB.getPostById(id)`
Obtiene un artículo por ID.

```javascript
const post = DB.getPostById(1);
```

#### `DB.createPost(postData)`
Crea un nuevo artículo.

**Parámetros:**
```javascript
{
  title: String,
  slug: String,
  excerpt: String,
  content: String,
  categoryId: Number,
  author: String,
  status: String
}
```

**Retorna:** `Post`

```javascript
const newPost = DB.createPost({
  title: 'Nuevo Artículo',
  slug: 'nuevo-articulo',
  excerpt: 'Resumen...',
  content: 'Contenido completo...',
  categoryId: 1,
  author: 'admin@webnova.com',
  status: 'published'
});
```

#### `DB.updatePost(id, updates)`
Actualiza un artículo.

```javascript
DB.updatePost(1, {
  views: 100,
  status: 'published'
});
```

#### `DB.deletePost(id)`
Elimina un artículo.

```javascript
DB.deletePost(1);
```

---

### Categorías

#### `DB.getCategories()`
Obtiene todas las categorías.

**Retorna:** `Array<Category>`

```javascript
const categories = DB.getCategories();
```

#### `DB.getCategoryById(id)`
Obtiene una categoría por ID.

**Retorna:** `Category | undefined`

```javascript
const category = DB.getCategoryById(1);
console.log(category.name); // 'Tecnología'
```

---

### Estadísticas

#### `DB.getStats()`
Obtiene estadísticas globales del sitio.

**Retorna:**
```javascript
{
  totalPages: Number,
  publishedPages: Number,
  totalPosts: Number,
  publishedPosts: Number,
  totalCategories: Number,
  totalUsers: Number,
  totalViews: Number
}
```

**Ejemplo:**
```javascript
const stats = DB.getStats();
console.log(`${stats.publishedPages} páginas publicadas`);
```

---

### Búsqueda

#### `DB.search(query)`
Busca en páginas y artículos.

**Parámetros:**
```javascript
query: String
```

**Retorna:**
```javascript
{
  pages: Array<Page>,
  posts: Array<Post>
}
```

**Ejemplo:**
```javascript
const results = DB.search('tecnología');
console.log(`${results.posts.length} artículos encontrados`);
```

---

## 🎨 Componentes CSS Disponibles

### Clases de Utilidad

```css
/* Spacing */
.mt-sm, .mt-md, .mt-lg      /* margin-top */
.mb-sm, .mb-md, .mb-lg      /* margin-bottom */
.p-sm, .p-md, .p-lg         /* padding */

/* Text */
.text-center                 /* text-align: center */
.text-muted                  /* color: gris */
.text-success, .text-danger  /* colores de estado */
.font-bold, .font-semibold   /* pesos de fuente */

/* Display */
.hidden                      /* display: none */
.visible                     /* display: block */

/* Grid */
.grid-2, .grid-3, .grid-4   /* responsive grids */
.flex, .flex-between         /* flexbox utilities */
```

### Componentes

```html
<!-- Card -->
<div class="card">
  <div class="card-header">Título</div>
  <div class="card-body">Contenido</div>
  <div class="card-footer">Pie</div>
</div>

<!-- Button -->
<button class="btn btn-primary">Primario</button>
<button class="btn btn-secondary">Secundario</button>

<!-- Alert -->
<div class="alert alert-success">✓ Éxito</div>
<div class="alert alert-danger">✗ Error</div>

<!-- Table -->
<table>
  <thead><tr><th>Encabezado</th></tr></thead>
  <tbody><tr><td>Datos</td></tr></tbody>
</table>
```

---

## 🔐 Seguridad (Notas Importantes)

### Estado Actual (Prototipo)
⚠️ **ESTO NO ES PRODUCCIÓN**
- Contraseñas en texto plano
- No hay validación de inputs
- localStorage es accesible desde DevTools
- Sin HTTPS
- Sin rate limiting

### Para Producción

```javascript
// Backend necesario (Node.js/Express ejemplo):

const bcrypt = require('bcrypt');
const jwt = require('jsonwebtoken');

// Hash de contraseña
const hashedPassword = await bcrypt.hash(password, 10);

// Token JWT
const token = jwt.sign({ userId }, SECRET, { expiresIn: '1h' });

// Validación
app.post('/login', (req, res) => {
  // Buscar user en BD
  // Validar contraseña
  // Generar JWT
  // Retornar token
});
```

---

## 🚀 Mejoras Futuras

### Corto Plazo
- [ ] Editor WYSIWYG (TinyMCE o Quill)
- [ ] Subida de archivos/imágenes
- [ ] Sistema de comentarios
- [ ] Roles y permisos granulares

### Medio Plazo
- [ ] Backend real (Express, Django, Laravel)
- [ ] Base de datos (PostgreSQL, MongoDB)
- [ ] API REST propia
- [ ] Autenticación JWT
- [ ] Sistema de plugins

### Largo Plazo
- [ ] Marketplace de plugins
- [ ] Integración con servicios (Stripe, SendGrid)
- [ ] Analytics avanzado
- [ ] Multisite management
- [ ] Auto-escalado en cloud

---

## 📚 Referencias

- [MDN Web Docs](https://developer.mozilla.org/)
- [Web Storage API](https://developer.mozilla.org/es/docs/Web/API/Web_Storage_API)
- [localStorage limitaciones](https://developer.mozilla.org/es/docs/Web/API/Window/localStorage)
- [Best Practices CMS](https://www.example.com)

---

**Versión:** 0.9 (Prototipo)
**Última actualización:** 5 de mayo de 2026

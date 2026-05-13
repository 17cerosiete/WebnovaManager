# 📊 WebNova Manager CMS - Guía de Uso Rápida

## 🚀 Inicio Rápido

### Paso 1: Abrir el Login
Abre en tu navegador:
```
http://localhost/WebnovaManager/admin/index.html
```

### Paso 2: Iniciar Sesión
**Credenciales de prueba:**
- Email: `admin@webnova.com`
- Contraseña: `0000`

### Paso 3: Explorar el Dashboard
Verás un panel con 4 secciones:

---

## 📖 Guía por Secciones

### 1️⃣ DASHBOARD
- **Ubicación:** `/admin/dashboard.html`
- **Qué ves:**
  - 📈 Estadísticas de tu sitio (páginas, artículos, vistas)
  - 🚀 Botones de acceso rápido
  - 📝 Contenido reciente
- **Acciones:**
  - Ver estadísticas en tiempo real
  - Acceso rápido a todas las secciones

### 2️⃣ GESTIONAR PÁGINAS
- **Ubicación:** `/admin/pages.html`
- **Qué es:** Las páginas estáticas de tu sitio (Inicio, Acerca de, Contacto)

#### Crear una página:
1. Haz clic en **➕ Nueva Página**
2. Rellena los campos:
   - **Título:** Nombre de la página
   - **URL (slug):** Dirección web amigable (ej: `mi-pagina`)
   - **Estado:** Publicado o Borrador
   - **Meta Descripción:** Para SEO (máx. 160 caracteres)
   - **Contenido:** Texto e HTML de la página
3. Haz clic en **Guardar Página**

#### Editar una página:
1. En la tabla, busca la página
2. Haz clic en **Editar**
3. Modifica lo que necesites
4. Haz clic en **Guardar Página**

#### Eliminar una página:
1. Haz clic en **Eliminar**
2. Confirma en el diálogo

### 3️⃣ GESTIONAR ARTÍCULOS (Blog)
- **Ubicación:** `/admin/posts.html`
- **Qué es:** Artículos del blog con categorías

#### Crear un artículo:
1. Haz clic en **✍️ Nuevo Artículo**
2. Rellena los campos:
   - **Título:** Titular del artículo
   - **URL (slug):** Dirección web (ej: `mi-articulo`)
   - **Categoría:** Selecciona una categoría existente
   - **Estado:** Publicado o Borrador
   - **Resumen:** Descripción corta del artículo
   - **Contenido:** Texto completo
3. Haz clic en **Guardar Artículo**

#### Filtrar por categoría:
- Los artículos se pueden filtrar por categoría
- Crear categorías en la sección **Categorías**

### 4️⃣ GESTIONAR CATEGORÍAS
- **Ubicación:** `/admin/categories.html` (próximamente)
- **Qué es:** Clasificaciones para organizar artículos
- **Ejemplo:** Tecnología, Diseño, Negocios

### 5️⃣ CONFIGURACIÓN DEL SITIO
- **Ubicación:** `/admin/settings.html` (próximamente)
- **Qué puedes cambiar:**
  - Nombre del sitio
  - Descripción
  - Logo
  - Colores del tema
  - Información de contacto

---

## 🌐 Ver el Sitio Público

El sitio público está en:
```
file:///c:\Users\vspc\Desktop\TFG\WebNovaManager-CMS\public\index.html
```

**Lo que verás:**
- 🏠 Página de inicio con páginas destacadas
- 📝 Sección de blog con últimos artículos
- 🔗 Links a todas las páginas publicadas
- 🔐 Botón de acceso al admin

---

## 💾 Almacenamiento de Datos

**¿Dónde se guardan los datos?**
- En el **localStorage** del navegador
- Cada navegador tiene su propia BD
- Los datos persisten cuando cierras el navegador

**¿Cómo hacer backup?**
```javascript
// En la consola del navegador (F12):
copy(localStorage);
```

**¿Cómo restaurar datos?**
```javascript
// En la consola:
localStorage.clear(); // Limpia todo
window.location.reload(); // Reinicia el sistema
```

---

## 🎨 Características de Diseño

### ✅ Lo que ya está hecho:
- ✓ Interfaz moderna y responsive
- ✓ Mobile-first
- ✓ Colores accesibles (WCAG 2.1)
- ✓ Formularios intuitivosy fáciles de usar
- ✓ Tablas con gestión de contenidos
- ✓ Modales para edición
- ✓ Sistema de autenticación simulado

### 🛠️ Lo que puedes mejorar:
- [ ] Agregar editor WYSIWYG avanzado (TinyMCE, Quill)
- [ ] Subir imágenes
- [ ] Previsualización en tiempo real
- [ ] Historial de versiones
- [ ] Sistema de comentarios
- [ ] Importar/exportar contenidos

---

## 📱 Responsive & Mobile

El CMS está diseñado para funcionar en:
- 💻 Escritorio (1200px+)
- 📱 Tablet (768px - 1199px)
- 📲 Móvil (320px - 767px)

**Prueba en móvil:**
- Abre en tu teléfono: `file:///...`
- O usa el DevTools de Chrome (F12) > Toggle device toolbar

---

## 🔒 Seguridad (Prototipo)

**⚠️ NOTA:** Este es un **prototipo educativo**. Para producción:

✅ **Será necesario:**
- [ ] Backend real (Node.js, PHP, Python)
- [ ] Base de datos segura (PostgreSQL, MongoDB)
- [ ] Autenticación con tokens JWT
- [ ] Encriptación de contraseñas (bcrypt)
- [ ] HTTPS/TLS
- [ ] Rate limiting
- [ ] Validación de inputs
- [ ] SQL injection prevention

---

## 📊 Estructura de Archivos

```
WebNovaManager-CMS/
├── admin/                    # Panel de administración
│   ├── index.html           # Login
│   ├── dashboard.html       # Panel principal
│   ├── pages.html           # Gestión de páginas
│   ├── posts.html           # Gestión de artículos
│   ├── categories.html      # (próximamente)
│   └── settings.html        # (próximamente)
│
├── public/                   # Sitio público
│   ├── index.html           # Página de inicio
│   ├── blog.html            # (próximamente)
│   └── page.html            # (próximamente)
│
├── assets/
│   ├── css/
│   │   └── styles.css       # Estilos globales
│   ├── js/
│   │   ├── core.js          # (próximamente)
│   │   ├── admin.js         # (próximamente)
│   │   ├── public.js        # (próximamente)
│   │   └── db.js            # Gestor de BD
│   └── images/              # Imágenes
│
└── docs/
    ├── API.md               # Documentación de API
    ├── MANUAL.md            # Manual completo
    └── ROADMAP.md           # Futuras características
```

---

## 🐛 Solucionar Problemas

### "No me abre el login"
- Verifica la ruta del archivo
- Asegúrate de que el archivo exista
- Prueba con un servidor local:
  ```bash
  python -m http.server 8000
  # Luego: http://localhost:8000/admin/
  ```

### "Pierdo los datos cuando cierro el navegador"
- Los datos se guardan en localStorage
- Limpia el caché del navegador solo si quieres borrar todo

### "Las páginas nuevas no aparecen en el sitio público"
- Asegúrate de que el estado sea **"Publicado"**
- Los borradores no se muestran

### "El formulario no guarda datos"
- Abre la consola (F12)
- Busca si hay errores rojos
- Verifica que todos los campos obligatorios (*) estén rellenos

---

## 📚 Próximos Pasos

### Fase 1 (Actual):
- ✓ Estructura base del CMS
- ✓ CRUD de páginas y artículos
- ✓ Autenticación simulada
- ✓ Interfaz admin

### Fase 2 (TODO):
- [ ] Gestor de archivos/media
- [ ] Editor WYSIWYG avanzado
- [ ] Roles y permisos
- [ ] Comentarios en artículos
- [ ] Sistema de búsqueda avanzado

### Fase 3 (Backend Real):
- [ ] API REST propia
- [ ] Base de datos real
- [ ] Autenticación JWT
- [ ] Hosting en servidor

---

## 👨‍💻 Créditos

**WebNova Manager CMS**
- Proyecto: TFG - Desarrollo de Aplicaciones Web
- Institución: [Tu Centro Educativo]
- Desarrolladores: Carlos, Sergio, Ester
- Fecha: Mayo 2026

---

## 📞 Soporte

¿Preguntas o problemas?
- 📧 Email: contacto@webnova.es
- 💬 Issues: Reporta en el proyecto

---

**¡Que disfrutes usando WebNova Manager!** 🎉

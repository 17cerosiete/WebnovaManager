/**
 * ════════════════════════════════════════════════════════════════
 * WebNova Manager - Database Manager (localStorage wrapper)
 * ════════════════════════════════════════════════════════════════
 * 
 * Este módulo gestiona toda la persistencia de datos en localStorage
 * simulando una base de datos relacional simple.
 */

const DB = {
  // Prefijo para todas las claves de localStorage
  PREFIX: 'webnova_',
  
  /**
   * Obtiene el nombre completo de clave con prefijo
   */
  getKey(name) {
    return `${this.PREFIX}${name}`;
  },
  
  /**
   * Inicializa la base de datos local (ejecutar solo una vez)
   */
  initialize() {
    if (!localStorage.getItem(this.getKey('initialized'))) {
      this.setDefaultData();
      localStorage.setItem(this.getKey('initialized'), 'true');
    }
  
  /**
   * Establece datos por defecto (ejemplo de usuario admin)
   */
  setDefaultData() {
    // Usuario admin
    const users = [
      {
        id: 1,
        email: 'admin@webnova.com',
        password: '0000', // En producción: usar environment variables
        name: 'Administrador',
        role: 'admin',
        createdAt: new Date().toISOString()
    ];
    this.set('users', users);
  
  /**
   * Obtiene un valor de la base de datos local.
   * @param {string} key La clave a obtener.
   * @param {*} defaultValue Valor por defecto si no se encuentra la clave.
   * @returns {*} El valor o el valor por defecto.
   */
  get(key, defaultValue = null) {
    try {
      const data = localStorage.getItem(this.getKey(key));
      return data ? JSON.parse(data) : defaultValue;
    } catch (error) {
      console.error(`Error al obtener ${key}:`, error);
      return defaultValue;
    }
  
  /**
   * Guarda un valor en la base de datos local.
   * @param {string} key La clave.
   * @param {*} data Los datos a guardar.
   * @returns {boolean} True si fue exitoso, false en caso contrario.
   */
  set(key, data) {
    try {
      localStorage.setItem(this.getKey(key), JSON.stringify(data));
      return true;
    } catch (error) {
      console.error(`Error al guardar ${key}:`, error);
      return false;
    }
  
  /**
   * Elimina un registro de la base de datos local.
   * @param {string} key La clave.
   * @returns {boolean} True si fue exitoso, false en caso contrario.
   */
  delete(key) {
    try {
      localStorage.removeItem(this.getKey(key));
      return true;
    } catch (error) {
      console.error(`Error al eliminar ${key}:`, error);
      return false;
    }
  
  /**
   * Intenta iniciar sesión con email y contraseña.
   * @param {string} email El email del usuario.
   * @param {string} password La contraseña ingresada.
   * @returns {object|null} Objeto de sesión si es exitoso, null si falla.
   * 
   * ADVERTENCIA: Esta función solo debe usarse para simular el estado de la sesión 
   * después de que el backend (PHP) haya verificado las credenciales. 
   * NO debe contener la lógica de verificación de contraseñas.
   */
  login(email, password) {
    // En un flujo real, esta función sería llamada después de recibir una respuesta 
    // exitosa del endpoint PHP (auth/login.php).
    
    // Aquí simulamos la obtención de datos del usuario por email (solo para la simulación)
    const users = this.get('users', []);
    const user = users.find(u => u.email === email);
    
    if (user) {
      // Si el login es exitoso (asumiendo que el backend ya lo verificó)
      // Creamos la sesión local.
      const session = {
        user: {
          id: user.id,
          email: user.email,
          name: user.name,
          role: user.role
        },
        token: 'fake_token',
        lastActivity: new Date().toISOString()
      };
      return session;
    }
    return null;
  }
  
  /**
   * Obtiene la sesión actual.
   * @returns {object|null} Los datos de la sesión o null.
   */
  getSession() {
    return this.get('session', null);
  
  /**
   * Cierra la sesión.
   */
  logout() {
    this.set('session', { user: null, token: null, lastActivity: null });
  }
  
  /**
   * Verifica si el usuario está autenticado.
   * @returns {boolean} True si hay sesión activa, false en caso contrario.
   */
  isAuthenticated() {
    const session = this.getSession();
    return session && session.user !== null;
  }
  
  /**
   * Obtiene la lista de páginas.
   * @param {string} [status=null] Filtrar por estado ('published', 'draft').
   * @returns {Array} Lista de páginas.
   */
  getPages(status = null) {
    const pages = this.get('pages', []);
    if (status) {
      return pages.filter(p => p.status === status);
    }
    return pages.sort((a, b) => b.order - a.order);
  }
  
  /**
   * Obtiene una página por ID.
   * @param {number} id El ID de la página.
   * @returns {object|undefined} Los datos de la página o undefined.
   */
  getPageById(id) {
    const pages = this.get('pages', []);
    return pages.find(p => p.id === parseInt(id));
  }
  
  /**
   * Obtiene una página por slug.
   * @param {string} slug El slug de la página.
   * @returns {object|undefined} Los datos de la página o undefined.
   */
  getPageBySlug(slug) {
    const pages = this.get('pages', []);
    return pages.find(p => p.slug === slug);
  }
  
  /**
   * Crea una nueva página.
   * @param {object} pageData Los datos de la página.
   * @returns {object} La página creada con su nuevo ID.
   */
  createPage(pageData) {
    const pages = this.get('pages', []);
    
    // Generar ID
    const newId = Math.max(...pages.map(p => p.id), 0) + 1;
  
    const newPage = {
      id: newId,
      title: pageData.title,
      slug: pageData.slug,
      content: pageData.content,
      status: pageData.status || 'draft',
      order: pages.length + 1,
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString()
    };
    
    pages.push(newPage);
    this.set('pages', pages);
    return newPage;
  }
  
  /**
   * Actualiza una página existente.
   * @param {number} id El ID de la página a actualizar.
   * @param {object} updates Los datos a actualizar.
   * @returns {object|null} La página actualizada o null si no se encontró.
   */
  updatePage(id, updates) {
    const pages = this.get('pages', []);
    const index = pages.findIndex(p => p.id === parseInt(id));
    if (index !== -1) {
      pages[index] = {
        ...pages[index],
        ...updates,
        updatedAt: new Date().toISOString()
      };
      this.set('pages', pages);
      return pages[index];
    }
    return null;
  
  /**
   * Elimina una página por ID.
   * @param {number} id El ID de la página a eliminar.
   * @returns {boolean} True si fue exitoso, false en caso contrario.
   */
  deletePage(id) {
    const pages = this.get('pages', []);
    const filtered = pages.filter(p => p.id !== parseInt(id));
    this.set('pages', filtered);
    return true;
  }
  
  /**
   * Obtiene la lista de widgets.
   * @returns {Array} Lista de widgets.
   */
  getWidgets() {
    return this.get('widgets', []);
  
  /**
   * Añade un widget.
   * @param {object} widgetData Los datos del widget.
   * @returns {object} El widget añadido.
   */
  addWidget(widgetData) {
    const widgets = this.get('widgets', []);
    const newId = Math.max(...widgets.map(w => w.id), 0) + 1;
    
    const newWidget = {
        id: newId,
        title: widgetData.title,
        type: widgetData.type,
        content: widgetData.content
    };
    
    widgets.push(newWidget);
    this.set('widgets', widgets);
    return newWidget;
  
  /**
   * Elimina un widget por ID.
   * @param {number} id El ID del widget a eliminar.
   * @returns {boolean} True si fue exitoso, false en caso contrario.
   */
  deleteWidget(id) {
    const widgets = this.get('widgets', []);
    const filtered = widgets.filter(w => w.id !== id);
    this.set('widgets', filtered);
    return true;
  }
  
  /**
   * Obtiene la lista de posts.
   * @param {string} [status=null] Filtrar por estado.
   * @returns {Array} Lista de posts.
   */
  getPosts(status = null) {
    const posts = this.get('posts', []);
    if (status) {
      return posts.filter(p => p.status === status);
    }
    return posts.sort((a, b) => new Date(b.createdAt) - new Date(a.createdAt));
  }
  
  /**
   * Obtiene posts por categoría.
   * @param {number} categoryId El ID de la categoría.
   * @returns {Array} Lista de posts.
   */
  getPostsByCategory(categoryId) {
    const posts = this.get('posts', []);
    return posts.filter(p => p.categoryId === categoryId && p.status === 'published');
  }
  
  /**
   * Obtiene un post por ID.
   * @param {number} id El ID del post.
   * @returns {object|undefined} Los datos del post o undefined.
   */
  getPostById(id) {
    const posts = this.get('posts', []);
    return posts.find(p => p.id === parseInt(id));
  }
  
  /**
   * Crea un nuevo post.
   * @param {object} post Los datos del post.
   * @returns {object} El post creado con su nuevo ID.
   */
  createPost(post) {
    const posts = this.get('posts', []);
    const newPost = {
      id: Math.max(...posts.map(p => p.id), 0) + 1,
      views: 0,
      comments: 0,
      ...post,
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString()
    };
    
    posts.push(newPost);
    this.set('posts', posts);
    return newPost;
  }
  
  /**
   * Actualiza un post existente.
   * @param {number} id El ID del post.
   * @param {object} updates Los datos a actualizar.
   * @returns {object|null} El post actualizado o null si no se encontró.
   */
  updatePost(id, updates) {
    const posts = this.get('posts', []);
    const index = posts.findIndex(p => p.id === parseInt(id));
    if (index !== -1) {
      posts[index] = {
        ...posts[index],
        ...updates,
        updatedAt: new Date().toISOString()
      };
      this.set('posts', posts);
      return posts[index];
    }
    return null;
  
  /**
   * Elimina un post por ID.
   * @param {number} id El ID del post.
   * @returns {boolean} True si fue exitoso, false en caso contrario.
   */
  deletePost(id) {
    const posts = this.get('posts', []);
    const filtered = posts.filter(p => p.id !== parseInt(id));
    this.set('posts', filtered);
    return true;
  }
  
  /**
   * Obtiene la lista de categorías.
   * @returns {Array} Lista de categorías.
   */
  getCategories() {
    return this.get('categories', []);
  
  /**
   * Obtiene una categoría por ID.
   * @param {number} id El ID de la categoría.
   * @returns {object|undefined} Los datos de la categoría o undefined.
   */
  getCategoryById(id) {
    const categories = this.get('categories', []);
    return categories.find(c => c.id === parseInt(id));
  }
  
  /**
   * Obtiene estadísticas generales del sitio.
   * @returns {object} Estadísticas.
   */
  getStats() {
    const pages = this.getPages();
    const posts = this.getPosts();
    const categories = this.getCategories();
    const users = this.get('users', []);
    
    return {
      totalPages: pages.length,
      publishedPages: pages.filter(p => p.status === 'published').length,
      totalPosts: posts.length,
      publishedPosts: posts.filter(p => p.status === 'published').length,
      totalCategories: categories.length,
      totalUsers: users.length
    };
  
  /**
   * Busca contenido en páginas y posts.
   * @param {string} query El término de búsqueda.
   * @returns {object} Objetos con resultados de páginas y posts.
   */
  search(query) {
    const pages = this.getPages();
    const posts = this.getPosts();
    
    const lowerQuery = query.toLowerCase();
    
    return {
      pages: pages.filter(p => 
        p.title.toLowerCase().includes(lowerQuery) ||
        p.content.toLowerCase().includes(lowerQuery)
      ),
      posts: posts.filter(p => 
        p.title.toLowerCase().includes(lowerQuery) ||
        p.content.toLowerCase().includes(lowerQuery)
      )
    };
  }
};

// Inicializar BD cuando se carga
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => DB.initialize());
} else {
  DB.initialize();
}

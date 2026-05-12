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
   * Inicializa la BD con datos por defecto
   */
  initialize() {
    if (!localStorage.getItem(this.getKey('initialized'))) {
      this.setDefaultData();
      localStorage.setItem(this.getKey('initialized'), 'true');
    }
  },
  
  /**
   * Establece datos por defecto
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
      }
    ];
    this.set('users', users);
    
    // Configuración del sitio
    const settings = {
      siteName: 'WebNova Manager',
      siteDescription: 'Plataforma moderna de gestión de contenidos',
      siteUrl: 'http://localhost:8000',
      logo: '/assets/images/logo.png',
      favicon: '/assets/images/favicon.ico',
      theme: {
        primaryColor: '#2563eb',
        secondaryColor: '#64748b',
        accentColor: '#06b6d4'
      },
      timezone: 'Europe/Madrid',
      language: 'es'
    };
    this.set('settings', settings);
    
    // Páginas
    const pages = [
      {
        id: 1,
        title: 'Inicio',
        slug: 'inicio',
        content: '<h1>Bienvenido a WebNova Manager</h1><p>Este es un prototipo moderno de CMS diseñado para demostrar las capacidades de WebNova Digital.</p>',
        meta_description: 'Página de inicio de WebNova Manager',
        meta_keywords: 'webnova, cms, administrador',
        status: 'published',
        createdAt: new Date().toISOString(),
        updatedAt: new Date().toISOString(),
        order: 1
      },
      {
        id: 2,
        title: 'Acerca de',
        slug: 'acerca-de',
        content: '<h1>Acerca de WebNova</h1><p>WebNova Digital es una empresa líder en desarrollo web con más de 10 años de experiencia.</p>',
        meta_description: 'Información sobre WebNova Digital',
        meta_keywords: 'web, diseño, desarrollo',
        status: 'published',
        createdAt: new Date().toISOString(),
        updatedAt: new Date().toISOString(),
        order: 2
      },
      {
        id: 3,
        title: 'Contacto',
        slug: 'contacto',
        content: '<h1>Contacta con nosotros</h1><p>Email: info@webnova.es | Teléfono: +34 XXX XXX XXX</p>',
        meta_description: 'Página de contacto',
        meta_keywords: 'contacto, correo, teléfono',
        status: 'published',
        createdAt: new Date().toISOString(),
        updatedAt: new Date().toISOString(),
        order: 3
      }
    ];
    this.set('pages', pages);
    
    // Categorías
    const categories = [
      {
        id: 1,
        name: 'Tecnología',
        slug: 'tecnologia',
        description: 'Artículos sobre tecnología web'
      },
      {
        id: 2,
        name: 'Diseño',
        slug: 'diseno',
        description: 'Artículos sobre diseño y UX'
      },
      {
        id: 3,
        name: 'Negocios',
        slug: 'negocios',
        description: 'Artículos sobre estrategia digital'
      }
    ];
    this.set('categories', categories);
    
    // Artículos (Posts)
    const posts = [
      {
        id: 1,
        title: 'Introducción a WebNova Manager',
        slug: 'introduccion-webnova-manager',
        excerpt: 'Descubre las principales características de nuestro nuevo CMS',
        content: '<h2>¿Qué es WebNova Manager?</h2><p>WebNova Manager es un sistema de gestión de contenidos moderno, seguro y fácil de usar diseñado para pequeñas y medianas empresas.</p><p>Está construido con tecnologías web actuales y sigue las mejores prácticas de desarrollo.</p>',
        categoryId: 1,
        author: 'admin@webnova.com',
        status: 'published',
        views: 342,
        comments: 5,
        createdAt: new Date(Date.now() - 86400000).toISOString(),
        updatedAt: new Date(Date.now() - 86400000).toISOString()
      },
      {
        id: 2,
        title: 'Mobile-First: El futuro del desarrollo web',
        slug: 'mobile-first-futuro',
        excerpt: 'Por qué el enfoque mobile-first es esencial en 2024',
        content: '<h2>Mobile-First en 2024</h2><p>Más del 70% de accesos web se realizan desde dispositivos móviles. Esto hace que el enfoque mobile-first no sea una opción, sino una necesidad.</p>',
        categoryId: 1,
        author: 'admin@webnova.com',
        status: 'published',
        views: 128,
        comments: 3,
        createdAt: new Date(Date.now() - 172800000).toISOString(),
        updatedAt: new Date(Date.now() - 172800000).toISOString()
      },
      {
        id: 3,
        title: 'Mejores prácticas de diseño UX',
        slug: 'mejores-practicas-ux',
        excerpt: 'Cómo crear interfaces intuitivas y accesibles',
        content: '<h2>Principios de UX Design</h2><p>El buen diseño es invisible. Los usuarios no deberían pensar en la interfaz, sino usar el producto de forma natural.</p>',
        categoryId: 2,
        author: 'admin@webnova.com',
        status: 'published',
        views: 256,
        comments: 8,
        createdAt: new Date(Date.now() - 259200000).toISOString(),
        updatedAt: new Date(Date.now() - 259200000).toISOString()
      }
    ];
    this.set('posts', posts);
    
    // Sesión actual
    const session = {
      user: null,
      token: null,
      lastActivity: null
    };
    this.set('session', session);
  },
  
  /**
   * Obtiene datos del localStorage
   */
  get(key, defaultValue = null) {
    try {
      const data = localStorage.getItem(this.getKey(key));
      return data ? JSON.parse(data) : defaultValue;
    } catch (error) {
      console.error(`Error al obtener ${key}:`, error);
      return defaultValue;
    }
  },
  
  /**
   * Guarda datos en localStorage
   */
  set(key, data) {
    try {
      localStorage.setItem(this.getKey(key), JSON.stringify(data));
      return true;
    } catch (error) {
      console.error(`Error al guardar ${key}:`, error);
      return false;
    }
  },
  
  /**
   * Elimina datos del localStorage
   */
  delete(key) {
    try {
      localStorage.removeItem(this.getKey(key));
      return true;
    } catch (error) {
      console.error(`Error al eliminar ${key}:`, error);
      return false;
    }
  },
  
  /**
   * Autenticación: Login
   */
  login(email, password) {
    const users = this.get('users', []);
    const user = users.find(u => u.email === email && u.password === password);
    
    if (user) {
      const session = {
        user: {
          id: user.id,
          email: user.email,
          name: user.name,
          role: user.role
        },
        token: `token_${Date.now()}`,
        lastActivity: new Date().toISOString()
      };
      this.set('session', session);
      return session;
    }
    return null;
  },
  
  /**
   * Obtiene la sesión actual
   */
  getSession() {
    return this.get('session', null);
  },
  
  /**
   * Logout
   */
  logout() {
    this.set('session', { user: null, token: null, lastActivity: null });
  },
  
  /**
   * Verifica si el usuario está autenticado
   */
  isAuthenticated() {
    const session = this.getSession();
    return session && session.user !== null;
  },
  
  /**
   * ════════════════════════════════════════════════════════════════
   * OPERACIONES CRUD PARA PÁGINAS
   * ════════════════════════════════════════════════════════════════
   */
  
  // Obtener todas las páginas
  getPages(status = null) {
    const pages = this.get('pages', []);
    if (status) {
      return pages.filter(p => p.status === status);
    }
    return pages.sort((a, b) => b.order - a.order);
  },
  
  // Obtener página por ID
  getPageById(id) {
    const pages = this.get('pages', []);
    return pages.find(p => p.id === parseInt(id));
  },
  
  // Obtener página por slug
  getPageBySlug(slug) {
    const pages = this.get('pages', []);
    return pages.find(p => p.slug === slug);
  },
  
  // Crear página
  createPage(page) {
    const pages = this.get('pages', []);
    const newPage = {
      id: Math.max(...pages.map(p => p.id), 0) + 1,
      ...page,
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString()
    };
    pages.push(newPage);
    this.set('pages', pages);
    return newPage;
  },
  
  // Actualizar página
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
  },
  
  // Eliminar página
  deletePage(id) {
    const pages = this.get('pages', []);
    const filtered = pages.filter(p => p.id !== parseInt(id));
    this.set('pages', filtered);
    return true;
  },
  
  /**
   * ════════════════════════════════════════════════════════════════
   * OPERACIONES CRUD PARA ARTÍCULOS
   * ════════════════════════════════════════════════════════════════
   */
  
  // Obtener todos los artículos
  getPosts(status = null) {
    const posts = this.get('posts', []);
    if (status) {
      return posts.filter(p => p.status === status);
    }
    return posts.sort((a, b) => new Date(b.createdAt) - new Date(a.createdAt));
  },
  
  // Obtener artículos por categoría
  getPostsByCategory(categoryId) {
    const posts = this.get('posts', []);
    return posts.filter(p => p.categoryId === categoryId && p.status === 'published');
  },
  
  // Obtener artículo por ID
  getPostById(id) {
    const posts = this.get('posts', []);
    return posts.find(p => p.id === parseInt(id));
  },
  
  // Crear artículo
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
  },
  
  // Actualizar artículo
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
  },
  
  // Eliminar artículo
  deletePost(id) {
    const posts = this.get('posts', []);
    const filtered = posts.filter(p => p.id !== parseInt(id));
    this.set('posts', filtered);
    return true;
  },
  
  /**
   * ════════════════════════════════════════════════════════════════
   * OPERACIONES PARA CATEGORÍAS
   * ════════════════════════════════════════════════════════════════
   */
  
  // Obtener todas las categorías
  getCategories() {
    return this.get('categories', []);
  },
  
  // Obtener categoría por ID
  getCategoryById(id) {
    const categories = this.get('categories', []);
    return categories.find(c => c.id === parseInt(id));
  },
  
  /**
   * ════════════════════════════════════════════════════════════════
   * ESTADÍSTICAS
   * ════════════════════════════════════════════════════════════════
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
      totalUsers: users.length,
      totalViews: posts.reduce((sum, p) => sum + (p.views || 0), 0)
    };
  },
  
  /**
   * ════════════════════════════════════════════════════════════════
   * BÚSQUEDA
   * ════════════════════════════════════════════════════════════════
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
        p.excerpt.toLowerCase().includes(lowerQuery) ||
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
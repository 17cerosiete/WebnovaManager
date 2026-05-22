import Page from '../../models/page/Page.js';

/**
 * Servicio encargado de manejar la lógica de negocio para la creación y gestión de páginas.
 * Simula la interacción con una capa de persistencia (DB).
 */
class PageService {
    constructor() {
        // Simulación de almacenamiento en memoria (reemplazar con conexión a DB real)
        this.pagesStore = new Map();
    }

    /**
     * Crea una nueva instancia de página.
     * @param {string} title - Título inicial de la página.
     * @returns {Page} La instancia de la página.
     */
    createPage(title) {
        const newPage = new Page(title);
        this.pagesStore.set(newPage.id, newPage);
        return newPage;
    }

    /**
     * Recupera una página por su ID.
     * @param {string} pageId - El ID de la página.
     * @returns {Page | null} La instancia de la página o null si no existe.
     */
    getPageById(pageId) {
        const page = this.pagesStore.get(pageId);
        return page ? new Page(page.toJSON()) : null; // Clonar para evitar mutaciones directas
    }

    /**
     * Guarda la estructura de la página en el almacenamiento.
     * @param {Page} page - La instancia de la página a guardar.
     * @returns {Promise<object>} Promesa que resuelve con la representación JSON guardada.
     */
    async savePage(page) {
        if (!(page instanceof Page)) {
            throw new Error("Debe pasar una instancia válida de Page.");
        }
        // Aquí iría la lógica de conexión a la base de datos (e.g., await db.save(page.toJSON()))
        this.pagesStore.set(page.id, page);
        console.log(`[PageService] Página "${page.title}" guardada exitosamente.`);
        return page.toJSON();
    }

    /**
     * Genera el HTML final de la página para su renderizado en el frontend.
     * @param {Page} page - La instancia de la página.
     * @returns {string} El HTML completo.
     */
    getRenderableHtml(page) {
        return page.renderPageHtml();
    }
}

export default new PageService();

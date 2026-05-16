const PageService = require('../../services/pageService/PageService');
const Page = require('../../models/page/Page');
const TextBlock = require('../blocks/TextBlock');
const ImageBlock = require('../blocks/ImageBlock');
// Se podrían añadir más bloques aquí (ej: ContainerBlock, ButtonBlock)

/**
 * Componente principal que simula la lógica de un editor visual de páginas.
 * Orquesta la creación, manipulación y guardado de la estructura de la página.
 */
class PageBuilderEditor {
    constructor(initialPageTitle = "Nueva Página") {
        // Inicializa la página usando el servicio para asegurar la gestión de estado.
        this.page = PageService.createPage(initialPageTitle);
        console.log(`[PageBuilderEditor] Inicializado el editor para la página: "${this.page.title}"`);
    }

    /**
     * Genera un ID único para un nuevo bloque.
     * @returns {string} Un ID único.
     */
    _generateUniqueId() {
        return Math.random().toString(36).substring(2);
    }

    /**
     * Añade un nuevo bloque a la página basándose en el tipo seleccionado.
     * @param {string} type - El tipo de bloque a añadir ('text', 'image', etc.).
     * @param {object} [initialContent={}] - Contenido inicial para el bloque.
     * @param {object} [styles={}] - Estilos iniciales.
     * @returns {Block} El bloque recién añadido.
     */
    addBlock(type, initialContent = {}, styles = {}) {
        let newBlock;
        const id = this._generateUniqueId();

        // Lógica de fábrica para instanciar el bloque correcto
        switch (type) {
            case 'text':
                newBlock = new TextBlock(id, initialContent.content || "Escribe tu texto aquí...", styles);
                break;
            case 'image':
                newBlock = new ImageBlock(id, initialContent.src || '', initialContent.alt || '', styles);
                break;
            // Aquí se añadirían más tipos de bloques (ej: 'container', 'button')
            default:
                console.error(`Tipo de bloque desconocido: ${type}`);
                return null;
        }

        this.page.addBlock(newBlock);
        console.log(`[PageBuilderEditor] Bloque de tipo "${type}" añadido con éxito.`);
        return newBlock;
    }

    /**
     * Elimina un bloque de la página por su ID.
     * @param {string} blockId - El ID del bloque a eliminar.
     * @returns {boolean} True si se eliminó, False si no se encontró.
     */
    removeBlock(blockId) {
        const success = this.page.removeBlock(blockId);
        if (success) {
            console.log(`[PageBuilderEditor] Bloque con ID ${blockId} eliminado.`);
        } else {
            console.warn(`[PageBuilderEditor] No se encontró ningún bloque con ID ${blockId}.`);
        }
        return success;
    }

    /**
     * Actualiza el contenido de un bloque existente.
     * @param {string} blockId - El ID del bloque a modificar.
     * @param {string} key - La clave del contenido a actualizar (ej: 'content', 'src').
     * @param {string} value - El nuevo valor.
     * @returns {boolean} True si la actualización fue exitosa.
     */
    updateBlockContent(blockId, key, value) {
        const block = this.page.blocks.find(b => b.id === blockId);
        if (!block) {
            console.error(`[PageBuilderEditor] Bloque no encontrado para actualizar: ${blockId}`);
            return false;
        }

        // Actualización genérica del contenido. Los bloques específicos deberían tener métodos de actualización.
        if (block.content && block.content[key] !== undefined) {
            block.content[key] = value;
            console.log(`[PageBuilderEditor] Contenido del bloque ${blockId} actualizado: ${key} = "${value}"`);
            return true;
        } else {
            console.error(`[PageBuilderEditor] No se pudo actualizar la propiedad ${key} en el bloque ${blockId}.`);
            return false;
        }
    }

    /**
     * Guarda la estructura completa de la página en el servicio de persistencia.
     * @returns {Promise<object>} La representación JSON guardada de la página.
     */
    async savePage() {
        try {
            // El servicio maneja la lógica de persistencia y el logging.
            const jsonData = await PageService.savePage(this.page);
            console.log("\n===================================================");
            console.log("✅ PÁGINA GUARDADA EXITOSAMENTE.");
            console.log("JSON de la página guardada:", JSON.stringify(jsonData, null, 2));
            console.log("===================================================\n");
            return jsonData;
        } catch (error) {
            console.error("[PageBuilderEditor] Error al guardar la página:", error.message);
            throw error;
        }
    }

    /**
     * Genera el HTML final de la página para su previsualización.
     * @returns {string} El HTML completo.
     */
    getPreviewHtml() {
        return this.page.renderPageHtml();
    }
}

module.exports = PageBuilderEditor;

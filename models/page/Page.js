const Block = require('./Block');

/**
 * Representa la estructura completa de una página.
 * Contiene un array de bloques que definen el contenido.
 */
class Page {
    constructor(title = "Nueva Página", blocks = []) {
        this.id = Math.random().toString(36).substring(2); // ID simple para ejemplo
        this.title = title;
        this.slug = title.toLowerCase().replace(/\s/g, '-');
        this.blocks = blocks; // Array de instancias de Block
    }

    /**
     * Añade un bloque a la página.
     * @param {Block} blockInstance - La instancia del bloque a añadir.
     */
    addBlock(blockInstance) {
        if (!(blockInstance instanceof Block)) {
            throw new Error("El elemento a añadir debe ser una instancia de Block.");
        }
        this.blocks.push(blockInstance);
    }

    /**
     * Elimina un bloque por su ID.
     * @param {string} blockId - El ID del bloque a eliminar.
     * @returns {boolean} True si se eliminó, False si no se encontró.
     */
    removeBlock(blockId) {
        const initialLength = this.blocks.length;
        this.blocks = this.blocks.filter(block => block.id !== blockId);
        return this.blocks.length < initialLength;
    }

    /**
     * Genera el HTML completo de la página renderizando todos los bloques.
     * @returns {string} El HTML completo.
     */
    renderPageHtml() {
        let html = '';
        this.blocks.forEach(block => {
            try {
                html += block.renderHtml();
            } catch (e) {
                console.error(`Error al renderizar el bloque ${block.id}:`, e.message);
                // En producción, aquí se manejaría el error de forma más elegante.
            }
        });
        return html;
    }

    /**
     * Serializa la página completa a un objeto JSON para almacenamiento en la base de datos.
     * @returns {object} Objeto JSON de la página.
     */
    toJSON() {
        return {
            id: this.id,
            title: this.title,
            slug: this.slug,
            blocks: this.blocks.map(block => block.toJSON())
        };
    }
}

module.exports = Page;

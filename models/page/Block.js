/**
 * Clase base para representar cualquier bloque de contenido en la página.
 * Todos los bloques específicos (Texto, Imagen, etc.) deben extender esta clase.
 */
class Block {
    constructor(id, type, content = {}, styles = {}) {
        this.id = id;
        this.type = type;
        this.content = content;
        this.styles = styles;
    }

    /**
     * Genera el HTML estructurado para este bloque.
     * Debe ser implementado por las subclases.
     * @returns {string} El HTML generado.
     */
    renderHtml() {
        throw new Error("Método renderHtml debe ser implementado por la subclase.");
    }

    /**
     * Serializa el bloque a un objeto JSON para almacenamiento.
     * @returns {object} Objeto JSON del bloque.
     */
    toJSON() {
        return {
            id: this.id,
            type: this.type,
            content: this.content,
            styles: this.styles
        };
    }
}

module.exports = Block;

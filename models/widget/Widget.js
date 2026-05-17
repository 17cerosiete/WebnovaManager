/**
 * Clase base para representar cualquier widget reutilizable del CMS.
 * Los widgets son bloques especializados que pueden ser reutilizados en múltiples páginas.
 */
class Widget {
    constructor(id, type, name, config = {}, styles = {}) {
        this.id = id;
        this.type = type; // Ej: 'hero', 'cta', 'text'
        this.name = name; // Nombre legible para el administrador
        this.config = config; // Configuración editable (ej: texto, URL, imagen)
        this.styles = styles;
    }

    /**
     * Genera el HTML estructurado para este widget.
     * Debe ser implementado por las subclases.
     * @returns {string} El HTML generado.
     */
    renderHtml() {
        throw new Error("Método renderHtml debe ser implementado por la subclase.");
    }

    /**
     * Serializa el widget a un objeto JSON para almacenamiento.
     * @returns {object} Objeto JSON del widget.
     */
    toJSON() {
        return {
            id: this.id,
            type: this.type,
            name: this.name,
            config: this.config,
            styles: this.styles
        };
    }
}

module.exports = Widget;

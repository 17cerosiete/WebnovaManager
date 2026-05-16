const Block = require('../../models/page/Block');

/**
 * Clase base para todos los componentes de bloques.
 * Proporciona métodos utilitarios comunes.
 */
class BaseBlock extends Block {
    constructor(id, type, content = {}, styles = {}) {
        super(id, type, content, styles);
    }

    /**
     * Método placeholder para el renderizado. Debe ser sobrescrito.
     * @returns {string} El HTML generado.
     */
    renderHtml() {
        // Implementación por defecto, debe ser sobreescrita.
        return `<div class="block block-${this.type}" style="${this.styles.css || ''}">
            <!-- Contenido genérico para ${this.type} -->
        </div>`;
    }
}

module.exports = BaseBlock;

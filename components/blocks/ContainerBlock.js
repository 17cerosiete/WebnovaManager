import BaseBlock from './BaseBlock.js';

/**
 * Bloque contenedor. No tiene contenido visible, sino que aplica estilos
 * de agrupación y estructura a los bloques internos.
 * En un sistema real, este bloque podría gestionar un array de IDs de bloques hijos.
 */
class ContainerBlock extends BaseBlock {
    constructor(id, styles = {}) {
        // El contenido es vacío, ya que su función es estructural.
        super(id, 'container', { children: [] }, styles);
    }

    /**
     * Renderiza el contenedor aplicando los estilos de agrupación.
     * @returns {string} El HTML del contenedor.
     */
    renderHtml() {
        // Usamos un div con una clase específica para que el CSS lo maneje.
        return `<div class="block block-container" data-id="${this.id}" style="${this.styles.css || ''}">
            <!-- Contenido de los bloques hijos se renderizará aquí -->
        </div>`;
    }
}

export default ContainerBlock;

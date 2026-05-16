const BaseBlock = require('./BaseBlock');

/**
 * Bloque para incrustar imágenes.
 */
class ImageBlock extends BaseBlock {
    constructor(id, src = '', alt = '', styles = {}) {
        super(id, 'image', { src: src, alt: alt }, styles);
    }

    renderHtml() {
        const src = this.content.src;
        const alt = this.content.alt;
        return `
            <div class="block block-image" style="${this.styles.css || ''}">
                <img src="${src}" alt="${alt}" style="max-width: 100%; height: auto;">
            </div>
        `;
    }
}

module.exports = ImageBlock;

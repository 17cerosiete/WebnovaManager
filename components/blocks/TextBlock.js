import BaseBlock from './BaseBlock.js';

/**
 * Bloque de texto simple.
 */
class TextBlock extends BaseBlock {
    constructor(id, content = "Escribe tu texto aquí...", styles = {}) {
        super(id, 'text', { content: content }, styles);
    }

    renderHtml() {
        const safeContent = this.content.replace(/</g, "&lt;").replace(/>/g, "&gt;");
        return `
            <div class="block block-text" data-id="${this.id}" style="${this.styles.css || ''}">
                <p>${safeContent}</p>
            </div>
        `;
    }
}

export default TextBlock;

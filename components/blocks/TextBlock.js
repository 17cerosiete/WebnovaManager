const BaseBlock = require('./BaseBlock');

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
            <div class="block block-text" style="${this.styles.css || ''}">
                <p>${safeContent}</p>
            </div>
        `;
    }
}

module.exports = TextBlock;

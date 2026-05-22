import Widget from '../../models/widget/Widget.js';

/**
 * Widget de texto enriquecido.
 */
class TextWidget extends Widget {
    constructor(id, type, name, config = {}, styles = {}) {
        super(id, type, name, config, styles);
    }

    renderHtml() {
        const safeContent = this.config.content.replace(/</g, "&lt;").replace(/>/g, "&gt;");
        return `
            <div class="widget widget-text" data-id="${this.id}" style="${this.styles.css || ''}">
                <div class="widget-content">
                    <p>${safeContent}</p>
                </div>
            </div>
        `;
    }
}
export default TextWidget;

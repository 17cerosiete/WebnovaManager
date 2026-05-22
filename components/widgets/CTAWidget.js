import Widget from '../../models/widget/Widget.js';

/**
 * Widget de Llamada a la Acción (Call to Action).
 */
class CTAWidget extends Widget {
    constructor(id, type, name, config = {}, styles = {}) {
        super(id, type, name, config, styles);
    }

    renderHtml() {
        const { buttonText, buttonUrl } = this.config;
        return `
            <div class="widget widget-cta" data-id="${this.id}" style="${this.styles.css || ''}">
                <p>¿Listo para empezar?</p>
                <a href="${buttonUrl}" class="btn-cta">${buttonText}</a>
            </div>
        `;
    }
}
export default CTAWidget;

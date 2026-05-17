const Widget = require('../../models/widget/Widget');

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
            <div class="widget widget-cta" style="${this.styles.css || ''}">
                <p>¿Listo para empezar?</p>
                <a href="${buttonUrl}" class="btn-cta">${buttonText}</a>
            </div>
        `;
    }
}

import Widget from '../../models/widget/Widget.js';

/**
 * Widget para banners de héroe.
 */
class HeroWidget extends Widget {
    constructor(id, type, name, config = {}, styles = {}) {
        super(id, type, name, config, styles);
    }

    renderHtml() {
        const { headline, subtitle } = this.config;
        return `
            <div class="widget widget-hero" data-id="${this.id}" style="${this.styles.css || ''}">
                <div class="hero-content">
                    <h1>${headline}</h1>
                    <p>${subtitle}</p>
                    <a href="#" class="btn-primary">¡Ver más!</a>
                </div>
            </div>
        `;
    }
}

export default HeroWidget;

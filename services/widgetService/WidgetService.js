const Widget = require('../../models/widget/Widget');
const HeroWidget = require('../../components/widgets/HeroWidget');
const CTAWidget = require('../../components/widgets/CTAWidget');
const TextWidget = require('../../components/widgets/TextWidget');

/**
 * Servicio encargado de gestionar el catálogo de widgets reutilizables.
 * Simula la interacción con una capa de persistencia de widgets.
 */
class WidgetService {
    constructor() {
        // Almacenamiento de widgets por ID
        this.widgetsStore = new Map();
        this.initializeDefaultWidgets();
    }

    /**
     * Inicializa widgets de ejemplo para la demostración.
     */
    initializeDefaultWidgets() {
        // Creamos un widget de ejemplo de Hero
        const heroId = 'widget-hero-1';
        const hero = new HeroWidget(heroId, 'hero', 'Banner Principal', { headline: 'Bienvenido a nuestro CMS', subtitle: 'Construye páginas potentes.' }, {});
        this.widgetsStore.set(heroId, hero);

        // Creamos un widget de ejemplo de CTA
        const ctaId = 'widget-cta-1';
        const cta = new CTAWidget(ctaId, 'cta', 'Llamada a la Acción', { buttonText: 'Contáctanos Hoy', buttonUrl: '#' }, {});
        this.widgetsStore.set(ctaId, cta);
        
        // Creamos un widget de texto genérico
        const textId = 'widget-text-1';
        const text = new TextWidget(textId, 'text', 'Texto de Contenido', { content: 'Este es un texto reutilizable.' }, {});
        this.widgetsStore.set(textId, text);
    }

    /**
     * Obtiene un widget por su ID.
     * @param {string} widgetId - El ID del widget.
     * @returns {Widget | null} La instancia del widget o null.
     */
    getWidgetById(widgetId) {
        const widget = this.widgetsStore.get(widgetId);
        return widget ? new (widget.constructor) (widget.id, widget.type, widget.name, widget.config, widget.styles) : null;
    }

    /**
     * Lista todos los widgets disponibles para el constructor.
     * @returns {Array<Widget>} Lista de widgets.
     */
    listWidgets() {
        return Array.from(this.widgetsStore.values());
    }

    /**
     * Simula la creación o actualización de un widget.
     * @param {Widget} widgetInstance - La instancia del widget a guardar.
     * @returns {Widget} El widget guardado.
     */
    saveWidget(widgetInstance) {
        if (!(widgetInstance instanceof Widget)) {
            throw new Error("Debe pasar una instancia válida de Widget.");
        }
        this.widgetsStore.set(widgetInstance.id, widgetInstance);
        console.log(`[WidgetService] Widget "${widgetInstance.name}" guardado/actualizado exitosamente.`);
        return widgetInstance;
    }
}

module.exports = new WidgetService();

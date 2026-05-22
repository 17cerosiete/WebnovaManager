import Widget from '../../models/widget/Widget.js';
import HeroWidget from '../../components/widgets/HeroWidget.js';
import CTAWidget from '../../components/widgets/CTAWidget.js';
import TextWidget from '../../components/widgets/TextWidget.js';

/**
 * Servicio encargado de gestionar el catálogo de widgets reutilizables.
 * Simula la interacción con una capa de persistencia de widgets.
 */
class WidgetService {
    constructor() {
        this.widgetsStore = new Map();
        this.fetchWidgets();
    }

    /**
     * Inicializa widgets de ejemplo para la demostración.
     */
    async fetchWidgets() {
        try {
            const response = await fetch('../../api/widgets.php');
            const widgets = await response.json();
            widgets.forEach(w => {
                this.widgetsStore.set(w.id.toString(), w);
            });
            console.log(`[WidgetService] ${widgets.length} widgets cargados desde la API.`);
        } catch (e) {
            console.error("[WidgetService] Error cargando widgets:", e);
        }
    }

    /**
     * Obtiene un widget por su ID.
     * @param {string} widgetId - El ID del widget.
     * @returns {Widget | null} La instancia del widget o null.
     */
    getWidgetById(widgetId) {
        const id = widgetId.toString();
        const widget = this.widgetsStore.get(id);
        return widget ? widget : null;
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

export default new WidgetService();

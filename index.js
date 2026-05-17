// index.js: Punto de entrada de demostración del Page Builder
const PageBuilderEditor = require('./components/page-builder/PageBuilderEditor');
const TextBlock = require('./components/blocks/TextBlock');
const ImageBlock = require('./components/blocks/ImageBlock');
const ContainerBlock = require('./components/blocks/ContainerBlock');
const WidgetService = require('./services/widgetService/WidgetService'); // <-- IMPORTACIÓN AGREGADA

async function runPageBuilderDemo() {
    console.log("===================================================================================");
    console.log("🚀 INICIANDO DEMOSTRACIÓN DEL CONSTRUCTOR DE PÁGINAS (V2 - CON WIDGETS REUTILIZABLES)");
    console.log("===================================================================================");

    // ====================================================================================
    // FASE 1: DEMOSTRACIÓN DE GESTIÓN DE WIDGETS (AÑADIR ARTÍCULO)
    // ====================================================================================
    console.log("\n\n===================================================================================");
    console.log("⚙️ FASE 1: GESTIÓN DE WIDGETS (CRUD DE MÓDULOS REUTILIZABLES)");
    console.log("===================================================================================");

    // 1. Obtener el widget de Hero (ya existe por defecto)
    const heroWidget = WidgetService.getWidgetById('widget-hero-1');
    console.log(`\n[WidgetService] Widget Hero cargado: ${heroWidget.name}`);
    
    // 2. Simular la actualización de un widget (Ej: cambiar el subtítulo)
    console.log("\n--- Actualizando Widget ---");
    heroWidget.config.subtitle = "¡Ahora con la nueva versión 2.0!";
    WidgetService.saveWidget(heroWidget);

    // 3. Obtener el widget de CTA
    const ctaWidget = WidgetService.getWidgetById('widget-cta-1');
    console.log(`\n[WidgetService] Widget CTA cargado: ${ctaWidget.name}`);
    
    // 4. Simular la creación de un widget nuevo (Ej: un widget de testimonios)
    const newWidgetId = 'widget-testimonial-1';
    const newWidget = require('./models/widget/Widget').constructor(
        newWidgetId, 
        'testimonial', 
        'Testimonio de Cliente', 
        { quote: '¡Excelente servicio!', author: 'Juan Pérez' }, 
        {}
    );
    WidgetService.saveWidget(newWidget);
    console.log(`[WidgetService] Widget de Testimonio creado con ID: ${newWidgetId}`);


    // ====================================================================================
    // FASE 2: DEMOSTRACIÓN DEL PAGE BUILDER USANDO WIDGETS
    // ====================================================================================
    console.log("\n\n===================================================================================");
    console.log("🏗️ FASE 2: CONSTRUCTOR DE PÁGINAS (USANDO WIDGETS)");
    console.log("===================================================================================");

    // 1. Inicialización del Editor
    const editor = new PageBuilderEditor("Página con Widgets Reutilizables");
    let page = editor.page;

    console.log("\n--- PASO 1: AÑADIENDO WIDGETS Y BLOQUES ---");

    // 2. Añadir el widget Hero (usando su ID)
    const heroBlock = editor.addBlock('widget', { widgetId: 'widget-hero-1' });
    
    // 3. Añadir un contenedor estructural
    const container = editor.addBlock('container', {}, { css: 'padding: 20px;' });
    
    // 4. Añadir un widget de texto (usando su ID)
    const textWidgetBlock = editor.addBlock('widget', { widgetId: 'widget-text-1' });
    
    // 5. Añadir un widget de Testimonio (el que acabamos de crear)
    const testimonialBlock = editor.addBlock('widget', { widgetId: 'widget-testimonial-1' });

    // 6. Añadir un widget CTA
    const ctaBlock = editor.addBlock('widget', { widgetId: 'widget-cta-1' });


    // 7. Demostrar la actualización de contenido (Editar el texto del widget de texto)
    console.log("\n--- PASO 2: ACTUALIZANDO CONTENIDO ---");
    editor.updateBlockContent(textWidgetBlock.id, 'content', "Este texto fue actualizado después de usar el widget.");

    // 8. Demostrar el guardado de la página
    console.log("\n--- PASO 3: GUARDANDO LA PÁGINA ---");
    try {
        await editor.savePage();
    } catch (e) {
        console.error("Fallo al guardar la página.");
    }

    // 9. Demostrar la previsualización del HTML final
    console.log("\n===================================================================================");
    console.log("✨ PREVISUALIZACIÓN DEL HTML FINAL GENERADO:");
    console.log("===================================================================================");
    const htmlOutput = editor.getPreviewHtml();
    console.log(htmlOutput);
    console.log("===================================================================================");
    console.log("✅ DEMOSTRACIÓN COMPLETADA. Widgets integrados con éxito.");
}

runPageBuilderDemo();

// index.js: Punto de entrada de demostración del Page Builder
const PageBuilderEditor = require('./components/page-builder/PageBuilderEditor');
const TextBlock = require('./components/blocks/TextBlock');
const ImageBlock = require('./components/blocks/ImageBlock');

async function runPageBuilderDemo() {
    console.log("=================================================================");
    console.log("🚀 INICIANDO DEMOSTRACIÓN DEL CONSTRUCTOR DE PÁGINAS (V1)");
    console.log("=================================================================");

    // 1. Inicialización del Editor
    const editor = new PageBuilderEditor("Mi Primera Página Web");
    let page = editor.page;

    console.log("\n--- PASO 1: AÑADIENDO BLOQUES INICIALES ---");

    // 2. Añadir un bloque de texto inicial
    const textBlock1 = editor.addBlock('text', { content: "Bienvenido a nuestro nuevo CMS. Este es un bloque de texto inicial." });
    
    // 3. Añadir un bloque de imagen
    const imageBlock = editor.addBlock('image', { src: 'https://via.placeholder.com/800x400', alt: 'Imagen de ejemplo' });

    // 4. Añadir otro bloque de texto
    const textBlock2 = editor.addBlock('text', { content: "Aquí podemos añadir más contenido estructurado." });

    // 5. Demostrar la actualización de contenido (Editar el texto del primer bloque)
    console.log("\n--- PASO 2: ACTUALIZANDO CONTENIDO ---");
    editor.updateBlockContent(textBlock1.id, 'content', "¡El contenido ha sido actualizado exitosamente!");

    // 6. Demostrar la eliminación de un bloque (Eliminar la imagen)
    console.log("\n--- PASO 3: ELIMINANDO BLOQUES ---");
    editor.removeBlock(imageBlock.id);

    // 7. Demostrar el guardado de la página
    console.log("\n--- PASO 4: GUARDANDO LA PÁGINA ---");
    try {
        await editor.savePage();
    } catch (e) {
        console.error("Fallo al guardar la página.");
    }

    // 8. Demostrar la previsualización del HTML final
    console.log("\n=================================================================");
    console.log("✨ PREVISUALIZACIÓN DEL HTML FINAL GENERADO:");
    console.log("=================================================================");
    const htmlOutput = editor.getPreviewHtml();
    console.log(htmlOutput);
    console.log("=================================================================");
    console.log("✅ DEMOSTRACIÓN COMPLETADA.");
}

runPageBuilderDemo();

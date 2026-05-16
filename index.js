// index.js: Punto de entrada de demostración del Page Builder
const PageBuilderEditor = require('./components/page-builder/PageBuilderEditor');
const TextBlock = require('./components/blocks/TextBlock');
const ImageBlock = require('./components/blocks/ImageBlock');
const ContainerBlock = require('./components/blocks/ContainerBlock'); // <-- IMPORTACIÓN AGREGADA

async function runPageBuilderDemo() {
    console.log("=================================================================");
    console.log("🚀 INICIANDO DEMOSTRACIÓN DEL CONSTRUCTOR DE PÁGINAS (V1.1 - CON CONTENEDORES)");
    console.log("=================================================================");

    // 1. Inicialización del Editor
    const editor = new PageBuilderEditor("Mi Página Estructurada");
    let page = editor.page;

    console.log("\n--- PASO 1: AÑADIENDO BLOQUES ESTRUCTURALES ---");

    // 2. Añadir un contenedor principal
    const container1 = editor.addBlock('container', {}, { css: 'padding: 20px; border: 1px solid #ccc;' });
    
    // 3. Añadir un bloque de texto dentro del contenedor
    const textBlock1 = editor.addBlock('text', { content: "Bienvenido a nuestro nuevo CMS. Este contenido está dentro de un contenedor." });
    
    // 4. Añadir un bloque de imagen
    const imageBlock = editor.addBlock('image', { src: 'https://via.placeholder.com/800x400', alt: 'Imagen de ejemplo' });

    // 5. Añadir otro contenedor (simulando una sección de columnas)
    const container2 = editor.addBlock('container', {}, { css: 'display: flex; justify-content: space-between; padding: 20px;' });
    
    // 6. Añadir un bloque de texto dentro del segundo contenedor
    const textBlock2 = editor.addBlock('text', { content: "Esta es la columna de texto." });
    
    // 7. Añadir otro bloque de texto para simular la segunda columna
    const textBlock3 = editor.addBlock('text', { content: "Y esta es la columna de contenido secundario." });


    // 8. Demostrar la actualización de contenido (Editar el texto del primer bloque)
    console.log("\n--- PASO 2: ACTUALIZANDO CONTENIDO ---");
    editor.updateBlockContent(textBlock1.id, 'content', "¡El contenido ha sido actualizado y ahora está mejor estructurado!");

    // 9. Demostrar la eliminación de un bloque (Eliminar la imagen)
    console.log("\n--- PASO 3: ELIMINANDO BLOQUES ---");
    editor.removeBlock(imageBlock.id);

    // 10. Demostrar el guardado de la página
    console.log("\n--- PASO 4: GUARDANDO LA PÁGINA ---");
    try {
        await editor.savePage();
    } catch (e) {
        console.error("Fallo al guardar la página.");
    }

    // 11. Demostrar la previsualización del HTML final
    console.log("\n=================================================================");
    console.log("✨ PREVISUALIZACIÓN DEL HTML FINAL GENERADO:");
    console.log("==================================================================");
    const htmlOutput = editor.getPreviewHtml();
    console.log(htmlOutput);
    console.log("=================================================================");
    console.log("✅ DEMOSTRACIÓN COMPLETADA.");
}

runPageBuilderDemo();

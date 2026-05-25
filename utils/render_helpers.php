<?php
function webnova_h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function webnova_json_array($value) {
    if (is_array($value)) {
        return $value;
    }

    if (!is_string($value) || trim($value) === '') {
        return [];
    }

    $decoded = json_decode($value, true);
    if (is_string($decoded)) {
        $decoded = json_decode($decoded, true);
    }

    return is_array($decoded) ? $decoded : [];
}

function webnova_render_widget($type, $config) {
    $config = webnova_json_array($config);
    $type = strtolower((string)$type);
    $theme = preg_replace('/[^a-z0-9_-]/i', '', $config['theme'] ?? 'blue');

    if ($type === 'hero') {
        $title = $config['title'] ?? 'Nueva seccion destacada';
        $subtitle = $config['subtitle'] ?? 'Contenido destacado creado con WebNova Manager.';
        $buttonText = $config['buttonText'] ?? 'Saber mas';
        $buttonUrl = $config['buttonUrl'] ?? '#';
        $imageUrl = $config['imageUrl'] ?? '';
        $imageAlt = $config['imageAlt'] ?? '';

        $media = $imageUrl !== '' ? '<img src="' . webnova_h($imageUrl) . '" alt="' . webnova_h($imageAlt) . '">' : '';
        return '<section class="wn-hero wn-theme-' . webnova_h($theme) . '"><div><h1>' . webnova_h($title) . '</h1><p>' . webnova_h($subtitle) . '</p><a href="' . webnova_h($buttonUrl) . '">' . webnova_h($buttonText) . '</a></div>' . $media . '</section>';
    }

    if ($type === 'cta') {
        $title = $config['title'] ?? 'Impulsa tu presencia digital';
        $text = $config['text'] ?? 'Contacta con nosotros hoy mismo';
        $buttonText = $config['buttonText'] ?? 'Contactar';
        $buttonUrl = $config['buttonUrl'] ?? '#';

        return '<section class="wn-cta wn-theme-' . webnova_h($theme) . '"><div><h2>' . webnova_h($title) . '</h2><p>' . webnova_h($text) . '</p></div><a href="' . webnova_h($buttonUrl) . '">' . webnova_h($buttonText) . '</a></section>';
    }

    if ($type === 'faq') {
        $items = $config['items'] ?? [];
        if (!is_array($items) || count($items) === 0) {
            $items = [['q' => 'Pregunta frecuente', 'a' => 'Respuesta breve para el cliente.']];
        }

        $html = '<section class="wn-faq">';
        foreach ($items as $item) {
            $html .= '<details><summary>' . webnova_h($item['q'] ?? 'Pregunta') . '</summary><p>' . webnova_h($item['a'] ?? '') . '</p></details>';
        }
        return $html . '</section>';
    }

    if ($type === 'features') {
        $items = $config['items'] ?? [];
        if (!is_array($items) || count($items) === 0) {
            $items = [
                ['title' => 'Usabilidad', 'text' => 'Interfaz clara para gestionar contenidos.'],
                ['title' => 'Responsive', 'text' => 'Bloques preparados para movil y tablet.'],
                ['title' => 'SEO', 'text' => 'Estructura preparada para publicar paginas visibles.'],
            ];
        }

        $html = '<section class="wn-features"><h2>' . webnova_h($config['title'] ?? 'Servicios destacados') . '</h2><div>';
        foreach ($items as $item) {
            $html .= '<article><h3>' . webnova_h($item['title'] ?? 'Ventaja') . '</h3><p>' . webnova_h($item['text'] ?? '') . '</p></article>';
        }
        return $html . '</div></section>';
    }

    if ($type === 'testimonial') {
        return '<section class="wn-testimonial"><blockquote>' . webnova_h($config['quote'] ?? 'Una experiencia clara, rapida y profesional.') . '</blockquote><p>' . webnova_h($config['author'] ?? 'Cliente WebNova') . '</p><span>' . webnova_h($config['role'] ?? 'PYME') . '</span></section>';
    }

    if ($type === 'metrics') {
        $items = $config['items'] ?? [];
        if (!is_array($items) || count($items) === 0) {
            $items = [
                ['value' => '70%', 'label' => 'Accesos desde movil'],
                ['value' => '24/7', 'label' => 'Disponibilidad online'],
                ['value' => 'SEO', 'label' => 'Contenido preparado para buscadores'],
            ];
        }

        $html = '<section class="wn-metrics">';
        foreach ($items as $item) {
            $html .= '<article><strong>' . webnova_h($item['value'] ?? '') . '</strong><span>' . webnova_h($item['label'] ?? '') . '</span></article>';
        }
        return $html . '</section>';
    }

    if ($type === 'gallery') {
        $items = $config['items'] ?? [];
        $html = '<section class="wn-gallery"><h2>' . webnova_h($config['title'] ?? 'Galeria') . '</h2><div>';
        foreach ($items as $item) {
            $src = $item['src'] ?? '';
            if ($src !== '') {
                $html .= '<figure><img src="' . webnova_h($src) . '" alt="' . webnova_h($item['alt'] ?? '') . '"><figcaption>' . webnova_h($item['alt'] ?? '') . '</figcaption></figure>';
            }
        }
        return $html . '</div></section>';
    }

    $content = $config['content'] ?? $config['text'] ?? 'Texto del widget';
    return '<section class="wn-text"><p>' . nl2br(webnova_h($content)) . '</p></section>';
}

function webnova_render_blocks($blocks) {
    $blocks = webnova_json_array($blocks);
    $html = '';

    foreach ($blocks as $block) {
        $type = $block['type'] ?? 'text';
        $content = $block['content'] ?? [];

        if ($type === 'text') {
            $text = is_array($content) ? ($content['content'] ?? '') : $content;
            $html .= '<section class="wn-text"><p>' . nl2br(webnova_h($text)) . '</p></section>';
            continue;
        }

        if ($type === 'image') {
            $src = is_array($content) ? ($content['src'] ?? '') : '';
            $alt = is_array($content) ? ($content['alt'] ?? '') : '';
            if ($src !== '') {
                $html .= '<figure class="wn-image"><img src="' . webnova_h($src) . '" alt="' . webnova_h($alt) . '"><figcaption>' . webnova_h($alt) . '</figcaption></figure>';
            }
            continue;
        }

        if ($type === 'container') {
            $title = is_array($content) ? ($content['title'] ?? 'Seccion') : 'Seccion';
            $text = is_array($content) ? ($content['content'] ?? '') : '';
            $html .= '<section class="wn-panel"><h2>' . webnova_h($title) . '</h2><p>' . nl2br(webnova_h($text)) . '</p></section>';
            continue;
        }

        if ($type === 'widget') {
            $widgetType = is_array($content) ? ($content['tipo'] ?? $content['type'] ?? 'text') : 'text';
            $config = is_array($content) ? ($content['config'] ?? []) : [];
            $html .= webnova_render_widget($widgetType, $config);
        }
    }

    return $html !== '' ? $html : '<section class="wn-empty"><p>Esta pagina aun no tiene bloques publicados.</p></section>';
}
?>

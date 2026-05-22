-- =====================================================
-- TABLA: WIDGETS (Módulos reutilizables)
-- =====================================================
CREATE TABLE IF NOT EXISTS widgets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL COMMENT 'Nombre del widget',
  tipo VARCHAR(50) NOT NULL COMMENT 'Tipo de widget (hero, cta, faq, etc)',
  config LONGTEXT NOT NULL COMMENT 'Configuración serializada en JSON',
  preview_html TEXT COMMENT 'HTML previsualización rápida',
  fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_tipo (tipo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Módulos reutilizables del CMS';

-- Insertar algunos widgets básicos para pruebas
INSERT INTO widgets (nombre, tipo, config, preview_html) VALUES
('Hero Principal', 'hero', '{"title": "Bienvenidos a WebNova", "subtitle": "Creamos tu web ideal", "buttonText": "Empezar ahora", "bgImage": "hero.jpg"}', '<div class="hero"><h1>Bienvenidos...</h1></div>'),
('CTA Contacto', 'cta', '{"text": "Contacta con nosotros hoy mismo", "buttonText": "Enviar Mensaje", "color": "blue"}', '<div class="cta">Contacta con nosotros...</div>'),
('FAQ Servicios', 'faq', '{"items": [{"q": "¿Qué hacemos?", "a": "Diseño web profesional"}]}', '<div class="faq">FAQ...</div>');

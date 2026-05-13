-- =====================================================
-- WEBNOVA MANAGER - Database Schema
-- Creada para TFG: Sistema de Gestión de Contenidos
-- =====================================================

-- 1. CREAR LA BASE DE DATOS
-- Si ya existe, la eliminamos y creamos de nuevo (útil para desarrollo)
DROP DATABASE IF EXISTS webnova_db;
CREATE DATABASE webnova_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 2. USAR LA BASE DE DATOS CREADA
USE webnova_db;

-- =====================================================
-- TABLA: USUARIOS
-- =====================================================
-- Por qué existe:
--   - Guarda la información de todos los usuarios del sistema
--   - Contiene credenciales (email + contraseña hasheada)
--   - Define roles para control de acceso
--   - Registra fechas para auditoría
-- =====================================================

CREATE TABLE usuarios (
  -- Identificador único (PRIMARY KEY)
  id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'ID único del usuario',

  -- Datos personales
  nombre VARCHAR(100) NOT NULL COMMENT 'Nombre completo del usuario',
  email VARCHAR(120) NOT NULL UNIQUE COMMENT 'Email único (identificador de login)',

  -- Seguridad
  -- ⚠️ IMPORTANTE: password_hash() genera hash seguro
  -- Nunca guardamos texto plano. Explicación en PASO 2
  password VARCHAR(255) NOT NULL COMMENT 'Password hasheado con password_hash()',

  -- Control de acceso
  rol ENUM('admin', 'editor', 'usuario') DEFAULT 'usuario' COMMENT 'Rol determina permisos',

  -- Auditoría
  fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Cuándo se creó la cuenta',
  fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Última actualización',

  -- Índices para búsquedas rápidas
  INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de usuarios del sistema';

-- =====================================================
-- INSERTAR USUARIOS DEMO
-- =====================================================
-- Las contraseñas están hasheadas con password_hash('0000', PASSWORD_BCRYPT)
-- En PHP: password_hash('0000', PASSWORD_BCRYPT)
-- Todos usan contraseña: 0000
-- =====================================================

-- Los hashes generados con: password_hash('0000', PASSWORD_BCRYPT)
-- Son seguros y no pueden revertirse
INSERT INTO usuarios (nombre, email, password, rol) VALUES
(
  'Carlos González',
  'carlos@webnova.com',
  '$2y$10$slYQmyNdGzin7olVA0/O2OPST9EF/ufuCvii/V9/f77QwzvjlHYeK',
  'admin'
),
(
  'Sergio Martínez',
  'sergio@webnova.com',
  '$2y$10$slYQmyNdGzin7olVA0/O2OPST9EF/ufuCvii/V9/f77QwzvjlHYeK',
  'editor'
),
(
  'Ester López',
  'ester@webnova.com',
  '$2y$10$slYQmyNdGzin7olVA0/O2OPST9EF/ufuCvii/V9/f77QwzvjlHYeK',
  'usuario'
),
(
  'Admin WEBNOVA',
  'admin@webnova.com',
  '$2y$10$slYQmyNdGzin7olVA0/O2OPST9EF/ufuCvii/V9/f77QwzvjlHYeK',
  'admin'
);

-- =====================================================
-- TABLA: PAGINAS (para CMS)
-- =====================================================
CREATE TABLE paginas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL UNIQUE,
  contenido LONGTEXT NOT NULL,
  meta_description VARCHAR(160) DEFAULT '',
  keywords VARCHAR(255) DEFAULT '',
  autor_id INT NOT NULL,
  publicada BOOLEAN DEFAULT FALSE,
  fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (autor_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  INDEX idx_slug (slug),
  INDEX idx_publicada (publicada)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Páginas estáticas del CMS';

-- =====================================================
-- TABLA: ARTICULOS/POSTS (para blog)
-- =====================================================
CREATE TABLE articulos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL UNIQUE,
  contenido LONGTEXT NOT NULL,
  meta_description VARCHAR(160) DEFAULT '',
  keywords VARCHAR(255) DEFAULT '',
  autor_id INT NOT NULL,
  publicado BOOLEAN DEFAULT FALSE,
  fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (autor_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  INDEX idx_slug (slug),
  INDEX idx_publicado (publicado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Artículos del blog';

-- =====================================================
-- TABLA: SESIONES PHP (para rastrear sesiones activas)
-- =====================================================
-- Por qué: Permite invalidar sesiones manualmente y rastrearlas
CREATE TABLE sesiones (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  token VARCHAR(255) NOT NULL UNIQUE,
  ip_address VARCHAR(45),
  user_agent TEXT,
  creada_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  expira_en TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  INDEX idx_token (token),
  INDEX idx_usuario_id (usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Registro de sesiones activas';

-- =====================================================
-- TABLA: CATEGORIAS (para posts/artículos)
-- =====================================================
CREATE TABLE categorias (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL UNIQUE,
  slug VARCHAR(100) NOT NULL UNIQUE,
  descripcion TEXT,
  fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Categorías para artículos del blog';

-- =====================================================
-- TABLA: COMENTARIOS
-- =====================================================
CREATE TABLE comentarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  articulo_id INT NOT NULL,
  usuario_id INT,
  nombre VARCHAR(100) NOT NULL,
  email VARCHAR(120),
  contenido TEXT NOT NULL,
  aprobado BOOLEAN DEFAULT FALSE,
  fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (articulo_id) REFERENCES articulos(id) ON DELETE CASCADE,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
  INDEX idx_articulo_id (articulo_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Comentarios en artículos';

-- =====================================================
-- TABLA: LOGS DE AUDITORIA
-- =====================================================
CREATE TABLE logs_auditoria (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  accion VARCHAR(100) NOT NULL,
  tabla VARCHAR(100),
  registro_id INT,
  descripcion TEXT,
  ip_address VARCHAR(45),
  fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  INDEX idx_usuario_id (usuario_id),
  INDEX idx_fecha (fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Log de auditoría de acciones';

-- =====================================================
-- TABLA: CONFIGURACION
-- =====================================================
CREATE TABLE configuracion (
  id INT AUTO_INCREMENT PRIMARY KEY,
  clave VARCHAR(100) NOT NULL UNIQUE,
  valor LONGTEXT,
  tipo VARCHAR(50) DEFAULT 'string',
  descripcion TEXT,
  actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Configuración general del CMS';

-- Insertar configuración por defecto
INSERT INTO configuracion (clave, valor, tipo, descripcion) VALUES
('sitio_titulo', 'WebNova Manager', 'string', 'Título del sitio'),
('sitio_descripcion', 'Sistema de Gestión de Contenidos profesional', 'string', 'Meta description'),
('sitio_url', 'http://localhost/WebnovaManager', 'string', 'URL principal del sitio'),
('email_contacto', 'contacto@webnova.com', 'string', 'Email de contacto'),
('email_soporte', 'soporte@webnova.com', 'string', 'Email de soporte técnico'),
('items_por_pagina', '10', 'integer', 'Items por página en listados'),
('permitir_comentarios', '1', 'boolean', 'Permitir comentarios en posts');

-- =====================================================
-- TABLA: MEDIA (Archivos subidos)
-- =====================================================
CREATE TABLE media (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre_original VARCHAR(255) NOT NULL,
  nombre_archivo VARCHAR(255) NOT NULL UNIQUE,
  ruta VARCHAR(255) NOT NULL,
  tipo_archivo VARCHAR(100),
  tamano INT,
  usuario_id INT NOT NULL,
  fecha_subida TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  INDEX idx_usuario_id (usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Archivos multimedia subidos';

-- =====================================================
-- FIN: Database Schema
-- =====================================================

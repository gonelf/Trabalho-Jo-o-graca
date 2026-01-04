-- ESCS Portfolio Database Schema
-- Created for Laboratório de Aplicações Interativas

CREATE DATABASE IF NOT EXISTS escs_portfolio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE escs_portfolio;

-- Tabela de utilizadores (administradores)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    full_name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE,
    INDEX idx_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de cursos
CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    acronym VARCHAR(20),
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de áreas científicas
CREATE TABLE IF NOT EXISTS scientific_areas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de unidades curriculares
CREATE TABLE IF NOT EXISTS curricular_units (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    code VARCHAR(20),
    course_id INT,
    scientific_area_id INT,
    semester INT,
    academic_year VARCHAR(10),
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE SET NULL,
    FOREIGN KEY (scientific_area_id) REFERENCES scientific_areas(id) ON DELETE SET NULL,
    INDEX idx_name (name),
    INDEX idx_course (course_id),
    INDEX idx_scientific_area (scientific_area_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de projetos
CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    student_name VARCHAR(100),
    student_number VARCHAR(20),
    course_id INT,
    curricular_unit_id INT,
    scientific_area_id INT,
    academic_year VARCHAR(10),
    semester INT,
    project_date DATE,
    thumbnail VARCHAR(255),
    project_url VARCHAR(500),
    video_url VARCHAR(500),
    images TEXT, -- JSON array de imagens
    tags TEXT, -- JSON array de tags
    views INT DEFAULT 0,
    is_featured BOOLEAN DEFAULT FALSE,
    is_published BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE SET NULL,
    FOREIGN KEY (curricular_unit_id) REFERENCES curricular_units(id) ON DELETE SET NULL,
    FOREIGN KEY (scientific_area_id) REFERENCES scientific_areas(id) ON DELETE SET NULL,
    INDEX idx_title (title),
    INDEX idx_course (course_id),
    INDEX idx_curricular_unit (curricular_unit_id),
    INDEX idx_scientific_area (scientific_area_id),
    INDEX idx_published (is_published),
    INDEX idx_featured (is_featured),
    FULLTEXT idx_fulltext (title, description, student_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de comentários
CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    author_name VARCHAR(100) NOT NULL,
    author_email VARCHAR(100),
    comment_text TEXT NOT NULL,
    is_approved BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    INDEX idx_project (project_id),
    INDEX idx_approved (is_approved),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserir dados de exemplo

-- Cursos
INSERT INTO courses (name, acronym, description) VALUES
('Licenciatura em Audiovisual e Multimédia', 'LAM', 'Curso de Audiovisual e Multimédia'),
('Licenciatura em Jornalismo', 'LJ', 'Curso de Jornalismo'),
('Licenciatura em Publicidade e Marketing', 'LPM', 'Curso de Publicidade e Marketing'),
('Licenciatura em Cinema e Televisão', 'LCT', 'Curso de Cinema e Televisão');

-- Áreas Científicas
INSERT INTO scientific_areas (name, description) VALUES
('Programação', 'Desenvolvimento de software e aplicações'),
('Design', 'Design gráfico e multimédia'),
('Audiovisual', 'Produção audiovisual'),
('Multimédia', 'Produção multimédia interativa'),
('Web Development', 'Desenvolvimento web'),
('Motion Graphics', 'Animação e motion graphics'),
('Fotografia', 'Fotografia'),
('Vídeo', 'Produção de vídeo');

-- Unidades Curriculares de exemplo
INSERT INTO curricular_units (name, code, course_id, scientific_area_id, semester, academic_year) VALUES
('Laboratório de Aplicações Interativas', 'LAI', 1, 1, 1, '2025-2026'),
('Design Multimédia', 'DM', 1, 2, 1, '2025-2026'),
('Produção Audiovisual', 'PA', 1, 3, 2, '2025-2026'),
('Programação Web', 'PW', 1, 5, 1, '2025-2026');

-- Utilizador admin de exemplo (password: admin123)
-- Nota: Em produção, use password_hash() do PHP
INSERT INTO users (username, password, email, full_name) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@escs.ipl.pt', 'Administrador');

-- Projetos de exemplo
INSERT INTO projects (title, description, student_name, student_number, course_id, curricular_unit_id, scientific_area_id, academic_year, semester, project_date, is_published, is_featured) VALUES
('Portfolio Interativo ESCS', 'Plataforma web para divulgação de trabalhos académicos com sistema de gestão de conteúdos e navegação interativa.', 'João Silva', '202312345', 1, 1, 1, '2025-2026', 1, '2026-01-04', TRUE, TRUE),
('Campanha Multimédia Sustentabilidade', 'Projeto de design multimédia sobre sustentabilidade ambiental com vídeo e animações.', 'Maria Santos', '202312346', 1, 2, 2, '2025-2026', 1, '2025-12-15', TRUE, FALSE),
('Documentário ESCS', 'Documentário sobre a história da ESCS e seus alunos.', 'Pedro Costa', '202312347', 1, 3, 3, '2025-2026', 2, '2025-11-20', TRUE, TRUE);

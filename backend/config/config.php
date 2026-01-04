<?php
/**
 * ESCS Portfolio - Configuração Global
 * Laboratório de Aplicações Interativas
 */

// Configurações da Base de Dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'escs_portfolio');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Configurações da Aplicação
define('APP_NAME', 'ESCS Portfolio');
define('APP_URL', 'http://localhost/escs-portfolio');
define('APP_VERSION', '1.0.0');

// Configurações de Upload
define('UPLOAD_PATH', __DIR__ . '/../../backend/uploads/');
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('ALLOWED_VIDEO_TYPES', ['video/mp4', 'video/webm', 'video/ogg']);

// Configurações de Segurança
define('JWT_SECRET', 'your-secret-key-change-this-in-production');
define('SESSION_LIFETIME', 3600); // 1 hora
define('PASSWORD_MIN_LENGTH', 6);

// Configurações de Paginação
define('PROJECTS_PER_PAGE', 12);
define('COMMENTS_PER_PAGE', 10);

// Timezone
date_default_timezone_set('Europe/Lisbon');

// Error Reporting (Desativar em produção)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// CORS Headers (ajustar conforme necessário)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=UTF-8');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

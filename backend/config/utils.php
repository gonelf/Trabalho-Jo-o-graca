<?php
/**
 * Funções Utilitárias
 */

/**
 * Retorna resposta JSON
 * @param mixed $data Dados a retornar
 * @param int $statusCode Código HTTP
 */
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit();
}

/**
 * Retorna erro JSON
 * @param string $message Mensagem de erro
 * @param int $statusCode Código HTTP
 */
function jsonError($message, $statusCode = 400) {
    jsonResponse(['error' => true, 'message' => $message], $statusCode);
}

/**
 * Retorna sucesso JSON
 * @param mixed $data Dados de sucesso
 * @param string $message Mensagem de sucesso
 */
function jsonSuccess($data = null, $message = 'Sucesso') {
    $response = ['success' => true, 'message' => $message];
    if ($data !== null) {
        $response['data'] = $data;
    }
    jsonResponse($response, 200);
}

/**
 * Sanitiza input
 * @param string $data Dados a sanitizar
 * @return string
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Valida email
 * @param string $email Email a validar
 * @return bool
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Gera slug a partir de texto
 * @param string $text Texto a converter
 * @return string
 */
function generateSlug($text) {
    $text = mb_strtolower($text, 'UTF-8');

    // Remover acentos
    $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);

    // Remover caracteres especiais
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);

    // Substituir espaços e múltiplos hífens por um hífen
    $text = preg_replace('/[\s-]+/', '-', $text);

    return trim($text, '-');
}

/**
 * Upload de ficheiro
 * @param array $file Ficheiro do $_FILES
 * @param string $destination Pasta de destino
 * @param array $allowedTypes Tipos MIME permitidos
 * @return array ['success' => bool, 'filename' => string, 'message' => string]
 */
function uploadFile($file, $destination, $allowedTypes) {
    if (!isset($file['error']) || is_array($file['error'])) {
        return ['success' => false, 'message' => 'Erro no upload do ficheiro'];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Erro no upload: ' . $file['error']];
    }

    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => 'Ficheiro excede o tamanho máximo permitido'];
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);

    if (!in_array($mimeType, $allowedTypes)) {
        return ['success' => false, 'message' => 'Tipo de ficheiro não permitido'];
    }

    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $filepath = $destination . $filename;

    if (!is_dir($destination)) {
        mkdir($destination, 0755, true);
    }

    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => false, 'message' => 'Erro ao mover ficheiro'];
    }

    return ['success' => true, 'filename' => $filename, 'message' => 'Upload realizado com sucesso'];
}

/**
 * Verifica se utilizador está autenticado
 * @return bool
 */
function isAuthenticated() {
    session_start();
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}

/**
 * Requer autenticação
 */
function requireAuth() {
    if (!isAuthenticated()) {
        jsonError('Autenticação necessária', 401);
    }
}

/**
 * Obtém dados do utilizador da sessão
 * @return array|null
 */
function getCurrentUser() {
    session_start();
    if (isAuthenticated()) {
        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'email' => $_SESSION['email'] ?? null,
            'full_name' => $_SESSION['full_name'] ?? null
        ];
    }
    return null;
}

/**
 * Hasheia password
 * @param string $password Password em texto limpo
 * @return string
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verifica password
 * @param string $password Password em texto limpo
 * @param string $hash Hash da password
 * @return bool
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

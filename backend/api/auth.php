<?php
/**
 * API de Autenticação
 * Endpoints: login, logout, verificar sessão
 */

require_once '../config/config.php';
require_once '../config/Database.php';
require_once '../config/utils.php';

$method = $_SERVER['REQUEST_METHOD'];

// Rotas
switch($method) {
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data['action'])) {
            switch($data['action']) {
                case 'login':
                    login($data);
                    break;
                case 'logout':
                    logout();
                    break;
                default:
                    jsonError('Ação inválida');
            }
        } else {
            login($data);
        }
        break;

    case 'GET':
        checkSession();
        break;

    default:
        jsonError('Método não permitido', 405);
}

/**
 * Login de utilizador
 */
function login($data) {
    if (!isset($data['username']) || !isset($data['password'])) {
        jsonError('Username e password são obrigatórios');
    }

    $username = sanitizeInput($data['username']);
    $password = $data['password'];

    try {
        $database = new Database();
        $db = $database->getConnection();

        $query = "SELECT id, username, password, email, full_name
                  FROM users
                  WHERE username = :username AND is_active = 1
                  LIMIT 1";

        $stmt = $db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            jsonError('Credenciais inválidas', 401);
        }

        $user = $stmt->fetch();

        if (!verifyPassword($password, $user['password'])) {
            jsonError('Credenciais inválidas', 401);
        }

        // Atualizar last_login
        $updateQuery = "UPDATE users SET last_login = NOW() WHERE id = :id";
        $updateStmt = $db->prepare($updateQuery);
        $updateStmt->bindParam(':id', $user['id']);
        $updateStmt->execute();

        // Iniciar sessão
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['login_time'] = time();

        jsonSuccess([
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'full_name' => $user['full_name']
            ]
        ], 'Login realizado com sucesso');

    } catch(Exception $e) {
        jsonError('Erro ao fazer login: ' . $e->getMessage(), 500);
    }
}

/**
 * Logout de utilizador
 */
function logout() {
    session_start();
    session_unset();
    session_destroy();
    jsonSuccess(null, 'Logout realizado com sucesso');
}

/**
 * Verifica se sessão está ativa
 */
function checkSession() {
    session_start();

    if (isAuthenticated()) {
        jsonSuccess([
            'authenticated' => true,
            'user' => getCurrentUser()
        ]);
    } else {
        jsonResponse([
            'authenticated' => false
        ], 200);
    }
}

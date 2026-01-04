<?php
// sistema de login simples
session_start();
include '../config/db.php';

// pegar dados do POST
$data = json_decode(file_get_contents('php://input'), true);
$user = $data['username'];
$pass = $data['password'];

// buscar user na BD
$sql = "SELECT * FROM users WHERE username = '$user' AND is_active = 1";
$stmt = $conn->query($sql);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if($usuario && password_verify($pass, $usuario['password'])) {
    // login ok
    $_SESSION['user_id'] = $usuario['id'];
    $_SESSION['username'] = $usuario['username'];
    $_SESSION['logado'] = true;

    // atualizar last login
    $conn->query("UPDATE users SET last_login = NOW() WHERE id = " . $usuario['id']);

    echo json_encode([
        'success' => true,
        'message' => 'Login com sucesso',
        'data' => [
            'user' => [
                'id' => $usuario['id'],
                'username' => $usuario['username'],
                'email' => $usuario['email']
            ]
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Username ou password errados']);
}
?>

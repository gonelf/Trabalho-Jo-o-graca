<?php
// adicionar comentario
include '../config/db.php';

$data = json_decode(file_get_contents('php://input'), true);

$projeto_id = $data['project_id'];
$nome = $data['author_name'];
$email = $data['author_email'] ?? '';
$comentario = $data['comment_text'];

// inserir (nao aprovado ainda)
$sql = "INSERT INTO comments (project_id, author_name, author_email, comment_text, is_approved)
        VALUES ($projeto_id, '$nome', '$email', '$comentario', 0)";

try {
    $conn->exec($sql);
    echo json_encode(['success' => true, 'message' => 'Comentario enviado para aprovacao']);
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao enviar comentario']);
}
?>

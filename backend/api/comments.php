<?php
/**
 * API de Comentários
 */

require_once '../config/config.php';
require_once '../config/Database.php';
require_once '../config/utils.php';

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        getComments();
        break;

    case 'POST':
        createComment();
        break;

    case 'PUT':
        requireAuth();
        $data = json_decode(file_get_contents('php://input'), true);
        approveComment($data);
        break;

    case 'DELETE':
        requireAuth();
        if (!isset($_GET['id'])) {
            jsonError('ID do comentário é obrigatório');
        }
        deleteComment($_GET['id']);
        break;

    default:
        jsonError('Método não permitido', 405);
}

function getComments() {
    try {
        $database = new Database();
        $db = $database->getConnection();

        if (!isset($_GET['project_id'])) {
            jsonError('ID do projeto é obrigatório');
        }

        $project_id = intval($_GET['project_id']);
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $limit = COMMENTS_PER_PAGE;
        $offset = ($page - 1) * $limit;

        // Query de contagem
        $countQuery = "SELECT COUNT(*) as total FROM comments WHERE project_id = :project_id AND is_approved = 1";
        $countStmt = $db->prepare($countQuery);
        $countStmt->bindParam(':project_id', $project_id, PDO::PARAM_INT);
        $countStmt->execute();
        $total = $countStmt->fetch()['total'];

        // Query principal
        $query = "SELECT * FROM comments
                  WHERE project_id = :project_id AND is_approved = 1
                  ORDER BY created_at DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $db->prepare($query);
        $stmt->bindParam(':project_id', $project_id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $comments = $stmt->fetchAll();

        jsonSuccess([
            'comments' => $comments,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'pages' => ceil($total / $limit)
            ]
        ]);

    } catch(Exception $e) {
        jsonError('Erro ao obter comentários: ' . $e->getMessage(), 500);
    }
}

function createComment() {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['project_id']) || !isset($data['author_name']) || !isset($data['comment_text'])) {
        jsonError('Campos obrigatórios: project_id, author_name, comment_text');
    }

    $project_id = intval($data['project_id']);
    $author_name = sanitizeInput($data['author_name']);
    $author_email = isset($data['author_email']) ? sanitizeInput($data['author_email']) : null;
    $comment_text = sanitizeInput($data['comment_text']);

    if ($author_email && !validateEmail($author_email)) {
        jsonError('Email inválido');
    }

    try {
        $database = new Database();
        $db = $database->getConnection();

        // Verificar se projeto existe
        $checkQuery = "SELECT id FROM projects WHERE id = :id AND is_published = 1";
        $checkStmt = $db->prepare($checkQuery);
        $checkStmt->bindParam(':id', $project_id, PDO::PARAM_INT);
        $checkStmt->execute();

        if ($checkStmt->rowCount() === 0) {
            jsonError('Projeto não encontrado', 404);
        }

        // Inserir comentário (não aprovado por padrão)
        $query = "INSERT INTO comments (project_id, author_name, author_email, comment_text, is_approved)
                  VALUES (:project_id, :author_name, :author_email, :comment_text, 0)";

        $stmt = $db->prepare($query);
        $stmt->bindParam(':project_id', $project_id, PDO::PARAM_INT);
        $stmt->bindParam(':author_name', $author_name);
        $stmt->bindParam(':author_email', $author_email);
        $stmt->bindParam(':comment_text', $comment_text);
        $stmt->execute();

        jsonSuccess(['id' => $db->lastInsertId()], 'Comentário submetido para aprovação');

    } catch(Exception $e) {
        jsonError('Erro ao criar comentário: ' . $e->getMessage(), 500);
    }
}

function approveComment($data) {
    if (!isset($data['id'])) {
        jsonError('ID do comentário é obrigatório');
    }

    try {
        $database = new Database();
        $db = $database->getConnection();

        $query = "UPDATE comments SET is_approved = 1 WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $data['id'], PDO::PARAM_INT);
        $stmt->execute();

        jsonSuccess(null, 'Comentário aprovado com sucesso');

    } catch(Exception $e) {
        jsonError('Erro ao aprovar comentário: ' . $e->getMessage(), 500);
    }
}

function deleteComment($id) {
    try {
        $database = new Database();
        $db = $database->getConnection();

        $query = "DELETE FROM comments WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        jsonSuccess(null, 'Comentário removido com sucesso');

    } catch(Exception $e) {
        jsonError('Erro ao remover comentário: ' . $e->getMessage(), 500);
    }
}

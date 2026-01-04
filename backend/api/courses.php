<?php
/**
 * API de Cursos
 */

require_once '../config/config.php';
require_once '../config/Database.php';
require_once '../config/utils.php';

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        getCourses();
        break;

    case 'POST':
        requireAuth();
        createCourse();
        break;

    case 'PUT':
        requireAuth();
        $data = json_decode(file_get_contents('php://input'), true);
        updateCourse($data);
        break;

    case 'DELETE':
        requireAuth();
        if (!isset($_GET['id'])) {
            jsonError('ID do curso é obrigatório');
        }
        deleteCourse($_GET['id']);
        break;

    default:
        jsonError('Método não permitido', 405);
}

function getCourses() {
    try {
        $database = new Database();
        $db = $database->getConnection();

        $query = "SELECT * FROM courses WHERE is_active = 1 ORDER BY name ASC";
        $stmt = $db->prepare($query);
        $stmt->execute();

        $courses = $stmt->fetchAll();
        jsonSuccess(['courses' => $courses]);

    } catch(Exception $e) {
        jsonError('Erro ao obter cursos: ' . $e->getMessage(), 500);
    }
}

function createCourse() {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['name'])) {
        jsonError('Nome do curso é obrigatório');
    }

    try {
        $database = new Database();
        $db = $database->getConnection();

        $query = "INSERT INTO courses (name, acronym, description) VALUES (:name, :acronym, :description)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':acronym', $data['acronym'] ?? null);
        $stmt->bindParam(':description', $data['description'] ?? null);
        $stmt->execute();

        jsonSuccess(['id' => $db->lastInsertId()], 'Curso criado com sucesso');

    } catch(Exception $e) {
        jsonError('Erro ao criar curso: ' . $e->getMessage(), 500);
    }
}

function updateCourse($data) {
    if (!isset($data['id'])) {
        jsonError('ID do curso é obrigatório');
    }

    try {
        $database = new Database();
        $db = $database->getConnection();

        $fields = [];
        $params = [':id' => $data['id']];

        if (isset($data['name'])) {
            $fields[] = "name = :name";
            $params[':name'] = $data['name'];
        }
        if (isset($data['acronym'])) {
            $fields[] = "acronym = :acronym";
            $params[':acronym'] = $data['acronym'];
        }
        if (isset($data['description'])) {
            $fields[] = "description = :description";
            $params[':description'] = $data['description'];
        }

        if (empty($fields)) {
            jsonError('Nenhum campo para atualizar');
        }

        $query = "UPDATE courses SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->execute($params);

        jsonSuccess(null, 'Curso atualizado com sucesso');

    } catch(Exception $e) {
        jsonError('Erro ao atualizar curso: ' . $e->getMessage(), 500);
    }
}

function deleteCourse($id) {
    try {
        $database = new Database();
        $db = $database->getConnection();

        $query = "UPDATE courses SET is_active = 0 WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        jsonSuccess(null, 'Curso removido com sucesso');

    } catch(Exception $e) {
        jsonError('Erro ao remover curso: ' . $e->getMessage(), 500);
    }
}

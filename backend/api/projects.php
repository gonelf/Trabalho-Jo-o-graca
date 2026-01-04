<?php
/**
 * API de Projetos
 * Endpoints: GET (listar/buscar), POST (criar), PUT (atualizar), DELETE (apagar)
 */

require_once '../config/config.php';
require_once '../config/Database.php';
require_once '../config/utils.php';

$method = $_SERVER['REQUEST_METHOD'];

// Rotas
switch($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            getProject($_GET['id']);
        } else {
            getProjects();
        }
        break;

    case 'POST':
        requireAuth();
        createProject();
        break;

    case 'PUT':
        requireAuth();
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['id'])) {
            jsonError('ID do projeto é obrigatório');
        }
        updateProject($data);
        break;

    case 'DELETE':
        requireAuth();
        if (!isset($_GET['id'])) {
            jsonError('ID do projeto é obrigatório');
        }
        deleteProject($_GET['id']);
        break;

    default:
        jsonError('Método não permitido', 405);
}

/**
 * Listar projetos com filtros e pesquisa
 */
function getProjects() {
    try {
        $database = new Database();
        $db = $database->getConnection();

        // Parâmetros de filtro
        $course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : null;
        $scientific_area_id = isset($_GET['scientific_area_id']) ? intval($_GET['scientific_area_id']) : null;
        $curricular_unit_id = isset($_GET['curricular_unit_id']) ? intval($_GET['curricular_unit_id']) : null;
        $search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : null;
        $featured = isset($_GET['featured']) ? (bool)$_GET['featured'] : null;
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $limit = isset($_GET['limit']) ? min(100, max(1, intval($_GET['limit']))) : PROJECTS_PER_PAGE;
        $offset = ($page - 1) * $limit;

        // Construir query
        $query = "SELECT p.*,
                         c.name as course_name,
                         c.acronym as course_acronym,
                         sa.name as scientific_area_name,
                         cu.name as curricular_unit_name
                  FROM projects p
                  LEFT JOIN courses c ON p.course_id = c.id
                  LEFT JOIN scientific_areas sa ON p.scientific_area_id = sa.id
                  LEFT JOIN curricular_units cu ON p.curricular_unit_id = cu.id
                  WHERE p.is_published = 1";

        $countQuery = "SELECT COUNT(*) as total FROM projects p WHERE p.is_published = 1";

        $params = [];

        // Aplicar filtros
        if ($course_id !== null) {
            $query .= " AND p.course_id = :course_id";
            $countQuery .= " AND p.course_id = :course_id";
            $params[':course_id'] = $course_id;
        }

        if ($scientific_area_id !== null) {
            $query .= " AND p.scientific_area_id = :scientific_area_id";
            $countQuery .= " AND p.scientific_area_id = :scientific_area_id";
            $params[':scientific_area_id'] = $scientific_area_id;
        }

        if ($curricular_unit_id !== null) {
            $query .= " AND p.curricular_unit_id = :curricular_unit_id";
            $countQuery .= " AND p.curricular_unit_id = :curricular_unit_id";
            $params[':curricular_unit_id'] = $curricular_unit_id;
        }

        if ($featured !== null && $featured === true) {
            $query .= " AND p.is_featured = 1";
            $countQuery .= " AND p.is_featured = 1";
        }

        if ($search !== null && $search !== '') {
            $query .= " AND (p.title LIKE :search OR p.description LIKE :search OR p.student_name LIKE :search)";
            $countQuery .= " AND (p.title LIKE :search OR p.description LIKE :search OR p.student_name LIKE :search)";
            $params[':search'] = "%{$search}%";
        }

        // Ordenação
        $query .= " ORDER BY p.is_featured DESC, p.created_at DESC";

        // Paginação
        $query .= " LIMIT :limit OFFSET :offset";

        // Executar query de contagem
        $countStmt = $db->prepare($countQuery);
        foreach ($params as $key => $value) {
            $countStmt->bindValue($key, $value);
        }
        $countStmt->execute();
        $totalProjects = $countStmt->fetch()['total'];

        // Executar query principal
        $stmt = $db->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $projects = $stmt->fetchAll();

        // Processar dados JSON (images, tags)
        foreach ($projects as &$project) {
            $project['images'] = $project['images'] ? json_decode($project['images']) : [];
            $project['tags'] = $project['tags'] ? json_decode($project['tags']) : [];
        }

        jsonSuccess([
            'projects' => $projects,
            'pagination' => [
                'total' => $totalProjects,
                'page' => $page,
                'limit' => $limit,
                'pages' => ceil($totalProjects / $limit)
            ]
        ]);

    } catch(Exception $e) {
        jsonError('Erro ao obter projetos: ' . $e->getMessage(), 500);
    }
}

/**
 * Obter um projeto específico
 */
function getProject($id) {
    try {
        $database = new Database();
        $db = $database->getConnection();

        $query = "SELECT p.*,
                         c.name as course_name,
                         c.acronym as course_acronym,
                         sa.name as scientific_area_name,
                         cu.name as curricular_unit_name
                  FROM projects p
                  LEFT JOIN courses c ON p.course_id = c.id
                  LEFT JOIN scientific_areas sa ON p.scientific_area_id = sa.id
                  LEFT JOIN curricular_units cu ON p.curricular_unit_id = cu.id
                  WHERE p.id = :id AND p.is_published = 1";

        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            jsonError('Projeto não encontrado', 404);
        }

        $project = $stmt->fetch();

        // Processar dados JSON
        $project['images'] = $project['images'] ? json_decode($project['images']) : [];
        $project['tags'] = $project['tags'] ? json_decode($project['tags']) : [];

        // Incrementar views
        $updateQuery = "UPDATE projects SET views = views + 1 WHERE id = :id";
        $updateStmt = $db->prepare($updateQuery);
        $updateStmt->bindParam(':id', $id, PDO::PARAM_INT);
        $updateStmt->execute();

        jsonSuccess(['project' => $project]);

    } catch(Exception $e) {
        jsonError('Erro ao obter projeto: ' . $e->getMessage(), 500);
    }
}

/**
 * Criar novo projeto
 */
function createProject() {
    try {
        // Processar dados do formulário
        $title = sanitizeInput($_POST['title'] ?? '');
        $description = sanitizeInput($_POST['description'] ?? '');
        $student_name = sanitizeInput($_POST['student_name'] ?? '');
        $student_number = sanitizeInput($_POST['student_number'] ?? '');
        $course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : null;
        $curricular_unit_id = isset($_POST['curricular_unit_id']) ? intval($_POST['curricular_unit_id']) : null;
        $scientific_area_id = isset($_POST['scientific_area_id']) ? intval($_POST['scientific_area_id']) : null;
        $academic_year = sanitizeInput($_POST['academic_year'] ?? '');
        $semester = isset($_POST['semester']) ? intval($_POST['semester']) : null;
        $project_date = $_POST['project_date'] ?? null;
        $project_url = sanitizeInput($_POST['project_url'] ?? '');
        $video_url = sanitizeInput($_POST['video_url'] ?? '');
        $tags = isset($_POST['tags']) ? json_encode(explode(',', $_POST['tags'])) : '[]';
        $is_featured = isset($_POST['is_featured']) ? (bool)$_POST['is_featured'] : false;
        $is_published = isset($_POST['is_published']) ? (bool)$_POST['is_published'] : true;

        if (empty($title) || empty($description)) {
            jsonError('Título e descrição são obrigatórios');
        }

        // Upload de thumbnail
        $thumbnail = null;
        if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
            $upload = uploadFile($_FILES['thumbnail'], UPLOAD_PATH . 'thumbnails/', ALLOWED_IMAGE_TYPES);
            if ($upload['success']) {
                $thumbnail = $upload['filename'];
            }
        }

        // Upload de imagens múltiplas
        $images = [];
        if (isset($_FILES['images'])) {
            $files = $_FILES['images'];
            $fileCount = count($files['name']);

            for ($i = 0; $i < $fileCount; $i++) {
                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                    $file = [
                        'name' => $files['name'][$i],
                        'type' => $files['type'][$i],
                        'tmp_name' => $files['tmp_name'][$i],
                        'error' => $files['error'][$i],
                        'size' => $files['size'][$i]
                    ];
                    $upload = uploadFile($file, UPLOAD_PATH . 'projects/', ALLOWED_IMAGE_TYPES);
                    if ($upload['success']) {
                        $images[] = $upload['filename'];
                    }
                }
            }
        }
        $images_json = json_encode($images);

        $database = new Database();
        $db = $database->getConnection();

        $query = "INSERT INTO projects
                  (title, description, student_name, student_number, course_id, curricular_unit_id,
                   scientific_area_id, academic_year, semester, project_date, thumbnail, project_url,
                   video_url, images, tags, is_featured, is_published)
                  VALUES
                  (:title, :description, :student_name, :student_number, :course_id, :curricular_unit_id,
                   :scientific_area_id, :academic_year, :semester, :project_date, :thumbnail, :project_url,
                   :video_url, :images, :tags, :is_featured, :is_published)";

        $stmt = $db->prepare($query);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':student_name', $student_name);
        $stmt->bindParam(':student_number', $student_number);
        $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
        $stmt->bindParam(':curricular_unit_id', $curricular_unit_id, PDO::PARAM_INT);
        $stmt->bindParam(':scientific_area_id', $scientific_area_id, PDO::PARAM_INT);
        $stmt->bindParam(':academic_year', $academic_year);
        $stmt->bindParam(':semester', $semester, PDO::PARAM_INT);
        $stmt->bindParam(':project_date', $project_date);
        $stmt->bindParam(':thumbnail', $thumbnail);
        $stmt->bindParam(':project_url', $project_url);
        $stmt->bindParam(':video_url', $video_url);
        $stmt->bindParam(':images', $images_json);
        $stmt->bindParam(':tags', $tags);
        $stmt->bindParam(':is_featured', $is_featured, PDO::PARAM_BOOL);
        $stmt->bindParam(':is_published', $is_published, PDO::PARAM_BOOL);

        $stmt->execute();

        jsonSuccess(['id' => $db->lastInsertId()], 'Projeto criado com sucesso');

    } catch(Exception $e) {
        jsonError('Erro ao criar projeto: ' . $e->getMessage(), 500);
    }
}

/**
 * Atualizar projeto existente
 */
function updateProject($data) {
    try {
        $id = intval($data['id']);

        $database = new Database();
        $db = $database->getConnection();

        // Verificar se projeto existe
        $checkQuery = "SELECT id FROM projects WHERE id = :id";
        $checkStmt = $db->prepare($checkQuery);
        $checkStmt->bindParam(':id', $id, PDO::PARAM_INT);
        $checkStmt->execute();

        if ($checkStmt->rowCount() === 0) {
            jsonError('Projeto não encontrado', 404);
        }

        // Construir query de atualização dinamicamente
        $fields = [];
        $params = [':id' => $id];

        $allowedFields = ['title', 'description', 'student_name', 'student_number', 'course_id',
                          'curricular_unit_id', 'scientific_area_id', 'academic_year', 'semester',
                          'project_date', 'project_url', 'video_url', 'tags', 'is_featured', 'is_published'];

        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = :$field";
                $params[":$field"] = $field === 'tags' && is_array($data[$field])
                    ? json_encode($data[$field])
                    : $data[$field];
            }
        }

        if (empty($fields)) {
            jsonError('Nenhum campo para atualizar');
        }

        $query = "UPDATE projects SET " . implode(', ', $fields) . " WHERE id = :id";

        $stmt = $db->prepare($query);
        $stmt->execute($params);

        jsonSuccess(null, 'Projeto atualizado com sucesso');

    } catch(Exception $e) {
        jsonError('Erro ao atualizar projeto: ' . $e->getMessage(), 500);
    }
}

/**
 * Apagar projeto
 */
function deleteProject($id) {
    try {
        $database = new Database();
        $db = $database->getConnection();

        // Obter informações do projeto para apagar ficheiros
        $query = "SELECT thumbnail, images FROM projects WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            jsonError('Projeto não encontrado', 404);
        }

        $project = $stmt->fetch();

        // Apagar thumbnail
        if ($project['thumbnail'] && file_exists(UPLOAD_PATH . 'thumbnails/' . $project['thumbnail'])) {
            unlink(UPLOAD_PATH . 'thumbnails/' . $project['thumbnail']);
        }

        // Apagar imagens
        if ($project['images']) {
            $images = json_decode($project['images']);
            foreach ($images as $image) {
                if (file_exists(UPLOAD_PATH . 'projects/' . $image)) {
                    unlink(UPLOAD_PATH . 'projects/' . $image);
                }
            }
        }

        // Apagar projeto da base de dados
        $deleteQuery = "DELETE FROM projects WHERE id = :id";
        $deleteStmt = $db->prepare($deleteQuery);
        $deleteStmt->bindParam(':id', $id, PDO::PARAM_INT);
        $deleteStmt->execute();

        jsonSuccess(null, 'Projeto apagado com sucesso');

    } catch(Exception $e) {
        jsonError('Erro ao apagar projeto: ' . $e->getMessage(), 500);
    }
}

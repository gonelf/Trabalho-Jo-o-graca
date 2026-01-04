<?php
/**
 * API de Unidades Curriculares
 */

require_once '../config/config.php';
require_once '../config/Database.php';
require_once '../config/utils.php';

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        getCurricularUnits();
        break;

    default:
        jsonError('Método não permitido', 405);
}

function getCurricularUnits() {
    try {
        $database = new Database();
        $db = $database->getConnection();

        $course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : null;

        $query = "SELECT cu.*, c.name as course_name, sa.name as scientific_area_name
                  FROM curricular_units cu
                  LEFT JOIN courses c ON cu.course_id = c.id
                  LEFT JOIN scientific_areas sa ON cu.scientific_area_id = sa.id
                  WHERE cu.is_active = 1";

        if ($course_id !== null) {
            $query .= " AND cu.course_id = :course_id";
        }

        $query .= " ORDER BY cu.name ASC";

        $stmt = $db->prepare($query);

        if ($course_id !== null) {
            $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
        }

        $stmt->execute();

        $units = $stmt->fetchAll();
        jsonSuccess(['curricular_units' => $units]);

    } catch(Exception $e) {
        jsonError('Erro ao obter unidades curriculares: ' . $e->getMessage(), 500);
    }
}

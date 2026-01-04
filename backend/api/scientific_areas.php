<?php
/**
 * API de Áreas Científicas
 */

require_once '../config/config.php';
require_once '../config/Database.php';
require_once '../config/utils.php';

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        getScientificAreas();
        break;

    default:
        jsonError('Método não permitido', 405);
}

function getScientificAreas() {
    try {
        $database = new Database();
        $db = $database->getConnection();

        $query = "SELECT * FROM scientific_areas WHERE is_active = 1 ORDER BY name ASC";
        $stmt = $db->prepare($query);
        $stmt->execute();

        $areas = $stmt->fetchAll();
        jsonSuccess(['scientific_areas' => $areas]);

    } catch(Exception $e) {
        jsonError('Erro ao obter áreas científicas: ' . $e->getMessage(), 500);
    }
}

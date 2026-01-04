<?php
// pegar um projeto especifico
include '../config/db.php';

$id = $_GET['id'];

$sql = "SELECT p.*, c.name as curso_nome, sa.name as area_nome, cu.name as unidade_nome
        FROM projects p
        LEFT JOIN courses c ON p.course_id = c.id
        LEFT JOIN scientific_areas sa ON p.scientific_area_id = sa.id
        LEFT JOIN curricular_units cu ON p.curricular_unit_id = cu.id
        WHERE p.id = $id";

$stmt = $conn->query($sql);
$projeto = $stmt->fetch(PDO::FETCH_ASSOC);

if($projeto) {
    // processar JSON
    $projeto['images'] = json_decode($projeto['images']) ?? [];
    $projeto['tags'] = json_decode($projeto['tags']) ?? [];

    $projeto['course_name'] = $projeto['curso_nome'];
    $projeto['scientific_area_name'] = $projeto['area_nome'];
    $projeto['curricular_unit_name'] = $projeto['unidade_nome'];

    // aumentar views
    $conn->query("UPDATE projects SET views = views + 1 WHERE id = $id");

    echo json_encode(['success' => true, 'data' => ['project' => $projeto]]);
} else {
    echo json_encode(['success' => false, 'message' => 'Projeto nao encontrado']);
}
?>

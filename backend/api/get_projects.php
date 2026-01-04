<?php
// API para buscar projetos
// TODO: melhorar isto depois

include '../config/db.php';

// pegar os filtros do GET
$curso = $_GET['course_id'] ?? '';
$area = $_GET['scientific_area_id'] ?? '';
$unidade = $_GET['curricular_unit_id'] ?? '';
$pesquisa = $_GET['search'] ?? '';
$destaque = $_GET['featured'] ?? '';

// montar a query (podiamos ter feito melhor mas funciona)
$sql = "SELECT p.*, c.name as curso_nome, sa.name as area_nome, cu.name as unidade_nome
        FROM projects p
        LEFT JOIN courses c ON p.course_id = c.id
        LEFT JOIN scientific_areas sa ON p.scientific_area_id = sa.id
        LEFT JOIN curricular_units cu ON p.curricular_unit_id = cu.id
        WHERE p.is_published = 1";

// adicionar filtros se existirem
if($curso != '') {
    $sql .= " AND p.course_id = $curso";
}

if($area != '') {
    $sql .= " AND p.scientific_area_id = $area";
}

if($unidade != '') {
    $sql .= " AND p.curricular_unit_id = $unidade";
}

if($pesquisa != '') {
    $sql .= " AND (p.title LIKE '%$pesquisa%' OR p.description LIKE '%$pesquisa%' OR p.student_name LIKE '%$pesquisa%')";
}

if($destaque == '1') {
    $sql .= " AND p.is_featured = 1";
}

$sql .= " ORDER BY p.created_at DESC";

// executar
$stmt = $conn->prepare($sql);
$stmt->execute();
$projetos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// processar os dados (tirar os JSONs)
foreach($projetos as &$projeto) {
    if($projeto['images']) {
        $projeto['images'] = json_decode($projeto['images']);
    } else {
        $projeto['images'] = [];
    }

    if($projeto['tags']) {
        $projeto['tags'] = json_decode($projeto['tags']);
    } else {
        $projeto['tags'] = [];
    }

    // corrigir os nomes das colunas
    $projeto['course_name'] = $projeto['curso_nome'];
    $projeto['scientific_area_name'] = $projeto['area_nome'];
    $projeto['curricular_unit_name'] = $projeto['unidade_nome'];
}

// retornar JSON
echo json_encode([
    'success' => true,
    'data' => [
        'projects' => $projetos,
        'pagination' => [
            'total' => count($projetos),
            'page' => 1,
            'limit' => count($projetos),
            'pages' => 1
        ]
    ]
]);
?>

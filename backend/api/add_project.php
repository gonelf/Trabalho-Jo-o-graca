<?php
// adicionar projeto novo
session_start();
include '../config/db.php';

// verificar se ta logado
if(!isset($_SESSION['logado'])) {
    echo json_encode(['success' => false, 'message' => 'Precisa estar logado']);
    exit;
}

// pegar dados do form
$titulo = $_POST['title'];
$descricao = $_POST['description'];
$aluno = $_POST['student_name'] ?? '';
$numero = $_POST['student_number'] ?? '';
$curso = $_POST['course_id'] ?? null;
$unidade = $_POST['curricular_unit_id'] ?? null;
$area = $_POST['scientific_area_id'] ?? null;
$ano = $_POST['academic_year'] ?? '';
$url_projeto = $_POST['project_url'] ?? '';
$url_video = $_POST['video_url'] ?? '';
$destaque = isset($_POST['is_featured']) ? 1 : 0;
$publicado = isset($_POST['is_published']) ? 1 : 0;

// upload da thumbnail (se tiver)
$thumbnail = null;
if(isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] == 0) {
    $ext = pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION);
    $thumbnail = uniqid() . '.' . $ext;
    move_uploaded_file($_FILES['thumbnail']['tmp_name'], '../uploads/thumbnails/' . $thumbnail);
}

// inserir na BD
$sql = "INSERT INTO projects (title, description, student_name, student_number, course_id, curricular_unit_id, scientific_area_id, academic_year, thumbnail, project_url, video_url, is_featured, is_published)
        VALUES ('$titulo', '$descricao', '$aluno', '$numero', $curso, $unidade, $area, '$ano', '$thumbnail', '$url_projeto', '$url_video', $destaque, $publicado)";

try {
    $conn->exec($sql);
    echo json_encode(['success' => true, 'message' => 'Projeto adicionado!', 'data' => ['id' => $conn->lastInsertId()]]);
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
}
?>

<?php
session_start();
include '../config/db.php';

if(!isset($_SESSION['logado'])) {
    echo json_encode(['success' => false, 'message' => 'Nao autorizado']);
    exit;
}

$id = $_GET['id'];

// apagar ficheiros primeiro (se existirem)
$sql = "SELECT thumbnail FROM projects WHERE id = $id";
$stmt = $conn->query($sql);
$projeto = $stmt->fetch();

if($projeto && $projeto['thumbnail']) {
    $file = '../uploads/thumbnails/' . $projeto['thumbnail'];
    if(file_exists($file)) {
        unlink($file);
    }
}

// apagar da BD
$sql = "DELETE FROM projects WHERE id = $id";
$conn->exec($sql);

echo json_encode(['success' => true, 'message' => 'Projeto apagado']);
?>

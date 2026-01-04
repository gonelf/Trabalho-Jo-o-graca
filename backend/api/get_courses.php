<?php
include '../config/db.php';

$sql = "SELECT * FROM courses WHERE is_active = 1 ORDER BY name";
$stmt = $conn->query($sql);
$cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['success' => true, 'data' => ['courses' => $cursos]]);
?>

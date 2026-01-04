<?php
include '../config/db.php';

$sql = "SELECT * FROM curricular_units WHERE is_active = 1 ORDER BY name";
$stmt = $conn->query($sql);
$unidades = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['success' => true, 'data' => ['curricular_units' => $unidades]]);
?>

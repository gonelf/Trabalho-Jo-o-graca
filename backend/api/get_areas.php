<?php
include '../config/db.php';

$sql = "SELECT * FROM scientific_areas WHERE is_active = 1 ORDER BY name";
$stmt = $conn->query($sql);
$areas = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['success' => true, 'data' => ['scientific_areas' => $areas]]);
?>

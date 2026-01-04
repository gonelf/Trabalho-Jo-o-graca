<?php
// Conexao com a base de dados
// Feito por: Grupo LAI 2025

$host = "localhost";
$dbname = "escs_portfolio";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Erro de conexao: " . $e->getMessage();
}

// Headers para o CORS (tivemos problemas com isto)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
?>

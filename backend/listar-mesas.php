<?php
header("Content-Type: application/json");
require 'conexao.php';

$sql = "SELECT * FROM mesas";

try {
    $stmt = $pdo->query($sql);
    $mesas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($mesas);

} catch (PDOException $e) {
    echo json_encode(["erro" => "Erro ao buscar dados: " . $e->getMessage()]);
}
?>

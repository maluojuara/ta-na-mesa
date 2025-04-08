<?php
header("Content-Type: application/json");
include 'conexao.php';

$sql = "SELECT * FROM mesas";
$result = $conn->query($sql);

$mesas = [];

if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    $mesas[] = $row;
  }
}

echo json_encode($mesas);
$conn->close();
?>

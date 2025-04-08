<?php
$servidor = "localhost";
$usuario = "root";
$senha = "";
$banco = "db_projeto"; 

// Criando a conexão
$conn = new mysqli($servidor, $usuario, $senha, $banco);

// Verificando a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}
?>

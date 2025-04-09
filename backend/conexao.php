<?php 
 
//Configurações no banco de dados 
$host = 'localhost'; //colocamos o endereço do servidor entre as simples 
$db = 'db_projeto'; //qual banco de dados vamos trabalhar 
$user = 'root'; 
$pass = ''; 
$charset = 'utf8mb4'; 
 
$dsn = "mysql:host=$host;dbname=$db;charset=$charset"; //nome do nosso servidor de dominio, domin name server. variavel mais facil de manter 
 
//criando a conexão com banco de dados pdo 
 
try { 
$pdo = new PDO($dsn, $user, $pass); 
} 
catch(PDOException $e){ 
    echo "Erro ao tentar concectar com o banco de dados <p>" . $e; 
} 
?>

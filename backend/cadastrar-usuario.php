<?php
require_once('conexao.php');

$nome = $_POST['nome'];
$email = $_POST['email'];
$senha = $_POST['senha'];

$sql = "INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)";

$query = $pdo ->prepare($sql);

$query->bindParam(':nome', $nome, PDO::PARAM_STR);
$query->bindParam(':email', $email, PDO::PARAM_STR);
$query->bindParam(':senha', $senha, PDO::PARAM_STR);

if ($query->execute()) {
    // Se tudo deu certo, exibe uma mensagem de sucesso
    echo "Usuário cadastrado com sucesso!";
} else {
    // Se houve erro, exibe a mensagem de erro
    echo "Erro ao cadastrar usuário.";
}
?>
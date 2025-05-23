<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
try { 
  $caminhoBanco = realpath(__DIR__ . '/../backend/db_projeto.db');

  if (!$caminhoBanco || !file_exists($caminhoBanco)) {
      die("Arquivo do banco de dados não encontrado. Caminho buscado: " . __DIR__ . '/../backend/db_projeto.db');
  }

  $pdo = new PDO("sqlite:$caminhoBanco");
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  echo "Caminho resolvido: $caminhoBanco";
  die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $nome = htmlspecialchars($_POST["nome"]);
  $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
  $senha = $_POST["senha"]; // Adicionei esta linha
  $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
  $confirmaSenha = $_POST["confirmaSenha"];

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      die("E-mail inválido!");
  }

  if ($_POST['senha'] === $_POST['confirmaSenha']) {
      try {
          $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
          $stmt->execute([$nome, $email, $senhaHash]); // Usando $senhaHash
          
          if ($stmt->rowCount() > 0) {
            header("Location: ../../telas/indexlogin.php"); 
            exit();
        } else {
            echo "<p style='color: red'>Erro: Nenhum registro foi salvo.</p>";
        }
      } catch (PDOException $e) {
          echo "<p>Erro ao cadastrar: " . $e->getMessage() . "</p>";
      }
  } else {
      echo "<p>As senhas não coincidem.</p>";
  }
}
  
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tela de Cadastro</title>
  <link rel="stylesheet" href="cadastramento.css">
  <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed&display=swap" rel="stylesheet">
</head>
<body>
  <div class="container">
    <div class="form-container">
      <div class="bg-pattern"></div>
      <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
        <img src="../imagens/tanamesa_preto.png" alt="Logo" class="logo">
       
        <label>Nome</label>
        <input type="text" name="nome" placeholder="Como devemos te chamar?" required>




        <label>E-mail</label>
        <input type="email" name="email" placeholder="Digite seu e-mail" required>




        <label>Senha</label>
        <input type="password" name="senha" placeholder="********" required>




        <label>Confirme sua senha</label>
        <input type="password" name="confirmaSenha" placeholder="********" required>




        <button type="submit">CADASTRE-SE</button>




        <p class="login-link">JÁ TEM CADASTRO? <a href="indexlogin.php">VOLTE AQUI!</a></p>
      </form>
    </div>
    <div class="image-container">
      <img src="../imagens/telacadastro.png" alt="Cenário fantasia">
    </div>
  </div>
</body>
</html>



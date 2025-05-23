<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Conecta ao banco (TENHO QUE USAR O MESMO CÓDIGO DO CADASTRO!)
        $caminhoBanco = realpath(__DIR__ . '/../backend/db_projeto.db');
        $pdo = new PDO("sqlite:$caminhoBanco");
        
        // Pega dados do formulário
        $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
        $senha = $_POST["senha"];
        
        // Busca usuário no banco
        $stmt = $pdo->prepare("SELECT senha FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($senha, $usuario['senha'])) {
        header("Location: /pages/home.php");
        exit;
          } else {
        echo "<p style='color: red'>E-mail ou senha inválidos!</p>";
      }
    } catch (PDOException $e) {
        echo "<p style='color: red'>Erro: " . $e->getMessage() . "</p>";
    }
}
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Tela de Login - Tá na Mesa</title>
  <link rel="stylesheet" href="login.css" />
  <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed&display=swap" rel="stylesheet">
</head>
<body>
  <div class="container">
    <div class="left-side"></div>
    <div class="right-side">
    <img src="../imagens/tanamesa_branco.png" alt="Logo Tá na Mesa" class="logo">




      <form class="login-form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
        <label for="email">E-mail</label>
        <input type="email" name="email" id="email" placeholder="Digite seu e-mail" required />




        <label for="senha">Senha</label>
        <input type="password" name="senha" id="senha" placeholder="********" required />




        <button type="submit">Entrar</button>




        <p class="cadastro-text">
          NÃO TEM CADASTRO? <a href="indexcadastramento.php">CADASTRE-SE</a>
        </p>
      </form>
    </div>
  </div>
</body>
</html>



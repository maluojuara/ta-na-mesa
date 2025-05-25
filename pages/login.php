<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

$erroLogin = '';

if (isset($_GET['erro']) && $_GET['erro'] == 1) {
	$erroLogin = 'E-mail ou senha inválidos. Tente novamente!';
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	try {
		// Conecta ao banco (TENHO QUE USAR O MESMO CÓDIGO DO CADASTRO!)
		$caminhoBanco = realpath(__DIR__ . '/../backend/db_projeto.db');
		$pdo = new PDO("sqlite:$caminhoBanco");

		// Pega dados do formulário
		$email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
		$senha = $_POST["senha"];


		// Busca usuário no banco
		$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
		$stmt->execute([$email]);


		$usuario = $stmt->fetch();

		if ($usuario && password_verify($senha, $usuario['senha'])) {
			foreach ($usuario as $chave => $valor) {
				if (!is_int($chave)) {
					$_SESSION[$chave] = $valor;
				}
			}

			header("Location: home.php");
			exit();

		} else {
			header("Location: login.php?erro=1");
			exit();
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
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Tela de Login - Tá na Mesa</title>
	<link rel="stylesheet" href="style/login.css" />
	<link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@100;300;400;700&display=swap"
		rel="stylesheet">
</head>

<body>
	<div class="container">
		<div class="left-side"></div>
		<div class="right-side">
			<img src="img/tanamesa_branco.png" alt="Logo Tá na Mesa" class="logo">




			<form class="login-form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
				<?php if (!empty($erroLogin)): ?>
					<p style="color: red;"><?php echo $erroLogin; ?></p>
				<?php endif; ?>

				<label for="email">E-mail</label>
				<input type="email" name="email" id="email" placeholder="Digite seu e-mail" required />




				<label for="senha">Senha</label>
				<input type="password" name="senha" id="senha" placeholder="********" required />




				<button type="submit">Entrar</button>




				<p class="cadastro-text">
					NÃO TEM CADASTRO? <a href="cadastrar_usuario.php">CADASTRE-SE</a>
				</p>
			</form>
		</div>
	</div>

	<script>
		if (window.location.search.includes('erro=1')) {
			window.history.replaceState({}, document.title, window.location.pathname);
		}
	</script>

</body>

</html>
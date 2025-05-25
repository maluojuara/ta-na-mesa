<?php
require '../backend/conexao.php';
$db = new MyDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$nome = htmlspecialchars($_POST['nome']);
	$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
	$senha = $_POST['senha'];
	$confirma = $_POST['confirmaSenha'];

	$verifica = $db->prepare("SELECT COUNT(*) FROM usuarios WHERE email = :email");
	$verifica->bindValue(':email', $email);
	$result = $verifica->execute();
	$dado = $result->fetchArray(SQLITE3_NUM);
	$jaExiste = $dado[0];

	if ($jaExiste > 0) {
		echo "<script>alert('Este e-mail já está cadastrado.'); window.history.back();</script>";
		exit;
	}


	if ($jaExiste > 0) {
		echo "<script>alert('Este e-mail já está cadastrado.'); window.history.back();</script>";
		exit;
	}

	$senhaHash = password_hash($senha, PASSWORD_DEFAULT);


	$stmt = $db->prepare("
    INSERT INTO usuarios (
        nome, email, senha
    ) VALUES (
        :nome, :email, :senha
    )");

	$stmt->bindValue(':nome', $nome);
	$stmt->bindValue(':email', $email);
	$stmt->bindValue(':senha', $senhaHash);

	if ($stmt->execute()) {
		echo "<script>
			alert('✅ Usuário cadastrado com sucesso!');
			window.location.href = 'login.php';
		</script>";
	}
	else {
		echo "<script>alert('❌ Erro ao cadastrar usuário.');</script>";
	}
}

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Tela de Cadastro</title>
	<link rel="stylesheet" href="style/novo_usuario.css">
	<link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@100;300;400;700&display=swap" rel="stylesheet">

</head>

<body>
	<div class="container">
		<div class="form-container">
			<div class="bg-pattern"></div>
			<form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
				<img src="img/tanamesa_preto.png" alt="Logo" class="logo">

				<label>Nome</label>
				<input type="text" name="nome" placeholder="Como devemos te chamar?" required>




				<label>E-mail</label>
				<input type="email" name="email" placeholder="Digite seu e-mail" required>


				<label>Senha</label>
				<input type="password" name="senha" id="senha" placeholder="********" required>

				<label>Confirme sua senha</label>
				<input type="password" name="confirmaSenha" id="confirmaSenha" placeholder="********" required>





				<button type="submit">CADASTRE-SE</button>




				<p class="login-link">JÁ TEM CADASTRO? <a href="login.php">VOLTE AQUI!</a></p>
			</form>
		</div>
		<div class="image-container">
			<img src="img/telacadastro.png" alt="Cenário fantasia">
		</div>
	</div>

	<script>
		const form = document.querySelector('form');
		const senha = document.getElementById('senha');
		const confirma = document.getElementById('confirmaSenha');

		form.addEventListener('submit', function (e) {
			if (senha.value !== confirma.value) {
				e.preventDefault();
				confirma.setCustomValidity("As senhas não coincidem.");
				confirma.reportValidity();
			} else {
				confirma.setCustomValidity("");
			}
		});

		senha.addEventListener('input', () => {
			confirma.setCustomValidity("");
		});

		confirma.addEventListener('input', () => {
			if (senha.value === confirma.value) {
				confirma.setCustomValidity("");
			}
		});
	</script>

</body>

</html>
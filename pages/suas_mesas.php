<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

require '../backend/conexao.php';
$db = new MyDB();



if ($_SERVER['REQUEST_METHOD'] === 'POST') {

	$stmt = $db->prepare("
    UPDATE usuarios
	SET foto_perfil = :foto_perfil,
		link_contato = :link_contato,
		descricao = :descricao
	WHERE id = :id_usuario
	");
	$foto = $_POST['foto_perfil'] ?? '';
	$link = $_POST['link_contato'] ?? '';
	$descricao = $_POST['descricao'] ?? '';
	$idUsuario = $_SESSION['id'] ?? null;

	$stmt->bindValue(':foto_perfil', $foto);
	$stmt->bindValue(':link_contato', $link);
	$stmt->bindValue(':descricao', $descricao);
	$stmt->bindValue(':id_usuario', $idUsuario, SQLITE3_INTEGER);


	if ($stmt->execute()) {
		echo "<script>alert('✅ Altualização feita com sucesso');</script>";
	} else {
		echo "<script>alert('❌ Erro na atualização das informações.');</script>";
	}
}
?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="style/suas_mesas.css">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link
		href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&display=swap"
		rel="stylesheet">
	<title>Suas mesas</title>
</head>

<body>
	<header class="navbar">

		<div class="logo">
			<a href="home.php"> <img src="img/logo.png"
					alt="Logo do site Ta na Mesa, em formato de um D20 com o nome do site"></a>
		</div>

		<nav class="menu">
			<a href="suas_mesas.php">Suas Mesas</a>
			<a href="home.php">Mesas</a>
			<a href="cadastrar_mesa.php">Cadastro de Mesas</a>
		</nav>
		<div class="avatar" style="display: flex; align-items: center; gap: 10px;">
			<a href="#">
				<img src="<?php echo htmlspecialchars($_SESSION['foto_perfil'] ?? 'img/mestre.svg'); ?>"
					alt="avatar do usuário" class="usuario"
					style="border-radius: 50%; width: 40px; height: 40px; object-fit: cover;">
			</a>
			<form action="logout.php" method="post" style="margin: 0;">
				<button type="submit"
					style="background: none; border: none; color: #fff; font-size: 14px; cursor: pointer;">
					sair
				</button>
			</form>
		</div>

	</header>

	<div class="banner">
		<img src="img/bg-suas-mesas.png" alt="Banner visual de fundo" class="banner-img">
		<div class="banner-texto">
			<h1>Suas mesas</h1>
			<img src="img/linha-decor.png" alt="Linha decorativa">
		</div>
	</div>

	<main>
		<div class="box-perfil">
			<div class="foto-perfil">
				<img src="img/foto-perfil.png" alt="Foto de perfil">
			</div>

			<div class="formulario">
				<h3>Seu perfil</h3>
				<form method="POST">
					<label for="foto">Foto de perfil:</label>
					<input name="foto_perfil" type="text" id="foto" placeholder="URL da imagem">

					<label for="link">Link para contato:</label>
					<input name="link_contato" type="text" id="link" placeholder="Ex: Discord, Telegram...">

					<label for="descricao">Breve descrição:</label>
					<textarea name="descricao" id="descricao" placeholder="Fale um pouco sobre você..."></textarea>

					<button type="submit">Salvar</button>
				</form>
			</div>
		</div>

		<section class="suas_mesas">
			<div class="mesa-container">
				<div class="mesa">
					<img class="mesa-foto" src="img/Mesa.png" alt="Mesa de RPG">
					<div class="mesa-titulo">
						CAMINHOS DA FLORESTA
					</div>
				</div>
				<div class="mesa-botoes">
					<button>editar</button>
					<button>excluir</button>
				</div>
			</div>
		</section>

	</main>

</body>

</html>
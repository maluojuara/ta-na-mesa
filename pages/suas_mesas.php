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
		<div class="avatar">
			<a href="#"> <img src="img/mestre.svg" alt="avatar do usuário" class="usuario"> </a>
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
					<input type="text" id="foto" placeholder="URL da imagem">

					<label for="link">Link para contato:</label>
					<input type="text" id="link" placeholder="Ex: Discord, Telegram...">

					<label for="descricao">Breve descrição:</label>
					<textarea id="descricao" rows="3" placeholder="Fale um pouco sobre você..."></textarea>

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
<?php
session_start();

if (!isset($_SESSION['id'])) {
	header("Location: login.php");
	exit();
}

require '../backend/conexao.php';
$db = new MyDB();

// 1. Sistemas de Regras
$sistemas_result = $db->query("SELECT id, nome FROM sistema_regras");
$sistemas = [];
if ($sistemas_result) { // Verifica se a consulta retornou um resultado válido
    while ($row = $sistemas_result->fetchArray(SQLITE3_ASSOC)) {
        $sistemas[] = $row;
    }
}

// 2. Categorias
$categorias_result = $db->query("SELECT id, nome FROM categorias");
$categorias = [];
if ($categorias_result) {
    while ($row = $categorias_result->fetchArray(SQLITE3_ASSOC)) {
        $categorias[] = $row;
    }
}

// 3. Tipos de Campanha
$tipos_campanha_result = $db->query("SELECT id, nome FROM tipos_campanha");
$tipos_campanha = [];
if ($tipos_campanha_result) {
    while ($row = $tipos_campanha_result->fetchArray(SQLITE3_ASSOC)) {
        $tipos_campanha[] = $row;
    }
}

// 4. Níveis de Experiência
$niveis_experiencia_result = $db->query("SELECT id, nome FROM niveis_experiencia");
$niveis_experiencia = [];
if ($niveis_experiencia_result) {
    while ($row = $niveis_experiencia_result->fetchArray(SQLITE3_ASSOC)) {
        $niveis_experiencia[] = $row;
    }
}

// 5. Localizações
$localizacoes_result = $db->query("SELECT id, nome FROM localizacoes");
$localizacoes = [];
if ($localizacoes_result) {
    while ($row = $localizacoes_result->fetchArray(SQLITE3_ASSOC)) {
        $localizacoes[] = $row;
    }
}

// Trata submissões do tipo POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

	// Se for o formulário de exclusão
	if (isset($_POST['id_mesa_excluir'])) {
		$idMesa = $_POST['id_mesa_excluir'];

		$stmt = $db->prepare("UPDATE mesas SET apagada = 1 WHERE id = :id AND id_organizador = :org");
		$stmt->bindValue(':id', $idMesa, SQLITE3_INTEGER);
		$stmt->bindValue(':org', $_SESSION['id'], SQLITE3_INTEGER);
		$stmt->execute();

		echo "<script>alert('✅ Mesa excluída com sucesso!');</script>";
		header("Location: suas_mesas.php");
		exit();
	}

	// Se for o formulário de perfil
	elseif (isset($_POST['foto_perfil']) || isset($_POST['link_contato']) || isset($_POST['descricao'])) {
		$foto = trim($_POST['foto_perfil'] ?? '');
		$link = trim($_POST['link_contato'] ?? '');
		$descricao = trim($_POST['descricao'] ?? '');
		$idUsuario = $_SESSION['id'] ?? null;

		// Se todos os campos estiverem vazios, não faz nada
		if ($foto === '' && $link === '' && $descricao === '') {
			// Ignora a submissão
		} else {
			$stmt = $db->prepare("
				UPDATE usuarios
				SET foto_perfil = :foto_perfil,
					link_contato = :link_contato,
					descricao = :descricao
				WHERE id = :id_usuario
			");

			$stmt->bindValue(':foto_perfil', $foto);
			$stmt->bindValue(':link_contato', $link);
			$stmt->bindValue(':descricao', $descricao);
			$stmt->bindValue(':id_usuario', $idUsuario, SQLITE3_INTEGER);

			if ($stmt->execute()) {
				$_SESSION['foto_perfil'] = $foto;
				$_SESSION['link_contato'] = $link;
				$_SESSION['descricao'] = $descricao;

				echo "<script>alert('✅ Atualização feita com sucesso');</script>";
				header("Location: suas_mesas.php");
				exit();
			} else {
				echo "<script>alert('❌ Erro na atualização das informações.');</script>";
				header("Location: suas_mesas.php");
				exit();
			}
		}
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
	<script src="https://cdn.tailwindcss.com"></script>
	<link
		href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&display=swap"
		rel="stylesheet">
	<title>Suas mesas</title>
</head>

<body>
	<header class="navbar">
		<div class="logo">
			<a href="home.php">
				<img src="img/logo.png" alt="Logo do site Ta na Mesa, em formato de um D20 com o nome do site">
			</a>
		</div>

		<nav class="menu">
			<a href="suas_mesas.php">Suas Mesas</a>
			<a href="home.php">Mesas</a>
			<a href="cadastrar_mesa.php">Cadastro de Mesas</a>
		</nav>

		<div class="avatar" style="display: flex; align-items: center; gap: 10px;">
			<a href="suas_mesas.php">
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
			<h3>Seu perfil</h3>

			<div class="formulario">
				<div class="foto-perfil">
					<img src="<?php echo htmlspecialchars($_SESSION['foto_perfil'] ?? 'img/mestre.svg'); ?>"
						alt="Foto de perfil">
				</div>
				<form method="POST">
					<label for="foto">Foto de perfil:</label>
					<input name="foto_perfil" type="url" id="foto" placeholder="URL da imagem">

					<label for="link">Link para contato:</label>
					<input name="link_contato" type="text" id="link" placeholder="Ex: Discord, Telegram...">

					<label for="descricao">Breve descrição:</label>
					<textarea name="descricao" id="descricao" placeholder="Fale um pouco sobre você..."></textarea>

					<button type="submit">Salvar</button>
				</form>
			</div>
		</div>

		<section class="suas_mesas">
			<?php
			$stmt = $db->prepare("SELECT * FROM mesas WHERE id_organizador = :id AND apagada = 0");
			$stmt->bindValue(':id', $_SESSION['id'], SQLITE3_INTEGER);
			$resultado = $stmt->execute();

			while ($row = $resultado->fetchArray(SQLITE3_ASSOC)):
				?>
				<div class="mesa-container">
					<div class="mesa">
						<img class="mesa-foto" src="<?php echo htmlspecialchars($row['img_capa']); ?>" alt="Mesa de RPG">
						<div class="mesa-titulo">
							<?php echo htmlspecialchars(strtoupper($row['nome'])); ?>
						</div>
					</div>
					<div class="mesa-botoes">
						<button class="editar" data-id="<?= $row['id'] ?>"
							data-nome="<?= htmlspecialchars(addslashes($row['nome'])) ?>"
							data-img="<?= htmlspecialchars(addslashes($row['img_capa'])) ?>">
							editar
						</button>





						<form method="POST" onsubmit="return confirmarExclusao();" style="display: inline;">
							<input type="hidden" name="id_mesa_excluir" value="<?= $row['id'] ?>">
							<button class="excluir" type="submit">excluir</button>
						</form>
					</div>
				</div>
			<?php endwhile; ?>
		</section>
	</main>

	<footer>
		<div class="footer-direitos">
			<img src="img/©.png" class="footer-img" alt="simbolo do copyright">
			<p>direitos reservados 2025</p>
		</div>
		<p>Este site foi desenvolvido por Maria Vivielle, Malu Araujo, Luana Miyashiro e Isabelle de Matos</p>

	</footer>

<!-- Modal de edição -->
<div id="modal-editar" class="fixed inset-0 bg-black bg-opacity-80 flex justify-center items-center hidden z-50">
    <div class="bg-[#111] w-[700px] rounded-lg overflow-hidden shadow-lg relative">

        <img id="editar-capa-img" src="" alt="Imagem da mesa" class="w-full h-60 object-cover">

        <button class="absolute top-2 right-4 text-white text-2xl font-bold fechar-modal">
            &times;
        </button>

        <form method="POST" action="atualizar_mesa.php" class="p-6 text-white space-y-4">
            <h2 class="text-3xl font-serif text-center">Edite sua mesa!</h2>
            <hr class="border-gray-600 mb-4">

            <input type="hidden" name="id_mesa" id="editar-id">

            <label for="editar-nome">Nome:</label>
            <input type="text" name="nome" id="editar-nome" class="w-full p-2 rounded text-black"
                placeholder="Nome da Campanha">

            <label for="editar-sistema">Sistema de regras:</label>
            <select name="id_sistema_regras" id="editar-sistema" class="w-full p-2 rounded text-black">
                <option value="" disabled selected>Selecione uma opção</option>
                <?php
                // Verifica se $sistemas existe e é um array antes de iterar
                if (isset($sistemas) && is_array($sistemas)) {
                    foreach ($sistemas as $sistema): ?>
                        <option value="<?= htmlspecialchars($sistema['id']) ?>"><?= htmlspecialchars($sistema['nome']) ?></option>
                    <?php endforeach;
                }
                ?>
            </select>

            <label for="editar-categoria">Categoria:</label>
            <select name="id_categoria" id="editar-categoria" class="w-full p-2 rounded text-black">
                <option value="" disabled selected>Selecione uma opção</option>
                <?php
                if (isset($categorias) && is_array($categorias)) {
                    foreach ($categorias as $categoria): ?>
                        <option value="<?= htmlspecialchars($categoria['id']) ?>"><?= htmlspecialchars($categoria['nome']) ?></option>
                    <?php endforeach;
                }
                ?>
            </select>

            <label for="editar-campanha">Campanha:</label>
            <select name="id_tipo_campanha" id="editar-campanha" class="w-full p-2 rounded text-black">
                <option value="" disabled selected>Selecione uma opção</option>
                <?php
                if (isset($tipos_campanha) && is_array($tipos_campanha)) {
                    foreach ($tipos_campanha as $tipo): ?>
                        <option value="<?= htmlspecialchars($tipo['id']) ?>"><?= htmlspecialchars($tipo['nome']) ?></option>
                    <?php endforeach;
                }
                ?>
            </select>

            <label for="editar-experiencia">Experiência recomendada:</label>
            <select name="id_nivel_experiencia" id="editar-experiencia" class="w-full p-2 rounded text-black">
                <option value="" disabled selected>Selecione uma opção</option>
                <?php
                if (isset($niveis_experiencia) && is_array($niveis_experiencia)) {
                    foreach ($niveis_experiencia as $nivel): ?>
                        <option value="<?= htmlspecialchars($nivel['id']) ?>"><?= htmlspecialchars($nivel['nome']) ?></option>
                    <?php endforeach;
                }
                ?>
            </select>

            <label for="editar-localizacao">Localização:</label>
            <select name="id_localizacao" id="editar-localizacao" class="w-full p-2 rounded text-black">
                <option value="" disabled selected>Selecione uma opção</option>
                <?php
                if (isset($localizacoes) && is_array($localizacoes)) {
                    foreach ($localizacoes as $localizacao): ?>
                        <option value="<?= htmlspecialchars($localizacao['id']) ?>"><?= htmlspecialchars($localizacao['nome']) ?></option>
                    <?php endforeach;
                }
                ?>
            </select>

            <label for="editar-vagas">Vagas:</label>
            <input type="number" name="vagas" id="editar-vagas" class="w-full p-2 rounded text-black"
                placeholder="0/5">

            <label for="editar-capa">Capa:</label>
            <input type="text" name="img_capa" id="editar-capa" class="w-full p-2 rounded text-black"
                placeholder="Adicione uma URL...">

            <label for="editar-sinopse">Sinopse:</label>
            <textarea name="sinopse" id="editar-sinopse" rows="4" class="w-full p-2 rounded text-black"
                placeholder="Conte um pouco da sua história..."></textarea>

            <div class="flex justify-between gap-4 pt-2">
                <button type="submit" name="salvar_edicao"
                    class="flex-1 bg-[#cd004a] text-white py-2 px-4 rounded font-bold">SALVAR</button>
                <button type="submit" name="toggle_ativa"
                    class="flex-1 bg-gray-300 text-black py-2 px-4 rounded font-bold" id="btn-ativar-desativar">
                    DESATIVAR
                </button>
            </div>
        </form>
    </div>
</div>


<script>
	document.addEventListener('DOMContentLoaded', () => {
		console.log("Script DOMContentLoaded foi executado!");
		// Excluir com confirmação
		function confirmarExclusao() {
			return confirm("Tem certeza que deseja excluir esta mesa?");
		}

		// Abertura do modal com dados da mesa
		function abrirModal(botao) {
			const id = botao.getAttribute('data-id');
			const nome = botao.getAttribute('data-nome');
			const imagem = botao.getAttribute('data-img');

			document.getElementById('editar-id').value = id;
			document.getElementById('editar-nome').value = nome;
			document.getElementById('editar-capa').value = imagem;
			document.getElementById('editar-capa-img').src = imagem;

			document.getElementById('modal-editar').classList.remove('hidden');
		}

		// Fechar o modal
		function fecharModal() {
			document.getElementById('modal-editar').classList.add('hidden');
		}

		// Atualizar imagem de capa ao digitar
		const inputCapa = document.getElementById('editar-capa');
		if (inputCapa) {
			inputCapa.addEventListener('input', (e) => {
				document.getElementById('editar-capa-img').src = e.target.value;
			});
		}

		// Adiciona o event listener a todos os botões de editar
		const botoesEditar = document.querySelectorAll('.editar');
		botoesEditar.forEach((botao) => {
			botao.addEventListener('click', () => abrirModal(botao));
		});

		// Adiciona event listener para o botão de fechar, se existir
		const botaoFechar = document.querySelector('#modal-editar .fechar-modal');
		if (botaoFechar) {
			botaoFechar.addEventListener('click', fecharModal);
		}

		// Torna a confirmação de exclusão global (ainda usa onclick no form)
		window.confirmarExclusao = confirmarExclusao;
	});
</script>

</body>

</html>
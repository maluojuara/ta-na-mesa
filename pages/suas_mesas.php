<?php
session_start();

if (!isset($_SESSION['id'])) {
	header("Location: login.php");
	exit();
}

require '../backend/conexao.php';
$db = new MyDB();

// Informações sobre o usuário naquela section
$idUsuario = $_SESSION['id'] ?? null;

$res = $db->query("SELECT foto_perfil, link_contato, descricao FROM usuarios WHERE id = $idUsuario");
$data = $res->fetchArray(SQLITE3_ASSOC);

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
		$idUsuario = $_SESSION['id'] ?? null;

		// Recupera o e-mail do usuário (para usar no "mailto" padrão, se for o caso)
		$resEmail = $db->query("SELECT email FROM usuarios WHERE id = $idUsuario");
		$usuario = $resEmail->fetchArray(SQLITE3_ASSOC);

		$emailUsuario = $usuario['email'] ?? '';

		// Cria um array para os campos a serem atualizados
		$campos = [];
		$params = [];

		// Foto de perfil
		if (isset($_POST['foto_perfil'])) {
			$foto = trim($_POST['foto_perfil']);
			if ($foto === '') {
				$foto = 'img/mestre.svg'; // padrão se vazio
			}
			$campos[] = "foto_perfil = :foto_perfil";
			$params[':foto_perfil'] = $foto;
		}

		// Link de contato
		if (isset($_POST['link_contato'])) {
			$link = trim($_POST['link_contato']);
			if ($link === '' && $emailUsuario !== '') {
				$link = 'mailto:' . $emailUsuario; // padrão se vazio e tem e-mail
			}
			$campos[] = "link_contato = :link_contato";
			$params[':link_contato'] = $link;
		}

		// Descrição (pode ser vazia mesmo)
		if (isset($_POST['descricao'])) {
			$campos[] = "descricao = :descricao";
			$params[':descricao'] = trim($_POST['descricao']);
		}

		// Se houver campos para atualizar
		if (!empty($campos)) {
			// Monta o SQL dinâmico com os campos informados
			$sql = "UPDATE usuarios SET " . implode(", ", $campos) . " WHERE id = :id_usuario";
			$stmt = $db->prepare($sql);

			// Vincula os valores ao statement
			foreach ($params as $key => $val) {
				$stmt->bindValue($key, $val);
			}
			$stmt->bindValue(':id_usuario', $idUsuario, SQLITE3_INTEGER);

			if ($stmt->execute()) {
				// Atualiza também a sessão
				if (isset($params[':foto_perfil'])) {
					$_SESSION['foto_perfil'] = $params[':foto_perfil'];
				}
				if (isset($params[':link_contato'])) {
					$_SESSION['link_contato'] = $params[':link_contato'];
				}
				if (isset($params[':descricao'])) {
					$_SESSION['descricao'] = $params[':descricao'];
				}

				echo "<script>alert('✅ Atualização feita com sucesso');</script>";
				header("Location: suas_mesas.php");
				exit();
			} else {
				echo "<script>alert('❌ Erro na atualização.');</script>";
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
		<link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
	<title>Suas mesas</title>
</head>

<body>
	<header class="navbar">

        <div class="logo">
            <a href="home.php"> <img src="img/logo.png"
                    alt="Logo do site Ta na Mesa, em formato de um D20 com o nome do site"></a>
        </div>

        <nav class="menu">
            <a href="home.php">Mesas</a>
            <a href="cadastrar_mesa.php">Cadastre sua mesa</a>

        </nav>
        <div class="avatar" style="display: flex; align-items: center;">
            <a href="suas_mesas.php">
                <img src="<?php echo htmlspecialchars($_SESSION['foto_perfil'] ?? 'img/mestre.svg'); ?>"
                    alt="avatar do usuário" class="usuario">
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
					<?php
					$fotoInput = ($data['foto_perfil'] !== 'img/mestre.svg') ? htmlspecialchars($data['foto_perfil']) : '';
					?>
					<input name="foto_perfil" type="url" id="foto" value="<?php echo $fotoInput; ?>"
						placeholder="URL da imagem">


					<label for="link">Link para contato:</label>
					<?php
					$linkInput = (strpos($data['link_contato'], 'mailto:') !== 0) ? htmlspecialchars($data['link_contato']) : '';
					?>
					<input name="link_contato" type="text" id="link" value="<?php echo $linkInput; ?>"
						placeholder="Ex: Discord, Instagram...">


					<label for="descricao">Breve descrição:</label>
					<textarea name="descricao" id="descricao"
						placeholder="Fale um pouco sobre você..."><?php echo htmlspecialchars($data['descricao'] ?? ''); ?></textarea>


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
						<img class="mesa-foto <?php echo ($row['ativa'] == 0) ? 'desativada' : ''; ?>"
							src="<?php echo htmlspecialchars($row['img_capa']); ?>" alt="Mesa de RPG">
						<?php if ($row['ativa'] == 0): ?>
							<div class="faixa-desativada">DESATIVADA</div>
						<?php endif; ?>

						<div class="mesa-titulo">
							<?php echo htmlspecialchars(strtoupper($row['nome'])); ?>
						</div>
					</div>
					<div class="mesa-botoes">

						<button class="editar" data-id="<?= $row['id'] ?>"
							data-nome="<?= htmlspecialchars(addslashes($row['nome'])) ?>"
							data-img="<?= htmlspecialchars(addslashes($row['img_capa'])) ?>"
							data-sistema="<?= $row['id_sistema_regras'] ?>" data-categoria="<?= $row['id_categoria'] ?>"
							data-campanha="<?= $row['id_tipo_campanha'] ?>"
							data-experiencia="<?= $row['id_nivel_experiencia'] ?>"
							data-localizacao="<?= $row['id_localizacao'] ?>" data-vagas="<?= $row['vagas'] ?>"
							data-sinopse="<?= htmlspecialchars(addslashes($row['sinopse'])) ?>"
							data-ativa="<?= $row['ativa'] ?>">
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
		&copy; direitos reservados 2025<br>
		Este site foi desenvolvido por Maria Vivielle, Malu Araujo, Luana Miyashiro e Isabelle Matos 💖<br>
	</footer>

	<!-- Modal de edição -->
	<div id="modal-editar" class="fixed inset-0 bg-black bg-opacity-80 flex justify-center items-center hidden z-50">
		<div
			class="bg-[#111] w-[90vw] max-w-[700px] max-h-[70vh] overflow-y-auto rounded-lg overflow-hidden shadow-lg relative">

			<img id="editar-capa-img" src="" alt="Imagem da mesa" class="w-full h-60 object-cover">

			<button class="absolute top-2 right-4 text-white text-2xl font-bold fechar-modal">
				&times;
			</button>

			<form method="POST" action="atualizar_mesa.php" class="p-6 text-white space-y-4">
    <h2 class="text-3xl font-serif text-center">Edite sua mesa!</h2>
    <div class="linha-div-titulo"></div>

    <input type="hidden" name="id_mesa" id="editar-id">

    <div class="modal-form-group">
        <label for="editar-nome">Nome:</label>
        <input type="text" name="nome" id="editar-nome" class="w-full p-2 rounded text-black"
            placeholder="Nome da Campanha">
    </div>

    <div class="modal-form-group">
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
    </div>

    <div class="modal-form-group">
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
    </div>

    <div class="modal-form-group">
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
    </div>

    <div class="modal-form-group">
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
    </div>

    <div class="modal-form-group">
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
    </div>

    <div class="modal-form-group">
        <label for="editar-vagas">Vagas:</label>
        <input type="number" name="vagas" id="editar-vagas" class="w-full p-2 rounded text-black"
            placeholder="0/5">
    </div>

    <div class="modal-form-group">
        <label for="editar-capa">Capa:</label>
        <input type="text" name="img_capa" id="editar-capa" class="w-full p-2 rounded text-black"
            placeholder="Adicione uma URL...">
    </div>

    <div class="modal-form-group">
    	<label for="editar-sinopse">Sinopse:</label>
    	<textarea name="sinopse" id="editar-sinopse" rows="4" class="w-full p-2 rounded text-black"
        placeholder="Conte um pouco da sua história..."></textarea>
</div>

    <div class="flex justify-between gap-4 pt-2">
        <button type="submit" name="salvar_edicao"
            class="flex-1 bg-[#cd004a] text-white py-2 px-4 rounded font-bold">SALVAR</button>

        <button type="button" class="flex-1 py-2 px-4 rounded font-bold" id="btn-ativar-desativar">DESATIVAR
        </button>
    </div>
</form>
	</div>
	</div>


	<script>
		document.addEventListener('DOMContentLoaded', () => {
			// Excluir com confirmação
			function confirmarExclusao() {
				return confirm("Tem certeza que deseja excluir esta mesa?");
			}


			// Abertura do modal com dados da mesa
			function abrirModal(botao) {
				const id = botao.getAttribute('data-id');
				const nome = botao.getAttribute('data-nome');
				const imagem = botao.getAttribute('data-img');

				// Novos campos
				const sistema = botao.getAttribute('data-sistema');
				const categoria = botao.getAttribute('data-categoria');
				const campanha = botao.getAttribute('data-campanha');
				const experiencia = botao.getAttribute('data-experiencia');
				const localizacao = botao.getAttribute('data-localizacao');
				const vagas = botao.getAttribute('data-vagas');
				const sinopse = botao.getAttribute('data-sinopse');
				const ativa = botao.getAttribute('data-ativa');

				document.getElementById('editar-id').value = id;
				document.getElementById('editar-nome').value = nome;
				document.getElementById('editar-capa').value = imagem;
				document.getElementById('editar-capa-img').src = imagem;

				document.getElementById('editar-sistema').value = sistema;
				document.getElementById('editar-categoria').value = categoria;
				document.getElementById('editar-campanha').value = campanha;
				document.getElementById('editar-experiencia').value = experiencia;
				document.getElementById('editar-localizacao').value = localizacao;
				document.getElementById('editar-vagas').value = vagas;
				document.getElementById('editar-sinopse').value = sinopse;

				const btnAtivarDesativar = document.getElementById('btn-ativar-desativar');
				if (btnAtivarDesativar) {
					if (ativa === '1') { // se o status que veio do banco é '1' (ativa)
						btnAtivarDesativar.textContent = 'DESATIVAR';
						btnAtivarDesativar.setAttribute('data-status', 'ativa'); // Guarda o status atual no próprio botão
						btnAtivarDesativar.style.backgroundColor = '#d1d5db'; // Cor para "DESATIVAR"
						btnAtivarDesativar.style.color = 'black';
					} else { // se o status que veio do banco é '0' (inativa)
						btnAtivarDesativar.textContent = 'ATIVAR';
						btnAtivarDesativar.setAttribute('data-status', 'inativa'); // Guarda o status atual no próprio botão
						btnAtivarDesativar.style.backgroundColor = '#22c55e'; // Cor para "ATIVAR"
						btnAtivarDesativar.style.color = 'white';
					}
				}
				document.getElementById('modal-editar').classList.remove('hidden');
			}


			// Fechar o modal
			function fecharModal() {
				document.getElementById('modal-editar').classList.add('hidden');
			}

			const fundoModal = document.getElementById('modal-editar');
			fundoModal.addEventListener('click', (event) => {
				if (event.target === fundoModal) {
					fecharModal();
				}
			});

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

			const btnAtivarDesativar = document.getElementById('btn-ativar-desativar');
			if (btnAtivarDesativar) {
				btnAtivarDesativar.addEventListener('click', () => {
					const idMesa = document.getElementById('editar-id').value; // Pega o ID da mesa do campo oculto no modal
					const statusAtual = btnAtivarDesativar.getAttribute('data-status'); // Pega o status que o JS já colocou ('ativa' ou 'inativa')
					let novoStatusDB = statusAtual === 'ativa' ? 0 : 1; // Converte para 0 (inativa) ou 1 (ativa) para o banco
					let acaoTexto = statusAtual === 'ativa' ? 'desativar' : 'ativar'; // Texto para o alerta

					// Pergunta ao usuário se ele tem certeza
					if (confirm(`Tem certeza que deseja ${acaoTexto} esta mesa?`)) {
						// Se confirmar, vamos criar um formulário temporário no JS
						const tempForm = document.createElement('form');
						tempForm.method = 'POST';
						tempForm.action = 'atualizar_mesa.php'; // Para onde o PHP vai receber esses dados

						// Cria um campo oculto para o ID da mesa
						const inputIdMesa = document.createElement('input');
						inputIdMesa.type = 'hidden';
						inputIdMesa.name = 'id_mesa_toggle_ativa'; // Nome que o PHP vai buscar
						inputIdMesa.value = idMesa;
						tempForm.appendChild(inputIdMesa);

						// Cria um campo oculto para o novo status
						const inputStatus = document.createElement('input');
						inputStatus.type = 'hidden';
						inputStatus.name = 'status_toggle_ativa'; // Nome que o PHP vai buscar
						inputStatus.value = novoStatusDB;
						tempForm.appendChild(inputStatus);

						// Anexa o formulário ao corpo da página (ele fica invisível) e submete
						document.body.appendChild(tempForm);
						tempForm.submit(); // Isso envia os dados para o PHP!
					}
				});
			}
		});
	</script>

</body>

</html>
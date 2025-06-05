<?php
session_start();


if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

require_once '../backend/conexao.php';
$db = new MyDB();
?>



<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Mesas de RPG</title>
    <link rel="stylesheet" href="style/home.css" />
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <header class="navbar">

        <div class="logo">
            <a href="home.php"> <img src="img/logo.png"
                    alt="Logo do site Ta na Mesa, em formato de um D20 com o nome do site"></a>
        </div>

        <nav class="menu">
            <a href="home.php">Mesas</a>
            <a href="cadastrar_mesa.php">Cadastro de Mesas</a>

        </nav>
        <div class="avatar" style="display: flex; align-items: center; gap: 10px;">
    <a href="suas_mesas.php">
        <img src="<?php echo htmlspecialchars($_SESSION['foto_perfil'] ?? 'img/mestre.svg'); ?>"
             alt="avatar do usuário"
             class="usuario"
             style="border-radius: 50%; width: 40px; height: 40px; object-fit: cover;">
    </a>
     <form action="logout.php" method="post" style="margin: 0;">
                    <button type="submit" style="background: none; border: none; color: #fff; font-size: 14px; cursor: pointer;">
                        sair
                    </button>
    </form>
</div>



    </header>

    <div class="hero">
        <img src="img/BannerInicial.png" alt="">
        <div class="hero-texto">
            <h1>Encontre a sua Nova Aventura!</h1>
        </div>
        <div class="seta-rolar">
            <img src="img/botao.svg" alt="">
        </div>
    </div>

    <section class="mesas-container">
        <section class="filtro-box">
            <h2 style="display: flex; align-items: center; gap: 0.5rem;">
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="3 4 21 4 14 12.5 14 19 10 20 10 12.5 3 4"></polygon>
                </svg>
                Filtro
            </h2>
            <form action="" method="GET" class="filtros-form">
                <div class="filtros-linha">
                

                    <label>Sistema:
                        <select name="sistema">
                            <option value="">Selecionar</option>
                            <?php
                            $sistemas = $db->query("SELECT * FROM sistema_regras ORDER BY nome");
                            if ($sistemas) {
                                while ($sistema = $sistemas->fetchArray(SQLITE3_ASSOC)) {
                                    $selected = (isset($_GET['sistema']) && $_GET['sistema'] == $sistema['id']) ? 'selected' : '';
                                    echo '<option value="' . $sistema['id'] . '" ' . $selected . '>' . $sistema['nome'] . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </label>

                    <label>Localização:
                        <select name="localizacao">
                            <option value="">Selecionar</option>
                            <?php
                            $localizacoes = $db->query("SELECT * FROM localizacoes ORDER BY nome");
                            if ($localizacoes) {
                                while ($loc = $localizacoes->fetchArray(SQLITE3_ASSOC)) {
                                    $selected = (isset($_GET['localizacao']) && $_GET['localizacao'] == $loc['id']) ? 'selected' : '';
                                    echo '<option value="' . $loc['id'] . '" ' . $selected . '>' . $loc['nome'] . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </label>
                </div>

                <div class="filtros-linha categorias">
                    <span>Categoria:</span>

                    <?php
                    $categorias = $db->query("SELECT * FROM categorias ORDER BY nome");
                    if ($categorias) {
                        while ($cat = $categorias->fetchArray(SQLITE3_ASSOC)) {
                            $id = $cat['id'];
                            $nome = $cat['nome'];
                            $checked = (isset($_GET['categoria']) && in_array($id, $_GET['categoria'])) ? 'checked' : '';
                            echo '<input type="checkbox" id="cat_' . $id . '" name="categoria[]" value="' . $id . '" ' . $checked . ' hidden>';
                            echo '<label for="cat_' . $id . '" class="categoria-botao">' . $nome . '</label>';
                        }
                    }
                    ?>
                </div>


                <div class="filtros-botoes">
                    <button type="submit" name="aplicar" value="1" class="aplicar">Aplicar</button>
                    <a href="home.php" class="resetar"
                        style="display: inline-block; text-decoration: none; text-align: center;">Limpar Filtros</a>
                </div>
            </form>
        </section>

        <div class="cards">
            <?php
            $where = ["m.apagada = 0", "m.ativa = 1"];
            $params = [];

            if (isset($_GET['aplicar'])) {
                if (!empty($_GET['sistema'])) {
                    $where[] = "m.id_sistema_regras = :sistema";
                    $params[':sistema'] = $_GET['sistema'];
                }

                if (!empty($_GET['localizacao'])) {
                    $where[] = "m.id_localizacao = :localizacao";
                    $params[':localizacao'] = $_GET['localizacao'];
                }

                if (!empty($_GET['data'])) {
                    $where[] = "DATE(m.created_at) = :data";
                    $params[':data'] = $_GET['data'];
                }

                if (!empty($_GET['categoria']) && is_array($_GET['categoria'])) {
                    $where[] = "m.id_categoria IN (" . implode(',', array_map('intval', $_GET['categoria'])) . ")";
                }
            }

           $query = "SELECT m.*, 
         sr.nome AS sistema_nome,
         ne.nome AS nivel_experiencia_nome,
         l.nome AS localizacao_nome,
         c.nome AS categoria_nome,
         tc.nome AS tipo_campanha_nome,
         u.nome AS mestre_nome,
         u.foto_perfil AS mestre_foto,
         u.descricao AS mestre_descricao,
         u.link_contato AS mestre_link
         FROM mesas m
         LEFT JOIN sistema_regras sr ON m.id_sistema_regras = sr.id
         LEFT JOIN niveis_experiencia ne ON m.id_nivel_experiencia = ne.id
         LEFT JOIN localizacoes l ON m.id_localizacao = l.id
         LEFT JOIN categorias c ON m.id_categoria = c.id
         LEFT JOIN tipos_campanha tc ON m.id_tipo_campanha = tc.id
         LEFT JOIN usuarios u ON m.id_organizador = u.id
         WHERE " . implode(' AND ', $where);

            $stmt = $db->prepare($query);
            if ($stmt) {
                foreach ($params as $param => $value) {
                    $stmt->bindValue($param, $value);
                }
                $results = $stmt->execute();
            } else {
                $whereSimple = ["apagada = 0"];
                if (!empty($_GET['sistema'])) {
                    $whereSimple[] = "id_sistema_regras = " . intval($_GET['sistema']);
                }
                if (!empty($_GET['localizacao'])) {
                    $whereSimple[] = "id_localizacao = " . intval($_GET['localizacao']);
                }
                if (!empty($_GET['categoria']) && is_array($_GET['categoria'])) {
                    $whereSimple[] = "id_categoria IN (" . implode(',', array_map('intval', $_GET['categoria'])) . ")";
                }
                $query = "SELECT * FROM mesas WHERE " . implode(' AND ', $whereSimple);
                $results = $db->query($query);
            }

            if ($results) {
                while ($mesa = $results->fetchArray(SQLITE3_ASSOC)) {
                    $statusClass = ($mesa['ativa'] == 1) ? 'jogar' : 'indisponivel';
                    $statusText = ($mesa['ativa'] == 1) ? 'JOGAR!' : 'INDISPONÍVEL';

                    echo '<div class="card" 
        data-id="' . $mesa['id'] . '"
        data-nome="' . htmlspecialchars($mesa['nome']) . '"
        data-imagem="' . htmlspecialchars($mesa['img_capa']) . '"
        data-sistema="' . htmlspecialchars($mesa['sistema_nome'] ?? 'Não especificado') . '"
        data-sinopse="' . htmlspecialchars($mesa['sinopse']) . '"
        data-vagas="' . $mesa['vagas'] . '"
        data-experiencia="' . htmlspecialchars($mesa['nivel_experiencia_nome'] ?? 'Não especificado') . '"
        data-localizacao="' . htmlspecialchars($mesa['localizacao_nome'] ?? 'Não especificado') . '"
        data-categoria="' . htmlspecialchars($mesa['categoria_nome'] ?? 'Não especificado') . '"
        data-tipo-campanha="' . htmlspecialchars($mesa['tipo_campanha_nome'] ?? 'Não especificado') . '"
        data-data-criacao="' . $mesa['created_at'] . '"
        data-mestre-nome="' . htmlspecialchars($mesa['mestre_nome'] ?? '') . '"
        data-mestre-foto="' . htmlspecialchars($mesa['mestre_foto'] ?? '') . '"
        data-mestre-descricao="' . htmlspecialchars($mesa['mestre_descricao'] ?? '') . '"
        data-mestre-link="' . htmlspecialchars($mesa['mestre_link'] ?? '') . '">';

                    echo '<img src="' . $mesa['img_capa'] . '" alt="' . $mesa['nome'] . '">';
                    echo '<div class="card-content">';
                    echo '<h3>' . $mesa['nome'] . '</h3>';
                    echo '<p><strong>Sistema:</strong> ' . ($mesa['sistema_nome'] ?? 'Não especificado') . '</p>';
                    echo '<p>' . substr($mesa['sinopse'], 0, 100) . '...</p>';
                    echo '<p><strong>Vagas:</strong> ' . $mesa['vagas'] . '</p>';
                    echo '<p><strong>Experiência:</strong> ' . ($mesa['nivel_experiencia_nome'] ?? 'Não especificado') . '</p>';
                    echo '<p><strong>Localização:</strong> ' . ($mesa['localizacao_nome'] ?? 'Não especificado') . '</p>';
                    echo '</div>';
                    echo '<div class="status ' . $statusClass . '">' . $statusText . '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p>Nenhuma mesa encontrada ou erro na consulta.</p>';
            }
            ?>
        </div>
    </section>

    <!-- Modal para detalhes da mesa -->
    <div class="modal" id="modalMesa">
        <div class="modal-conteudo">
           <span class="fechar" id="fecharModalBtn">&times;</span>

            <div class="imagem-topo">
                <img id="modalImagem" src="" alt="Imagem da mesa">
            </div>

            <div class="detalhes">
    <div class="titulo-container"> <h2 class="titulo-mesa" id="modalTitulo">Título da Mesa</h2>
        <div class="linha-divisoria-titulo"></div> </div>

    <p class="sinopse" id="modalSinopse">Sinopse da aventura.</p>
                

                <div class="info-mesa">
                    <p><strong>Sistema:</strong> <span id="modalSistema"></span></p>
                    <p><strong>Vagas:</strong> <span id="modalVagas"></span></p>
                    <p><strong>Experiência:</strong> <span id="modalExperiencia"></span></p>
                    <p><strong>Localização:</strong> <span id="modalLocalizacao"></span></p>
                    <p><strong>Categoria:</strong> <span id="modalCategoria"></span></p>
                    <p><strong>Tipo de campanha:</strong> <span id="modalTipoCampanhaFull"></span></p>
                    <p><strong>Criada em:</strong> <span id="modalDataCriacao"></span></p>
                </div>
               
     <div class="linha-divisoria-mestre"></div> <div class="mestre-info">
                   <img id="modalMestreFoto" src="img/mestre.svg" alt="Avatar Mestre" class="avatar-mestre">
                   <div> <p><strong>Mestre:</strong> <span id="modalMestreNome"></span></p>
                       <p><strong>Breve descrição:</strong> <em id="modalMestreDescricao"></em></p>
                       <a id="modalMestreLink" href="#" target="_blank" class="botao-discord">
                           Vamos jogar
                       </a>
                   </div> </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer>
        &copy; direitos reservados 2025<br>
        Este site foi desenvolvido por Maria Vivielle, Malu Araujo, Luana Miyashiro e Isabelle Matos 💖<br>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
    const botoesJogar = document.querySelectorAll('.status.jogar');
    const modal = document.getElementById('modalMesa');

    botoesJogar.forEach(botao => {
        botao.addEventListener('click', function () {
            const card = this.closest('.card');

            // Informações da Mesa
            document.getElementById('modalTitulo').textContent = card.dataset.nome;
            document.getElementById('modalImagem').src = card.dataset.imagem;
            document.getElementById('modalSistema').textContent = card.dataset.sistema;
            document.getElementById('modalSinopse').textContent = card.dataset.sinopse;
            document.getElementById('modalVagas').textContent = card.dataset.vagas;
            document.getElementById('modalExperiencia').textContent = card.dataset.experiencia;
            document.getElementById('modalLocalizacao').textContent = card.dataset.localizacao;
            document.getElementById('modalCategoria').textContent = card.dataset.categoria;
            document.getElementById('modalTipoCampanhaFull').textContent = card.dataset.tipoCampanha;

            const dataCriacao = new Date(card.dataset.dataCriacao);
            document.getElementById('modalDataCriacao').textContent = dataCriacao.toLocaleDateString('pt-BR');

            // NOVAS LINHAS: Informações do Mestre
            // Verifica se a foto do mestre existe e usa a padrão se não
            document.getElementById('modalMestreFoto').src = card.dataset.mestreFoto ? card.dataset.mestreFoto : 'img/mestre.svg';
            document.getElementById('modalMestreNome').textContent = `@${card.dataset.mestreNome}`; // Adiciona o '@'
            document.getElementById('modalMestreDescricao').textContent = card.dataset.mestreDescricao;

            const modalMestreLink = document.getElementById('modalMestreLink');
            if (card.dataset.mestreLink) {
                modalMestreLink.href = card.dataset.mestreLink;
                modalMestreLink.style.display = 'inline-flex'; // Mostra o botão se houver link
            } else {
                modalMestreLink.style.display = 'none'; // Esconde o botão se não houver link
            }

            modal.style.display = 'block';
        });
    });

    document.querySelector('.fechar').addEventListener('click', function () {
        modal.style.display = 'none';
    });

    window.addEventListener('click', function (event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
});
    </script>
</body>

</html>
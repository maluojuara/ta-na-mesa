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
    INSERT INTO mesas (
        nome, id_organizador, id_sistema_regras, id_categoria,
        id_tipo_campanha, id_nivel_experiencia, id_localizacao,
        vagas, img_capa, sinopse, ativa, apagada
    ) VALUES (
        :nome, :id_organizador, :id_sistema_regras, :id_categoria,
        :id_tipo_campanha, :id_nivel_experiencia, :id_localizacao,
        :vagas, :img_capa, :sinopse, 1, 0
    )");
    $stmt->bindValue(':nome', $_POST['nome']);
    $stmt->bindValue(':id_organizador', $_SESSION['id']);
    $stmt->bindValue(':id_sistema_regras', $_POST['id_sistema_regras']);
    $stmt->bindValue(':id_categoria', $_POST['id_categoria']);
    $stmt->bindValue(':id_tipo_campanha', $_POST['id_tipo_campanha']);
    $stmt->bindValue(':id_nivel_experiencia', $_POST['id_nivel_experiencia']);
    $stmt->bindValue(':id_localizacao', $_POST['id_localizacao']);
    $stmt->bindValue(':vagas', $_POST['vagas']);
    $stmt->bindValue(':img_capa', $_POST['img_capa']);
    $stmt->bindValue(':sinopse', $_POST['sinopse']);

   if ($stmt->execute()) {
    echo "<script>
        alert('✅ Mesa cadastrada com sucesso!');
        window.location.href = 'suas_mesas.php'; 
    </script>";
    exit();
} else {
    echo "<script>
        alert('❌ Erro ao cadastrar mesa.');
    </script>";
}
}
?>

<?php
// Carregar dados auxiliares pro modal
$sistemas = $db->query("SELECT * FROM sistema_regras");
$categorias = $db->query("SELECT * FROM categorias");
$tipos_campanha = $db->query("SELECT * FROM tipos_campanha");
$niveis_experiencia = $db->query("SELECT * FROM niveis_experiencia");
$localizacoes = $db->query("SELECT * FROM localizacoes");
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Mesa</title>
    <link rel="stylesheet" href="style/cadastrar_mesa.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
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
    <main>
        <div class="formulario">
            <form action="" class="form-campanha" method="POST">
                <h3>Insira suas informações</h3>
                <img src="img/linha.svg" class="linha-formulario" alt="linha separando conteúdo de título">
                <div class="formulario-alinhamento">
                    <label for="nome">Nome:</label>
                    <input type="text" name="nome" placeholder="Nome da Campanha" id="opcao" required>
                </div>




                <div class="formulario-alinhamento">
                    <label for="Sistema De Regras">Sistema de regras:</label>
                    <select name="id_sistema_regras" id="opcao" required>
                        <option disabled selected>Selecione uma opção</option>
                        <?php
                        $results = $db->query("SELECT * FROM sistema_regras");
                        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
                            echo "<option value='{$row['id']}'>{$row['nome']}</option>";
                        }
                        ?>
                    </select>

                </div>




                <div class="formulario-alinhamento">
                    <label for="Categoria">Categoria:</label>
                    <select name="id_categoria" id="opcao" required>
                        <option disabled selected>Selecione uma categoria</option>
                        <?php
                        $results = $db->query("SELECT * FROM categorias");
                        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
                            echo "<option value='{$row['id']}'>{$row['nome']}</option>";
                        }
                        ?>
                    </select>
                </div>




                <div class="formulario-alinhamento">
                    <label for="Campanha">Campanha:</label>
                    <select name="id_tipo_campanha" id="opcao" required>
                        <option disabled selected>Selecione uma opção</option>
                        <?php
                        $results = $db->query("SELECT * FROM tipos_campanha");
                        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
                            echo "<option value='{$row['id']}'>{$row['nome']}</option>";
                        }
                        ?>
                    </select>
                </div>




                <div class="formulario-alinhamento">
                    <label for="Experiencia recomendada">Experiência Recomendada:</label>
                    <select name="id_nivel_experiencia" id="opcao" required>
                        <option disabled selected>Selecione uma opção</option>
                        <?php
                        $results = $db->query("SELECT * FROM niveis_experiencia");
                        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
                            echo "<option value='{$row['id']}'>{$row['nome']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="formulario-alinhamento">
                    <label for="Localização">Localização:</label>
                    <select name="id_localizacao" id="opcao" required>
                        <option disabled selected>Selecione uma opção</option>
                        <?php
                        $results = $db->query("SELECT * FROM localizacoes");
                        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
                            echo "<option value='{$row['id']}'>{$row['nome']}</option>";
                        }
                        ?>
                    </select>
                </div>


                <div class="formulario-alinhamento">
                    <label for="Vagas">Vagas:</label>
                    <input type="text" name="vagas" placeholder="Ex: 5" required>
                </div>


                <div class="formulario-alinhamento">
                    <label>Capa:</label>
                    <input type="text" name="img_capa" placeholder="Insira a url: www.foto.com.br">
                </div>



                <div class="formulario-alinhamento">
                    <label>Sinopse:</label>
                    <textarea placeholder="Conte um pouco da sua história..." rows="4" name="sinopse"></textarea>
                </div>
                <button type="submit">DIVULGAR!</button>
            </form>
            <h2>Monte a<br> sua nova<br> aventura!</h2>
        </div>
    </main>

    <footer>
        &copy; direitos reservados 2025<br>
        Este site foi desenvolvido por Maria Vivielle, Malu Araujo, Luana Miyashiro e Isabelle Matos 💖<br>
    </footer>


</body>

</html>
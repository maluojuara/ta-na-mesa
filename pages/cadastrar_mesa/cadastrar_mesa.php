<?php
require '../../backend/conexao.php';
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
    $stmt->bindValue(':id_organizador', 1);
    $stmt->bindValue(':id_sistema_regras', $_POST['id_sistema_regras']);
    $stmt->bindValue(':id_categoria', $_POST['id_categoria']);
    $stmt->bindValue(':id_tipo_campanha', $_POST['id_tipo_campanha']);
    $stmt->bindValue(':id_nivel_experiencia', $_POST['id_nivel_experiencia']);
    $stmt->bindValue(':id_localizacao', $_POST['id_localizacao']);
    $stmt->bindValue(':vagas', $_POST['vagas']);
    $stmt->bindValue(':img_capa', $_POST['img_capa']);
    $stmt->bindValue(':sinopse', $_POST['sinopse']);

    if ($stmt->execute()) {
        echo "<script>alert('✅ Mesa cadastrada com sucesso!');</script>";
    } else {
        echo "<script>alert('❌ Erro ao cadastrar mesa.');</script>";
    }    
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Mesa</title>
    <link rel="stylesheet" href="cadastrar_mesa.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
</head>

<body>
    <header class="navbar">

        <div class="logo">
            <a href="#"> <img src="../../img/logo.png"
                    alt="Logo do site Ta na Mesa, em formato de um D20 com o nome do site"></a>
        </div>

        <nav class="menu">
            <a href="#">Suas Mesas</a>
            <a href="#">Mesas</a>
            <a href="#">Cadastro de Mesas</a>
        </nav>
        <div class="avatar">
            <a href="#"> <img src="../../img/mestre.svg" alt="avatar do usuário" class="usuario"> </a>
        </div>

    </header>
    <main>
        <div class="formulario">
            <form action="" class="form-campanha" method="POST">
                <h3>Insira suas informações</h3>
                <img src="../../img/linha.svg" class="linha-formulario" alt="linha separando conteúdo de título">
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

    <Footer>
        <div class="footer-direitos">
            <img src="../../img/©.png" class="footer-img" alt="simbolo do copyright">
            <p>direitos reservados 2025</p>
        </div>
        <p>Este site foi desenvolvido por Maria Vivielle, Malu Araujo, Luana Miyashiro e Isabelle de Matos</p>

    </Footer>


</body>

</html>
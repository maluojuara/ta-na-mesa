<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Mesas de RPG</title>
    <link rel="stylesheet" href="style/home.css" />
</head>
<body>
    <?php
    require_once '../backend/conexao.php';
    $db = new MyDB();
    ?>

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
    


<div class="hero" >
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
            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="3 4 21 4 14 12.5 14 19 10 20 10 12.5 3 4"></polygon>
            </svg>
            Filtro
        </h2>
        <form action="" method="GET" class="filtros-form">
            <div class="filtros-linha">
                <label>Data:
                    <input type="date" name="data" value="<?php echo isset($_GET['data']) ? htmlspecialchars($_GET['data']) : ''; ?>">
                </label>
    
                <label>Sistema:
                    <select name="sistema">
                        <option value="">Selecionar</option>
                        <?php
                        // Buscar sistemas de regras do banco
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
                        // Buscar localizações do banco
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
                // Buscar categorias do banco
                $categorias = $db->query("SELECT * FROM categorias ORDER BY nome");
                if ($categorias) {
                    while ($cat = $categorias->fetchArray(SQLITE3_ASSOC)) {
                        $id = $cat['id'];
                        $nome = $cat['nome'];
                        $valor = strtolower($nome);
                        $checked = (isset($_GET['categoria']) && in_array($id, $_GET['categoria'])) ? 'checked' : '';
                        echo '<input type="checkbox" id="cat_' . $id . '" name="categoria[]" value="' . $id . '" ' . $checked . ' hidden>';
                        echo '<label for="cat_' . $id . '" class="categoria-botao">' . $nome . '</label>';
                    }
                    ?>


                <?php } ?>
            </div>
            
    
            <div class="filtros-botoes">
                <button type="submit" name="aplicar" value="1" class="aplicar">Aplicar</button>
                <a href="index.php" class="resetar" style="display: inline-block; text-decoration: none; text-align: center;">Limpar Filtros</a>
            </div>
        </form>
    </section>

    <div class="cards">
        <?php
        
        // Construir a consulta com filtros
        $where = ["m.apagada = 0"];
        $params = [];
        
        // Verificar se o botão de filtro foi clicado
        $filtroAplicado = isset($_GET['aplicar']);
        
        // Aplicar filtros apenas se o botão foi clicado
        if ($filtroAplicado) {
            // Filtro de sistema
            if (!empty($_GET['sistema'])) {
                $where[] = "m.id_sistema_regras = :sistema";
                $params[':sistema'] = $_GET['sistema'];
            }
            
            // Filtro de localização
            if (!empty($_GET['localizacao'])) {
                $where[] = "m.id_localizacao = :localizacao";
                $params[':localizacao'] = $_GET['localizacao'];
            }
            
            // Filtro de data (se implementado)
            if (!empty($_GET['data'])) {
                $where[] = "DATE(m.created_at) = :data";
                $params[':data'] = $_GET['data'];
            }
            
            // Filtro de categorias
            if (!empty($_GET['categoria']) && is_array($_GET['categoria'])) {
                $where[] = "m.id_categoria IN (" . implode(',', array_map('intval', $_GET['categoria'])) . ")";
            }
        }
        
        // Montar a consulta com JOIN
        $query = "SELECT m.*, 
                 sr.nome AS sistema_nome,
                 ne.nome AS nivel_experiencia_nome,
                 l.nome AS localizacao_nome
                 FROM mesas m
                 LEFT JOIN sistema_regras sr ON m.id_sistema_regras = sr.id
                 LEFT JOIN niveis_experiencia ne ON m.id_nivel_experiencia = ne.id
                 LEFT JOIN localizacoes l ON m.id_localizacao = l.id
                 WHERE " . implode(' AND ', $where);
        
        // Preparar e executar a consulta
        $stmt = $db->prepare($query);
        if ($stmt) {
            foreach ($params as $param => $value) {
                $stmt->bindValue($param, $value);
            }
            $results = $stmt->execute();
        } else {
            // Fallback para consulta simples se prepare falhar
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
                
                echo '<div class="card">';
                echo '<img src="' . $mesa['img_capa'] . '" alt="' . $mesa['nome'] . '">';
                echo '<div class="card-content">';
                echo '<h3>' . $mesa['nome'] . '</h3>';
                // Buscar nomes das tabelas relacionadas
                $localizacao = 'Não especificado';
                $locResult = $db->query("SELECT nome FROM localizacoes WHERE id = " . $mesa['id_localizacao']);
                if ($locResult) {
                    $locRow = $locResult->fetchArray(SQLITE3_ASSOC);
                    if ($locRow) {
                        $localizacao = $locRow['nome'];
                    }
                }
                
                $sistema = 'Não especificado';
                $sysResult = $db->query("SELECT nome FROM sistema_regras WHERE id = " . $mesa['id_sistema_regras']);
                if ($sysResult) {
                    $sysRow = $sysResult->fetchArray(SQLITE3_ASSOC);
                    if ($sysRow) {
                        $sistema = $sysRow['nome'];
                    }
                }
                
                $experiencia = 'Não especificado';
                $expResult = $db->query("SELECT nome FROM niveis_experiencia WHERE id = " . $mesa['id_nivel_experiencia']);
                if ($expResult) {
                    $expRow = $expResult->fetchArray(SQLITE3_ASSOC);
                    if ($expRow) {
                        $experiencia = $expRow['nome'];
                    }
                }
                
                // Se a consulta JOIN funcionou, use os campos já obtidos
                if (isset($mesa['sistema_nome']) && isset($mesa['nivel_experiencia_nome']) && isset($mesa['localizacao_nome'])) {
                    echo '<p><strong>Sistema:</strong> ' . $mesa['sistema_nome'] . '</p>';
                    echo '<p>' . substr($mesa['sinopse'], 0, 100) . '...</p>';
                    echo '<p><strong>Vagas:</strong> ' . $mesa['vagas'] . '</p>';
                    echo '<p><strong>Experiência:</strong> ' . $mesa['nivel_experiencia_nome'] . '</p>';
                    echo '<p><strong>Localização:</strong> ' . $mesa['localizacao_nome'] . '</p>';
                } else {
                    // Caso contrário, use os valores obtidos das consultas individuais
                    echo '<p><strong>Sistema:</strong> ' . $sistema . '</p>';
                    echo '<p>' . substr($mesa['sinopse'], 0, 100) . '...</p>';
                    echo '<p><strong>Vagas:</strong> ' . $mesa['vagas'] . '</p>';
                    echo '<p><strong>Experiência:</strong> ' . $experiencia . '</p>';
                    echo '<p><strong>Localização:</strong> ' . $localizacao . '</p>';
                }
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

</div>

<footer>
    &copy; direitos reservados 2025<br>
    Este site foi desenvolvido por Maria Vivielle, Malu Araujo, Luana Miyashiro e Isabelle Matos 💖<br>
</footer>

</body>
</html>

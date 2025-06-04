<?php
require 'conexao.php';

$db = new MyDB();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Conexão com o Banco</title>
</head>
<body>
    <h1>
        <?php
        if (!$db) {
            echo "❌ Erro na conexão com o banco.";
        } else {
            echo "✅ Conexão estabelecida com sucesso!";
        }
        ?>
    </h1>
</body>
</html>

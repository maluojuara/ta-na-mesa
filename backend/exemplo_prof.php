<?php
require 'conexao.php';
$db = new MyDB();
?>

<html>
<head>
    <title>Lista</title>
</head>
<body>
<table>
    <?php
    $results = $db->query("SELECT * FROM artists");
    while ($row = $results->fetchArray()) {
        echo '<tr>';
        echo '<td>'.$row['ArtistId'].'</td>';
        echo '<td>'.$row['Name'].'</td>';
        echo '</tr>';
    }
    ?>
</table>
</body>
</html>
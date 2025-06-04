<?php
session_start();
require '../backend/conexao.php';
$db = new MyDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['salvar_edicao'])) {
	$id = $_POST['id_mesa'] ?? null;
	$nome = trim($_POST['nome'] ?? '');
	$img = trim($_POST['img_capa'] ?? '');
	$vagas = $_POST['vagas'] ?? null;
	$sinopse = trim($_POST['sinopse'] ?? '');
	$id_sistema = $_POST['id_sistema_regras'] ?? null;
	$id_categoria = $_POST['id_categoria'] ?? null;
	$id_campanha = $_POST['id_tipo_campanha'] ?? null;
	$id_experiencia = $_POST['id_nivel_experiencia'] ?? null;
	$id_localizacao = $_POST['id_localizacao'] ?? null;

	if ($id && $_SESSION['id']) {
		$stmt = $db->prepare("
			UPDATE mesas SET
				nome = :nome,
				img_capa = :img,
				vagas = :vagas,
				sinopse = :sinopse,
				id_sistema_regras = :sistema,
				id_categoria = :categoria,
				id_tipo_campanha = :campanha,
				id_nivel_experiencia = :experiencia,
				id_localizacao = :localizacao
			WHERE id = :id AND id_organizador = :organizador
		");

		$stmt->bindValue(':nome', $nome);
		$stmt->bindValue(':img', $img);
		$stmt->bindValue(':vagas', $vagas, SQLITE3_INTEGER);
		$stmt->bindValue(':sinopse', $sinopse);
		$stmt->bindValue(':sistema', $id_sistema, SQLITE3_INTEGER);
		$stmt->bindValue(':categoria', $id_categoria, SQLITE3_INTEGER);
		$stmt->bindValue(':campanha', $id_campanha, SQLITE3_INTEGER);
		$stmt->bindValue(':experiencia', $id_experiencia, SQLITE3_INTEGER);
		$stmt->bindValue(':localizacao', $id_localizacao, SQLITE3_INTEGER);
		$stmt->bindValue(':id', $id, SQLITE3_INTEGER);
		$stmt->bindValue(':organizador', $_SESSION['id'], SQLITE3_INTEGER);

		if ($stmt->execute()) {
			echo "<script>alert('✅ Mesa atualizada com sucesso!'); window.location.href = 'suas_mesas.php';</script>";
		} else {
			echo "<script>alert('❌ Erro ao atualizar mesa.'); window.location.href = 'suas_mesas.php';</script>";
		}
	} else {
		echo "<script>alert('❌ Dados incompletos.'); window.location.href = 'suas_mesas.php';</script>";
	}
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_mesa_toggle_ativa']) && isset($_POST['status_toggle_ativa'])) {
        $idMesa = $_POST['id_mesa_toggle_ativa'];
        $novoStatus = $_POST['status_toggle_ativa']; 

        // Prepara a query para atualizar o campo 'ativa'
        $stmt = $db->prepare("UPDATE mesas SET ativa = :status WHERE id = :id AND id_organizador = :org");
        $stmt->bindValue(':status', $novoStatus, SQLITE3_INTEGER); // Garante que é um número inteiro
        $stmt->bindValue(':id', $idMesa, SQLITE3_INTEGER);
        $stmt->bindValue(':org', $_SESSION['id'], SQLITE3_INTEGER); // Por segurança, só o organizador pode alterar

        if ($stmt->execute()) {
            // Sucesso: pode dar um feedback e redirecionar
            echo "<script>alert('✅ Status da mesa atualizado com sucesso!');</script>";
			header("Location: suas_mesas.php");
        } else {
            // Erro: pode dar um feedback de erro
            echo "<script>alert('❌ Erro ao atualizar status da mesa.');</script>";
			header("Location: suas_mesas.php");
        }
        
    }
else {
	header("Location: suas_mesas.php");
	exit();
}

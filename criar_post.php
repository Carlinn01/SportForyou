<?php
include "login/incs/valida-sessao.php";
require_once "login/src/ConexaoBD.php";

$texto = $_POST['texto'];
$idusuario = $_SESSION['idusuarios'];
$foto_nome = null;

// Verifica se enviou imagem
if(isset($_FILES['foto']) && $_FILES['foto']['error'] == 0){
    $pasta = 'login/uploads/';
    $foto_nome = time() . '_' . $_FILES['foto']['name']; // evita conflito de nomes
    move_uploaded_file($_FILES['foto']['tmp_name'], $pasta . $foto_nome);
}

$conexao = ConexaoBD::conectar();
$sql = "INSERT INTO postagens (idusuario, texto, foto, criado_em) VALUES (?, ?, ?, NOW())";
$stmt = $conexao->prepare($sql);
$stmt->bindParam(1, $idusuario);
$stmt->bindParam(2, $texto);
$stmt->bindParam(3, $foto_nome);
$stmt->execute();

header("Location: home.php");
exit;
?>

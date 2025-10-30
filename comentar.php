<?php
session_start();
require_once "login/src/ConexaoBD.php";

$idusuario = $_SESSION['idusuarios'];
$idpostagem = $_POST['idpostagem'] ?? null;
$comentario = $_POST['comentario'] ?? null;

if (!$idpostagem || !$comentario) exit;  // Verifica se os dados foram enviados corretamente

$pdo = ConexaoBD::conectar();

// Prepara o comentário para inserir no banco de dados
$stmt = $pdo->prepare("INSERT INTO comentarios (idusuario, idpostagem, comentario) VALUES (?, ?, ?)");
$stmt->execute([$idusuario, $idpostagem, $comentario]);

// Redireciona de volta para a página atual (feed)
header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
?>

<?php
session_start();
require_once "login/src/ConexaoBD.php";

$idusuario = $_SESSION['idusuarios'];
$idpostagem = $_GET['idpostagem'] ?? null;

if (!$idpostagem) exit;  // Se não houver ID de postagem, não faz nada

$pdo = ConexaoBD::conectar();

// Verifica se o usuário já curtiu
$stmt = $pdo->prepare("SELECT * FROM curtidas WHERE idusuario = ? AND idpostagem = ?");
$stmt->execute([$idusuario, $idpostagem]);

if ($stmt->rowCount() > 0) {
    // Se já curtiu, removemos a curtida
    $pdo->prepare("DELETE FROM curtidas WHERE idusuario = ? AND idpostagem = ?")
         ->execute([$idusuario, $idpostagem]);
} else {
    // Se não curtiu, adicionamos a curtida
    $pdo->prepare("INSERT INTO curtidas (idusuario, idpostagem) VALUES (?, ?)")
         ->execute([$idusuario, $idpostagem]);
}

// Redireciona de volta para a página do feed (sem recarregar a página)
header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
?>

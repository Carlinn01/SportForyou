<?php
include("../login/incs/valida-sessao.php");
require_once "../login/src/ConexaoBD.php";

$idusuario_logado = $_SESSION['idusuarios'];
$idusuario_destino = $_GET['id'] ?? null;

if (!$idusuario_destino || !is_numeric($idusuario_destino)) {
    header("Location: ../pages/mensagens.php");
    exit;
}

$idusuario_destino = (int)$idusuario_destino;

if ($idusuario_destino == $idusuario_logado) {
    header("Location: ../pages/mensagens.php");
    exit;
}

$conexao = ConexaoBD::conectar();

// Verifica se já existe uma conversa entre esses dois usuários
$sql = "SELECT idconversa FROM conversas 
        WHERE (usuario1_id = ? AND usuario2_id = ?) 
        OR (usuario1_id = ? AND usuario2_id = ?)";
$stmt = $conexao->prepare($sql);
$stmt->execute([
    $idusuario_logado, 
    $idusuario_destino,
    $idusuario_destino,
    $idusuario_logado
]);
$conversa = $stmt->fetch(PDO::FETCH_ASSOC);

if ($conversa) {
    // Já existe conversa, redireciona para ela
    header("Location: ../pages/mensagens.php?conversa=" . $conversa['idconversa']);
    exit;
}

// Cria nova conversa (sempre menor ID primeiro para evitar duplicatas)
$usuario1 = min($idusuario_logado, $idusuario_destino);
$usuario2 = max($idusuario_logado, $idusuario_destino);

$sql = "INSERT INTO conversas (usuario1_id, usuario2_id) VALUES (?, ?)";
$stmt = $conexao->prepare($sql);
$stmt->execute([$usuario1, $usuario2]);

$idconversa = $conexao->lastInsertId();

header("Location: ../pages/mensagens.php?conversa=" . $idconversa);
exit;


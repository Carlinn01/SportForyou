<?php
include("login/incs/valida-sessao.php");
require_once "login/src/ConexaoBD.php";

if (!isset($_GET['id'])) {
    header("Location: home.php");
    exit;
}

$idusuario = $_GET['id'];
$idusuario_logado = $_SESSION['idusuarios'];

$conexao = ConexaoBD::conectar();

// Pega dados do usuário
$sql = "SELECT idusuarios, nome, nome_usuario, email, nascimento, foto_perfil 
        FROM usuarios 
        WHERE idusuarios = ?";
$stmt = $conexao->prepare($sql);
$stmt->bindParam(1, $idusuario);
$stmt->execute();
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    echo "Usuário não encontrado!";
    exit;
}

// Quantos seguidores o usuário tem
$sqlSeguidores = "SELECT COUNT(*) as total FROM seguidores WHERE idusuario = ?";
$stmt = $conexao->prepare($sqlSeguidores);
$stmt->bindParam(1, $idusuario);
$stmt->execute();
$totalSeguidores = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Quantos ele segue
$sqlSeguindo = "SELECT COUNT(*) as total FROM seguidores WHERE idseguidor = ?";
$stmt = $conexao->prepare($sqlSeguindo);
$stmt->bindParam(1, $idusuario);
$stmt->execute();
$totalSeguindo = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Perfil de <?= htmlspecialchars($usuario['nome_usuario']) ?></title>
    <link rel="stylesheet" href="/css/perfil.css">
</head>
<body>
    <div class="perfil-container">
        <div class="perfil-info">
            <img src="login/uploads/<?= htmlspecialchars($usuario['foto_perfil']) ?>" alt="Foto de perfil" width="120" height="120">
            <h2><?= htmlspecialchars($usuario['nome']) ?></h2>
            <p>@<?= htmlspecialchars($usuario['nome_usuario']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($usuario['email']) ?></p>
            <p><strong>Nascimento:</strong> <?= htmlspecialchars($usuario['nascimento']) ?></p>
        </div>

        <div class="stats">
            <span>Seguidores: <?= $totalSeguidores ?></span>
            <span>Seguindo: <?= $totalSeguindo ?></span>
        </div>

        <?php if ($idusuario_logado != $idusuario): ?>
            <a href="seguir.php?idseguidor=<?= $idusuario ?>" class="seguir-btn">Seguir</a>
        <?php endif; ?>
    </div>
</body>
</html>

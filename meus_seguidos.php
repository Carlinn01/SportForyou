<?php
include("login/incs/valida-sessao.php");
require_once "login/src/ConexaoBD.php";

$conexao = ConexaoBD::conectar();
$idusuario = $_SESSION["idusuarios"]; // você

// Pega todos os usuários que te seguem
$sql = "SELECT u.idusuarios, u.nome, u.nome_usuario, u.foto_perfil
        FROM seguidores s
        JOIN usuarios u ON u.idusuarios = s.idseguidor
        WHERE s.idusuario = ?";

$stmt = $conexao->prepare($sql);
$stmt->bindParam(1, $idusuario);
$stmt->execute();

$seguidores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Meus Seguidores</title>
</head>
<body>
  <h2>Quem te segue:</h2>

  <?php if ($seguidores): ?>
    <ul>
      <?php foreach ($seguidores as $s): ?>
        <li>
          <img src="uploads/<?= htmlspecialchars($s['foto_perfil']) ?>" width="40" height="40" style="border-radius:50%;">
          <?= htmlspecialchars($s['nome']) ?> (@<?= htmlspecialchars($s['nome_usuario']) ?>)
        </li>
      <?php endforeach; ?>
    </ul>
  <?php else: ?>
    <p>Ninguém te segue ainda </p>
  <?php endif; ?>
</body>
</html>

<?php
include "../login/incs/valida-sessao.php";
require_once "../login/src/ConexaoBD.php";
require_once "../login/src/ComentarioDAO.php";

// Define header JSON
header('Content-Type: application/json');

$idusuario = $_SESSION['idusuarios'];
$idpostagem = $_POST['idpostagem'] ?? null;
$comentario = trim($_POST['comentario'] ?? '');

if (!$idpostagem || !is_numeric($idpostagem)) {
    echo json_encode(['success' => false, 'message' => 'ID de postagem inválido']);
    exit;
}

if (empty($comentario)) {
    echo json_encode(['success' => false, 'message' => 'Comentário não pode estar vazio']);
    exit;
}

$idpostagem = (int)$idpostagem;
$pdo = ConexaoBD::conectar();

// Insere o comentário no banco de dados
$sql = "INSERT INTO comentarios (idusuario, idpostagem, comentario, data_comentario) VALUES (?, ?, ?, NOW())";
$stmt = $pdo->prepare($sql);
$stmt->execute([$idusuario, $idpostagem, $comentario]);

// Busca os dados do usuário para retornar na resposta
$stmt = $pdo->prepare("SELECT nome_usuario, foto_perfil FROM usuarios WHERE idusuarios = ?");
$stmt->execute([$idusuario]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode([
    'success' => true,
    'comentario' => [
        'nome_usuario' => $usuario['nome_usuario'],
        'foto_perfil' => $usuario['foto_perfil'],
        'comentario' => htmlspecialchars($comentario)
    ]
]);
exit;
?>

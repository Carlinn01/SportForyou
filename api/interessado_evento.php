<?php
include("../login/incs/valida-sessao.php");
require_once "../login/src/EventoDAO.php";

header('Content-Type: application/json');

$idusuario_logado = $_SESSION['idusuarios'];
$idevento = $_POST['evento_id'] ?? $_GET['evento_id'] ?? null;

if (!$idevento || !is_numeric($idevento)) {
    echo json_encode(['success' => false, 'message' => 'ID de evento inválido']);
    exit;
}

$idevento = (int)$idevento;

// Verifica se o evento existe
$evento = EventoDAO::buscarPorId($idevento);

if (!$evento) {
    echo json_encode(['success' => false, 'message' => 'Evento não encontrado']);
    exit;
}

// Verifica se já tem interesse
$temInteresse = EventoDAO::temInteresse($idevento, $idusuario_logado);

if ($temInteresse) {
    // Remove interesse
    if (EventoDAO::removerInteresse($idevento, $idusuario_logado)) {
        echo json_encode([
            'success' => true,
            'interesse' => false,
            'message' => 'Interesse removido'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao remover interesse']);
    }
} else {
    // Adiciona interesse
    if (EventoDAO::adicionarInteresse($idevento, $idusuario_logado)) {
        // Cria notificação para o organizador
        require_once "../login/src/UsuarioDAO.php";
        $nomeUsuario = $_SESSION['nome_usuario'];
        $mensagemNotificacao = "@{$nomeUsuario} tem interesse no seu evento: {$evento['titulo']}";
        $linkNotificacao = "eventos.php";
        UsuarioDAO::adicionarNotificacao($evento['organizador_id'], 'evento', $mensagemNotificacao, $linkNotificacao);
        
        echo json_encode([
            'success' => true,
            'interesse' => true,
            'message' => 'Interesse registrado'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao registrar interesse']);
    }
}
exit;


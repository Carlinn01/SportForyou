<?php
include("../login/incs/valida-sessao.php");
require_once "../login/src/EventoDAO.php";

$idusuario_logado = $_SESSION['idusuarios'];
$idevento = $_GET['id'] ?? null;

if (!$idevento || !is_numeric($idevento)) {
    header("Location: ../pages/eventos.php?erro=" . urlencode("ID de evento inválido"));
    exit;
}

$idevento = (int)$idevento;

// Verifica se o evento existe e pertence ao usuário
$evento = EventoDAO::buscarPorId($idevento);

if (!$evento) {
        header("Location: ../pages/eventos.php?erro=" . urlencode("Evento não encontrado"));
    exit;
}

if ($evento['organizador_id'] != $idusuario_logado) {
    header("Location: ../pages/eventos.php?erro=" . urlencode("Você não tem permissão para deletar este evento"));
    exit;
}

// Deleta foto se existir
if ($evento['foto'] && file_exists('../login/uploads/' . $evento['foto'])) {
    unlink('../login/uploads/' . $evento['foto']);
}

// Deleta evento
if (EventoDAO::deletar($idevento, $idusuario_logado)) {
    header("Location: ../pages/eventos.php?mensagem=" . urlencode("Evento deletado com sucesso!"));
} else {
    header("Location: ../pages/eventos.php?erro=" . urlencode("Erro ao deletar evento"));
}
exit;


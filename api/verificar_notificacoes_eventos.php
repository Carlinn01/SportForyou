<?php
/**
 * Script para verificar e criar notificações de eventos
 * Deve ser executado via cron job diariamente
 * 
 * Exemplo de cron (executar todo dia às 8h):
 * 0 8 * * * php /caminho/para/api/verificar_notificacoes_eventos.php
 */

require_once '../login/src/ConexaoBD.php';
require_once '../login/src/EventoDAO.php';
require_once '../login/src/UsuarioDAO.php';

$pdo = ConexaoBD::conectar();
$hoje = date('Y-m-d');
$amanha = date('Y-m-d', strtotime('+1 day'));

// Busca eventos que acontecem amanhã (notificação 1 dia antes)
$sqlAmanha = "SELECT e.*, ei.usuario_id 
              FROM eventos e
              INNER JOIN eventos_interessados ei ON e.idevento = ei.evento_id
              WHERE e.data_evento = ?";
$stmtAmanha = $pdo->prepare($sqlAmanha);
$stmtAmanha->execute([$amanha]);
$eventosAmanha = $stmtAmanha->fetchAll(PDO::FETCH_ASSOC);

foreach ($eventosAmanha as $evento) {
    $horaFormatada = $evento['hora_evento'] ? date('H:i', strtotime($evento['hora_evento'])) : '';
    $mensagem = "O evento '{$evento['titulo']}' acontece amanhã";
    if ($horaFormatada) {
        $mensagem .= " às {$horaFormatada}";
    }
    $link = "eventos.php#evento-{$evento['idevento']}";
    
    // Verifica se já existe notificação para evitar duplicatas
    $sqlCheck = "SELECT COUNT(*) FROM notificacoes 
                 WHERE idusuario = ? AND tipo = 'evento' AND mensagem LIKE ? AND data >= DATE_SUB(NOW(), INTERVAL 1 DAY)";
    $stmtCheck = $pdo->prepare($sqlCheck);
    $stmtCheck->execute([$evento['usuario_id'], "%{$evento['titulo']}%"]);
    
    if ($stmtCheck->fetchColumn() == 0) {
        UsuarioDAO::adicionarNotificacao($evento['usuario_id'], 'evento', $mensagem, $link);
    }
}

// Busca eventos que acontecem hoje (notificação no dia)
$sqlHoje = "SELECT e.*, ei.usuario_id 
            FROM eventos e
            INNER JOIN eventos_interessados ei ON e.idevento = ei.evento_id
            WHERE e.data_evento = ?";
$stmtHoje = $pdo->prepare($sqlHoje);
$stmtHoje->execute([$hoje]);
$eventosHoje = $stmtHoje->fetchAll(PDO::FETCH_ASSOC);

foreach ($eventosHoje as $evento) {
    $horaFormatada = $evento['hora_evento'] ? date('H:i', strtotime($evento['hora_evento'])) : '';
    $mensagem = "O evento '{$evento['titulo']}' acontece hoje";
    if ($horaFormatada) {
        $mensagem .= " às {$horaFormatada}";
    }
    $link = "eventos.php#evento-{$evento['idevento']}";
    
    // Verifica se já existe notificação para evitar duplicatas
    $sqlCheck = "SELECT COUNT(*) FROM notificacoes 
                 WHERE idusuario = ? AND tipo = 'evento' AND mensagem LIKE ? AND DATE(data) = ?";
    $stmtCheck = $pdo->prepare($sqlCheck);
    $stmtCheck->execute([$evento['usuario_id'], "%{$evento['titulo']}%", $hoje]);
    
    if ($stmtCheck->fetchColumn() == 0) {
        UsuarioDAO::adicionarNotificacao($evento['usuario_id'], 'evento', $mensagem, $link);
    }
}

echo "Notificações de eventos verificadas e criadas com sucesso!\n";


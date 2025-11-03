<?php
include("../login/incs/valida-sessao.php");
require_once "../login/src/ConexaoBD.php";
require_once "../login/src/EventoDAO.php";

$idusuario_logado = $_SESSION['idusuarios'];

// Valida dados obrigatórios
$titulo = trim($_POST['titulo'] ?? '');
$tipo_esporte = trim($_POST['tipo_esporte'] ?? '');
$data_evento = $_POST['data_evento'] ?? '';
$local = trim($_POST['local'] ?? '');

if (empty($titulo) || empty($tipo_esporte) || empty($data_evento) || empty($local)) {
    header("Location: criar_evento.php?erro=" . urlencode("Preencha todos os campos obrigatórios"));
    exit;
}

// Valida data
if (strtotime($data_evento) < strtotime(date('Y-m-d'))) {
    header("Location: criar_evento.php?erro=" . urlencode("A data do evento não pode ser no passado"));
    exit;
}

// Processa upload da foto
$foto = null;
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $arquivo = $_FILES['foto'];
    $nomeArquivo = $arquivo['name'];
    $tipoArquivo = $arquivo['type'];
    $tamanhoArquivo = $arquivo['size'];
    $tmpName = $arquivo['tmp_name'];
    
    // Valida tamanho (máximo 5MB)
    $maxSize = 5 * 1024 * 1024;
    if ($tamanhoArquivo > $maxSize) {
        header("Location: criar_evento.php?erro=" . urlencode("Arquivo muito grande. Máximo: 5MB"));
        exit;
    }
    
    // Valida tipo
    $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    
    // Fallback para validação de tipo
    $extensao = strtolower(pathinfo($nomeArquivo, PATHINFO_EXTENSION));
    $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    if (!in_array($tipoArquivo, $tiposPermitidos) && !in_array($extensao, $extensoesPermitidas)) {
        header("Location: criar_evento.php?erro=" . urlencode("Tipo de arquivo não permitido. Use JPG, PNG ou GIF"));
        exit;
    }
    
    // Gera nome único
    $novoNome = uniqid('evento_', true) . '.' . $extensao;
    $caminhoDestino = '../login/uploads/eventos/' . $novoNome;
    
    // Cria diretório se não existir
    if (!file_exists('../login/uploads/eventos')) {
        mkdir('../login/uploads/eventos', 0755, true);
    }
    
    // Move arquivo
    if (move_uploaded_file($tmpName, $caminhoDestino)) {
        $foto = 'eventos/' . $novoNome;
    } else {
        header("Location: criar_evento.php?erro=" . urlencode("Erro ao fazer upload da foto"));
        exit;
    }
}

// Prepara dados
$dados = [
    'organizador_id' => $idusuario_logado,
    'titulo' => $titulo,
    'descricao' => trim($_POST['descricao'] ?? ''),
    'tipo_esporte' => $tipo_esporte,
    'data_evento' => $data_evento,
    'hora_evento' => $_POST['hora_evento'] ?? null,
    'local' => $local,
    'cidade' => trim($_POST['cidade'] ?? ''),
    'estado' => trim($_POST['estado'] ?? ''),
    'foto' => $foto
];

// Verifica se é edição ou criação
$idevento = $_POST['idevento'] ?? null;

if ($idevento && is_numeric($idevento)) {
    // Edição
    $idevento = (int)$idevento;
    $evento = EventoDAO::buscarPorId($idevento);
    
    if (!$evento || $evento['organizador_id'] != $idusuario_logado) {
        header("Location: ../pages/eventos.php?erro=" . urlencode("Evento não encontrado ou sem permissão"));
        exit;
    }
    
    // Mantém foto antiga se não foi enviada nova
    if (!$foto && $evento['foto']) {
        $dados['foto'] = $evento['foto'];
    }
    
    if (EventoDAO::atualizar($idevento, $dados)) {
        header("Location: ../pages/eventos.php?mensagem=" . urlencode("Evento atualizado com sucesso!"));
    } else {
        header("Location: editar_evento.php?id={$idevento}&erro=" . urlencode("Erro ao atualizar evento"));
    }
} else {
    // Criação
    $novoEventoId = EventoDAO::criar($dados);
    
    if ($novoEventoId) {
        header("Location: ../pages/eventos.php?mensagem=" . urlencode("Evento cadastrado com sucesso!"));
    } else {
        header("Location: criar_evento.php?erro=" . urlencode("Erro ao cadastrar evento"));
    }
}
exit;


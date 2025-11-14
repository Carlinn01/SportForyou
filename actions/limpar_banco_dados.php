<?php
/**
 * Script para limpar o banco de dados
 * Mantém apenas o usuário admin logado
 * 
 * ATENÇÃO: Esta ação é IRREVERSÍVEL!
 */

include("../login/incs/valida-admin.php");
require_once "../login/src/ConexaoBD.php";
require_once "../login/src/CSRF.php";

$idusuario_admin = $_SESSION['idusuarios'];
$conexao = ConexaoBD::conectar();

// Validação CSRF
$token = $_GET['token'] ?? $_POST['token'] ?? '';
if (!CSRF::validarToken($token)) {
    $_SESSION['msg'] = 'Token de segurança inválido.';
    $_SESSION['msg_tipo'] = 'erro';
    header("Location: ../pages/admin.php");
    exit;
}

// Confirmação adicional - requer POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Mostra página de confirmação
    ?>
    <!DOCTYPE html>
    <html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Limpar Banco de Dados - Admin</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                max-width: 600px;
                margin: 50px auto;
                padding: 20px;
                background: #f5f5f5;
            }
            .warning-box {
                background: #fff3cd;
                border: 2px solid #ffc107;
                border-radius: 8px;
                padding: 20px;
                margin-bottom: 20px;
            }
            .warning-box h2 {
                color: #856404;
                margin-top: 0;
            }
            .warning-box ul {
                color: #856404;
                line-height: 1.8;
            }
            .btn-danger {
                background: #dc3545;
                color: white;
                padding: 12px 24px;
                border: none;
                border-radius: 6px;
                font-size: 16px;
                cursor: pointer;
                margin-right: 10px;
            }
            .btn-danger:hover {
                background: #c82333;
            }
            .btn-cancel {
                background: #6c757d;
                color: white;
                padding: 12px 24px;
                border: none;
                border-radius: 6px;
                font-size: 16px;
                cursor: pointer;
                text-decoration: none;
                display: inline-block;
            }
            .btn-cancel:hover {
                background: #5a6268;
            }
        </style>
    </head>
    <body>
        <div class="warning-box">
            <h2>⚠️ ATENÇÃO: Ação Irreversível!</h2>
            <p><strong>Esta ação vai deletar:</strong></p>
            <ul>
                <li>Todos os usuários (exceto você, o admin)</li>
                <li>Todas as postagens</li>
                <li>Todos os stories</li>
                <li>Todos os comentários</li>
                <li>Todas as curtidas</li>
                <li>Todas as mensagens e conversas</li>
                <li>Todos os eventos</li>
                <li>Todas as notificações</li>
                <li>Todos os seguidores</li>
                <li>Todos os erros reportados</li>
            </ul>
            <p><strong>Você tem certeza que deseja continuar?</strong></p>
        </div>
        
        <form method="POST" action="">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token ?: CSRF::gerarToken()) ?>">
            <button type="submit" class="btn-danger" onclick="return confirm('CONFIRMAÇÃO FINAL: Você realmente quer deletar TUDO? Esta ação NÃO pode ser desfeita!')">
                SIM, LIMPAR TUDO
            </button>
            <a href="../pages/admin.php" class="btn-cancel">Cancelar</a>
        </form>
    </body>
    </html>
    <?php
    exit;
}

// Se chegou aqui, é POST confirmado - procede com a limpeza
try {
    $conexao->beginTransaction();
    
    // 1. Deleta mensagens lidas
    $sql = "DELETE FROM mensagens_lidas";
    $conexao->exec($sql);
    
    // 2. Deleta mensagens
    $sql = "DELETE FROM mensagens";
    $conexao->exec($sql);
    
    // 3. Deleta conversas
    $sql = "DELETE FROM conversas";
    $conexao->exec($sql);
    
    // 4. Deleta interessados em eventos
    $sql = "DELETE FROM eventos_interessados";
    $conexao->exec($sql);
    
    // 5. Deleta eventos
    $sql = "DELETE FROM eventos";
    $conexao->exec($sql);
    
    // 6. Deleta comentários
    $sql = "DELETE FROM comentarios";
    $conexao->exec($sql);
    
    // 7. Deleta curtidas
    $sql = "DELETE FROM curtidas";
    $conexao->exec($sql);
    
    // 8. Deleta compartilhamentos
    $sql = "DELETE FROM compartilhamentos";
    $conexao->exec($sql);
    
    // 9. Busca fotos de posts antes de deletar
    $sql = "SELECT foto FROM postagens WHERE foto IS NOT NULL AND foto != ''";
    $stmt = $conexao->query($sql);
    $fotosPosts = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // 10. Deleta postagens
    $sql = "DELETE FROM postagens";
    $conexao->exec($sql);
    
    // 11. Busca mídias de stories antes de deletar
    $sql = "SELECT midia FROM stories WHERE midia IS NOT NULL AND midia != ''";
    $stmt = $conexao->query($sql);
    $midiasStories = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // 12. Deleta stories
    $sql = "DELETE FROM stories";
    $conexao->exec($sql);
    
    // 13. Deleta notificações
    $sql = "DELETE FROM notificacoes";
    $conexao->exec($sql);
    
    // 14. Deleta seguidores
    $sql = "DELETE FROM seguidores";
    $conexao->exec($sql);
    
    // 15. Deleta erros reportados
    $sql = "DELETE FROM erros_reportados";
    $conexao->exec($sql);
    
    // 16. Busca fotos de perfil dos usuários (exceto admin) antes de deletar
    $sql = "SELECT foto_perfil FROM usuarios WHERE idusuarios != ? AND foto_perfil IS NOT NULL AND foto_perfil != ''";
    $stmt = $conexao->prepare($sql);
    $stmt->execute([$idusuario_admin]);
    $fotosPerfil = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // 17. Deleta todos os usuários EXCETO o admin
    $sql = "DELETE FROM usuarios WHERE idusuarios != ?";
    $stmt = $conexao->prepare($sql);
    $stmt->execute([$idusuario_admin]);
    
    // 18. Limpa arquivos físicos
    $pasta = '../login/uploads/';
    
    // Remove fotos de posts
    foreach ($fotosPosts as $foto) {
        if ($foto && file_exists($pasta . $foto)) {
            @unlink($pasta . $foto);
        }
    }
    
    // Remove mídias de stories
    foreach ($midiasStories as $midia) {
        if ($midia) {
            $caminhoCompleto = '../login/' . $midia;
            if (file_exists($caminhoCompleto)) {
                @unlink($caminhoCompleto);
            }
            // Também tenta no caminho direto
            if (file_exists($pasta . $midia)) {
                @unlink($pasta . $midia);
            }
        }
    }
    
    // Remove fotos de perfil (exceto do admin)
    foreach ($fotosPerfil as $foto) {
        if ($foto && file_exists($pasta . $foto)) {
            @unlink($pasta . $foto);
        }
    }
    
    // 19. Reset auto_increment das tabelas
    $tabelas = [
        'comentarios',
        'compartilhamentos',
        'conversas',
        'curtidas',
        'erros_reportados',
        'eventos',
        'eventos_interessados',
        'mensagens',
        'mensagens_lidas',
        'notificacoes',
        'postagens',
        'seguidores',
        'stories',
        'usuarios'
    ];
    
    foreach ($tabelas as $tabela) {
        try {
            $conexao->exec("ALTER TABLE $tabela AUTO_INCREMENT = 1");
        } catch (PDOException $e) {
            // Ignora erros de tabelas que não têm auto_increment ou não existem
        }
    }
    
    $conexao->commit();
    
    $_SESSION['msg'] = 'Banco de dados limpo com sucesso! Apenas seu usuário admin foi mantido.';
    $_SESSION['msg_tipo'] = 'sucesso';
    
} catch (PDOException $e) {
    $conexao->rollBack();
    error_log("Erro ao limpar banco de dados: " . $e->getMessage());
    $_SESSION['msg'] = 'Erro ao limpar banco de dados: ' . $e->getMessage();
    $_SESSION['msg_tipo'] = 'erro';
}

header("Location: ../pages/admin.php");
exit;


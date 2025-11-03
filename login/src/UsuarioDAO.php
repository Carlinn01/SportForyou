<?php
require_once "ConexaoBD.php";
require_once "Util.php";

class UsuarioDAO {

    // Verifica se um email já existe
    public static function emailExiste(string $email): bool {
        $conexao = ConexaoBD::conectar();
        $sql = "SELECT COUNT(*) FROM usuarios WHERE email = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(1, $email);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    // Verifica se um nome de usuário já existe
    public static function nomeUsuarioExiste(string $nome_usuario): bool {
        $conexao = ConexaoBD::conectar();
        $sql = "SELECT COUNT(*) FROM usuarios WHERE nome_usuario = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(1, $nome_usuario);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public static function cadastrarUsuario($dados) {
        $conexao = ConexaoBD::conectar();

        $email = trim($dados['email']);
        $senha = $dados['senha'];
        $nome = trim($dados['nome']);
        $nome_usuario = trim($dados['nome_usuario']);
        $nascimento = $dados['nascimento'];
        
        // Validação: verifica se email já existe
        if (self::emailExiste($email)) {
            throw new Exception("Este email já está cadastrado. Use outro email ou faça login.");
        }

        // Validação: verifica se nome de usuário já existe
        if (self::nomeUsuarioExiste($nome_usuario)) {
            throw new Exception("Este nome de usuário já está em uso. Escolha outro.");
        }
        
        $foto_perfil = Util::salvarArquivo();

        $sql = "INSERT INTO usuarios (email, senha, nome, nome_usuario, nascimento, foto_perfil) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conexao->prepare($sql);

        $senhaCriptografada = md5($senha);
        $stmt->bindParam(1, $email);
        $stmt->bindParam(2, $senhaCriptografada);
        $stmt->bindParam(3, $nome);
        $stmt->bindParam(4, $nome_usuario);
        $stmt->bindParam(5, $nascimento);
        $stmt->bindParam(6, $foto_perfil);

        $stmt->execute();
    }

    public static function validarUsuario($dados) {
    $usuario_email = ($dados['usuario_email']);
    $senhaCriptografada = md5($dados['senha']);

    $conexao = ConexaoBD::conectar();

    // Se for email válido, pesquisa no campo email, senão no campo nome_usuario
    if (filter_var($usuario_email, FILTER_VALIDATE_EMAIL)) {
        $sql = "SELECT * FROM usuarios WHERE email = ? AND senha = ? LIMIT 1";
    } else {
        $sql = "SELECT * FROM usuarios WHERE nome_usuario = ? AND senha = ? LIMIT 1";
    }


    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(1, $usuario_email);
    $stmt->bindParam(2, $senhaCriptografada);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($stmt->rowCount() > 0){
        return $usuario;
    }else{
        return false;
    }
    
}
public static function Listar($idusuarios){
    $sql = "SELECT * FROM usuarios WHERE idusuarios!=?";

    $conexao = ConexaoBD::conectar();
    $stmt = $conexao->prepare($sql);
    $stmt-> bindParam(1,$idusuarios);
    $stmt->execute();
    

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public static function buscarUsuarioNome($nome){
    $sql = "SELECT * FROM usuarios WHERE nome like ?";

    $conexao = ConexaoBD::conectar();
    $stmt = $conexao->prepare($sql);
    $nome = "%".$nome."%";
    $stmt-> bindParam(1,$nome);
    $stmt->execute(); 

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public static function adicionarNotificacao(int $id_usuario, string $tipo, string $mensagem, ?string $link = null): void {
    $pdo = ConexaoBD::conectar();
    
    // Verifica se a coluna link existe na tabela
    $sql = "SHOW COLUMNS FROM notificacoes LIKE 'link'";
    $stmt = $pdo->query($sql);
    $linkExiste = $stmt->rowCount() > 0;
    
    if ($linkExiste) {
        $sql = "INSERT INTO notificacoes (id_usuario, tipo, mensagem, link) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(1, $id_usuario, PDO::PARAM_INT);
        $stmt->bindValue(2, $tipo, PDO::PARAM_STR);
        $stmt->bindValue(3, $mensagem, PDO::PARAM_STR);
        $stmt->bindValue(4, $link, PDO::PARAM_STR);
    } else {
        $sql = "INSERT INTO notificacoes (id_usuario, tipo, mensagem) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(1, $id_usuario, PDO::PARAM_INT);
        $stmt->bindValue(2, $tipo, PDO::PARAM_STR);
        $stmt->bindValue(3, $mensagem, PDO::PARAM_STR);
    }
    $stmt->execute();
}

public static function listarNotificacoes(int $id_usuario): array {
    $pdo = ConexaoBD::conectar();
    
    // Verifica se a coluna link existe
    $sql = "SHOW COLUMNS FROM notificacoes LIKE 'link'";
    $stmt = $pdo->query($sql);
    $linkExiste = $stmt->rowCount() > 0;
    
    if ($linkExiste) {
        // Recuperar notificações não lidas com link
        $sql = "SELECT *, link FROM notificacoes WHERE id_usuario = ? AND lida = 0 ORDER BY data DESC";
    } else {
        // Recuperar notificações não lidas sem link
        $sql = "SELECT * FROM notificacoes WHERE id_usuario = ? AND lida = 0 ORDER BY data DESC";
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(1, $id_usuario, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public static function marcarComoLida(int $id_notificacao): void {
    $pdo = ConexaoBD::conectar();
    
    // Atualiza o status da notificação para 'lida'
    $sql = "UPDATE notificacoes SET lida = 1 WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(1, $id_notificacao, PDO::PARAM_INT);
    $stmt->execute();
}

public static function limparNotificacoes(int $id_usuario): void {
    $pdo = ConexaoBD::conectar();
    
    // Marca todas as notificações do usuário como lidas
    $sql = "UPDATE notificacoes SET lida = 1 WHERE id_usuario = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(1, $id_usuario, PDO::PARAM_INT);
    $stmt->execute();
}



 public static function listarSugestoes(int $idusuario_logado, int $limite = 5): array {
    $pdo = ConexaoBD::conectar();
    
    // SQL ajustado: idusuario = quem é seguido, idseguidor = quem está seguindo
    // Exclui usuários que o logado já está seguindo
    $sql = "SELECT idusuarios, nome, nome_usuario, foto_perfil
            FROM usuarios
            WHERE idusuarios != ? 
            AND idusuarios NOT IN (
                SELECT idusuario  -- Pessoas que o usuário logado (idseguidor) já está seguindo
                FROM seguidores
                WHERE idseguidor = ?  -- idseguidor = usuário logado (quem está seguindo)
            )
            ORDER BY RAND()
            LIMIT ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(1, $idusuario_logado, PDO::PARAM_INT);  // Exclui o usuário logado
    $stmt->bindValue(2, $idusuario_logado, PDO::PARAM_INT);  // Exclui os usuários que o logado já segue
    $stmt->bindValue(3, $limite, PDO::PARAM_INT);  // Limite de sugestões
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



    public static function buscarPorNomeOuUsuario(string $q): array {
    $pdo = ConexaoBD::conectar();
    $sql = "SELECT idusuarios, nome, nome_usuario, foto_perfil FROM usuarios 
            WHERE nome LIKE ? OR nome_usuario LIKE ? LIMIT 5";
    $stmt = $pdo->prepare($sql);
    $like = "%$q%";
    $stmt->execute([$like, $like]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


}
?>

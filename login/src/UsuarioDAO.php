<?php
require_once "ConexaoBD.php";
require_once "Util.php";

class UsuarioDAO {

    public static function cadastrarUsuario($dados) {
        $conexao = ConexaoBD::conectar();

        $email = $dados['email'];
        $senha = $dados['senha'];
        $nome = $dados['nome'];
        $nome_usuario = $dados['nome_usuario'];
        $nascimento = $dados['nascimento'];
        
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

public static function adicionarNotificacao(int $id_usuario, string $tipo, string $mensagem): void {
    $pdo = ConexaoBD::conectar();
    
    $sql = "INSERT INTO notificacoes (id_usuario, tipo, mensagem) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(1, $id_usuario, PDO::PARAM_INT);
    $stmt->bindValue(2, $tipo, PDO::PARAM_STR);
    $stmt->bindValue(3, $mensagem, PDO::PARAM_STR);
    $stmt->execute();
}

public static function listarNotificacoes(int $id_usuario): array {
    $pdo = ConexaoBD::conectar();
    
    // Recuperar notificações não lidas
    $sql = "SELECT * FROM notificacoes WHERE id_usuario = ? AND lida = 0 ORDER BY data DESC";
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



 public static function listarSugestoes(int $idusuario_logado, int $limite = 5): array {
    $pdo = ConexaoBD::conectar();
    
    // SQL ajustado para refletir as colunas 'idseguidor' e 'idusuario'
    $sql = "SELECT idusuarios, nome, nome_usuario, foto_perfil
            FROM usuarios
            WHERE idusuarios != ? 
            AND idusuarios NOT IN (
                SELECT idseguidor  -- Ajustando para usar 'idseguidor' como o usuário seguido
                FROM seguidores
                WHERE idusuario = ?  -- 'idusuario' indica o usuário logado
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

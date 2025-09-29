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

        $senhaCriptografada = password_hash($senha, PASSWORD_BCRYPT);

        $stmt->bindParam(1, $email);
        $stmt->bindParam(2, $senhaCriptografada);
        $stmt->bindParam(3, $nome);
        $stmt->bindParam(4, $nome_usuario);
        $stmt->bindParam(5, $nascimento);
        $stmt->bindParam(6, $foto_perfil);

        $stmt->execute();
    }

    public static function validarUsuario($dados) {
        $senhaCriptografada = md5($dados['senha']);
        $sql = "SELECT * FROM usuarios WHERE email=? AND senha=?";

        $conexao = ConexaoBD::conectar();
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(1, $dados['email']);
        $stmt->bindParam(2, $senhaCriptografada);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>

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
}
?>

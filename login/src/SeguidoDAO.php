<?php
require_once "ConexaoBD.php";

class SeguidoDAO{

    public static function seguir($idusuario, $idseguidor) {
        $conexao = ConexaoBD::conectar();

        $sql = "insert into seguidores (idusuario, idseguidor) values (?,?)";
        $stmt = $conexao->prepare($sql);

        $stmt->bindParam(1, $idusuario);
        $stmt->bindParam(2, $idseguidor);

        $stmt->execute();

    }

    public static function deixarDeSeguir($idusuario, $idseguidor) {
    $conexao = ConexaoBD::conectar();

    $sql = "DELETE FROM seguidores WHERE idusuario = ? AND idseguidor = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(1, $idusuario);
    $stmt->bindParam(2, $idseguidor);
    $stmt->execute();
}

    public static function listarSeguidores($idusuario) {
    $conexao = ConexaoBD::conectar();

    $sql = "SELECT u.nome_usuario, u.foto_perfil 
            FROM seguidores s
            JOIN usuarios u ON u.idusuarios = s.idseguidor
            WHERE s.idusuario = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(1, $idusuario);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}


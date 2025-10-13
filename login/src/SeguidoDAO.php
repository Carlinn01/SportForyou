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

}


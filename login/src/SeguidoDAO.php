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
}
<?php
class ConexaoBD{

    public static function conectar():PDO{
        $conexao = new PDO("mysql:host=localhost:3306;dbname=meuprojeto","root","123456");
        return $conexao;
    }
}
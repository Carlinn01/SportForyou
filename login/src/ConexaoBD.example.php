<?php
class ConexaoBD{

    public static function conectar():PDO{
        // ALTERE AS CREDENCIAIS DO BANCO DE DADOS AQUI
        $host = 'localhost';  // ou o IP do servidor
        $port = '3306';       // porta do MySQL (padrão: 3306)
        $dbname = 'nome_do_banco';
        $username = 'usuario_do_banco';
        $password = 'senha_do_banco';
        
        $conexao = new PDO("mysql:host=$host:$port;dbname=$dbname", $username, $password);
        $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conexao;
    }
}
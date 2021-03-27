<?php
include_once 'config.php';

class Conexao {
    public static function pegaConexao() 
    {
        $conexao = new PDO(DB_DRIVER.":host=".DB_HOST.";dbname=".DB_BASE, DB_USER, DB_SENHA);
        $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conexao;
    }
       
}
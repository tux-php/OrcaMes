<?php
class Mensagem{
    private string $msg;
    
    public static function dispararSucesso($mensagem)
    {
        return "<div class='alert alert-success' role='alert'>$mensagem</div>";
    }
    public static function dispararErro($mensagem)
    {
        return "<div class='alert alert-danger' role='alert'>$mensagem</div>";                    
    }
}
?>
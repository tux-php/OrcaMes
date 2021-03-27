<?php
class AutenticacaoUsuario{
    private string $email;
    private string $senha;    

    public function __construct(string $email,string $senha){
        $this->email = $email;
        $this->senha = $senha;
    }

    public function carregaEmail():string
    {
        return $this->email;
    }
    public function carregaSenha():string
    {
        return $this->senha;
    }
}
?>
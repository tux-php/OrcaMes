<?php

class Usuario{
    
    private string $nome;
    private string $sobrenome;
    private $salario;
    private int $orgaoId;
    private int $statusId;    
    private UsuarioDAO $usuarioDAO;
    private AutenticaoUsuario $AutenticacaoUsuario;

    public function __construct(string $nome='',string $sobrenome='', $salario=false,int $orgaoId=0,int $statusId=0) {
        $this->nome = $nome;
        $this->sobrenome = $sobrenome;
        $this->salario = $salario;
        $this->orgaoId = $orgaoId;
        $this->statusId = $statusId;   
             
        $this->usuarioDAO = new UsuarioDAO();
    }

    public function carregaNome():string
    {
        return $this->nome;
    }
    public function carregaSobrenome():string
    {
        return $this->sobrenome;
    }
    public function carregaSalario()
    {
        return $this->salario;
    }
    public function carregaOrgaoId():int
    {
        return $this->orgaoId;
    }
    public function carregaStatusId():int
    {
        return $this->statusId;
    }
    public function carregaEmailUsuario():string
    {
        return $this->AutenticacaoUsuario->carregaEmail();
    }
    public function carregaSenhaUsuario():string
    {
        return $this->AutenticacaoUsuario->carregaSenha();
    }

   public function autenticacaoUser(string $email, string $senha)
   {       
       return $this->usuarioDAO->autenticacaoUser($email,$senha);
   }

   public function pegaLogado(string $email, string $senha)
   {
       return $this->usuarioDAO->pegaLogado($email,$senha);
   }


    public function getUsuario($id) {
        return $this->banco->pesquisar('usuario', "id_usuario=$id");
    }

    final function pegaIdLogado() {           
        return $_SESSION['usuario'];
    }

   

}

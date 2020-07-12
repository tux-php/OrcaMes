<?php
class Usuario extends login{
    public $nome;
    public $sobrenome;
    public $salario;
    public $orgao_pagador;
    public $status_usuario;
    public $email;
    public $senha;
    
    protected $banco;
    
    
    const TABELA = 'usuario';
    
    public function __construct() {
        $this->banco = Banco::Instanciar();
    }
    
    public function listar(){
        $this->banco->listar();
    }
    
    public function inserir()
    {
        if($_POST) {
            //$_POST['admin'] = isset($_POST['admin']) ? 1 : 0;
            $this->banco->inserir(self::TABELA, $_POST);
    
            header('Location: admin.php');
        }
    }

    public function alterar($id)
    {
        if($_POST) {
            //$_POST['admin'] = isset($_POST['admin']) ? 1 : 0;
            $this->banco->alterar(self::TABELA, $_POST, "id=$id");
    
            header('Location: admin.php');
        } else {
            return $this->banco->ver(self::TABELA, '*', "id=$id");
        }
    }

    public function apagar($id)
    {
        $this->banco->apagar(self::TABELA, array("id" => $id));
       
        header('Location:admin.php'); 
    }

    public function __sleep()
    {
        return array('id', 'usuario', 'admin');
    }
    
}
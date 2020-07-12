<?php

/**
 * Description of login
 *
 * @author tux-php
 */
abstract class login {
    public function logar(){
        if(!empty($_POST)){
            $logado = $this->validaLogin($_POST['email'],$_POST['senha']);
            
            if($logado){
                header('Location:index.php');
            }else{
                echo 'Usuário ou senha inválido!';
            }
        }
        
    }
    
    public function deslogar() {
        unset($_SESSION['login']);
        header('Location:index.php');
    }
    
     protected function validaLogin($email, $senha)
    {
        $return = $this->banco->validarLogin(Usuarios::TABELA,$email,$senha);
        if(empty($return)) {
            return false;
        } else {
            $this->id      = $return['id'];
            $this->usuario = $return['usuario'];
            $this->admin   = $return['admin'];

            return true;
        }
    }
}

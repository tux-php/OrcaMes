<?php
class StatusUsuario extends Modelo{
    
    const TABELA = 'status_usuario';
    
    public function __construct($id = null) {        
        $this->id = $id;
        $this->banco = Banco::Instanciar();
    }
    
}
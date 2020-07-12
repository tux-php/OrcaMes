<?php

class Relatorio extends Modelo{        
    
    public function __construct($id = NULL) {        
        $this->id = $id;
        $this->banco = Banco::Instanciar();
    }
}

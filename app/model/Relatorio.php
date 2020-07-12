<?php

class Relatorio extends Modelo{        
    
    public function __construct($id = NULL) {        
        $this->id = $id;
       parent::__construct();
    }
}

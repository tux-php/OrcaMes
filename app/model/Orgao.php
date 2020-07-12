<?php
class Orgao extends Modelo{        
    const TABELA = 'orgao_pagador';
    
    public function __construct($id = null) {                
        $this->id = $id;
        parent::__construct();
    }
    
     public function getOrgaoPagador($id) {
        return $this->banco->pesquisar('orgao_pagador', "id_orgao_pagador=$id");
    }
    
}
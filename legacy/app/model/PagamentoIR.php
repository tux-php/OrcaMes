<?php
class PagamentoIR extends Modelo{    
    const TABELA = 'imposto_renda';
    
    public function __construct() {
        parent::__construct();
    }
    
    public function inserirPagamentoIR($id_usuario,$id_tipo_despesa,$descricao,$valor,$img,$data_proc){        
        $this->banco->inserirPagamentoIR(static::TABELA,$id_usuario,$id_tipo_despesa,$descricao,$valor,$img,$data_proc);
    }
    
    public function listarPagIr($id_usuario){                
       return $this->banco->listarPagamentoIR(static::TABELA,$id_usuario);
    }
}
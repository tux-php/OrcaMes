<?php

abstract class Modelo {

    protected $banco;

    public function __construct() {
        $this->banco = Banco::Instanciar();
    }

    public function listar() {
        return $this->banco->listar(static::TABELA);
    }

    protected function inserir($dados) {
        $this->banco->inserir(static::TABELA, $dados);
    }

    public function alterar($id, $id_tabela, $dados) {
        //die('oi');
        $this->banco->alterar(static::TABELA, $id_tabela, $id, $dados);
    }

    public function buscar($identificador_id, $id) {
        return $this->banco->buscar(static::TABELA, $identificador_id, $id);
    }

     public function excluir($campo, $id) {
        return $this->banco->excluir(static::TABELA, $campo, $id);
    }

}

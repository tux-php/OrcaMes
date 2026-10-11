<?php
class SalarioMensal extends Modelo {
    const TABELA = "salario_mensal";
    const TB_REF_ORGAO_PAGADOR = "orgao_pagador";
    
    public function __construct() {
        parent::__construct();
    }
    
}

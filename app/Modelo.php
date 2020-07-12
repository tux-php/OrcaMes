<?php

abstract class Modelo {

    protected $banco;

    public function __construct() {
        $this->banco = Banco::Instanciar();
    }

    public function getOrgaoPagador($id) {
        return $this->banco->pesquisar('orgao_pagador', "id_orgao_pagador=$id");
    }

    public function getPagamento($id) {
        return $this->banco->pesquisar('pagamentos', "id_pagamento = $id");
    }

    public function getContent() {
        $conteudo['orgaoPagador'] = $this->banco->listar('orgao_pagador');
        $conteudo['usuario'] = $this->banco->listar('usuario');

        return $conteudo;
    }

    final public function listar() {
        return $this->banco->listar(static::TABELA);
    }

    final public function inserir($dados) {
        $this->banco->inserir(static::TABELA, $dados);
    }

    final public function alterar($id, $id_tabela, $dados) {
        $this->banco->alterar(static::TABELA, $id_tabela, $id, $dados);
    }

    final public function buscar($identificador_id, $id) {        
        return $this->banco->buscar(static::TABELA, $identificador_id, $id);
    }

    final public function buscarPagamento($id_usuario, $id_mes_referencia) {
        return $this->banco->buscarPagamento(static::TABELA, $id_usuario, $id_mes_referencia);
    }

    final public function excluir($campo, $id) { 
        //var_dump($campo);die();
        return $this->banco->excluir(static::TABELA, $campo, $id);
    }

    public function pegaValorItem($mes_referencia, $id_usuario, $id_status_pagamento) {
        $lista_valor = $this->banco->listarValorItem(static::TABELA, $mes_referencia, $id_usuario, $id_status_pagamento);
        return $lista_valor;
    }

    public function pegaValorSubtotal($mes_referencia, $id_usuario) {
        $lista_valor = $this->banco->listarValorSubtotal(static::TABELA, $mes_referencia, $id_usuario);
        return $lista_valor;
    }

    public function SomarSubtotal($mes, $usuario) {
        $subtotal = 0;  
        foreach ($this->pegaValorSubtotal($mes, $usuario) as $chave => $valor) {
            $subtotal += $valor["valor_pagamento"];
        }
        return $subtotal;
    }

    public function SomarTotal($mes, $usuario, $id_status_pagamento) {
        $total = 0;
        foreach ($this->pegaValorItem($mes, $usuario, $id_status_pagamento) as $chave => $valor) {
            $total += $valor["valor_pagamento"];
        }
        return $total;
    }

    public function pegaSalario($id) {
        return $this->banco->pegaSalarioPorUser(static::TABELA, $id);
    }

    public function pegaMesAnterior($id_mes_ref) {
        return $this->banco->pegaMesAnterior(static::TABELA, $id_mes_ref);
    }

    public function pegaStatusPago($ch) {   
        
        return $this->banco->pegaStatusPago('status_pagamento', $ch);
    }

    public function pegaStatus($chave) {
        return $this->banco->pegaStatus('status_pagamento', $chave);
    }

   

    public function pegaValorUnidade($id) {
        return $this->banco->pegaValorUnidade(static::TABELA, $id);
    }

    public function clonarPag(array $dados) {
        try {
            $dados['data_lancamento'] = date('Y-m-d');
            $npg = $this->pegaStatus('NPG');
            $dados['id_status_pagamento'] = (int) $npg['id_status_pagamento'];
            $dados['ch_clone'] = 'S';
            $clonar = $this->banco->clonarPagamento('pagamentos', $dados);
            if ($clonar == false) {
                throw new Exception('Falha na clonagem dos dados');
            } else {
                return true;
            }
        } catch (Exception $exc) {
            echo $exc->getMessage() . '<br>';
        }
    }

    public function ajustarData($data) {
        $divisao = explode('-', $data);
        $data_correta = implode('/', array_reverse($divisao));
        return $data_correta;
    }

    final function pegaLogado($email, $senha) {
        return $this->banco->pegaLogado('autenticacao_user', $email, $senha);
    }

    final function pegaSeguranca($sessao) {
        if ($sessao == null) {
            return session_destroy();
        } else {
            return $sessao;
        }
    }
    public function gerarRelatorio() {
        return $this->banco->gerarRelatorio();
    }

}

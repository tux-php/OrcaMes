<?php

class Pagamento{
    private int $idPagamento;
    private int $idTipoPagamento;    
    private $valorPagamento;
    private $data_lancamento;
    private int $id_usuario;
    private int $id_status_pagamento;
    private $data_processamento;
    private int $id_mes_referencia;
    private string $clone;    
    private PagamentoDAO $pagamentoDAO;

    public function __construct(int $idTipoPagamento = 0, 
                                $valorPagamento = FALSE,
                                $data_lancamento = NULL, 
                                int $id_usuario = 0, 
                                int $id_status_pagamento = 0,                                
                                int $id_mes_referencia = 0,
                                string $clone = '') 
    {
        $this->idTipoPagamento = $idTipoPagamento.
        $this->valorPagamento = $valorPagamento;
        $this->data_lancamento = $data_lancamento;
        $this->id_usuario = $id_usuario;
        $this->id_status_pagamento = $id_status_pagamento;        
        $this->id_mes_referencia = $id_mes_referencia;
        $this->clone = $clone;
    }
    public function carregarTipoPagamentoId():int
    {
        return $this->idTipoPagamento;
    }
    public function carregarValorPagamento():float
    {
        return $this->valorPagamento;
    }
    public function carregarDataLancamento()
    {
        return $this->data_lancamento;
    }
    public function carregarUsuarioId():int
    {
        return $this->id_usuario;
    }
    public function carregarStatusPagamentoId():int
    {
        return $this->id_status_pagamento;
    }
    public function carregarDataProcessamento()
    {
        return $this->data_processamento;
    }
    public function carregarMesReferenciaId():int
    {
        return $this->id_mes_referencia;
    }
    public function carregarClone():string
    {
        return $this->clone;
    }
    public function ajustarData($data) {
        try {
            $divisao = explode('-', $data);
            $data_correta = implode('/', array_reverse($divisao));
            if($data_correta){
                return $data_correta;
            }
        } catch (Exception $e) {
            echo "Falha ao efetuar correção da data. ".$e->getMessage();
        }
    }

    public function processarPag($id) {  
        try {
            $this->pagamentoDAO = new PagamentoDAO();            
            $status = $this->pagamentoDAO->pegaStatus('PG');              
            $cod = $status['id_status_pagamento'];            
            $data_proc = date('Y-m-d');        
            if (isset($status)) {
                $salvar = $this->pagamentoDAO->alterarStatusPagamento($cod, $data_proc, $id);                
                if ($salvar) {
                    return true;
                    exit;
                }
            }
        } catch (Exception $e) {
            echo "Falha ao processar pagamento para 'PAGO' .".$e->getMessage();
        }   
    }

    public function cancelarPag($id) {
        $this->pagamentoDAO = new PagamentoDAO();
        $status = $this->pagamentoDAO->pegaStatus('NPG');        
        $cod = $status['id_status_pagamento'];
        $data_proc = date('Y-m-d');
        $salvar = $this->pagamentoDAO->alterarStatusPagamento($cod, $data_proc, $id);
        if ($salvar) {
            return true;
        }
        return false;
    }

    public function clonarPag(array $dados) {
        try {
            $pagamentoDAO = new PagamentoDAO();
            $dados['data_lancamento'] = date('Y-m-d');
            $npg = $pagamentoDAO->pegaStatus('NPG');
            $dados['id_status_pagamento'] = (int) $npg['id_status_pagamento'];
            $dados['ch_clone'] = 'S';
            $clonar = $pagamentoDAO->clonarPagamento($dados);
            if ($clonar == false) {
                throw new Exception('Não foi possível processar a operação!');
            } else {
                return true;
            }
        } catch (Exception $exc) {
            echo $exc->getMessage() . '<br>';
        }
    }
}

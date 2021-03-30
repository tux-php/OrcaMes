<?php
class PagamentoDAO{    

    final public function listarPagamentoMes($id_mes_ref, $id_usuario) {        
        try {
            $query = "SELECT * FROM pagamentos WHERE id_mes_referencia = $id_mes_ref ";
            if (isset($id_usuario)) {
                $query .= " and id_usuario = $id_usuario ";
            }
            $query .= " AND d_e_l_e_t_e IS NULL";            
            $conexao = Conexao::pegaConexao();
            $rs = $conexao->query($query);
            if($rs){
                return $rs->fetchAll(PDO::FETCH_ASSOC);
            }            
        } catch (Exception $e) {
            echo "Falha ao carregar listagem de pagamento por mês. ".$e->getMessage();
        }
    }

    public function inserir(Pagamento $pagamento)
    {
        try{
            $query = "INSERT INTO pagamento(id_tipo_pagamento,valor_pagamento,data_lancamento,id_usuario,id_status_pagamento,id_mes_referencia,ch_clone)
                         VALUES('{$pagamento->carregarTipoPagamentoId()}',
                                '{$pagamento->carregarValorPagamento()}',
                                '{$pagamento->carregarDataLancamento()}',
                                '{$pagamento->carregarUsuarioId()}',
                                '{$pagamento->carregarStatusPagamentoId()}',
                                '{$pagamento->carregarMesReferenciaId()}',
                                '{$pagamento->carregarClone()}')";
                  $conexao = Conexao::pegaConexao();
                  $stmt = $conexao->prepare($query);
                  $rs = $stmt->execute();
                  if($rs){
                      return TRUE;
                      exit;
                  }              
        }catch(Exception $e){
            echo "Falha ao salvar pagamento. ".$e->getMessage();
        }
    }

    public function excluirPagamento(int $id){
        try{
            $query = "DELETE FROM pagamentos WHERE id_pagamento = $id";
            $conexao = Conexao::pegaConexao();
            $stmt = $conexao->prepare($query);
            $rs = $stmt->execute();
            if($rs){
                return TRUE;
                exit();
        }
        }catch(Exception $e){
            echo "Pagamento não excluído. ".$e->getMessage();
        }
        
    }

    public function pegaStatus($chave) {
        try {            
            $query = "SELECT * FROM status_pagamento where chave = '{$chave}'";            
            $conexao = Conexao::pegaConexao();
            $rs = $conexao->query($query);
            if ($rs) {
                return $rs->fetch();
         }
        } catch (Exception $e) {
            echo "Falha ao recuperar status do pagamento. ".$e->getMessage();
        }
        
    }
    
    public function alterarStatusPagamento($cod, $data_proc, $id) {        
        try {
            $query = "UPDATE pagamentos SET id_status_pagamento = $cod, data_processamento = '$data_proc' where id_pagamento = $id ";            
            $conexao = Conexao::pegaConexao();
            $stmt = $conexao->prepare($query);
            $rs = $stmt->execute();
            if ($rs) {
                return 'Pagamento Processado com sucesso!';
            } else {
                return 'Falha ao processar pagamento!';
            }
        } catch (Exception $e) {
            echo "Falha ao alterar status do pagamento. ".$e->getMessage();
        }
        
    }
    
     public function validandoMesReferenciaVazio($mes_ref,$id_usuario){
         try {
            $query = "SELECT * FROM pagamentos WHERE id_mes_referencia = $mes_ref 
                                AND id_usuario = $id_usuario AND d_e_l_e_t_e IS NULL";  
                                //var_dump($query);die();          
            $conexao = Conexao::pegaConexao();
            $stmt = $conexao->query($query);                        
            $count = $stmt->rowCount();                        
            if ($count == 0):
                return true;
                exit;
            endif;            
         } catch (Exception $e) {
             echo "Falha no processo de validação do mês precedente." .$e->getMessage();
         }
         
        
    }

    public function alterarPagamento($id, $id_tab, $dados){
        //var_dump($dados);die();
        $sql = "UPDATE $tab SET 
                    ID_USUARIO='{$dados['id_usuario']}',
                    ID_TIPO_PAGAMENTO='{$dados['id_tipo_pagamento']}',
                    VALOR_PAGAMENTO='{$dados['valor_pagamento']}',
                    DATA_LANCAMENTO='{$dados['data_lancamento']}',
                    ID_STATUS_PAGAMENTO='{$dados['id_status_pagamento']}' 
                    WHERE $id_tab='$id'";        
        $rs = $this->conexao->query($sql);
        if ($rs)
            return true;
        return false;
    }

    public function buscar($id) {
        try{
            $conexao = Conexao::pegaConexao();
            $query = "SELECT * FROM pagamentos WHERE id_pagamento = '$id' AND d_e_l_e_t_e IS NULL";
            $results = $conexao->query($query);
            if($results){
                return $results->fetch();
                exit();
            }
        }catch(Exception $e){
            echo "Houve falha ao encontrar usuário. ".$e->getMessage();
        }
    }

    public function SomarTotal($mes, $usuario, $id_status_pagamento) {
        try {
            $total = 0;
            foreach ($this->pegaValorItem($mes, $usuario, $id_status_pagamento) as $chave => $valor) {
                $total += $valor["valor_pagamento"];
            }
            return $total;
        } catch (Exception $e) {
            echo "Falha no processo de cálculo da soma. ".$e->getMessage();
        }
        
    }

    /*
     * @pegaValorItem contempla Mês de Referencia
     * avaliar sua classe de origem
     */
    public function pegaValorItem($mes_referencia, $id_usuario, $id_status_pagamento) {
        try {
            $query = "SELECT
                      valor_pagamento FROM pagamentos WHERE id_usuario = $id_usuario
                      AND id_status_pagamento = $id_status_pagamento 
                      AND id_mes_referencia = $mes_referencia 
                      AND d_e_l_e_t_e IS NULL";
            $conexao = Conexao::pegaConexao();
            $rs = $conexao->query($query);
            if($rs){
                return $rs->fetchAll(PDO::FETCH_ASSOC);
                exit;
            }
            
        } catch (Exception $e) {
            echo "Falha ao retornar valor do item. ".$e->getMessage();
        }
    }

     /*
     * @SomarSubtotal contempla Mês de Referencia
     * avaliar sua classe de origem
     */
    public function SomarSubtotal($mes, $usuario) {
        try {
            $subtotal = 0;
            foreach ($this->pegaValorSubtotal($mes, $usuario) as $chave => $valor) {
                $subtotal += $valor["valor_pagamento"];
            }
            return $subtotal;
        } catch (Exception $e) {
            echo "Falha na somatória do sub-total. ".$e->getMessage();
        }
        
    }

    /*
     * @pegaValorSubtotal contempla Mês de Referencia
     * avaliar sua classe de origem
     */
    public function pegaValorSubtotal($mes_referencia, $id_usuario) {
        try {
            $query = "SELECT valor_pagamento FROM pagamentos 
                    WHERE id_usuario = $id_usuario 
                    AND id_mes_referencia = $mes_referencia AND d_e_l_e_t_e IS NULL";
             $conexao = Conexao::pegaConexao();       
             $rs = $conexao->query($query);
             if($rs){
                return $rs->fetchAll(PDO::FETCH_ASSOC);
                exit;
             }                
        } catch (Exception $e) {
            echo "Falha no retorno do sub-total. ".$e->getMessage();
        }
        
    }

    public function clonarPagamento(array $dados) {
        /*
         * tratar falha conexao com banco
         * fazer um verificador para não permitir colocar dados clonados outra vez
         * 
         */
        try {
            $query = "INSERT INTO pagamentos(id_tipo_pagamento,
                                            valor_pagamento,
                                            id_usuario,
                                            id_status_pagamento) 
                    SELECT a.id_tipo_pagamento,a.valor_pagamento,a.id_usuario,a.id_status_pagamento 
                        FROM pagamentos a, tipo_pagamento b, 
                        mes_referencia c, usuario d 
                        WHERE(a.d_e_l_e_t_e IS NULL AND b.d_e_l_e_t_e IS NULL AND c.d_e_l_e_t_e IS NULL AND d.d_e_l_e_t_e IS NULL)
                        AND a.id_tipo_pagamento = b.id_tipo_pagamento
                        AND a.id_mes_referencia = c.id_mes_referencia 
                        AND a.id_usuario = d.id_usuario 
                        AND a.id_mes_referencia = '{$dados['id_mes_clone']}'
                        AND a.id_usuario = '{$dados['id_usuario']}'";
            $conexao = Conexao::pegaConexao();
            $stmt = $conexao->prepare($query);
            $stmt->execute();    
            $num = $stmt->rowCount();            
            if ($num > 0) {
                $this->processarClonagem($dados);
                return true;
            } else {
                throw new Exception("Mês precedente encontra-se vazio!");
            }
        } catch (Exception $exc) {
            echo $exc->getMessage() . '<br>';
        }
    }

    public function processarClonagem($dados) {
        try {
            $query = "UPDATE pagamentos SET 
                            data_lancamento = '{$dados['data_lancamento']}', 
                            id_status_pagamento = '{$dados['id_status_pagamento']}',
                            id_mes_referencia = '{$dados['id_mes_referencia']}',
                            ch_clone = '{$dados['ch_clone']}'
                                WHERE data_lancamento = '0000-00-00' 
                                AND id_usuario = '{$dados['id_usuario']}'
                                AND d_e_l_e_t_e is null";
            $conexao = Conexao::pegaConexao();
            $stmt = $conexao->prepare($query);                       
            $rs = $stmt->execute();
            if ($rs) {
                return true;
            }
            return false;
        } catch (Exception $e) {
            echo "Houve problemas no processo de clonagem. ".$e->getMessage();
        }
    }
    
}
?>
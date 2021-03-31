<?php
class TipoPagamentoDAO{

    final public function listarTP($id_usuario) {        
        try{
            $query = "SELECT distinct *  FROM tipo_pagamento as a
                    INNER JOIN usuario_tipo_pagamento as b
                    WHERE(a.d_e_l_e_t_e is null)
                    and a.id_tipo_pagamento = b.id_tipo_pagamento
                    AND b.id_usuario = $id_usuario 
                    order by a.descricao";                    
                $conexao = Conexao::pegaConexao();                
                $rs = $conexao->query($query);
                if($rs){
                    return $rs->fetchAll(PDO::FETCH_ASSOC);
                }                
        }catch(Exception $e){
            echo "Falha ao carregar a listagem do Tipo de Despesa. ".$e->getMessage();
        }
    }
    
    final public function recuperarIdTP(){        
        try {
            $query = "select id_tipo_pagamento from tipo_pagamento order by id_tipo_pagamento desc limit 1";
            $conexao = Conexao::pegaConexao();            
            $rs = $conexao->query($query);            
        while ($row = $rs->fetch()) {
            $id_usuario = (int) $row['id_tipo_pagamento'];
            return $id_usuario;
        }
        } catch (Exception $e) {
            echo "Houve falha ao recuperar id_usuário vinculado. ".$e->getMessage();
        }
        
    }
    
    final public function inserirTP(TipoPagamento $tipoPagamento){        
        try {
            $query = "INSERT INTO tipo_pagamento(descricao,id_tipo_despesa)
                             VALUES ('{$tipoPagamento->carregaDescricao()}',
                                    '{$tipoPagamento->carregaTipoDespesaId()}')";                                    
            $conexao = Conexao::pegaConexao();
            $stmt = $conexao->prepare($query);
            $rs = $stmt->execute();                        
            if ($rs) {
                return true;
            }
            return false;
        } catch (Exception $e) {
            echo "Falha ao inserir Tipo de Despesa. ".$e->getMessage();
        }
        
    }

    public function alterar($id, $id_tabela, $dados) {  
        try
        {
            foreach ($dados as $campo => $valor) {
                $set[] = "$campo='$valor'";
            }
            $sets = strtoupper(implode(',', $set));
            $conexao = Conexao::pegaConexao();
            $query = "UPDATE tipo_pagamento SET $sets WHERE $id_tabela='$id'";            
            $stmt = $conexao->prepare($query);
            $stmt->execute();            
        }catch(Exception $e){
            echo $e->getMessage() . " - Detalhe:" . $exc->getTraceAsString();
        }   
        
    }   

    public function buscar($id) {
        try{
            $conexao = Conexao::pegaConexao();
            $results = $conexao->query("SELECT * FROM tipo_pagamento WHERE id_tipo_pagamento = '$id' and d_e_l_e_t_e is null");            
            return $results->fetch();
        }catch(Exception $e){
            echo $e->getMessage() . " - Detalhe:" . $exc->getTraceAsString();
        }
        
    }

    public function listar() {
        try
        {
            $lista = "SELECT * FROM tipo_pagamento WHERE d_e_l_e_t_e is null";
            $conexao = Conexao::pegaConexao();
            $rs = $conexao->query($lista);
            return $rs->fetchAll(PDO::FETCH_ASSOC);                
        }catch(Exception $e){
            echo "Houve uma falha ao gerar lista de tipo de pagamentos. ".$e->getMessage();
        }
        
    }

    public function excluir($id):string
    {
        try
        {
            $query = "DELETE FROM tipo_pagamento WHERE id_tipo_pagamento = '$id'";      
            $conexao = Conexao::pegaConexao();
            $stmt = $conexao->prepare($query);
            $rs = $stmt->execute();
            if ($rs) {
                return true;
            }
            return false;
        }catch(Exception $e){
            echo $e->getMessage() . " - Detalhe:" . $exc->getTraceAsString();
        }
        
    }

}

?>
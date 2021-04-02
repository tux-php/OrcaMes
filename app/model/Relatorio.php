<?php

class Relatorio{        
    
    public function gerarRelatorio() {
        $query = "select distinct(a.descricao), sum(b.valor_pagamento)
                        from mes_referencia a, pagamentos b
                        where(a.d_e_l_e_t_e is null and b.d_e_l_e_t_e is null)
                        and a.id_mes_referencia = b.id_mes_referencia
                        and b.data_processamento between '2017-01-01' and '2017-12-30'
                        group by a.descricao order by a.id_mes_referencia";
        $conexao = Conexao::pegaConexao();
        $rs = $conexao->query($query);
        if($rs){
            return $rs->fetchAll(PDO::FETCH_ASSOC);
            exit;
        }
        
    }
}

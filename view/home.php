<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="././css/bootstrap.min.css" />
        <link rel="stylesheet" href="./css/meu_estilo/estilo_meu.css" />
        <script type="text/javascript" src="././jquery/jquery-1.11.3.min.js"></script>
        <script type="text/javascript" src="././js/bootstrap.min.js"></script>
         <meta charset="UTF-8" />
        <title>Balanço Mensal</title>
    </head>
    <body>
        <ul class="nav nav-pills">
            <li role="presentation" class="active"><a href="?action=listaOrgaoPagador">Orgão Pagador</a></li>
            <li role="presentation"><a href="?action=listaUsuario">Usuários</a></li> 
             <!--<li role="presentation"><a href="?action=listarStatusUsuario">Inserir Status Usuário</a></li>-->
            <li role="presentation"><a href="?action=listaTipoPagamento">Cadastro de Pagamento</a></li>
            <li role="presentation"><a href="?action=listarTipoDespesa">Tipo de Despesa</a></li>
            <li role="presentation"><a href="?action=listarMesRef">Inserir Meses/Ano</a></li>            
            <li role="presentation"><a href="?action=listarPagamentosIR">Inserir Pagamentos IR</a></li>
            <li role="presentation"><a href="?action=listarPagamentoMes">Inserir Pagamentos</a></li>
            <!--<li role="presentation"><a href="?action=listarRelatorios">Relatórios</a></li>-->
            <!--<li role="presentation"><a href="?action=listaStatusPagamento">Inserir Status Pagamentos</a></li>-->
            <li role="presentation"><a href="logout.php">Sair</a></li>
        </ul>
        
            
        
        
        <h3>Tipo de Pagamentos</h3>
        <div class="panel-default">
           <div class="panel-heading">Tipos de Pagamentos</div>
        <table class="table">
            <thead>
            <tr  class="list-group">
                <th>#</th>
                <th>Descricao</th>
                <th colspan="2">Ações</th>
            </tr>
            </thead>
            <?php
                foreach($dados['tipo_pagamento'] as $tp){
            ?>
            <tbody>
            <tr>
                <td><?= $tp['id_tipo_pagamento'];?></td>
                <td><?= $tp['descricao'];?></td>
                <td><a href="?action=alterarTP&id_tipo_pagamento=<?=$tp['id_tipo_pagamento'];?>">Alterar</a></td>
                <td>
                    <a href="?action=excluirTP&id_tipo_pagamento=<?php echo $tp['id_tipo_pagamento']?>">Excluir</a>
                </td>
            </tr>
            </tbody>
                <?php } ?>
        </table>
            </div>
        <a href="?action=inserirTipoPagamento">Novo</a>
        
        
    </body>
    
</html>
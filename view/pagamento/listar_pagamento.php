<!DOCTYPE HTML>
<html lang="en-US">
    <head>
        <meta charset="UTF-8">
        <title></title>   
        <link rel="stylesheet" href="././css/bootstrap.min.css" />
        <link rel="stylesheet" href="././css/meu_estilo/estilo_meu.css" />
        <script type="text/javascript" src="././jquery/jquery-1.11.3.min.js"></script>        
        <script type="text/javascript" src="././js/bootstrap.min.js"></script>
        <script type="text/javascript" src="././js/efeitos.js"></script>        
    </head>
    <body>
        <div style="float: right;">
            <a href="?action=inserirPagamento" title="Novo Pagamento"><span class="glyphicon glyphicon-plus" style="font-size: 30px; color: #888"></span></a>            
            <br /><a href="?action=listarPagamentoMes" title="voltar"><span class="glyphicon glyphicon-arrow-left" style="font-size: 30px; color: #888"><?php // unset($_SESSION['id_mes_ref']); ?></span></a>
        </div>

        <div class="container-fluid">
            <h3>Pagamentos</h3>

            <div class="panel-default">

                <div class="panel-heading">Lista de Pagamentos</div>

                <table border="0" class="table">
                    <thead>
                        <tr class="list-group">
                            <th>#</th>
                            <th>Tipo Pagamento</th>
                            <th>Valor</th>
                            <th>Lançamento</th>
                            <th>Processamento</th>
                            <th>Mes Referencia</th>
                            <th>Responsável</th>
                            <th colspan="4"><center>Ações</center></th>
                    <th>Status</th>


                    </tr>      
                    </thead>
                    <?php
                    if ($dados['pagamentos']) {
                        //var_dump($dados['pagamentos']);
                        foreach ($dados['pagamentos'] as $pag) {
                            ?>
                            <tbody>
                                <tr class="mes_referencia">
                                    <td><?= $pag['id_pagamento']; ?></td>
                                    <td><?= $pag['id_tipo_pagamento']; ?></td>
                                    <td><?= $pag['valor_pagamento']; ?></td>
                                    <td><?= $pag['data_lancamento']; ?></td>
                                    <td><?php
                                        if (isset($pag['data_processamento']) && $pag['data_processamento']) {
                                            echo $pag['data_processamento'];
                                            ?></td>
                                    <?php
                                    } else {
                                        echo '--';
                                    }
                                    ?>

                                    <td><?= $pag['id_mes_referencia']; ?></td>
                                    <td><?= $pag['id_usuario']; ?></td>

                                    <td>
                                        <a href="?action=alterarPagamento&id_pagamento=<?php echo $pag['id_pagamento'] ?>" title="Editar"><span class="glyphicon glyphicon-cog" style="color: #777"></span></a>
                                    </td>
                                    <td>
                                        <a href="?action=excluirPagamento&id_pagamento=<?php echo $pag['id_pagamento']; ?>" title="Excluir"><span class="glyphicon glyphicon-remove" style="color: #777"></span></a>
                                    </td>
                                    <!-- href=# alterei pois esqueci o porq q utilizei jquery :)-->
                                    <td><a href="?action=processarPagamento&id_pagamento=<?php echo $pag['id_pagamento']; ?>" title="Processar Pagamento" id="processar" rel="<?= $pag['valor_pagamento']; ?>"><span class="glyphicon glyphicon-thumbs-up" style="color: #777"></span></a></td>
                                    <td><a href="?action=cancelarPagamento&id_pagamento=<?php echo $pag['id_pagamento']; ?>" title="Cancelar Pagamento" id="cancelar" rel="<?= $pag['valor_pagamento']; ?>"><span class="glyphicon glyphicon-ban-circle" style="color: #777"></span></a></td>
                                    <td><?= $pag['id_status_pagamento'] == 'PG' ? '<span class="label label-success" id="pag_sucesso">pagamento efetuado</span>' : '<span class="label label-danger" id="pag_nao_processado">pagamento pendente </span>'; ?></td>                                                                                                
                                </tr>     
                            </tbody>
                            <?php
                        }
                    }
                    ?> 
                </table>
            </div>
            <br />            
            <div id="calculo" class="input-group">
                <span class="input-group-addon" id="basic-addon1">Salario:</span>
                <input type="text" class="form-control" disabled="true" placeholder="Salario" aria-describedby="basic-addon1" id="salario2" value="R$ <?= $dados['salario']; ?>">

                <span class="input-group-addon" id="basic-addon1">Total Processado:</span>
                <input type="text" class="form-control" disabled="true" placeholder="Total" aria-describedby="basic-addon1" id="total" value="R$ <?= $dados['total']; ?>">

                <span class="input-group-addon" id="basic-addon1">Sobra:</span>
                <input type="text" class="form-control" disabled="true" placeholder="Sobra" aria-describedby="basic-addon1" id="sobra" value="R$ <?= $dados['sobra']; ?>">
                <input type="hidden" name="" id="salario" value="R$ <?= $dados['salario']; ?>" />
            </div>
            <br />
            <div id="calculo" class="input-group">
                <span class="input-group-addon" id="basic-addon1">Subtotal:</span>
                <input type="text" class="form-control" disabled="true" placeholder="Subtotal" aria-describedby="basic-addon1" id="subtotal" value="R$ <?= $dados['subtotal']; ?>">
            </div>
        </div>
    </body>

</html>
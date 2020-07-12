<!DOCTYPE HTML>
<html lang="en-US">
    <head>
        <link rel="stylesheet" href="././css/bootstrap.min.css" />
        <link rel="stylesheet" href="././css/meu_estilo/estilo_meu.css" />
        <script type="text/javascript" src="././jquery/jquery-1.11.3.min.js"></script>
        <script type="text/javascript" src="././js/bootstrap.min.js"></script> 
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <div id="back">
            <a href="?action=inserirTipoPagamento" title="Novo Tipo Pagamento"><span class="glyphicon glyphicon-plus" ></span></a>
            </br><a href="?action=home" title="voltar"><span class="glyphicon glyphicon-arrow-left"></span></a>
        </div>

        <div class="panel-heading">Tipo de Pagamentos</h1></div>
    <table class="table">
        <tr>
            <th>Id</th>                                   
            <th>Descrição</th>                
            <th>Tipo de Despesa</th>
            <th colspan="2">Status</th>

        </tr>            
        <?php
        if ($dados['tipoPagamentos']) {
            foreach ($dados['tipoPagamentos'] as $tp) {
                //var_dump($tp);
                ?>
                <tr>
                    <td><?= $tp['id_tipo_pagamento']; ?></td>                        
                    <td><?= $tp['descricao'] ?></td>
                    <td><?= $tp['id_tipo_despesa'] ?></td>
                    <td>
                        <a href="?action=alterarTP&id_tipo_pagamento=<?php echo $tp['id_tipo_pagamento'] ?>"title="Alterar"><span class="glyphicon glyphicon-cog" style="color: #777"></span></a>
                    </td>
                    <td>
                        <a href="?action=excluirTP&id_tipo_pagamento=<?php echo $tp['id_tipo_pagamento'] ?>"title="Excluir"><span class="glyphicon glyphicon-remove" style="color: #777"></span></a>
                    </td>
                </tr>                                   
            <?php }
        } ?>            
    </table>
</body>
</html>
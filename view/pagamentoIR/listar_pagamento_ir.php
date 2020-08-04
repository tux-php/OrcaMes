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
        <?php
                include('./menu.php');
        ?>
        <div id="back">
            <a href="?action=inserirPagamentoIR" title="Novo PagamentoIR"><span class="glyphicon glyphicon-plus" ></span></a>
            </br><a href="?action=home" title="voltar"><span class="glyphicon glyphicon-arrow-left"></span></a>
        </div>

        <div class="panel-heading">Tipo de Pagamentos IR</h1></div>
    <table class="table">
        <tr>
            <th>Id</th>                                   
            <th>Descrição</th>                
            <th>Valor</th>
            <th>Imagem</th>
            <th>Data Processamento</th>
            <th colspan="2">Status</th>

        </tr>            
        <?php
        //var_dump($dados);
        if ($dados['listarIR']) {
            //die('oi');
            foreach ($dados['listarIR'] as $pag_ir) {
                
                //var_dump($tp);
                ?>
                <tr>
                    <td><?= $pag_ir['id_ir']; ?></td>                        
                    <td><?= $pag_ir['descricao'] ?></td>
                    <td><?= $pag_ir['valor'] ?></td>
                    <td><?= $pag_ir['imagem'] ?></td>
                    <td><?= $pag_ir['data_processamento'] ?></td>
                    <td>
                        <a href="?action=alterarTP&id_tipo_pagamento=<?php echo $tp['id_tipo_pagamento'] ?>"title="Alterar"><span class="glyphicon glyphicon-cog" style="color: #777"></span></a>
                    </td>
                    <td>
                        <a href="?action=excluirPagamentoIR&id_pagamento_ir=<?php echo $pag_ir['id_ir'] ?>"title="Excluir"><span class="glyphicon glyphicon-remove" style="color: #777"></span></a>
                    </td>
                </tr>                                   
            <?php }
        } ?>            
    </table>
</body>
</html>
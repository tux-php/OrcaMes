<!DOCTYPE HTML>
<html lang="en-US">
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="././css/bootstrap.min.css" />
        <link rel="stylesheet" href="././css/meu_estilo/estilo_meu.css" />
        <script type="text/javascript" src="././js/bootstrap.min.js"></script>
        <script type="text/javascript" src="././jquery/jquery-1.11.3.min.js"></script>
        <script type="text/javascript" src="././js/efeitos.js"></script>
        <title></title>
    </head>
    <div id="back">
            <a href="?action=inserirTD" title="Novo Tipo Despesa"><span class="glyphicon glyphicon-plus" ></span></a>
            </br><a href="?action=home" title="voltar"><span class="glyphicon glyphicon-arrow-left"></span></a>
        </div>
    <body>
        
        <div class="panel panel-default">
            
            <div class="panel-heading">Tipo de Despesa</div>
            <table  class="table">
                <tr id="listaTD">
                    <th>Id</th>
                    <th>Chave</th>
                    <th>Descrição</th>  
                    <th colspan="2">Ações</th>
                </tr>            
                <?php
                if ($dados['tipoDespesa']) {
                    foreach ($dados['tipoDespesa'] as $td) {                        
                        ?>
                        <tr>
                            <td><?php echo $td['id_tipo_despesa']; ?></td>
                            <td>
                                <a href="?action=tipoDespesa&id=<?php echo $td['id_tipo_despesa']; ?>">
                                    <?php echo $td['chave']; ?>
                                </a>
                            </td>
                            <td>
                                <a href="?action=tipoDespesa&id=<?php echo $td['id_tipo_despesa']; ?>">
                                    <?php echo $td['descricao']; ?>
                                </a>
                            </td>
                            <td>
                                <a href="?action=alterarTD&id_tipo_despesa=<?php echo $td['id_tipo_despesa'] ?>"title="Alterar"><span class="glyphicon glyphicon-cog" style="color: #777"></span></a>
                            </td>
                            <td>
                                <a href="?action=excluirTD&id_tipo_despesa=<?php echo $td['id_tipo_despesa'] ?>"title="Excluir"><span class="glyphicon glyphicon-remove" style="color: #777"></span></a>
                            </td>
                        </tr>                                   
                    <?php
                    }
                };
                ?>            
            </table>
        </div>        
    </body>
</html>
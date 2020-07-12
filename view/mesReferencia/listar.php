<!DOCTYPE HTML>
<html lang="en-US">
    <head>
        <link rel="stylesheet" href="././css/bootstrap.min.css" />
        <link rel="stylesheet" href="././css/meu_estilo/estilo_meu.css" />
        <script type="text/javascript" src="././jquery/jquery-1.11.3.min.js"></script>
        <script type="text/javascript" src="././js/bootstrap.min.js"></script> 
        <script type="text/javascript" src="././js/efeitos.js"></script>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <div id="back">
        <a href="?action=inserirMesRef"><span class="glyphicon glyphicon-plus" ></span></a>
        <br /><a href="?action=home"><span class="glyphicon glyphicon-arrow-left"></span></a>

    </div>
    <body>        
        <div class="panel panel-default">
            <div class="panel-heading">Meses/Ano</div>
            <table class="table">
                <tr>
                    <th>Id</th>
                    <th>Descrição</th>    
                    <th colspan="2">Ações</th>
                </tr>            
                <?php
                if ($dados['meses']) {
                    foreach ($dados['meses'] as $mes) {
                        ?>
                        <tr>
                            <td><?php echo $mes['id_mes_referencia']; ?></td>
                            <td>                            
                                <?php echo $mes['descricao']; ?>
                                </a>
                            </td>
                            <td>
                                <a href="?action=alterarMesRef&id_mes_referencia=<?php echo $mes['id_mes_referencia'] ?>">Alterar</a>
                            </td>
                            <td>
                                <a href="?action=excluirMesRef&id_mes_referencia=<?php echo $mes['id_mes_referencia'] ?>">Excluir</a>
                            </td>
                        </tr>                                   
                    <?php }
                };
                ?>            
            </table>
        </div>

    </body>
</html>
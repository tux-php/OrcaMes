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
        <div class="container-fluid">          

            <div class="panel-default">
                <div class="panel-heading">Lista de Pagamentos</div>
                <div id="back">                
                    <a href="?action=home" title="voltar"><span class="glyphicon glyphicon-arrow-left"></span></a>
                </div> 
                <h3>Pagamentos</h3>
                <table class="table">
                    <thead>
                        <tr class="list-group">
                            <th>#</th>                            
                            <th>Mes/Ano</th>                            
                            <th colspan="3">
                    <center>Ações</center>

                    </th>
                    </tr>      
                    </thead>
                    <?php
                    if ($dados['meses']) {
                        //var_dump($dados['pagamentos']);
                        foreach ($dados['meses'] as $mes) {
                            ?>
                            <tbody>
                                <tr class="mes_referencia">
                                    <td><?= $mes['id_mes_referencia']; ?></td>
                                    <td><?= $mes['chave']; ?></td>
                                    <td>
                                        <a href="?action=detalharPagamentoMes&id_mes_referencia=<?php echo $mes['id_mes_referencia'] ?>" title="Detalhar Pagamento"><span class="glyphicon glyphicon-edit" style="color: #777"></span></a>
                                    <td>
                                        <a href="?action=adicionarSalarioExtra&id_mes_referencia=<?php echo $mes['id_mes_referencia']; ?>" title="Adicionar Salário Extra"><span class="glyphicon glyphicon-plus" style="color: #777"></span></a>
                                    </td>                                    
                                    <td>
                                        <a href="?action=clonarPagamentoMes&id_mes_referencia=<?php echo $mes['id_mes_referencia']; ?>" title="Clonar Pagamento do Mês Anterior"><span class="glyphicon glyphicon-copy" style="color: #777"></span></a>
                                    </td>

                                    </td>

                                </tr>     
                            </tbody>
                            <?php
                        }
                    }                    
                    ?>      <a href="logout.php">Sair</a>
                </table>
                <?php
                
                ?>
            </div>


        </div>
    </body>

</html>
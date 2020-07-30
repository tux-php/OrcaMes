<!DOCTYPE HTML>
<html lang="en-US">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title></title>   
        <link rel="stylesheet" href="././css/bootstrap.min.css" />
        <!--<link rel="stylesheet" href="././css/meu_estilo/estilo_meu.css" />-->
        <script type="text/javascript" src="././jquery/jquery-1.11.3.min.js"></script>
        <script type="text/javascript" src="././js/bootstrap.min.js"></script>
        <script type="text/javascript" src="././js/efeitos.js"></script>
    </head>
    <body>
        <div class="container-fluid">

            <div class="panel panel-info">
                <div class="panel-heading text-uppercase"><strong>Lista Mês Pagamento</strong></div>
                <div id="back">                
                    <!--<a href="?action=home" title="voltar"><span class="glyphicon glyphicon-arrow-left"></span></a>-->
                    
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-condensed">
                        <thead>
                            <tr class="list-group">
                                <th scope="col">#</th>                            
                                <th scope="col">Mes/Ano</th>                            
                                <th scope="col" colspan="3" class="text-center text-uppercase">Ações</th>
                        </tr>      
                        </thead>
                        <tbody>
                        <?php
                        if ($dados['meses']) {
                            //var_dump($dados['pagamentos']);
                            foreach ($dados['meses'] as $ch=>$mes) {
                                ?>
                                    <tr class="mes_referencia">
                                        <th scope="row"><?=$ch;?></th>                                    
                                        <td><?= $mes['chave']; ?></td>
                                        <td class="text-center">
                                            <a href="?action=detalharPagamentoMes&id_mes_referencia=<?php echo $mes['id_mes_referencia'] ?>" title="Detalhar Pagamento"><span class="glyphicon glyphicon-edit" style="color: #777"></span></a>
                                        <td class="text-center">
                                            <a href="?action=adicionarSalarioExtra&id_mes_referencia=<?php echo $mes['id_mes_referencia']; ?>" title="Adicionar Salário Extra"><span class="glyphicon glyphicon-plus" style="color: #777"></span></a>
                                        </td>                                    
                                        <td class="text-center">
                                            <a href="?action=clonarPagamentoMes&id_mes_referencia=<?php echo $mes['id_mes_referencia']; ?>" title="Clonar Pagamento do Mês Anterior"><span class="glyphicon glyphicon-copy" style="color: #777"></span></a>
                                        </td>
                                        </td>

                                    </tr>     
                                
                                <?php
                            }
                        }                    
                        ?> 
                        </tbody>                        
                    </table>
                    
                </div>          
            </div>
            <div>            
            <a class="btn btn-primary text-uppercase text-center" href="?action=home" role="button" title="voltar">Voltar</span></a>
            <a class="btn btn-danger text-uppercase text-center" href="logout.php">Sair</a>
            </div>

        </div>
    </body>

</html>
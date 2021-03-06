<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="././css/bootstrap.min.css" />
        <link rel="stylesheet" href="././css/meu_estilo/estilo_meu.css" />   
        <script type="text/javascript" src="././jquery/jquery-1.11.3.min.js"></script>
        <script type="text/javascript" src="././js/bootstrap.min.js"></script>
         <meta charset="UTF-8" />
         <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Balanço Mensal</title>
    </head>
    <body>
    <?php
        include_once("menu.php");
    ?>
        <div class="container-fluid">

            <div class="panel panel-info">
                <div class="panel-heading text-uppercase"><strong>Lista Mês Pagamento</strong></div>
                <div id="back">                
                    <!--<a href="?action=home" title="voltar"><span class="glyphicon glyphicon-arrow-left"></span></a>-->
                    
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-condensed">
                        <thead>
                            <tr class="active">
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
                                        <td scope="row"><strong><?=$ch;?></strong></td>                                    
                                        <td class="text-uppercase"><?= $mes['chave']; ?></td>
                                        <td class="text-center col-sm-5">
                                            <a class="btn-sm btn-success text-uppercase text-center" style="margin:1%;" role="button" href="?action=detalharPagamentoMes&id_mes_referencia=<?php echo $mes['id_mes_referencia'] ?>" title="Detalhar Pagamento">Detalhar</a>                                        
                                            <a class="btn-sm btn-primary text-uppercase text-center" style="margin:1%;" role="button" href="?action=adicionarSalarioExtra&id_mes_referencia=<?php echo $mes['id_mes_referencia']; ?>" title="Adicionar Salário Extra">Adicionar R$</a>
                                            <a class="btn-sm btn-danger text-uppercase text-center" style="margin:1%;"  role="button" href="?action=clonarPagamentoMes&id_mes_referencia=<?php echo $mes['id_mes_referencia']; ?>" title="Clonar Pagamento do Mês Anterior">Clonar</a>                                        
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
            <div class="col-sm-offset-5 col-sm-10">                        
            <!--<a class="btn btn-danger text-uppercase btn-lg text-center" href="logout.php">Sair</a>-->
            </div>

        </div>
        
        
        
    </body>
    
</html>
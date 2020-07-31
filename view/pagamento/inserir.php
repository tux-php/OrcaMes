<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="././css/bootstrap.min.css" />
        <script type="text/javascript" src="././jquery/jquery-1.11.3.min.js"></script>
        <script type="text/javascript" src="././jquery/jquery.maskMoney.min.js"></script>
        <script type="text/javascript" src="././js/efeitos.js"></script>
        <script type="text/javascript" src="././js/bootstrap.min.js"></script>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Novo pagamento</title>
    </head>
    <body>
        <form class="form-horizontal" action="" method="post" >   
            <div class="container-fluid">
                <div class="panel-info">
                    <div class="panel-heading text-uppercase"><h4>Inserir Pagamento</h4></div>
                </div>
            </div>                      
            <div class="container-fluid">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <label class="control-label"> 
                            <select name="id_mes_referencia" id="" class="form-control">
                                <option value="<?= $dados['mes_ref']['id_mes_referencia']; ?>"><?= $dados['mes_ref']['descricao']; ?></option>
                            </select>  
                        </label><br />
                        <label class="control-label text-uppercase"> Tipo Pagamento:</label>                
                        <select name="id_tipo_pagamento" id="" class="form-control text-lowercase">Selecione Tipo Pagamento:

                            <?php
                            foreach ($dados['id_tipo_pagamento'] as $tp) {
                                ?>
                                <option value="<?php echo $tp['id_tipo_pagamento'] ?>" name="id_tipo_pagamento"><?= $tp['descricao']; ?></option>
                            <?php } ?>
                        </select>



                        <br /><br />

                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon1">Valor(R$):</span>

                            <input type="text" class="form-control" style="width: 180px;" name="valor_pagamento" id="valor_tp" />                   

                        </div>
                        <br /><br />

                    </div>
                    <div>
                        <input type="hidden" name="data_lancamento" value="<?= $dados['data_lancamento']; ?>" />
                        <input type="hidden" name="id_status_pagamento" value="<?= $dados['id_status_pagamento']; ?>" />                
                        <input type="hidden" name="ch_clone" value="<?= $dados['ch_clone']; ?>" />                
                    </div>
                </div>
                <div>
                    <a class="btn btn-primary text-uppercase text-center" role="button" href="index.php?action=detalharPagamentoMes&id_mes_referencia=<?= $dados['mes_ref']['id_mes_referencia']; ?>">Voltar</a>                
                    <input class="btn btn-success text-uppercase text-center" role="button" type="submit" value="Enviar" />
                </div>                
            </div>
        </form>
    </body>
</html>
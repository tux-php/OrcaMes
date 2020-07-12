<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="././css/bootstrap.min.css" />
        <script type="text/javascript" src="././jquery/jquery-1.11.3.min.js"></script>
        <script type="text/javascript" src="././jquery/jquery.maskMoney.min.js"></script>
        <script type="text/javascript" src="././js/efeitos.js"></script>
        <script type="text/javascript" src="././js/bootstrap.min.js"></script>
        <meta charset="UTF-8">
        <title>Novo pagamento</title>
    </head>
    <body>
        <form class="form-horizontal" action="" method="post" >             
            <h3> <span class="label label-default">Inserir Pagamento</span></h3>  
            <div class="container-fluid">
                <div class="panel panel-default">                
                    <div class="panel-body">
                        <label class="control-label"> 
                            <select name="id_mes_referencia" id="">
                                <option value="<?= $dados['mes_ref']['id_mes_referencia']; ?>"><?= $dados['mes_ref']['descricao']; ?></option>
                            </select>  
                        </label><br />
                        <label class="control-label"> Tipo Pagamento:</label>                
                        <select name="id_tipo_pagamento" id="" class="selectpicker control-group">Selecione Tipo Pagamento:

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
                <div class="control-group">
                    <center><input type="submit" value="Enviar" /></center>
                </div>
                <br /><a href="index.php?action=detalharPagamentoMes&id_mes_referencia=<?= $dados['mes_ref']['id_mes_referencia']; ?>">Voltar</a>
            </div>
        </form>
    </body>
</html>
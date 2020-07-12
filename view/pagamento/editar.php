<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="././css/bootstrap.min.css" />
        <script type="text/javascript" src="././jquery/jquery-1.11.3.min.js"></script>
        <script type="text/javascript" src="././jquery/jquery.maskMoney.min.js"></script>
        <script type="text/javascript" src="././js/efeitos.js"></script>
        <script type="text/javascript" src="././js/bootstrap.min.js"></script> 
        <meta charset="utf-8" />
    </head>

    <body>
        <h2>Alterar Pagamento</h2>
        <fieldset>
            <form class="form-horizontal" action="" method="post" >             
                <h3> <span class="label label-default">Alterar Pagamento</span></h3>  
                <div class="container-fluid">
                    <div class="panel panel-default">                
                        <div class="panel-body">
                            <label class="control-label"> Tipo Pagamento:</label>                
                            <select name="id_tipo_pagamento" id="" class="selectpicker control-group">Selecione Tipo Pagamento:

                                <?php
                                $id_tp = $dados['pagamento']['id_tipo_pagamento'];
                                foreach ($dados['tipo_pagamento'] as $tp) {
                                    if ($tp['id_tipo_pagamento'] == $id_tp) {
                                        ?>
                                        <option value="<?php echo $tp['id_tipo_pagamento'] ?>" name="id_tipo_pagamento"><?= $tp['descricao']; ?></option>
                                    <?php }
                                }
                                ?>
                            </select>



                            <br /><br />

                            <div class="input-group">
                                <span class="input-group-addon" id="basic-addon1">Valor(R$):</span>

                                <input type="text" class="form-control" style="width: 150px;" name="valor_pagamento" id="valor_tp_corrigir" value="<?= $dados['pagamento']['valor_pagamento']; ?>" />                   

                            </div>
                            <br /><br />
                            <div class="input-group">
                                <label class="control-label"> Usuário: </label>


                                <select name="id_usuario" id="" class="selectpicker control-group">
                                    <?php
                                    $id_user = $dados['pagamento']['id_usuario'];
                                    foreach ($dados['usuario'] as $user) {
                                        if ($user['id_usuario'] == $id_user) {
                                            ?>                                
                                            <option value="<?php echo $user['id_usuario'] ?>"><?= $user['nome']; ?></option>
    <?php }
} ?>
                                </select>

                            </div>
                        </div>
                        <div>
                            <input type="hidden" name="data_lancamento" value="<?= $dados['data_lancamento']; ?>" />
                            <input type="hidden" name="id_status_pagamento" value="<?= $dados['id_status_pagamento']; ?>" />                
                        </div>
                    </div>
                    <div class="control-group">
                        <center><input type="submit" value="Enviar" /></center>                        
                    </div>                    
                </div>
            </form>
        </fieldset>
        <br /><a href="?action=detalharPagamentoMes&id_mes_referencia=<?=$dados['id_mes_ref']?>?>">Voltar</a>
    </body>
</html>
<!DOCTYPE html>
<html>
    <head>
    <link rel="stylesheet" href="././css/bootstrap.min.css" />
      <script type="text/javascript" src="././jquery/jquery-1.11.3.min.js"></script>
      <!--<script type="text/javascript" src="././js/bootstrap.min.js"></script> -->
      <script type="text/javascript" src="././jquery/jquery.maskMoney.min.js"></script>
      <script type="text/javascript" src="././js/efeitos.js"></script>
      <meta charset="utf-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>

    <body>
    <form class="form-horizontal" action="" method="post" >
        <div class="container-fluid">
            <div class="panel-info">
               <div class="panel-heading text-uppercase">
                  <h4>Alterar Tipo Pagamento</h4>
               </div>
            </div>
         </div>

         <div class="container-fluid">
            <div class="panel panel-default">
               <div class="panel-body">

               <div class="form-group">
                     <label for="valor"  class="col-sm-2 control-label">Descrição:</label>
                     <div class="col-sm-3">                        
                        <input type="text" class="form-control text-uppercase" name="descricao"  required="" id="descricao"  value="<?php echo $dados['tp']['descricao']; ?>">
                     </div>
                </div>

                <div class="form-group">
                     <label for="id_tipo_despesa" class="col-sm-2 control-label">Tipo Pagamento:</label>
                     <div class="col-sm-5">
                        <select class="form-control text-uppercase" name="id_tipo_despesa" id="id_tipo_despesa">
                           <?php 
                           
                           $id_td = $dados['tp']['id_tipo_despesa'];
                           //print_r($id_op);
                           
                           foreach ($dados['td'] as $tipo) {
                               
                                   ?>              
                           <option value="<?= $tipo['id_tipo_despesa'];?>"<?=($tipo['id_tipo_despesa']==$id_td)?'selected':''?>
                                     name="id_tipo_despesa"><?= $tipo['descricao']; ?>
                           </option>
                           <?php } ?>
                        </select>
                     </div>
                  </div>
               </div>
            </div>
            <div class="col-sm-offset-5 col-sm-10">
            <a class="btn btn-primary text-uppercase text-center" role="button" href="?action=listaTipoPagamento">Voltar</a>                
            <input class="btn btn-success text-uppercase text-center" role="button" type="submit" value="Salvar" />
         </div>
        </div>
    </form>
    </body>
</html>
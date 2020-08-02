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
      <title>Salário Extra</title>
   </head>
   <body>
      <form class="form-horizontal" action="" method="post">
         <div class="container-fluid">
            <div class="panel-info">
               <div class="panel-heading text-uppercase">
                  <h4>Inserir Valor Extra do Mês</h4>
               </div>
            </div>
         </div>
         <div class="container-fluid">
            <div class="panel panel-default">
               <div class="panel-body">
                  <div class="form-group">
                     <label for="mes"  class="col-sm-2 control-label">Mês/Ano:</label>
                     <div class="col-sm-3">
                        <select name="mes" id=""class="form-control">
                           <?php                              
                              foreach($dados['meses'] as $obj){
                                  if ($user['id_usuario'] == $id_user) { ?>                                
                           <option value="<?php echo $obj['id_mes_referencia'] ?>"><?= $obj['descricao']; ?></option>
                           <?php }} ?>
                        </select>
                     </div>
                  </div>
                  <div class="form-group">
                     <label for="salario_extra" class="col-sm-2 control-label">Remuneração Extra:</label>
                     <div class="col-sm-4">
                        <input type="text" class="form-control" required="" name="salario_extra" id="salario_extra" placeholder="R$..">
                     </div>
                  </div>
                  <div class="col-sm-offset-5 col-sm-10">
                     <a class="btn btn-primary text-uppercase text-center" role="button" href="?action=listarPagamentoMes">Voltar</a>                
                     <input class="btn btn-success text-uppercase text-center" role="button" type="submit" value="Salvar" />
                  </div>
               </div>
            </div>
         </div>
      </form>
   </body>
</html>
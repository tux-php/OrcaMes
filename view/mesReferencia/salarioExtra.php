<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="././css/bootstrap.min.css" />
        <script type="text/javascript" src="././jquery/jquery-1.11.3.min.js"></script>
        <script type="text/javascript" src="././js/bootstrap.min.js"></script> 
        <meta charset="utf-8" />
    </head>
    
    <body>
        <h2>Inserir Mês/Ano</h2>
        <fieldset>
            <form action="" method="POST">    
                <?php
                foreach($dados['meses'] as $obj){                    
                ?>
                Mês: 
                <input type="text" name="mes" id="" value="" placeholder="<?php echo $obj['descricao']?>" disabled="true" />
                </br></br>
                Valor Extra: 
                <input type="text" name="salario_extra" id="salario_extra" value="<?php echo $obj['salario_extra']?>" /><br /></br>
                <?php } ?>
                <input type="submit" value="Adicionar" />
                
            </form>
        </fieldset>
        <br /><a href="?action=listarPagamentoMes">Voltar</a>
    </body>
</html>
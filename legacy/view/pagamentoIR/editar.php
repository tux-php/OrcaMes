<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="././css/bootstrap.min.css" />
        <script type="text/javascript" src="././jquery/jquery-1.11.3.min.js"></script>
        <script type="text/javascript" src="././js/bootstrap.min.js"></script> 
        <meta charset="utf-8" />
    </head>

    <body>
        <h2>Alterar Tipo Pagamento</h2>
        <fieldset>
            <form action="" method="POST">
                Descrição: <br />                
                <input type="text" name="descricao" id="" value="<?php echo $dados['tp']['descricao']; ?>" /><br />
                
                    <?php
                   $id_td = $dados['tp']['id_tipo_despesa'];
                   
                ?>
                
                Tipo Pagamento: <br />
                <select name="id_tipo_despesa">                       
                    <?php                    
                    foreach ($dados['td'] as $tipo) {
                        ?>
                    
                    <option selected="<?php if($id_td == $tipo['id_tipo_despesa']){echo $tipo['id_tipo_despesa']; }?>"value="<?php echo $tipo['id_tipo_despesa']?>">
                        
                        <?= $tipo['descricao']; ?>
                        </option>
                    <?php }?>
                </select>
                <input type="submit" value="Salvar" />

            </form>
        </fieldset>
        <br /><a href="?action=listaTipoPagamento">Voltar</a>
    </body>
</html>
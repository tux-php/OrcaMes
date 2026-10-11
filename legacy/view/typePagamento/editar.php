<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="././css/bootstrap.min.css" />
        <script type="text/javascript" src="././jquery/jquery-1.11.3.min.js"></script>
        <script type="text/javascript" src="././js/bootstrap.min.js"></script> 
        <meta charset="utf-8" />
    </head>
    
    <body>
        <h2>Alterar Status Pagamento</h2>
        <fieldset>
            <form action="" method="POST">
                Chave: <br />
                <input type="text" name="chave" id="" value="<?php echo $dados['chave'];?>" /><br />
                Descrição: <br />
                <input type="text" name="descricao" id="" value="<?php echo $dados['descricao'];?>" /><br />
                <input type="submit" value="Salvar" />
                
            </form>
        </fieldset>
        <br /><a href="?action=home">Voltar</a>
    </body>
</html>
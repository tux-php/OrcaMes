<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
    </head>
    
    <body>
        <h2>Alterar Tipo Despesa</h2>
        <fieldset>
            <form action="" method="POST">
                Chave: <br />
                <input type="text" name="chave" id="" value="<?php echo $dados['chave'];?>" /><br />
                Descrição: <br />
                <input type="text" name="descricao" id="" value="<?php echo $dados['descricao'];?>" /><br />
                <input type="submit" value="Salvar" />
                
            </form>
        </fieldset>
        <br /><a href="index.php?action=listarTipoDespesa">Voltar</a>
    </body>
</html>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="././css/meu_estilo/estilo_meu.css" />
    </head>

    <body>
        <h2>Novo Tipo de Despesa</h2>
        <fieldset>
            <legend>Cadastro</legend>
            <form action="" method="POST">
                <ol>
                    <li>
                        <label for="chave">Chave:</label>
                        <input type="text" name="chave" id="" />
                    </li>
                    <li>
                        <label for="Descricao">Descrição:</label>
                        <input type="text" name="descricao" id="" /><br />
                    </li>
                    <li>
                        <input type="submit" value="Salvar" />
                    </li>
                </ol>
            </form>
        </fieldset>
        <br /><a href="index.php?action=listarTipoDespesa">Voltar</a>
    </body>
</html>
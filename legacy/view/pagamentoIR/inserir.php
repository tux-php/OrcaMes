
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="././css/bootstrap.min.css" />    
        <script type="text/javascript" src="././jquery/jquery-1.11.3.min.js"></script>
        <script type="text/javascript" src="././js/bootstrap.min.js"></script>        
        <link rel="stylesheet" href="././css/meu_estilo/estilo_meu.css" />
        <meta charset="utf-8" />
    </head>

    <body>
        <?php
                include('./menu.php');
        ?>
        <h2>Novo Pagamento IR</h2>
        <form action="" method="POST">     
            <fieldset>
                <ol>
                    <li>
                        <label for="descricao">Descrição:</label>
                        <input type="text" name="val_desc" id="val_desc" />
                    </li>
                    <li>
                        <label for="valor">Valor:</label>
                        <input type="text" name="valor" id="" />                    
                    </li>
                    <li>                        
                        <input type="file" name="img_ir" id="arquivo" />
                    </li>
                    <li>
                        <label for="tipo_pagamento">Tipo de Pagamento:</label>
                        <select name="id_tipo_despesa" id="">
                            <?php foreach ($dados['tipo_despesa'] as $td) { ?>
                                <option value="<?= $td['id_tipo_despesa'] ?>"><?= $td['descricao'] ?></option>
                            <?php } ?>
                        </select>
                    </li>
                </ol>





                <input type="submit" value="Salvar" />
            </fieldset>
        </form>

        <br /><a href="?action=listaTipoPagamento">Voltar</a>
    </body>
</html>
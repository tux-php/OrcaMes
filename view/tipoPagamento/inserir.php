<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="././css/bootstrap.min.css" />    
    <script type="text/javascript" src="././jquery/jquery-1.11.3.min.js"></script>
    <script type="text/javascript" src="././js/bootstrap.min.js"></script>        
    <meta charset="utf-8" />
</head>

<body>
    <h2>Novo Tipo Pagamento</h2>
    <fieldset>
        <form action="" method="POST"> 
            <div class="form-group">
                <label for="descricao">Descrição:</label>
                <input type="text" name="descricao" id="" class="form-control"/><br />     
                <label for="tipo_despesa">Tipo de Despesa:</label>
                <select name="id_tipo_despesa" id="" class="form-control form-control-sm">                    
                    <?php foreach ($dados['tipo_despesa'] as $td) { ?>
                        <option value="<?= $td['id_tipo_despesa'] ?>"><?= $td['descricao'] ?></option>
                    <?php } ?>
                </select>
            </div>
            <input type="submit" class="btn btn-primary" value="Salvar" />
        </form>
    </fieldset>
    <br /><a href="?action=listaTipoPagamento">Voltar</a>
</body>
</html>
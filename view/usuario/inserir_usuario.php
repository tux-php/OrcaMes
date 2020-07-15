<!DOCTYPE html>
<html>
    <head>
        <!--<link rel="stylesheet" href="././css/bootstrap.min.css" />-->
        <link rel="stylesheet" href="././css/meu_estilo/usuario.css" />
        <script type="text/javascript" src="././jquery/jquery-1.11.3.min.js"></script>
        <!--<script type="text/javascript" src="././js/bootstrap.min.js"></script> -->
        <script type="text/javascript" src="././jquery/jquery.maskMoney.min.js"></script>
        <script type="text/javascript" src="././js/efeitos.js"></script>
        <meta charset="utf-8" />
    </head>
    <body>
        <form action="" method="POST">

            <main>
                <fieldset>
                    <div class="conteudo-principal">
                        <h2>Novo Usuário</h2>

                        <label for="nome">Nome:</label>
                        <input type="text" name="nome" id="nome" required autofocus="" placeholder="nome"/><br/>
                        <label for="sobrenome">Sobrenome:</label>
                        <input type="text" id="sobrenome" required="" name="sobrenome" placeholder="sobrenome" /><br/>
                        <label for="id_orgao_pagador">Instituição Patrocinadora:</label>
                        <select name="id_orgao_pagador" id="id_orgao_pagador">
                            <?php foreach ($dados['OrgaoPagador'] as $fp) { ?>                    
                                <option value="<?= $fp['id_orgao_pagador'] ?>"
                                        name="id_orgao_pagador"><?= $fp['descricao'] ?>
                                </option>
                            <?php } ?>
                        </select><br/>
                        <label for="salario">Remuneração:</label>
                        <input type="text" name="salario" required="" id="salario" placeholder="R$.." /><br/>
                        <label for="email">E-mail:</label>
                        <input type="email" name="email" required="" placeholder="nome@gmail.com" /><br/>
                        <label for="senha">Senha:</label>
                        <input type="password" name="senha" required="" id="senha" placeholder="@#$%*()..." /><br/>
                        <label for="id_status_usuario">Status:</label>
                        <select name="id_status_usuario" id="status_usuario">
                            <?php
                            foreach ($dados['StatusUsuario'] as $su) {
                                ?>
                                <option value="<?= $su['id_status_usuario']; ?>" 
                                        name="id_status_usuario"><?= $su["descricao"] ?>
                                </option>
                            <?php } ?>
                        </select><br />
                        <input type="submit" id="botao-salvar" value="SALVAR" />
                    </div>
                </fieldset>
            </main>
            <br /><a href="?action=home">Voltar</a>
        </form>
        
    </body>
</html>
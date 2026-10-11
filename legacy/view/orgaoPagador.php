<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="././css/bootstrap.min.css" />
        <script type="text/javascript" src="././jquery/jquery-1.11.3.min.js"></script>
        <script type="text/javascript" src="././js/bootstrap.min.js"></script> 
        <meta charset="utf-8" />
        <title>Instituição Pagadora</title>
    </head>
    <table>
        <h1><?php echo $dados['descricao'];?></h1>
        <p>
            ID:<?php echo $dados['id_orgao_pagador'];?><br />
            DESCRIÇÃO:<?php echo $dados['descricao'];?>
        </p>
    </table>
</html>
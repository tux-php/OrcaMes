<?php

function appLoad($classe) {
    $arquivo = "app/$classe.php";
    if (file_exists($arquivo)) {
        require_once($arquivo);
    }
}

function libLoad($classe) {
    $arquivo = "lib/$classe.php";
    if (file_exists($arquivo)) {
        require_once ($arquivo);
    }
}

function modLoad($classe) {
    $arquivo = "app/model/$classe.php";
    if (file_exists($arquivo)) {
        require_once ($arquivo);
    }
}

spl_autoload_register('appLoad');
spl_autoload_register('libLoad');
spl_autoload_register('modLoad');

new Controle();

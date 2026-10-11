<?php

/*
 * Classe utilizada para carregar o html. O método load é utilizado para fazer 
 * a ligaçção entre o Controle e a View.
 */

class View {

    public function load($template, $dados = null) {
        require("view/$template.php");
    }

}

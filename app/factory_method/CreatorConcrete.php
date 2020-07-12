<?php

class CreatorConcrete extends ACreator {

    private $obj;

    protected function factoryMethod(\Modelo $tipo_pagamento) {
        $this->obj = $tipo_pagamento;
        return $this->obj;
    }

}

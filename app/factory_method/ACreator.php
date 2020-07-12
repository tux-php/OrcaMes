<?php
abstract class ACreator {

    protected abstract function factoryMethod(Modelo $classe);

    public function iniciarFabrica($classeNow) {
        $objNow = $this->factoryMethod($classeNow);
        return $objNow;
    }

}

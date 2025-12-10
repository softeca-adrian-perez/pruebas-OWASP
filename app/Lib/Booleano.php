<?php

class Booleano{

    public static function toString($booleano){
        return $booleano ? __t('General.Yes') : __t('General.No');
    }

}
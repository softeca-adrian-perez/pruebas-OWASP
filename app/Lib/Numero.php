<?php

class Numero{

    const _DEFAULT_FORMATO_VISTA = '';
    const _DEFAULT_FORMATO_BD = null;
    const _DEFAULT_FORMATO = null;

    const _DEFAULT_NUM_DECIMAL = 2;

    public static function toFormatoVista($numero, $num_decimales =  self::_DEFAULT_NUM_DECIMAL){
        if(self::esNumero($numero)){
            return number_format($numero , $num_decimales, ',', '.');
        }else{
            return self::_DEFAULT_FORMATO_VISTA;
        }
    }

    public static function toFormatoBd($numero){
        if(self::esDecimalEs($numero)){
            $numero = str_replace(',', '.', str_replace('.', '', $numero));
            return $numero;
        }else{
            return self::_DEFAULT_FORMATO_BD;
        }
    }

    public static function esNumero($numero){
        return is_numeric($numero);
    }

    public static function esEntero($numero){
        return is_numeric($numero) && !is_float($numero);
    }

    public static function esNatural($numero){
        return (self::esEntero($numero) && $numero > 0);
    }

    public static function esDecimal($numero){
        return is_float($numero);
    }

    public static function esDecimalEs($numero, $num_decimales = self::_DEFAULT_NUM_DECIMAL){
        return ((preg_match ('/^(-){0,1}([0-9]+)(.[0-9][0-9][0-9])*([,][0-9]{1,'.$num_decimales.'}){0,1}$/', $numero) == 1) || ($numero === "") || ($numero === null));
    }

    public static function redondear($numero, $precision = 0){
        if(self::esNumero($numero)){
            return round($numero, $precision);
        }else{
            return self::_DEFAULT_FORMATO;
        }
    }

    public static function redondearArriba($numero){
        if(self::esNumero($numero)){
            return ceil($numero);
        }else{
            return self::_DEFAULT_FORMATO;
        }
    }

    public static function redondearAbajo($numero){
        if(self::esNumero($numero)){
            return floor($numero);
        }else{
            return self::_DEFAULT_FORMATO;
        }
    }
	public static function validateSessionNumeric($data) {
		if (self::esNumero($data)) {
			return $data;
		}
		return null;
	}

    public static function convert_unit_to_km($numero, $typeUnit){
        switch ($typeUnit) {
            case ConstantsDistanceUnit::MILES:
                $result = $numero * 1.60934;
                break;
            default:
                $result = $numero;
        }
        return $result;
    }
}

<?php

App::uses('Numero', 'Lib');

/**
 * Manejo transparente de numeros
 */
class DecimalBehavior extends ModelBehavior{
    /**
     * Array bidimensional de campos de tipo decimal agrupados por nombre de modelo
     *
     * @var array
     */
    protected $camposDecimal = array();

    /**
     * Número de decimales por defecto
     *
     * @var array
     */
    protected $numeroDecimales = 2;
    
    /**
     * Obtenemos y guardarmos los nombres de los campos de tipo decimal.
     * Este método se llama una vez por cada modelo involucrado (en un orden aparentemente arbitrario).
     *
     * @param Model $model Model using this behavior
     * @param array $config Configuration settings for $model
     * @return void
     */
    public function setup(Model $model, $config = array()){
        if( $model->useTable && !isset($this->camposDecimal[$model->name]) ){
            if(!isset($config['fields'])){
                $this->camposDecimal[$model->name] = $this->_obtenerCamposDecimal($model);
            }else{
                $this->camposDecimal[$model->name] = $config['fields'];
            }
        }
    }


    /**
     * Convertimos los decimales a formato para mostrar tras obtenerlas de la base de datos.
     * Este método sólo se llama para el modelo principal así que también procesamos los modelos asociados.
     *
     * @param Model $model Model using this behavior
     * @param mixed $results The results of the find operation
     * @param boolean $primary Whether this model is being queried directly (vs. being queried as an association)
     * @return mixed An array value will replace the value of $results - any other value will be ignored.
     */
    public function afterFind(Model $Model, $results, $primary=false){
        $this->_afterFindModelo($results);
        return $results;
    }


    /**
     * Convertimos los decimales a formato para la base de datos justo antes de guardar.
     * Este método se llama primero para cada modelo asociado y después para el modelo principal (con la totalidad
     * de los datos y en formato original) así que procesamos siempre los modelos asociados.
     *
     * @param Model $model Model using this behavior
     * @return mixed False if the operation should abort. Any other result will continue.
     */
    public function beforeSave(Model $Model, $options = array()){
        $this->_beforeSaveModelo($Model->data, $Model->name);
        return true;
    }


    /**
     * Devuelve los campos del modelo que contienen decimales
     *
     * @param Model $modelo
     * @return string[]
     */
    protected function _obtenerCamposDecimal(Model $modelo){
        $camposDecimal = array();
        foreach($modelo->getColumnTypes() as $campo => $tipo){
            if($tipo=='float'){
                $camposDecimal[$campo] = $this->numeroDecimales;
            }
        }
        return $camposDecimal;
    }


    /**
     * Método recursivo para encontrar y convertir los decimales de los datos que acabamos de obtener de la base de datos
     *
     * @param array $datos Array de datos con, posiblemente, otros arrays de datos anidados
     * @param string $nombreModelo Nombre del modelo a que pertenecen los datos de nivel superior
     */
    protected function _afterFindModelo(array &$datos, $nombreModelo=null){
        foreach($datos as $clave => &$valor){
            if( is_array($valor) ){
                $this->_afterFindModelo($valor, is_numeric($clave) ? $nombreModelo : $clave);
            }else{
                if( !is_null($nombreModelo) && !empty($this->camposDecimal[$nombreModelo]) ){
                    if( array_key_exists($clave, $this->camposDecimal[$nombreModelo]) ){
                        $valor = Numero::toFormatoVista($valor, $this->camposDecimal[$nombreModelo][$clave]);
                    }
                }
            }
            unset($valor);
        }
    }


    /**
     * Método recursivo para encontrar y convertir los decimales de los datos que vamos a guardar en base de datos
     *
     * @param array $datos Array de datos con, posiblemente, otros arrays de datos anidados
     * @param string $nombreModelo Nombre del modelo a que pertenecen los datos de nivel superior
     */
    protected function _beforeSaveModelo(array &$datos, $nombreModelo=null){
        foreach($datos as $clave => &$valor){
            if( is_array($valor) ){
                $this->_beforeSaveModelo($valor, is_numeric($clave) ? $nombreModelo : $clave);
            }else{
                if( !is_null($nombreModelo) && !empty($this->camposDecimal[$nombreModelo]) ){
                    if( array_key_exists($clave, $this->camposDecimal[$nombreModelo]) ){
                        $valor = Numero::toFormatoBd($valor);
                    }
                }
            }
            unset($valor);
        }
    }
}

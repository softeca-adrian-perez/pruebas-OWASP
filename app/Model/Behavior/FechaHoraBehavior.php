<?php

App::uses('Fecha', 'Lib');

/**
 * Manejo transparente de numeros
 */
class FechaHoraBehavior extends ModelBehavior{
    /**
     * Array bidimensional de campos de tipo fecha agrupados por nombre de modelo
     *
     * @var array
     */
    protected $camposFecha = array();

    /**
     * Obtenemos y guardarmos los nombres de los campos de tipo fecha.
     * Este método se llama una vez por cada modelo involucrado (en un orden aparentemente arbitrario).
     *
     * @param Model $model Model using this behavior
     * @param array $config Configuration settings for $model
     * @return void
     */
    public function setup(Model $model, $config = array()){
        if( $model->useTable && !isset($this->camposFecha[$model->name]) ){
            if(!isset($config['fields'])){
                $this->camposFecha[$model->name] = $this->_obtenerCamposFecha($model);
            }else{
                $this->camposFecha[$model->name] = $config['fields'];
            }
        }
    }


    /**
     * Convertimos los fechas a formato para mostrar tras obtenerlas de la base de datos.
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
     * Convertimos los fechas a formato para la base de datos justo antes de guardar.
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
     * Devuelve los campos del modelo que contienen fechas
     *
     * @param Model $modelo
     * @return string[]
     */
    protected function _obtenerCamposFecha(Model $modelo){
        $camposFecha = array();
        foreach($modelo->getColumnTypes() as $campo => $tipo){
            if($tipo == 'datetime'){
                $camposFecha[$campo] = $campo;
            }
        }
        return $camposFecha;
    }


    /**
     * Método recursivo para encontrar y convertir los fechas de los datos que acabamos de obtener de la base de datos
     *
     * @param array $datos Array de datos con, posiblemente, otros arrays de datos anidados
     * @param string $nombreModelo Nombre del modelo a que pertenecen los datos de nivel superior
     */
    protected function _afterFindModelo(array &$datos, $nombreModelo=null){
        foreach($datos as $clave => &$valor){
            if( is_array($valor) ){
                $this->_afterFindModelo($valor, !is_array($valor) ? $nombreModelo : $clave);
            }else{
                if( !is_null($nombreModelo) && !empty($this->camposFecha[$nombreModelo]) ){
                    if( in_array($clave, $this->camposFecha[$nombreModelo]) ){
                        $valor = Fecha::toFormatoVistaFechaHora($valor);
                    }
                }
            }
            unset($valor);
        }
    }


    /**
     * Método recursivo para encontrar y convertir los fechas de los datos que vamos a guardar en base de datos
     *
     * @param array $datos Array de datos con, posiblemente, otros arrays de datos anidados
     * @param string $nombreModelo Nombre del modelo a que pertenecen los datos de nivel superior
     */
    protected function _beforeSaveModelo(array &$datos, $nombreModelo=null){
        foreach($datos as $clave => &$valor){
            if( is_array($valor) ){
                $this->_beforeSaveModelo($valor, !is_array($valor) ? $nombreModelo : $clave);
            }else{
                if( !is_null($nombreModelo) && !empty($this->camposFecha[$nombreModelo]) ){
                    if( in_array($clave, $this->camposFecha[$nombreModelo]) ){
                        $valor = Fecha::toFormatoBdFechaHora($valor);
                    }
                }
            }
            unset($valor);
        }
    }
}

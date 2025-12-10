<?php
/**
 * Application model for CakePHP.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model {

    public $actsAs = array(
        'Containable',
    );

    public $recursive = -1;

    public function guardar($entidad, $fields, $validate = true){
        return $this->save(
            $entidad,
            array(
                'validate' => $validate,
                'fieldList' => $fields,
            )
        );
    }

    public function guardarVarios($entidad, $fields, $validate = true){

        if(!empty($entidad)){
            return $this->saveAll(
                $entidad,
                array(
                    'validate' => $validate,
                    'fieldList' => $fields,
                )
            );
        }
        else{
            return true;
        }

    }

    public function eliminar($id){
        return $this->delete($id);
    }

    public function beforeValidate( $options = array() ){
        if($this->name != 'Email' && $this->name != 'Shortcut'){
            $columns = array_keys($this->schema());
            $validator = $this->validator();
            foreach($columns as $field){
                $validator->add(
                    $field,
                    'special',
                    array(
                        'rule' => 'validateCheckSpecialCharacters',
                        'allowEmpty' => true,
                        'message' => __t('Validation.Special_chars_not_allowed'),
                    )
                );
            }
        }
    }

    public function customAfterFindDecimal($data, $path, $num_decimales = 2){
        $item = Hash::map($data, $path, function($valor) use ($num_decimales) { return Numero::toFormatoVista($valor, $num_decimales);});
        foreach($item as $key => $val){
            $data = Hash::insert($data, str_replace('{n}', $key, $path ), $val);
        }
        return $data;
    }

    public function customAfterFindFecha($data, $path){
        $item = Hash::map($data, $path, function($valor) { return Fecha::toFormatoVista($valor);});
        foreach($item as $key => $val){
            $data = Hash::insert($data, str_replace('{n}', $key, $path ), $val);
        }
        return $data;
    }

    public function validateDni($check){
        $value = array_values($check);
        $dni = $value[0];

        return Texto::esNifCif($dni);
    }

    public function validateCheckSpecialCharacters($check){
        $value = array_values($check);
        $value = $value[0];
        return !is_array($check) ? !preg_match('/(\{|\}|>|<|~|\\|\º|%|\$|\#|\*)|\[|\]/', $value) : true;  
    }

    public function validateDecimalEs($check, $decimales){
        $value = array_values($check);
        $numero = $value[0];
        return Numero::esDecimalEs($numero, $decimales);
    }

    public function validateRangoDecimalEs($check, $min, $max){
        $value = array_values($check);
        $value = Numero::toFormatoBd($value[0]);
        return ($value >= $min && $value <= $max);
    }

    public function validateUnique($check, $fields) {
        if (!is_array($fields)) {
            $fields = array($fields);
        }
        $tmp = array();
        foreach($fields as $key) {
            $tmp[$key] = $this->data[$this->name][$key];
        }
        if (!empty($this->data[$this->name][$this->primaryKey])) {
            $tmp[$this->primaryKey] = "<>".$this->data[$this->name][$this->primaryKey];
        }

        return $this->isUnique($tmp, false);
    }

    public function validateCamposIguales($check, $field){
        $value = array_values($check);
        $value = $value[0];

        return $value === $this->data[$this->name][$field];
    }

    // Validate password:
    // ^ represents the initial character of the string.
    // (?=.*\d) represents that a digit must appear at least once.
    // (?=.*[a-z]) represents that the lowercase alphabet must appear at least once.
    // (?=.*[A-Z]) represents an uppercase alphabet that must appear at least once.
    // (?=.*[#?!@$%^&*-] represents a special character that must appear at least once.
    // (?=\\S+$) no blank spaces are allowed in the entire string.
    // .{10,50} represents at least 10 characters and at most 50 characters.
    // $ represents the end of the string.

    public function validatePassword($check){
        $key = array_keys($check);
        $password = $this->data[$this->alias][$key[0]];
        if(!preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[#?!@$%^&*-])(?=\\S+$).{10,50}$/', $password)){
            return false;
        }
        return true;
    }

    public function checkUpload($data, $required = false) {
        $data = array_shift($data);
        if(!$required && $data['error'] == 4) {
            return true;
        }
        if($required && $data['error'] !== 0) {
            return false;
        }
        return true;
    }

    // [PERMISOS]
    public function insertar_datos_de_prueba(){

        $this->Permiso = ClassRegistry::init('Permission');

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 1)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 2)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 3)';
        $this->Permiso->query($sql);

        // $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 4)';
        // $this->Permiso->query($sql);

        // $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 5)';
        // $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 6)';
        $this->Permiso->query($sql);

        // $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 7)';
        // $this->Permiso->query($sql);

        // $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 8)';
        // $this->Permiso->query($sql);

        // $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 9)';
        // $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 10)';
        $this->Permiso->query($sql);

        // $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 11)';
        // $this->Permiso->query($sql);

        // $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 12)';
        // $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 13)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 14)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 15)';
        $this->Permiso->query($sql);

        // $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 16)';
        // $this->Permiso->query($sql);

        // $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 17)';
        // $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 18)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 19)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 20)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 21)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 22)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 23)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 24)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 25)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 26)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 27)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 28)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 29)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 30)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 31)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 32)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 33)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 34)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 35)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 36)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 37)';
        $this->Permiso->query($sql);

        // $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 38)';
        // $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 39)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 40)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 41)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 42)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 43)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 44)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 45)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 46)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 47)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 48)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 49)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 50)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 51)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 52)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 53)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 54)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 55)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 56)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 57)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 58)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 59)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 60)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (1, 61)';
        $this->Permiso->query($sql);

        //--------------------------------------------------------------------------------------------------------------

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (2, 1)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (2, 2)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (2, 3)';
        $this->Permiso->query($sql);

        // $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (2, 4)';
        // $this->Permiso->query($sql);

        // $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (2, 5)';
        // $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (2, 6)';
        $this->Permiso->query($sql);

        // $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (2, 7)';
        // $this->Permiso->query($sql);

        // $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (2, 8)';
        // $this->Permiso->query($sql);

        // $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (2, 9)';
        // $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (2, 10)';
        $this->Permiso->query($sql);

        // $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (2, 11)';
        // $this->Permiso->query($sql);

        // $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (2, 12)';
        // $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (2, 13)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (2, 14)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (2, 15)';
        $this->Permiso->query($sql);

        // $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (2, 16)';
        // $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (2, 25)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (2, 26)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (2, 28)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (2, 29)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (2, 31)';
        $this->Permiso->query($sql);

        //--------------------------------------------------------------------------------------------------------------

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (3, 1)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (3, 2)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (3, 3)';
        $this->Permiso->query($sql);

        // $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (3, 4)';
        // $this->Permiso->query($sql);

        // $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (3, 5)';
        // $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (3, 6)';
        $this->Permiso->query($sql);

        // $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (3, 7)';
        // $this->Permiso->query($sql);

        // $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (3, 8)';
        // $this->Permiso->query($sql);

        // $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (3, 9)';
        // $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (3, 10)';
        $this->Permiso->query($sql);

        // $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (3, 11)';
        // $this->Permiso->query($sql);

        // $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (3, 12)';
        // $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (3, 13)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (3, 14)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (3, 15)';
        $this->Permiso->query($sql);

        // $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (3, 16)';
        // $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (3, 25)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (3, 26)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (3, 28)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (3, 29)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (3, 31)';
        $this->Permiso->query($sql);

        //--------------------------------------------------------------------------------------------------------------

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (4, 2)';
        $this->Permiso->query($sql);

        // $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (4, 5)';
        // $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (4, 6)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (4, 10)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (4, 14)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (4, 18)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (4, 19)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (4, 20)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (4, 21)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (4, 22)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (4, 23)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (4, 24)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (4, 25)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (4, 26)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (4, 27)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (4, 28)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (4, 29)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (4, 30)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (4, 31)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (4, 32)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (4, 34)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (4, 35)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (4, 36)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (4, 37)';
        $this->Permiso->query($sql);

        // $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (4, 38)';
        // $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (4, 39)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (4, 40)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (4, 41)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (4, 42)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (4, 43)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (4, 44)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (4, 45)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (4, 46)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (4, 47)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (4, 48)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (4, 49)';
        $this->Permiso->query($sql);
        //-----------------------------------------------------------------------------------------------------------

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (5, 2)';
        $this->Permiso->query($sql);

        // $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (5, 5)';
        // $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (5, 6)';
        $this->Permiso->query($sql);

        // $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (5, 7)';
        // $this->Permiso->query($sql);

        // $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (5, 8)';
        // $this->Permiso->query($sql);

        // $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (5, 9)';
        // $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (5, 10)';
        $this->Permiso->query($sql);

        // $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (5, 11)';
        // $this->Permiso->query($sql);

        // $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (5, 12)';
        // $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (5, 14)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (5, 25)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (5, 26)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (5, 28)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (5, 29)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (5, 31)';
        $this->Permiso->query($sql);

        //----------------------------------------------------------------------------------------------------------

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (6, 2)';
        $this->Permiso->query($sql);

        // $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (6, 5)';
        // $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (6, 6)';
        $this->Permiso->query($sql);

        // $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (6, 7)';
        // $this->Permiso->query($sql);

        // $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (6, 8)';
        // $this->Permiso->query($sql);

        // $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (6, 9)';
        // $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (6, 10)';
        $this->Permiso->query($sql);

        // $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (6, 11)';
        // $this->Permiso->query($sql);

        // $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (6, 12)';
        // $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (6, 14)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (6, 25)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (6, 26)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (6, 28)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (6, 29)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (6, 31)';
        $this->Permiso->query($sql);

        //----------------------------------------------------------------------------------------------------------

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (7, 2)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (7, 3)';
        $this->Permiso->query($sql);

        // $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (7, 5)';
        // $this->Permiso->query($sql);

        // $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (7, 8)';
        // $this->Permiso->query($sql);

        // $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (7, 9)';
        // $this->Permiso->query($sql);

        // $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (7, 12)';
        // $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (7, 14)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (7, 15)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (7, 22)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (7, 24)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (7, 25)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (7, 26)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (7, 28)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (7, 29)';
        $this->Permiso->query($sql);

        //----------------------------------------------------------------------------------------------------------

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (8, 2)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (8, 3)';
        $this->Permiso->query($sql);

        // $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (8, 5)';
        // $this->Permiso->query($sql);

        // $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (8, 8)';
        // $this->Permiso->query($sql);

        // $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (8, 9)';
        // $this->Permiso->query($sql);

        // $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (8, 12)';
        // $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (8, 14)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (8, 15)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (8, 22)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (8, 24)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (8, 25)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (8, 26)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (8, 28)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (8, 29)';
        $this->Permiso->query($sql);

        //----------------------------------------------------------------------------------------------------------

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (9, 10)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (9, 14)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (9, 28)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (9, 29)';
        $this->Permiso->query($sql);

        //----------------------------------------------------------------------------------------------------------

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (10, 2)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (10, 10)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (10, 28)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_permissions (group_permission_id, permission_id) VALUES (10, 29)';
        $this->Permiso->query($sql);

        //==============================================================================================================

        $sql = 'INSERT INTO groups_permissions_users (group_permission_id, user_id) VALUES (1, 1)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_users (group_permission_id, user_id) VALUES (10, 2)';
        $this->Permiso->query($sql);

        $sql = 'INSERT INTO groups_permissions_users (group_permission_id, user_id) VALUES (9, 3)';
        $this->Permiso->query($sql);

        //==============================================================================================================

        $sql = 'INSERT INTO groups_permissions_roles (group_permission_id, role_id) VALUES (1, 1)'; //Administrator
        $this->Permiso->query($sql);

        //--------------------------------------------------------------------------------------------------------------

        $sql = 'INSERT INTO groups_permissions_roles (group_permission_id, role_id) VALUES (2, 6)'; //BDM AAG
        $this->Permiso->query($sql);

        //--------------------------------------------------------------------------------------------------------------

        $sql = 'INSERT INTO groups_permissions_roles (group_permission_id, role_id) VALUES (3, 11)'; //BDM TG
        $this->Permiso->query($sql);

        //--------------------------------------------------------------------------------------------------------------

        $sql = 'INSERT INTO groups_permissions_roles (group_permission_id, role_id) VALUES (4, 3)'; //AAG Director
        $this->Permiso->query($sql);

        //--------------------------------------------------------------------------------------------------------------

        $sql = 'INSERT INTO groups_permissions_roles (group_permission_id, role_id) VALUES (5, 4)'; //AAG MANAGER
        $this->Permiso->query($sql);

        //--------------------------------------------------------------------------------------------------------------

        $sql = 'INSERT INTO groups_permissions_roles (group_permission_id, role_id) VALUES (6, 8)'; //GENERAL / BRANCHMANAGER
        $this->Permiso->query($sql);

        //--------------------------------------------------------------------------------------------------------------

        $sql = 'INSERT INTO groups_permissions_roles (group_permission_id, role_id) VALUES (7, 10)'; //GARAGE NETWORK MANAGER
        $this->Permiso->query($sql);

        //--------------------------------------------------------------------------------------------------------------

        $sql = 'INSERT INTO groups_permissions_roles (group_permission_id, role_id) VALUES (8, 9)'; //TG Director
        $this->Permiso->query($sql);

        //--------------------------------------------------------------------------------------------------------------

        $sql = 'INSERT INTO groups_permissions_roles (group_permission_id, role_id) VALUES (9, 12)'; // Distributor Manager
        $this->Permiso->query($sql);

        //--------------------------------------------------------------------------------------------------------------

        $sql = 'INSERT INTO groups_permissions_roles (group_permission_id, role_id) VALUES (10, 7)'; //Garage Manager
        $this->Permiso->query($sql);

        //==============================================================================================================

    }

}

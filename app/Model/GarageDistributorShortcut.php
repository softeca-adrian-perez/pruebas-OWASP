<?php

class GarageDistributorShortcut extends AppModel
{
    public $useTable = 'garages_distributors_shortcuts';

    public $validate = array(
        'parameter_value_1' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'parameter_value_2' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
    );

    public function add($shortcut_data)
    {
        $key = Texto::encryptDecryptText(JWT_CODE_WORD, false);
        if (isset($shortcut_data['Shortcut']['parameter_value_2'])) {
            $param = $shortcut_data['Shortcut']['parameter_value_2'];
            $jwt_encode = JWT::encode($param, $key);
            $shortcut_data['Shortcut']['parameter_value_2'] = $jwt_encode;
        } else {
            $shortcut_data['Shortcut']['parameter_value_2'] = null;
        }

        $fields = array(
            'GarageDistributorShortcut' => array(
                'garage_id',
                'distributor_id',
                'shortcut_id',
                'user_id',
                'network_id',
                'parameter_value_1',
                'parameter_value_2',
                'fav',
                'creation_date',
            )
        );

        $shortcut_data['GarageDistributorShortcut'] = $shortcut_data['Shortcut'];
        $shortcut_data['GarageDistributorShortcut']['creation_date'] = date('Y-m-d');
        $this->create();

        $shortcut_data_bd = $this->guardar($shortcut_data, $fields);
        if (!$shortcut_data_bd) {
            return false;
        }

        $this->commit();
        return $shortcut_data_bd;
    }

    public function edit($shortcut_favorite)
    {
        $fields = array(
            'id',
            'fav'
        );

        if ($this->guardar($shortcut_favorite, $fields)) {
            $this->commit();
            return true;
        } else {
            return false;
        }
    }
}

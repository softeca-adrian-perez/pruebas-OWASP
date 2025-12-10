<?php

class TradingGroup extends AppModel
{
    public $useTable = 'trading_groups';
    public $displayField = 'name';

    public $hasMany = array(
        'Network' => array(
            'foreignKey' => 'network_id',
        ),
    );

    public $hasOne = array(
        'AagRegion'
    );

    public $validate = array(
        'name' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_name',
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'image' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_an_image',
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'aag_region_id' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_region',
            ),
        ),
        'code' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'web' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'primary_color' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'primary_font_color' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'primary_background_color' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'secondary_color' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'secondary_font_color' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'secondary_background_color' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'color_active' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'tertiary_color' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'menu_color' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'menu_background_color' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'color_exito' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'color_fallo' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'color_informacion' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'color_disabled' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
    );

    public function new_trading_group($trading_group)
    {
        $fields = array(
            'TradingGroup' => array(
                'name',
                'web',
                'image',
                'is_cv',
                'primary_color',
                'primary_font_color',
                'primary_background_color',
                'secondary_color',
                'secondary_font_color',
                'secondary_background_color',
                'tertiary_color',
                'color_active',
                'menu_color',
                'menu_background_color',
                'color_exito',
                'color_fallo',
                'color_informacion',
                'color_disabled',
                'creation_date',
                'aag_region_id'
            )
        );

        if (!isset($trading_group['TradingGroup']['is_cv']) || $trading_group['TradingGroup']['is_cv'] == '') {
            $trading_group['TradingGroup']['is_cv'] = null;
        }
        if (!empty($trading_group['TradingGroup']['web']) && substr($trading_group['TradingGroup']['web'], 0, 7) !== "http://" && substr($trading_group['TradingGroup']['web'], 0, 8) !== "https://") {
            $trading_group['TradingGroup']['web'] = 'http://' . $trading_group['TradingGroup']['web'];
        }

        $this->create();
        $trading_group['TradingGroup']['creation_date'] = date('Y-m-d');
        if (!$this->guardar($trading_group, $fields)) {
            return false;
        }

        return $trading_group;
    }

    public function edit_trading_group($trading_group)
    {
        $fields = array(
            'TradingGroup' => array(
                'name',
                'web',
                'image',
                'is_cv',
                'primary_color',
                'primary_font_color',
                'primary_background_color',
                'secondary_color',
                'secondary_font_color',
                'secondary_background_color',
                'tertiary_color',
                'color_active',
                'menu_color',
                'menu_background_color',
                'color_exito',
                'color_fallo',
                'color_informacion',
                'color_disabled',
                'aag_region_id'
            )
        );

        if ($trading_group['TradingGroup']['is_cv'] == '') {
            $trading_group['TradingGroup']['is_cv'] = null;
        }
        if (!empty($trading_group['TradingGroup']['web']) && substr($trading_group['TradingGroup']['web'], 0, 7) !== "http://" && substr($trading_group['TradingGroup']['web'], 0, 8) !== "https://") {
            $trading_group['TradingGroup']['web'] = 'http://' . $trading_group['TradingGroup']['web'];
        }

        if (!$this->guardar($trading_group, $fields)) {
            return false;
        }

        return true;
    }

    public function tradingGroupsList()
    {
        return $this->find('list', array(
            'fields' => array(
                'id',
                'name'
            )
        ));
    }

    public function getTradingGroupIndependentWithOutPermissions($aag_region_id)
    {
        $conditions = array(
            'TradingGroup.independent' => ConstantsBooleans::YES,
            'TradingGroup.aag_region_id' => $aag_region_id
        );

        $trading_groups = $this->find(
            'all',
            array(
                'conditions' => $conditions,
                'fields' => array(
                    'TradingGroup.*'
                ),
            )
        );

        return $trading_groups;
    }

    public function getTradingGroupIndependent()
    {
        $user = CakeSession::read('Auth.User');
        $user_aag_region_id = $user['aag_region_id'];

        $trading_groups = $this->find(
            'all',
            array(
                'conditions' => array(
                    'TradingGroup.independent' => ConstantsBooleans::YES,
                    'TradingGroup.aag_region_id' => $user_aag_region_id
                ),
                'fields' => array(
                    'TradingGroup.*'
                ),
                'order' => array(
                    'TradingGroup.name'
                )
            )
        );

        $distributor_permissions = CakeSession::read('Auth.User.Permissionsv2.trading_groups');

        if (!is_null($distributor_permissions)) {
            if (isset($distributor_permissions[0])) {
                return $trading_groups;
            } else {
                foreach ($trading_groups as $key => $trading_group) {
                    if (!array_key_exists($trading_group['TradingGroup']['id'], $distributor_permissions)) {
                        unset($trading_groups[$key]);
                    }
                }
            }
        }

        return $trading_groups;
    }

    public function getIndependent($aag_region_id)
    {
        $conditions = array(
            'TradingGroup.independent' => ConstantsBooleans::YES
        );
        
        $conditions[] = array('TradingGroup.aag_region_id' => $aag_region_id);

        return $this->find(
            'list',
            array(
                'conditions' => $conditions,
                'order' => array(
                    'TradingGroup.name'
                ),
            )
        );
    }

    public function getList()
    {
        return $this->find(
            'list',
            array(
                'fields' => array(
                    'TradingGroup.id',
                ),
            )
        );
    }

    public function getTradingGroup()
    {
        $trading_groups = $this->find(
            'list',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Distributor',
                        'table' => 'distributors',
                        'type' => 'INNER',
                        'conditions' => array(
                            'TradingGroup.id = Distributor.trading_group_id'
                        )
                    ),
                )
            )
        );

        $distributor_permissions = CakeSession::read('Auth.User.Permissionsv2.trading_groups');
        if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
            if (isset($distributor_permissions[0])) {
                return $trading_groups;
            } else {
                foreach ($trading_groups as $key => $trading_group) {
                    if (!array_key_exists($key, $distributor_permissions)) {
                        unset($trading_groups[$key]);
                    }
                }
            }
        }

        return $trading_groups;
    }

    public function getTradingGroupRegion($aag_region_id)
    {
        $trading_groups = $this->find(
            'list',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Distributor',
                        'table' => 'distributors',
                        'type' => 'INNER',
                        'conditions' => array(
                            'TradingGroup.id = Distributor.trading_group_id',
                            'TradingGroup.aag_region_id' => $aag_region_id
                        )
                    ),
                ),
                'order' => 'TradingGroup.name'
            )
        );

        $distributor_permissions = CakeSession::read('Auth.User.Permissionsv2.trading_groups');
        if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
            if (isset($distributor_permissions[0])) {
                return $trading_groups;
            } else {
                foreach ($trading_groups as $key => $trading_group) {
                    if (!array_key_exists($key, $distributor_permissions)) {
                        unset($trading_groups[$key]);
                    }
                }
            }
        }

        return $trading_groups;
    }

    public function getTradingGroupGarage()
    {
        return $this->find(
            'list',
            array(
                'joins' => array(
                    array(
                        'alias' => 'GarageNetwork',
                        'table' => 'garages_networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'TradingGroup.id = GarageNetwork.trading_group_id'
                        )
                    ),
                )
            )
        );
    }

    public function getTradingGroupGarageConditions($aag_region_id)
    {
        return $this->find(
            'list',
            array(
                'joins' => array(
                    array(
                        'alias' => 'GarageNetwork',
                        'table' => 'garages_networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'TradingGroup.id = GarageNetwork.trading_group_id',
                            'TradingGroup.aag_region_id' => $aag_region_id
                        )
                    ),
                )
            )
        );
    }

    public function getTradingGroupIndependentByLVType($aag_region_id)
    {
        return $this->find(
            'all',
            array(
                'conditions' => array(
                    'TradingGroup.aag_region_id' => $aag_region_id,
                    'TradingGroup.independent' => ConstantsBooleans::YES,
                    'OR' => array(
                        array('TradingGroup.is_cv' => ConstantsBooleans::NO),
                        array('TradingGroup.is_cv' => null),
                    )
                ),
                'fields' => array(
                    'TradingGroup.*'
                ),
            )
        );
    }

    public function getTradingGroupIndependentByCVType($aag_region_id)
    {
        return $this->find(
            'all',
            array(
                'conditions' => array(
                    'TradingGroup.aag_region_id' => $aag_region_id,
                    'TradingGroup.independent' => ConstantsBooleans::YES,
                    'OR' => array(
                        array('TradingGroup.is_cv' => ConstantsBooleans::YES),
                        array('TradingGroup.is_cv' => null),
                    )
                ),
                'fields' => array(
                    'TradingGroup.*'
                ),
            )
        );
    }

    public function getTradingGroupById($trading_group_id)
    {
        return $this->find(
            'first',
            array(
                'conditions' => array(
                    'TradingGroup.id' => $trading_group_id
                ),
                'fields' => array(
                    'TradingGroup.id',
                    'TradingGroup.name',
                    'TradingGroup.image'
                ),
            )
        );
    }

    public function generate_style_trading_group($trading_group, $hashtag = null)
    {
        require_once(dirname(__FILE__) . '/../Vendor/Smarty/libs/Smarty.class.php');
        require_once(dirname(__FILE__) . '/../Vendor/scssphp/scss.inc.php');

        $file = dirname(__FILE__) . '/../webroot/css/scss/base/_variables.scss';
        $main_data = file_get_contents($file);

        $smarty = new Smarty();
        $smarty->setCompileDir(APP . '/tmp/Smarty/templates_c/');
        $smarty->assign('primary_color', $trading_group['TradingGroup']['primary_color']);
        $smarty->assign('primary_font_color', $trading_group['TradingGroup']['primary_font_color']);
        $smarty->assign('primary_background_color', $trading_group['TradingGroup']['primary_background_color']);
        $smarty->assign('secondary_color', $trading_group['TradingGroup']['secondary_color']);
        $smarty->assign('secondary_font_color', $trading_group['TradingGroup']['secondary_font_color']);
        $smarty->assign('secondary_background_color', $trading_group['TradingGroup']['secondary_background_color']);
        $smarty->assign('tertiary_color', $trading_group['TradingGroup']['tertiary_color']);
        $smarty->assign('color_active', $trading_group['TradingGroup']['color_active']);
        $smarty->assign('menu_color', $trading_group['TradingGroup']['menu_color']);
        $smarty->assign('menu_background_color', $trading_group['TradingGroup']['menu_background_color']);
        $smarty->assign('color_exito', $trading_group['TradingGroup']['color_exito']);
        $smarty->assign('color_fallo', $trading_group['TradingGroup']['color_fallo']);
        $smarty->assign('color_informacion', $trading_group['TradingGroup']['color_informacion']);
        $smarty->assign('color_disabled', $trading_group['TradingGroup']['color_disabled']);

        $data = $smarty->fetch(dirname(__FILE__) . '/../Vendor/Smarty/libs/templates/style.tpl');
        $handle = fopen($file, 'w');
        fwrite($handle, $data);
        fclose($handle);

        $scss = new scss_server(dirname(__FILE__) . '/../webroot/css');
        $dir_scss = dirname(__FILE__) . '/../webroot/css/estilos.scss';
        $dir_css = dirname(__FILE__) . '/../webroot/css/styles_trd_' . $trading_group['TradingGroup']['id'] . '_' . 'en' . '.css';
        $version = file_get_contents(VERSION_CACHE_NETWORK);
        $version = $version + 1;
        file_put_contents(VERSION_CACHE_NETWORK, $version);
        CakeSession::write('Auth.User.version_cache_network', Numero::validateSessionNumeric($version));

        $scss->compile($dir_scss, $dir_css);
        file_put_contents($file, $main_data);
    }

    public function update_styles()
    {
        $trading_groups = $this->find('all');
        foreach ($trading_groups as $trading_group) {
            $this->generate_style_trading_group($trading_group, false);
        }

        $this->autoRender = false;
    }

    private $_queries = array(
        'home' => array(
            'fields' => array(
                'TradingGroup.*'
            ),
            'order' => 'TradingGroup.name asc'
        )
    );

    public function _query($index)
    {
        return $this->_queries[$index];
    }
}

<?php

class DistributorNetwork extends AppModel
{
    public $useTable = 'distributors_networks';
    public $displayField = 'name';

    //Problems: In database, this field must be distributor_network_id
    var $hasAndBelongsToMany = array(
        'Distributor' => array(
            'foreignKey' => 'network_id',
        ),
    );

    public $hasMany = array(
        'TradingGroup' => array(
            'foreignKey' => 'trading_group_id',
        ),
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
        'network_type' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_network_type',
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'image_pin' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'image_cluster' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'web' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_website',
            ),
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

    public function new_networks($distributor_network)
    {
        $fields = array(
            'DistributorNetwork' => array(
                'name',
                'web',
                'image',
                'image_pin',
                'image_cluster',
                'network_type',
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
                'aag_region_id',
            )
        );
        if (!empty($distributor_network['DistributorNetwork']['web']) && substr($distributor_network['DistributorNetwork']['web'], 0, 7) !== "http://" && substr($distributor_network['DistributorNetwork']['web'], 0, 8) !== "https://") {
            $distributor_network['DistributorNetwork']['web'] = 'http://' . $distributor_network['DistributorNetwork']['web'];
        }
        $distributor_network['DistributorNetwork']['creation_date'] = date('Y-m-d');

        $distributor_network_bd = $this->guardar($distributor_network, $fields);
        if (!$distributor_network_bd) {
            return false;
        }

        //Related Trading Group
        if (isset($distributor_network['TradingGroup'])) {
            foreach ($distributor_network['TradingGroup'] as $key => $trading_groups_network) {
                if ($trading_groups_network['value']) {
                    $this->TradingGroupDistributorNetwork = ClassRegistry::init("TradingGroupDistributorNetwork");
                    $model = array(
                        'trading_group_id' => $key,
                        'network_id' => $distributor_network_bd['DistributorNetwork']['id'],
                    );
                    if (!$this->TradingGroupDistributorNetwork->new_trading_groups_networks($model)) {
                        return false;
                    }
                }
            }
        }

        return $distributor_network_bd;
    }

    public function edit_network($distributor_network)
    {
        $fields = array(
            'DistributorNetwork' => array(
                'id',
                'name',
                'web',
                'image',
                'image_pin',
                'image_cluster',
                'network_type',
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
                'aag_region_id',
            )
        );

        if ($distributor_network['RadioGroup'] == ConstantsNetworksPins::CUSTOM) {
            if ($distributor_network['DistributorNetwork']['image_pin'] == '') {
                unset($fields['DistributorNetwork'][4]);
            }
            if ($distributor_network['DistributorNetwork']['image_cluster'] == '') {
                unset($fields['DistributorNetwork'][5]);
            }
        }
        if (!empty($distributor_network['DistributorNetwork']['web']) && substr($distributor_network['DistributorNetwork']['web'], 0, 7) !== "http://" && substr($distributor_network['DistributorNetwork']['web'], 0, 8) !== "https://") {
            $distributor_network['DistributorNetwork']['web'] = 'http://' . $distributor_network['DistributorNetwork']['web'];
        }
        $distributor_network['DistributorNetwork']['creation_date'] = date('Y-m-d');
        $distributor_network_bd = $this->guardar($distributor_network['DistributorNetwork'], $fields);
        if (!$distributor_network_bd) {
            return false;
        }

        ///Deleted old Related Trading Group
        $this->TradingGroupDistributorNetwork = ClassRegistry::init("TradingGroupDistributorNetwork");
        $this->TradingGroupDistributorNetwork->removeTradingGroupDistributorNetwork($distributor_network_bd['DistributorNetwork']['id']);

        //Related Trading Group
        foreach ($distributor_network['TradingGroup'] as $key => $trading_groups_network) {
            if ($trading_groups_network['value']) {
                $model = array(
                    'trading_group_id' => $key,
                    'network_id' => $distributor_network_bd['DistributorNetwork']['id'],
                );
                if (!$this->TradingGroupDistributorNetwork->new_trading_groups_networks($model)) {
                    return false;
                }
            }
        }

        $this->commit();
        return true;
    }

    public function ajax_get_tradings_groups($aag_region_id)
    {
        $trading_groups = null;
        if (!$this->request->is('get')) {
            $data = $this->request->data;
            if (isset($data['network_type']) && $data['network_type'] == 'LV') {
                $trading_groups = $this->Network->TradingGroup->getTradingGroupIndependentByLVType($aag_region_id);
            } else if (isset($data['network_type']) && $data['network_type'] == 'CV') {
                $trading_groups = $this->Network->TradingGroup->getTradingGroupIndependentByCVType($aag_region_id);
            } else {
                $trading_groups = null;
            }
        }

        $this->set(array(
            'trading_groups' => $trading_groups,
        ));

        $this->layout = false;
        $this->render('../DistributorNetworks/Elements/trading_groups');
    }

    public function distributorNetworksList()
    {
        return $this->find('list', array(
            'fields' => array(
                'id',
                'name'
            )
        ));
    }

    public function getRelatedNetworks($trading_group_id)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'TradingGroupDistributorNetwork',
                        'table' => 'trading_groups_networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'TradingGroupDistributorNetwork.network_id = Network.id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'TradingGroupDistributorNetwork.trading_group_id' => $trading_group_id,
                ),
                'fields' => array(
                    'DistributorNetwork.*'
                ),
            )
        );
    }

    public function getRelatedNetworksList($trading_group_id)
    {
        return $this->find(
            'list',
            array(
                'joins' => array(
                    array(
                        'alias' => 'TradingGroupDistributorNetwork',
                        'table' => 'trading_groups_distributors_networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'TradingGroupDistributorNetwork.network_id = DistributorNetwork.id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'TradingGroupDistributorNetwork.trading_group_id' => $trading_group_id,
                ),
                'order' => array(
                    'DistributorNetwork.id',
                    'DistributorNetwork.name',
                )
            )
        );
    }

    public function findDatas($distributor_network_id)
    {
        return $this->find(
            'first',
            array(
                'joins' => array(
                    array(
                        'alias' => 'TradingGroupDistributorNetwork',
                        'table' => 'trading_groups_distributors_networks',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'TradingGroupDistributorNetwork.network_id = DistributorNetwork.id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'DistributorNetwork.id' => $distributor_network_id,
                ),
                'fields' => array(
                    'DistributorNetwork.*',
                    'group_concat(distinct TradingGroupDistributorNetwork.trading_group_id separator ",") as TradingGroupDistributorNetwork',
                ),
            )
        );
    }

    public function findNetworksByType($type)
    {

        if ($type === null) {
            $distributor_network_types = array(ConstantsNetworks::HEAVY_COMMERCIAL_VEHICLE, ConstantsNetworks::LIGHT_COMMERCIAL_VEHICLE);
        } elseif ($type == 0) {
            $distributor_network_types = array(ConstantsNetworks::LIGHT_COMMERCIAL_VEHICLE,);
        } elseif ($type == 1) {
            $distributor_network_types = array(ConstantsNetworks::HEAVY_COMMERCIAL_VEHICLE);
        }

        return $this->find(
            'list',
            array(
                'conditions' => array(
                    'DistributorNetwork.network_type' => $distributor_network_types,
                ),
                'order' => array(
                    'name'
                )
            )
        );
    }

    public function getListLV()
    {
        return $this->find(
            'list',
            array(
                'joins' => array(
                    array(
                        'alias' => 'TradingGroupDistributorNetwork',
                        'table' => 'trading_groups_networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'TradingGroupDistributorNetwork.network_id = Network.id',
                        ),
                    ),
                    array(
                        'alias' => 'TradingGroup',
                        'table' => 'trading_groups',
                        'type' => 'INNER',
                        'conditions' => array(
                            'TradingGroupDistributorNetwork.trading_group_id = TradingGroup.id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'TradingGroup.independent' => ConstantsBooleans::YES,
                    'DistributorNetwork.network_type' => ConstantsDistributorActivity::LV,
                ),
                'order' => 'DistributorNetwork.name',
                'fields' => array(
                    'DistributorNetwork.id',
                    'DistributorNetwork.name',
                ),
            )
        );
    }

    public function getListCV()
    {
        return $this->find(
            'list',
            array(
                'joins' => array(
                    array(
                        'alias' => 'TradingGroupDistributorNetwork',
                        'table' => 'trading_groups_networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'TradingGroupDistributorNetwork.network_id = Network.id',
                        ),
                    ),
                    array(
                        'alias' => 'TradingGroup',
                        'table' => 'trading_groups',
                        'type' => 'INNER',
                        'conditions' => array(
                            'TradingGroupDistributorNetwork.trading_group_id = TradingGroup.id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'TradingGroup.independent' => ConstantsBooleans::YES,
                    'DistributorNetwork.network_type' => ConstantsDistributorActivity::CV,
                ),
                'order' => 'DistributorNetwork.name',
                'fields' => array(
                    'DistributorNetwork.id',
                    'DistributorNetwork.name'
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
                    'DistributorNetwork.id',
                ),
            )
        );
    }

    public function getListDistributorNetwork()
    {
        return $this->find(
            'list',
            array(
                'fields' => array(
                    'DistributorNetwork.id',
                    'DistributorNetwork.name',
                ),
            )
        );
    }

    public function generate_style_network($distributor_network, $hashtag = null)
    {

        require_once '../Vendor/Smarty/libs/Smarty.class.php';
        require_once '../Vendor/scssphp/scss.inc.php';

        $file = '../webroot/css/scss/base/_variables.scss';
        $main_data = file_get_contents($file);

        $smarty = new Smarty();
        $smarty->setCompileDir(APP . '/tmp/Smarty/templates_c/');
        $smarty->assign('primary_color', $distributor_network['DistributorNetwork']['primary_color']);
        $smarty->assign('primary_font_color', $distributor_network['DistributorNetwork']['primary_font_color']);
        $smarty->assign('primary_background_color', $distributor_network['DistributorNetwork']['primary_background_color']);
        $smarty->assign('secondary_color', $distributor_network['DistributorNetwork']['secondary_color']);
        $smarty->assign('secondary_font_color', $distributor_network['DistributorNetwork']['secondary_font_color']);
        $smarty->assign('secondary_background_color', $distributor_network['DistributorNetwork']['secondary_background_color']);
        $smarty->assign('tertiary_color', $distributor_network['DistributorNetwork']['tertiary_color']);
        $smarty->assign('color_active', $distributor_network['DistributorNetwork']['color_active']);
        $smarty->assign('menu_color', $distributor_network['DistributorNetwork']['menu_color']);
        $smarty->assign('menu_background_color', $distributor_network['DistributorNetwork']['menu_background_color']);
        $smarty->assign('color_exito', $distributor_network['DistributorNetwork']['color_exito']);
        $smarty->assign('color_fallo', $distributor_network['DistributorNetwork']['color_fallo']);
        $smarty->assign('color_informacion', $distributor_network['DistributorNetwork']['color_informacion']);
        $smarty->assign('color_disabled', $distributor_network['DistributorNetwork']['color_disabled']);

        $data = $smarty->fetch('../Vendor/Smarty/libs/templates/style.tpl');
        $handle = fopen($file, 'w');
        fwrite($handle, $data);
        fclose($handle);

        $scss = new scss_server('../webroot/css');
        $dir_scss = '../webroot/css/estilos.scss';
        $version = file_get_contents(VERSION_CACHE_NETWORK);
        $version = $version + 1;
        file_put_contents(VERSION_CACHE_NETWORK, $version);
        CakeSession::write('Auth.User.version_cache_network', Numero::validateSessionNumeric($version));

        $scss->compile($dir_scss, $dir_css);
        file_put_contents($file, $main_data);
    }

    public function getNetworks()
    {
        return $this->find(
            'list',
            array(
                'joins' => array(
                    array(
                        'alias' => 'DistributorDistributorNetwork',
                        'table' => 'distributors_distributors_networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'DistributorNetwork.id = DistributorDistributorNetwork.network_id'
                        )
                    ),
                )
            )
        );
    }

    public function findNetworksWithDistributors()
    {
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'DistributorDistributorNetwork',
                    'table' => 'distributors_distributors_networks',
                    'type' => 'INNER',
                    'conditions' => array(
                        'AND' => array(
                            'DistributorDistributorNetwork.network_id = DistributorNetwork.id',
                            'DistributorDistributorNetwork.status' => ConstantsNetworksStatus::LIVE,
                        )
                    )
                ),
                array(
                    'alias' => 'Distributor',
                    'table' => 'distributors',
                    'type' => 'RIGHT',
                    'conditions' => 'Distributor.id = DistributorDistributorNetwork.distributor_id'
                ),
            ),
            'fields' => array(
                'DistributorNetwork.id',
                'DistributorNetwork.name',
                'DistributorNetwork.image_pin',
                'DistributorNetwork.image_cluster',
                'DistributorDistributorNetwork.id',
                'Distributor.id',
                'Distributor.name',
                'Distributor.town',
                'Distributor.latitude',
                'Distributor.longitude',
            ),
        ));
    }

    public function getListRegion($aag_region_id)
    {
        return $this->find(
            'list',
            array(
                'fields' => array(
                    'DistributorNetwork.id',
                    'DistributorNetwork.name',
                    'DistributorNetwork.image',
                ),
                'conditions' => array(
                    'DistributorNetwork.aag_region_id' => $aag_region_id
                ),
            )
        );
    }

    public function findByRegion($aag_region_id)
    {
        return $this->find(
            'list',
            array(
                'fields' => array(
                    'DistributorNetwork.id',
                    'DistributorNetwork.name',
                ),
                'conditions' => array(
                    'DistributorNetwork.aag_region_id' => $aag_region_id
                ),
            )
        );
    }
}

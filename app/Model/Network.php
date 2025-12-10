<?php

use Composer\Autoload\ClassLoader;

class Network extends AppModel
{
	public $sendInfoCacheDataWebs = false;
    public $useTable = 'networks';
    public $displayField = 'name';

    public $hasAndBelongsToMany = array(
        'Garage',
    );

    public $hasMany = array(
        'TradingGroup' => array(
            'foreignKey' => 'trading_group_id',
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
        'aag_region_id' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_region',
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
        'tertiary_color' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'tertiary_font_color' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'quaternary_color' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'quaternary_font_color' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'font_default_color' => array(
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
        'ref_code' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'reviews_info' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_TEXT),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'default_mileage' => array(
            array(
                'rule' => 'numeric',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_number',
            ),
            'range' => array(
                'rule' => array('range', 0, ConstantsValidation::MAX_VALUE_INT),
                'message' => 'Validation.Mandatory_to_choose_a_number_bigger_than_zero',
            ),
        ),
        'distance_unit_id' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_distance_unit',
            ),
        ),
    );

    public function search_list()
    {
        return $this->find('list', array(
            'fields' => array(
                'id',
                'name'
            ),
            'order' => array(
                'name'
            )
        ));
    }

    public function new_networks($network)
    {
        $fields = array(
            'Network' => array(
                'name',
                'web',
                'image',
                'image_pin',
                'image_cluster',
                'network_type',
                'internal',
                'primary_color',
                'primary_font_color',
                'primary_background_color',
                'secondary_color',
                'secondary_font_color',
                'secondary_background_color',
                'tertiary_color',
                'tertiary_font_color',
                'quaternary_color',
                'quaternary_font_color',
                'font_default_color',
                'menu_background_color',
                'color_exito',
                'color_fallo',
                'color_informacion',
                'color_disabled',
                'creation_date',
                'ref_code',
                'recommended',
                'aag_region_id',
                'training',
                'guid',
                'modification_date',
                'loop'
            )
        );
        $network['Network']['guid'] = CakeText::uuid();

        if (
            !empty($network['Network']['web']) &&
            substr($network['Network']['web'], 0, 7) !== "http://" &&
            substr($network['Network']['web'], 0, 8) !== "https://"
        ) {
            $network['Network']['web'] = 'http://' . $network['Network']['web'];
        }
        $network['Network']['creation_date'] = date('Y-m-d');
        $network['Network']['modification_date'] = date('Y-m-d H:i:s');

        $network_bd = $this->guardar($network, $fields);
        if (!$network_bd) {
            return false;
        }

        // Related Trading Group
        if (isset($network['TradingGroup'])) {
            foreach ($network['TradingGroup'] as $key => $trading_groups_network) {
                if ($trading_groups_network['value']) {
                    $this->TradingGroupNetwork = ClassRegistry::init("TradingGroupNetwork");
                    $model = array(
                        'trading_group_id' => $key,
                        'network_id' => $network_bd['Network']['id'],
                    );
                    if (!$this->TradingGroupNetwork->new_trading_groups_networks($model)) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    public function edit_network($network)
    {
        $fields = array(
            'Network' => array(
                'id',
                'name',
                'web',
                'image',
                'image_pin',
                'image_cluster',
                'network_type',
                'internal',
                'primary_color',
                'primary_font_color',
                'primary_background_color',
                'secondary_color',
                'secondary_font_color',
                'secondary_background_color',
                'tertiary_color',
                'tertiary_font_color',
                'quaternary_color',
                'quaternary_font_color',
                'font_default_color',
                'menu_background_color',
                'color_exito',
                'color_fallo',
                'color_informacion',
                'color_disabled',
                'ref_code',
                'credit',
                'date_restarting_credit',
                'recommended',
                'aag_region_id',
                'training',
                'modification_date',
                'loop',
                'optimized'
            )
        );

        if (
            isset($network['Network']['date_restarting_credit']) &&
            !empty($network['Network']['date_restarting_credit']) &&
            $network['Network']['training'] == ConstantsBooleans::ACTIVE
        ) {
            $network['Network']['date_restarting_credit'] = Fecha::toFormatoBd($network['Network']['date_restarting_credit']);
        } else {
            $network['Network']['date_restarting_credit'] = null;
        }
        if (
            isset($network['Network']['credit']) &&
            !empty($network['Network']['credit']) &&
            $network['Network']['training'] == ConstantsBooleans::ACTIVE
        ) {
            $network['Network']['credit'] = $network['Network']['credit'];
        } else {
            $network['Network']['credit'] = null;
        }

        $network['Network']['tertiary_font_color'] = $network['Network']['tertiary_font_color'];

        if ($network['RadioGroup'] == ConstantsNetworksPins::CUSTOM) {
            if ($network['Network']['image_pin'] == '') {
                unset($fields['Network'][4]);
            }
            if ($network['Network']['image_cluster'] == '') {
                unset($fields['Network'][5]);
            }
        }
        if (
            !empty($network['Network']['web']) &&
            substr($network['Network']['web'], 0, 7) !== "http://" &&
            substr($network['Network']['web'], 0, 8) !== "https://"
        ) {
            $network['Network']['web'] = 'http://' . $network['Network']['web'];
        }

        $network['Network']['modification_date'] = date('Y-m-d H:i:s');

        $network_bd = $this->guardar($network['Network'], $fields);

        if (!$network_bd) {
            return false;
        }

        // Deleted old Related Trading Group
        $this->TradingGroupNetwork = ClassRegistry::init("TradingGroupNetwork");
        $this->TradingGroupNetwork->removeTradingGroupNetwork($network_bd['Network']['id']);

        if (isset($network['TradingGroup']) && !empty($network['TradingGroup'])) {
            //Related Trading Group
            foreach ($network['TradingGroup'] as $key => $trading_groups_network) {
                if ($trading_groups_network['value']) {
                    $model = array(
                        'trading_group_id' => $key,
                        'network_id' => $network_bd['Network']['id'],
                    );
                    if (!$this->TradingGroupNetwork->new_trading_groups_networks($model)) {
                        return false;
                    }
                }
            }
        }

        $this->commit();
        return true;
    }

    public function getRelatedNetworks($trading_group_id)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'TradingGroupNetwork',
                        'table' => 'trading_groups_networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'TradingGroupNetwork.network_id = Network.id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'TradingGroupNetwork.trading_group_id' => $trading_group_id,
                ),
                'fields' => array(
                    'Network.*'
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
                        'alias' => 'TradingGroupNetwork',
                        'table' => 'trading_groups_networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'TradingGroupNetwork.network_id = Network.id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'TradingGroupNetwork.trading_group_id' => $trading_group_id,
                ),
                'order' => array(
                    'name'
                )
            )
        );
    }

    public function findDatas($network_id, $aagRegionId)
    {
        return $this->find(
            'first',
            array(
                'joins' => array(
                    array(
                        'alias' => 'TradingGroupNetwork',
                        'table' => 'trading_groups_networks',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'TradingGroupNetwork.network_id = Network.id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'Network.id' => $network_id,
                    'Network.aag_region_id' => $aagRegionId,
                ),
                'fields' => array(
                    'Network.*',
                    'group_concat(distinct TradingGroupNetwork.trading_group_id separator ",") as TradingGroupNetwork',
                ),
            )
        );
    }

    public function findInternal($aagRegionId, $excludeNetworkId = null)
    {
        $conditions = array(
            'Network.internal' => ConstantsBooleans::YES,
            'Network.aag_region_id' => $aagRegionId
        );
        if (isset($excludeNetworkId)) {
            $conditions[] = array('Network.id !=' => $excludeNetworkId);
        }
        return $this->find(
            'list',
            array(
                'order' => 'Network.name',
                'conditions' => $conditions
            )
        );
    }

    public function findExternal($aagRegionId)
    {
        return $this->find(
            'list',
            array(
                'order' => 'Network.name',
                'conditions' => array(
                    'OR' => array(
                        'Network.internal !=' => ConstantsBooleans::YES,
                        'Network.internal' => null
                    ),
                    'Network.aag_region_id' => $aagRegionId
                )
            )
        );
    }

    public function findNetworksByType($type)
    {

        if ($type === null) {
            $network_types = array(ConstantsNetworks::HEAVY_COMMERCIAL_VEHICLE, ConstantsNetworks::LIGHT_COMMERCIAL_VEHICLE);
        } elseif ($type == 0) {
            $network_types = array(ConstantsNetworks::LIGHT_COMMERCIAL_VEHICLE,);
        } elseif ($type == 1) {
            $network_types = array(ConstantsNetworks::HEAVY_COMMERCIAL_VEHICLE);
        }

        return $this->find(
            'list',
            array(
                'conditions' => array(
                    'Network.network_type' => $network_types,
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
                        'alias' => 'TradingGroupNetwork',
                        'table' => 'trading_groups_networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'TradingGroupNetwork.network_id = Network.id',
                        ),
                    ),
                    array(
                        'alias' => 'TradingGroup',
                        'table' => 'trading_groups',
                        'type' => 'INNER',
                        'conditions' => array(
                            'TradingGroupNetwork.trading_group_id = TradingGroup.id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'TradingGroup.independent' => ConstantsBooleans::YES,
                    'Network.network_type' => ConstantsDistributorActivity::LV,
                ),
                'order' => 'Network.name',
                'fields' => array(
                    'Network.id',
                    'Network.name',
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
                        'alias' => 'TradingGroupNetwork',
                        'table' => 'trading_groups_networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'TradingGroupNetwork.network_id = Network.id',
                        ),
                    ),
                    array(
                        'alias' => 'TradingGroup',
                        'table' => 'trading_groups',
                        'type' => 'INNER',
                        'conditions' => array(
                            'TradingGroupNetwork.trading_group_id = TradingGroup.id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'TradingGroup.independent' => ConstantsBooleans::YES,
                    'Network.network_type' => ConstantsDistributorActivity::CV,
                ),
                'order' => 'Network.name',
                'fields' => array(
                    'Network.id',
                    'Network.name'
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
                    'Network.id',
                ),
            )
        );
    }

    public function getNetworksByPermissions()
    {
        $position_permissions = CakeSession::read('Auth.User.Permissionsv2Reverse');
        $networks_tmp = array();

        if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN) {
            return $this->find('list', array(
                'joins' => array(
                    array(
                        'alias' => 'GarageNetwork',
                        'table' => 'garages_networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Network.id = GarageNetwork.network_id'
                        )
                    ),
                ),
                'conditions' => array(
                    'Network.aag_region_id' => CakeSession::read('Auth.User.aag_region_id')
                )
            ));
        }


        foreach ($position_permissions[ConstantsPermissionsGrouping::VIEW_NETWORK]['networks_regions'] as $key => $network_region) {
            $networks_tmp[] = key($network_region);
            if (isset($network_region[0])) {
                return $this->find(
                    'list',
                    array(
                        'joins' => array(
                            array(
                                'alias' => 'GarageNetwork',
                                'table' => 'garages_networks',
                                'type' => 'INNER',
                                'conditions' => array(
                                    'Network.id = GarageNetwork.network_id'
                                )
                            ),
                        ),
                    )
                );
            }
        }

        return $this->find('list', array(
            'joins' => array(
                array(
                    'alias' => 'GarageNetwork',
                    'table' => 'garages_networks',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Network.id = GarageNetwork.network_id'
                    )
                ),
            ),
            'conditions' => array(
                'Network.id' => $networks_tmp
            )
        ));
    }

    public function getNetworks()
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
                            'Network.id = GarageNetwork.network_id'
                        )
                    ),
                )
            )
        );
    }

    public function getNetworksConditions($aag_region_id)
    {
        $this->GarageNetwork = ClassRegistry::init('GarageNetwork');
        $garageNetworks = $this->GarageNetwork->find(
            'all',
            array(
                'fields' => array('distinct(network_id)')
            )
        );

        $networksWithGarages = Hash::extract($garageNetworks, '{n}.GarageNetwork.network_id');

        return $this->find(
            'list',
            array(
                'fields' => array(
                    'id',
                    'name'
                ),
                'conditions' => array(
                    'Network.id IN' => $networksWithGarages,
                    'Network.aag_region_id' => $aag_region_id
                )
            )
        );
    }

    public function networksList()
    {
        return $this->find('list', array(
            'fields' => array(
                'id',
                'name'
            )
        ));
    }

    public function findNetworksWithGarages($aagRegionId)
    {
        $position_permissions = CakeSession::read('Auth.User.Permissionsv2Reverse');
        $networks_tmp = array();
        $all = false;
        foreach ($position_permissions[ConstantsPermissionsGrouping::VIEW_NETWORK]['networks_regions'] as $key => $network_region) {
            $networks_tmp[] = key($network_region);
            if (isset($network_region[0])) {
                $all = true;
            }
        }
        if ($all) {
            $conditions = array();
        } else {
            $conditions['Network.id'] = $networks_tmp;
        }

        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'GarageNetwork',
                    'table' => 'garages_networks',
                    'type' => 'INNER',
                    'conditions' => array(
                        'AND' => array(
                            'GarageNetwork.network_id = Network.id',
                            'GarageNetwork.status' => ConstantsNetworksStatus::LIVE,
                        )
                    )
                ),
                array(
                    'alias' => 'Garage',
                    'table' => 'garages',
                    'type' => 'RIGHT',
                    'conditions' => 'Garage.id = GarageNetwork.garage_id'
                ),
            ),
            'conditions' => array(
                $conditions,
                'Garage.aag_region_id' => $aagRegionId,
            ),
            'fields' => array(
                'Network.id',
                'Network.name',
                'Network.image_pin',
                'Network.image_cluster',
                'GarageNetwork.id',
                'Garage.id',
                'Garage.name',
                'Garage.town',
                'Garage.latitude',
                'Garage.longitude',
            ),
        ));
    }

    public function findNetworksWithGaragesDashboard($conditions)
    {
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'GarageNetwork',
                    'table' => 'garages_networks',
                    'type' => 'INNER',
                    'conditions' => array(
                        'AND' => array(
                            'GarageNetwork.network_id = Network.id',
                            'GarageNetwork.status' => ConstantsNetworksStatus::LIVE,
                        )
                    )
                ),
                array(
                    'alias' => 'Garage',
                    'table' => 'garages',
                    'type' => 'RIGHT',
                    'conditions' => 'Garage.id = GarageNetwork.garage_id'
                ),
                array(
                    'alias' => 'City',
                    'table' => 'cities',
                    'type' => 'RIGHT',
                    'conditions' => 'City.id = Garage.city_id'
                ),
                array(
                    'alias' => 'Province',
                    'table' => 'provinces',
                    'type' => 'RIGHT',
                    'conditions' => 'Province.id = City.province_id'
                ),
                array(
                    'alias' => 'Country',
                    'table' => 'countries',
                    'type' => 'RIGHT',
                    'conditions' => 'Country.id = Province.country_id'
                )
            ),
            'conditions' => $conditions,
            'fields' => array(
                'Network.id',
                'Network.name',
                'Network.image_pin',
                'Network.image_cluster',
                'GarageNetwork.id',
                'Garage.id',
                'Garage.name',
                'Garage.town',
                'Garage.latitude',
                'Garage.longitude',
            ),
        ));
    }

    public function getAllByPermissionsAndRegion()
    {
        $position_permissions = CakeSession::read('Auth.User.Permissionsv2Reverse');
        $user = CakeSession::read('Auth.User');
        $user_role_id = $user['role_id'];
        $user_aag_region_id = $user['aag_region_id'];
        $networks_tmp = array();

        if ($user_role_id != ConstantsRoles::SUPER_ADMIN) {
            $conditions = array('Network.aag_region_id' => $user_aag_region_id);
        } else {
            //If the role is super admin we filter by the actual aag_region_id in session
            $aag_region_id = CakeSession::read('Auth.User.aag_region_id');
            $conditions = array();
            if (isset($aag_region_id)) {
                $conditions = array('Network.aag_region_id' => $aag_region_id);
            }
        }

        if ($user_role_id != ConstantsRoles::SUPER_ADMIN) {
            foreach ($position_permissions[ConstantsPermissionsGrouping::VIEW_NETWORK]['networks_regions'] as $key => $network_region) {
                $networks_tmp[] = key($network_region);
                if (isset($network_region[0])) {
                    return $this->find('all', array(
                        'conditions' => $conditions,
                        'order' => array(
                            'Network.name'
                        )
                    ));
                }
            }
        } else {
            //The superadmin is not limited by permissions, can see every network belonging to its actual aag_region
            return $this->find('all', array(
                'conditions' => $conditions,
                'order' => array(
                    'Network.name'
                )
            ));
        }

        return $this->find('all', array(
            'conditions' => array(
                'Network.id' => $networks_tmp,
                'Network.aag_region_id' => $user_aag_region_id
            ),
            'order' => array(
                'Network.name'
            )
        ));
    }

    public function getNetworkNameCreditAndDateByIdNetwork($network_id)
    {
        $query = $this->find('all', array(
            'conditions' => array(
                'Network.id' => $network_id
            ),
            'fields' => array(
                'Network.id',
                'Network.credit',
                'Network.date_restarting_credit',
            ),
        ));

        return $query;
    }

    public function edit_network_credit($network)
    {
        $fields = array(
            'Network' => array(
                'id',
                'credit',
            )
        );

        $network_bd = $this->guardar($network['Network'], $fields);
        if (!$network_bd) {
            return false;
        }

        $this->commit();
        return true;
    }

    public function edit_network_restarting_date($network)
    {
        $fields = array(
            'Network' => array(
                'id',
                'date_restarting_credit',
            )
        );

        $network['Network']['date_restarting_credit'] = Fecha::toFormatoBd($network['Network']['date_restarting_credit']);

        $network_bd = $this->guardar($network['Network'], $fields);
        if (!$network_bd) {
            return false;
        }

        $this->commit();
        return true;
    }

    public function getLeadgenNetworks()
    {
        return $this->find("all", array(
            "fields" => array("Network.id"),
            "conditions" => array(
                "Network.quoting_type_id IS NOT NULL",
                "Network.pricing_type_id IS NOT NULL"
            )
        ));
    }

    public function get_networks_number_garages($conditions)
    {

        $condition_region = [];
        $condition_country = [];
        $condition_network = [];

        if (!empty($conditions)) {
            if (!empty($conditions['aag_region_id'])) {
                $condition_region[] = ['AagRegion.id' => $conditions['aag_region_id']];
            }
            if (!empty($conditions['country_id'])) {
                $condition_country[] = ['Country.id' => $conditions['country_id']];
            }
            if (!empty($conditions['network_id'])) {
                $condition_network[] = ['Network.id' => $conditions['network_id']];
            }
        }

        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'GarageNetwork',
                    'table' => 'garages_networks',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'GarageNetwork.network_id = Network.id',
                    ),
                ),
                array(
                    'alias' => 'Garage',
                    'table' => 'garages',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Garage.id = GarageNetwork.garage_id',
                    ),
                ),
                array(
                    'alias' => 'City',
                    'table' => 'cities',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'City.id = Garage.city_id',
                    ),
                ),
                array(
                    'alias' => 'Province',
                    'table' => 'provinces',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Province.id = City.province_id',
                    ),
                ),
                array(
                    'alias' => 'Country',
                    'table' => 'countries',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Country.id = Province.country_id',
                    ),
                ),
                array(
                    'alias' => 'AagRegion',
                    'table' => 'aag_regions',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'AagRegion.id = Country.aag_region_id',
                    ),
                )
            ),
            'conditions' => array(
                $condition_region,
                $condition_country,
                $condition_network
            ),
            'fields' => array(
                'Network.id, Network.name, Network.image',
                'sum(case when (Network.id) then 1 else 0 end) NumberGarages',
            ),
            'group' => array(
                'Network.id'
            )
        ));
    }

    public function get_networks_with_type()
    {
        return $this->find('list', array(
            'joins' => array(
                array(
                    'alias' => 'GarageNetwork',
                    'table' => 'garages_networks',
                    'type' => 'INNER',
                    'conditions' => array(
                        'GarageNetwork.network_id = Network.id',
                    ),
                ),
                array(
                    'alias' => 'Garage',
                    'table' => 'garages',
                    'type' => 'INNER',
                    'conditions' => array(
                        'GarageNetwork.garage_id = Garage.id',
                    ),
                )
            ),
            'fields' => array(
                'id',
                'network_type'
            ),
            'group' => array(
                'id'
            )
        ));
    }

    public function getNetworksTraining($aag_region_id)
    {
        return $this->find(
            'list',
            array(
                'conditions' => array(
                    'Network.training' => ConstantsBooleans::ACTIVE,
                    'Network.aag_region_id' => $aag_region_id
                ),
            )
        );
    }

    /**
     * Returns the region associated with the network.
     */
    public function get_network_region($networkId)
    {
        switch ($networkId) {
            case NETWORK_ID_AGN:
                return Configure::read('AAG_REGION_ID_UK_IRELAND');

            case NETWORK_ID_GV:
            case NETWORK_ID_GC:
                return Configure::read('AAG_REGION_ID_BENELUX');

            default:
                return null;
        }
    }

    public function getTrainingByNetworkId($network_id)
    {
        return $this->find(
            'first',
            array(
                'conditions' => array(
                    'id' => $network_id,
                ),
                'fields' => array(
                    'training',
                ),
            )
        );
    }

    public function getNameByIdNetwork($network_id)
    {
        $query = $this->find('list', array(
            'conditions' => array(
                'Network.id' => $network_id
            ),
            'fields' => array(
                'Network.id',
                'Network.name',
            ),
        ));

        return $query;
    }

    public function getAllByInternalNetwork($network_id)
    {
        $network = $this->findById($network_id);
        if (!$network) {
            return array();
        }
        $position_permissions = CakeSession::read('Auth.User.Permissionsv2Reverse');
        $networks_tmp = array();
        $conditions = array(
            'Network.aag_region_id' => $network['Network']['aag_region_id'],
            'Network.internal' => true,
        );
        foreach ($position_permissions[ConstantsPermissionsGrouping::VIEW_NETWORK]['networks_regions'] as $key => $network_region) {
            $networks_tmp[] = key($network_region);
            if (isset($network_region[0])) {
                return $this->find('all', array(
                    'conditions' => array_merge($conditions, array('NOT' => array('Network.id' => $network_id))),
                    'order' => array(
                        'Network.name'
                    )
                ));
            }
        }
        return $this->find('all', array(
            'conditions' => array(
                'Network.id' => $networks_tmp,
                'Network.aag_region_id' => $network['Network']['aag_region_id'],
                'Network.internal' => true,
                'NOT' => array('Network.id' => $network_id)
            ),
            'order' => array(
                'Network.name'
            )
        ));
    }

    public function getListByRegion($aag_region_id)
    {
        $conditions = array('Network.aag_region_id' => $aag_region_id);

        return $this->find('list', array(
            'conditions' => $conditions,
            'fields' => array(
                'id',
                'name'
            ),
            'order' => array(
                'name'
            )
        ));
    }

    public function edit_labour_interval($network)
    {
        $fields = array(
            'Network' => array(
                'max_labour_price_ev',
                'min_labour_price_ev',
                'max_labour_price',
                'min_labour_price',
                'slider_increment',
                'slider_increment_ev',
                'modification_date'
            )
        );

        $network['Network']['modification_date'] = date('Y-m-d H:i:s');

        if (!$this->guardar($network, $fields)) {
            return false;
        }
        return true;
    }

    public function edit_mileage_emails_network_by_network_id($network)
    {
        $fields = array(
            'Network' => array(
                'id',
                'default_mileage',
				'email_booking_contact_id',
				'email_enquiry_contact_id',
                'hide_phone_numbers_in_pws',
                'modification_date'
            )
        );

        $network['Network']['modification_date'] = date('Y-m-d H:i:s');

        $network_bd = $this->guardar($network['Network'], $fields);
        if (!$network_bd) {
            return false;
        }
        $this->commit();
        return true;
    }

    public function getNetworkListByRegion($aag_region_id)
    {
        $conditions = array('Network.aag_region_id' => $aag_region_id);

        return $this->find('list', array(
            'conditions' => $conditions,
            'fields' => array(
                'id',
                'name'
            ),
            'order' => array(
                'name'
            )
        ));
    }

    public function getNetworksByRegion($aag_region_id)
    {
        $conditions = array('Network.aag_region_id' => $aag_region_id);

        return $this->find('all', array(
            'conditions' => $conditions
        ));
    }

    public function getNetworksById($network_id)
    {
        $conditions = array('Network.id' => $network_id);

        return $this->find('all', array(
            'conditions' => $conditions
        ));
    }

    public function getNetworksByIds($networks_ids)
    {
        return $this->find('all', array(
            'conditions' => array(
                'Network.id IN' => $networks_ids
            ),
        ));
    }

    public function getNetworksByCountryIds($countries_ids)
    {
        return $this->find('all', array(
            'joins' => array(
                array(
                    'table' => 'countries',
                    'alias' => 'Country',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Country.id IN' => $countries_ids
                    ),
                    'fields' => array(
                        'Country.aag_region_id'
                    )
                )
            ),
            'conditions' => array(
                'Network.aag_region_id = Country.aag_region_id'
            ),
        ));
    }

    public function getListofChildNetworks($parentNetworkId)
    {
        return $this->find(
            'list',
            array(
                'conditions' => array(
                    'Network.parent_network_id' => $parentNetworkId
                ),
                'fields' => array(
                    'Network.id'
                )
            )
        );
    }

    public function getListofChildNetworksWithNames($parentNetworkId)
    {
        return $this->find(
            'list',
            array(
                'conditions' => array(
                    'Network.parent_network_id' => $parentNetworkId
                ),
                'fields' => array(
                    'Network.id',
                    'Network.name'
                )
            )
        );
    }

    public function hasChildNetworks($parentNetworkId)
    {
        $result = $this->find(
            'list',
            array(
                'conditions' => array(
                    'Network.parent_network_id' => $parentNetworkId,
                ),
                'fields' => array(
                    'Network.id'
                )
            )
        );
        return !empty($result);
    }

    public function hasCvChildNetworks($parentNetworkId)
    {
        $result = $this->find(
            'list',
            array(
                'conditions' => array(
                    'Network.parent_network_id' => $parentNetworkId,
                    'Network.network_type' => ConstantsNetworks::HEAVY_COMMERCIAL_VEHICLE
                ),
                'fields' => array(
                    'Network.id'
                )
            )
        );
        return !empty($result);
    }

    public function getListofChildNetworksByNetworkType($parentNetworkId, $networkType = ConstantsNetworks::HEAVY_COMMERCIAL_VEHICLE)
    {
        return $this->find(
            'list',
            array(
                'conditions' => array(
                    'Network.parent_network_id' => $parentNetworkId,
                    'Network.network_type' => $networkType
                ),
                'fields' => array(
                    'Network.id'
                )
            )
        );
    }

    public function getNumberofChildNetworksByNetworkType($parentNetworkId, $networkType = ConstantsNetworks::HEAVY_COMMERCIAL_VEHICLE)
    {
        return count($this->getListofChildNetworksByNetworkType($parentNetworkId, $networkType));
    }

    public function validateGnmChildNetworkId($childNetworkId, $networkId){
        $foundNetwork = $this->findById($childNetworkId);
        $parentNetwork = $this->findById($networkId);

        if (isset($foundNetwork) && !empty($foundNetwork) && isset($parentNetwork) && !empty($parentNetwork)) {
            return $parentNetwork["Network"]["id"] == $foundNetwork["Network"]["parent_network_id"];
        }
        return false;
    }

    public function isCvNetworkById($id){
        $network = $this->findById($id);
        return isset($network["Network"]["network_type"]) && $network["Network"]["network_type"] == ConstantsNetworks::HEAVY_COMMERCIAL_VEHICLE;
    }

}

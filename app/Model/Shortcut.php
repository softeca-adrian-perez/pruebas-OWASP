<?php

class Shortcut extends AppModel
{
    public $useTable = 'shortcuts';

    public $validate = array(
        'title' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_title'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'url' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_an_url'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'image' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_logo'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'shortcut_type_id' =>  array(
            'rule' => 'notBlank',
            'required' => true,
            'message' => 'Validation.Mandatory_to_choose_a_shortcut_type',
        ),
        'start_date' => array(
            'rule' => 'date',
            'dmy',
            'message' => 'Validation.Format_date',
            'allowEmpty' => true
        ),
        'end_date' => array(
            'rule' => 'date',
            'dmy',
            'message' => 'Validation.Format_date',
            'allowEmpty' => true
        ),
        'tooltip' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_TEXT),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'parameter_name_1' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'parameter_name_2' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'code' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_CORTO),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
    );

    private $_queries = array(
        'search' => array(
            'joins' => array(
                array(
                    'alias' => 'ShortcutNetwork',
                    'table' => 'shortcuts_networks',
                    'type' => 'INNER',
                    'conditions' => array(
                        'ShortcutNetwork.shortcut_id = Shortcut.id',
                    ),
                ),
                array(
                    'alias' => 'ShortcutRole',
                    'table' => 'shortcuts_roles',
                    'type' => 'INNER',
                    'conditions' => array(
                        'ShortcutRole.shortcut_id = Shortcut.id',
                    ),
                ),
                array(
                    'alias' => 'ShortcutType',
                    'table' => 'shortcuts_types',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Shortcut.shortcut_type_id = ShortcutType.id',
                    ),
                ),
            ),
            'fields' => array(
                'Shortcut.*',
                'group_concat(distinct ShortcutNetwork.network_id separator ",") as Networks',
                'group_concat(distinct ShortcutRole.role_id separator ",") as Roles',
            ),
            'group' => array(
                'Shortcut.id',
            ),
        ),
        'search_maintenance' => array(
            'joins' => array(
                array(
                    'alias' => 'ShortcutNetwork',
                    'table' => 'shortcuts_networks',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'ShortcutNetwork.shortcut_id = Shortcut.id',
                    ),
                ),
                array(
                    'alias' => 'ShortcutRole',
                    'table' => 'shortcuts_roles',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'ShortcutRole.shortcut_id = Shortcut.id',
                    ),
                ),
                array(
                    'alias' => 'ShortcutType',
                    'table' => 'shortcuts_types',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Shortcut.shortcut_type_id = ShortcutType.id',
                    ),
                ),
                array(
                    'alias' => 'Network',
                    'table' => 'networks',
                    'type' => 'INNER',
                    'conditions' => array(
                        'ShortcutNetwork.network_id = Network.id'
                    )
                ),
            ),
            'fields' => array(
                'Shortcut.*',
                'group_concat(distinct ShortcutNetwork.network_id separator ",") as Networks',
                'group_concat(distinct ShortcutRole.role_id separator ",") as Roles',
            ),
            'group' => array(
                'Shortcut.id',
            ),
            'order' => 'Shortcut.id desc'
        ),
    );

    public function _query($index)
    {
        return $this->_queries[$index];
    }

    public function conditions($fields)
    {
        $conditions = array();
        if (!empty($fields['title'])) {
            $conditions[] = $this->_conditionTitle($fields['title']);
        }
        if (!empty($fields['network'])) {
            $conditions[] = $this->_conditionNetwork($fields['network']);
        }
        if (!empty($fields['role'])) {
            $conditions[] = $this->_conditionRole($fields['role']);
        }
        if (!empty($fields['type'])) {
            $conditions[] = $this->_conditionType($fields['type']);
        }
        if (isset($fields['active'])) {
            $conditions[] = $this->_conditionActive($fields['active']);
        }
        if (isset($fields['aag_region_id'])) {
            $conditions[] = $this->_conditionAagRegion($fields['aag_region_id']);
        }
        return $conditions;
    }

    public function _conditionTitle($title)
    {
        return array('Shortcut.title LIKE' => '%' . $title . '%');
    }

    public function _conditionNetwork($network_id)
    {
        $this->ShortcutNetwork = ClassRegistry::init('ShortcutNetwork');
        $shortcuts_network = $this->ShortcutNetwork->findAllByNetworkId($network_id);
        return array('Shortcut.id' => Hash::extract($shortcuts_network, '{n}.ShortcutNetwork.shortcut_id'));
    }

    public function _conditionRole($role_id)
    {
        $this->ShortcutRole = ClassRegistry::init('ShortcutRole');
        $shortcuts_role = $this->ShortcutRole->findAllByRoleId($role_id);
        return array('Shortcut.id' => Hash::extract($shortcuts_role, '{n}.ShortcutRole.shortcut_id'));
    }

    public function _conditionType($shortcut_type_id)
    {
        return array('Shortcut.shortcut_type_id' => $shortcut_type_id);
    }

    private function _conditionActive($active)
    {
        //If you get a 2, recharge as is.
        if ($active == '1' or $active == '0') {
            return array('Shortcut.active' => $active);
        }
    }

    public function _conditionAagRegion($aag_region_id)
    {
        return array('Network.aag_region_id' => $aag_region_id);
    }

    public function add($shortcut)
    {

        $fields = array(
            'Shortcut' => array(
                'title',
                'url',
                'tooltip',
                'image',
                'shortcut_type_id',
                'start_date',
                'end_date',
                'active',
                'is_sso',
                'parameter_name_1',
                'parameter_name_2',
                'creation_date',
                'without_networks',
                'without_distributor_networks',
                'without_activity',
                'aag_member_yes',
                'aag_member_no',
            )
        );

        if (!empty($shortcut['Shortcut']['url']) && substr($shortcut['Shortcut']['url'], 0, 7) !== "http://" && substr($shortcut['Shortcut']['url'], 0, 8) !== "https://") {
            $shortcut['Shortcut']['url'] = 'http://' . $shortcut['Shortcut']['url'];
        }

        $this->create();
        $shortcut['Shortcut']['creation_date'] = date('Y-m-d H:i:s');
        $shortcut_bd = $this->guardar($shortcut, $fields);
        if (!$shortcut_bd) {
            return false;
        }

        return $shortcut_bd;
    }

    public function edit($shortcut)
    {

        $fields = array(
            'Shortcut' => array(
                'id',
                'title',
                'url',
                'tooltip',
                'shortcut_type_id',
                'start_date',
                'end_date',
                'active',
                'is_sso',
                'parameter_name_1',
                'parameter_name_2',
                'creation_date',
                'without_networks',
                'without_distributor_networks',
                'without_activity',
                'aag_member_yes',
                'aag_member_no',
            )
        );

        if (!empty($shortcut['Shortcut']['new_image'])) {
            $fields['Shortcut'][] = 'image';
        }
        if (!empty($shortcut['Shortcut']['url']) && substr($shortcut['Shortcut']['url'], 0, 7) !== "http://" && substr($shortcut['Shortcut']['url'], 0, 8) !== "https://") {
            $shortcut['Shortcut']['url'] = 'http://' . $shortcut['Shortcut']['url'];
        }
        $shortcut_bd = $this->guardar($shortcut, $fields);
        if (!$shortcut_bd) {
            return false;
        }

        return $shortcut_bd;
    }

    public function findShorcutDatas($shortcut_id)
    {
        return $this->find(
            'first',
            array(
                'joins' => array(
                    array(
                        'alias' => 'ShortcutNetwork',
                        'table' => 'shortcuts_networks',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'ShortcutNetwork.shortcut_id = Shortcut.id',
                        ),
                    ),
                    array(
                        'alias' => 'ShortcutRole',
                        'table' => 'shortcuts_roles',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'ShortcutRole.shortcut_id = Shortcut.id',
                        ),
                    ),
                    array(
                        'alias' => 'ShortcutType',
                        'table' => 'shortcuts_types',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Shortcut.shortcut_type_id = ShortcutType.id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'Shortcut.id' => $shortcut_id,
                ),
                'fields' => array(
                    'Shortcut.*',
                    'group_concat(distinct ShortcutNetwork.network_id separator ",") as Networks',
                    'group_concat(distinct ShortcutRole.role_id separator ",") as Roles',

                ),
                'group' => array(
                    'Shortcut.id',
                ),
            )
        );
    }

    public function findsGaragesShortcuts($role_id, $position, $network_id)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'ShortcutNetwork',
                        'table' => 'shortcuts_networks',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'ShortcutNetwork.shortcut_id = Shortcut.id',
                        ),
                    ),
                    array(
                        'alias' => 'ShortcutRole',
                        'table' => 'shortcuts_roles',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'ShortcutRole.shortcut_id = Shortcut.id',
                        ),
                    ),
                    array(
                        'alias' => 'ShortcutType',
                        'table' => 'shortcuts_types',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Shortcut.shortcut_type_id = ShortcutType.id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'Shortcut.shortcut_type_id' => $position,
                    'ShortcutNetwork.network_id' => $network_id,
                    'ShortcutRole.role_id' => $role_id,
                    'Shortcut.active' => ConstantsBooleans::YES,
                    array(
                        'OR' => array(
                            'start_date' => null,
                            'start_date <=' => date('Y-m-d', strtotime(date('Y-m-d'))),
                        )
                    ),
                    array(
                        'OR' => array(
                            'end_date' => null,
                            'end_date >=' => date('Y-m-d')
                        )
                    ),

                ),
                'fields' => array(
                    'Shortcut.*',
                    'group_concat(distinct ShortcutNetwork.network_id separator ",") as Networks',
                    'group_concat(distinct ShortcutRole.role_id separator ",") as Roles',

                ),
                'group' => array(
                    'Shortcut.id',
                ),
            )
        );
    }

    public function findsGaragesShortcutsPreview($position, $network_id)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'ShortcutNetwork',
                        'table' => 'shortcuts_networks',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'ShortcutNetwork.shortcut_id = Shortcut.id',
                        ),
                    ),
                    array(
                        'alias' => 'ShortcutType',
                        'table' => 'shortcuts_types',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Shortcut.shortcut_type_id = ShortcutType.id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'Shortcut.shortcut_type_id' => $position,
                    'ShortcutNetwork.network_id' => $network_id,
                    'Shortcut.active' => ConstantsBooleans::YES,
                    array(
                        'OR' => array(
                            'start_date' => null,
                            'start_date <=' => date('Y-m-d', strtotime(date('Y-m-d'))),
                        )
                    ),
                    array(
                        'OR' => array(
                            'end_date' => null,
                            'end_date >=' => date('Y-m-d')
                        )
                    ),

                ),
                'fields' => array(
                    'Shortcut.*',
                    'group_concat(distinct ShortcutNetwork.network_id separator ",") as Networks'

                ),
                'group' => array(
                    'Shortcut.id',
                ),
            )
        );
    }

    public function findsDistributorsShortcuts($role_id, $position, $trading_group_id)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'ShortcutTradingGroup',
                        'table' => 'shortcuts_trading_groups',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'ShortcutTradingGroup.shortcut_id = Shortcut.id',
                        ),
                    ),
                    array(
                        'alias' => 'ShortcutRole',
                        'table' => 'shortcuts_roles',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'ShortcutRole.shortcut_id = Shortcut.id',
                        ),
                    ),
                    array(
                        'alias' => 'ShortcutType',
                        'table' => 'shortcuts_types',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Shortcut.shortcut_type_id = ShortcutType.id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'Shortcut.shortcut_type_id' => $position,
                    'ShortcutTradingGroup.trading_group_id' => $trading_group_id,
                    'ShortcutRole.role_id' => $role_id,
                    'Shortcut.active' => ConstantsBooleans::YES,
                    array(
                        'OR' => array(
                            'start_date' => null,
                            'start_date <=' => date('Y-m-d', strtotime(date('Y-m-d'))),
                        )
                    ),
                    array(
                        'OR' => array(
                            'end_date' => null,
                            'end_date >=' => date('Y-m-d')
                        )
                    ),

                ),
                'fields' => array(
                    'Shortcut.*',
                    'group_concat(distinct ShortcutTradingGroup.trading_group_id separator ",") as TradingGroups',
                    'group_concat(distinct ShortcutRole.role_id separator ",") as Roles',

                ),
                'group' => array(
                    'Shortcut.id',
                ),
            )
        );
    }

    public function findsDistributorsShortcutsPreview($position, $trading_group_id)
    {
        return $this->find(
            'first',
            array(
                'joins' => array(
                    array(
                        'alias' => 'ShortcutTradingGroup',
                        'table' => 'shortcuts_trading_groups',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'ShortcutTradingGroup.shortcut_id = Shortcut.id',
                        ),
                    ),
                    array(
                        'alias' => 'ShortcutType',
                        'table' => 'shortcuts_types',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Shortcut.shortcut_type_id = ShortcutType.id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'Shortcut.shortcut_type_id' => $position,
                    'ShortcutTradingGroup.trading_group_id' => $trading_group_id,
                    'Shortcut.active' => ConstantsBooleans::YES,
                    array(
                        'OR' => array(
                            'start_date' => null,
                            'start_date <=' => date('Y-m-d', strtotime(date('Y-m-d'))),
                        )
                    ),
                    array(
                        'OR' => array(
                            'end_date' => null,
                            'end_date >=' => date('Y-m-d')
                        )
                    ),

                ),
                'fields' => array(
                    'Shortcut.*',
                    'group_concat(distinct ShortcutTradingGroup.trading_group_id separator ",") as TradingGroups'

                ),
                'group' => array(
                    'Shortcut.id',
                ),
            )
        );
    }

    public function findsFavoritesShortcuts($user_id, $network_id)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'GarageDistributorShortcut',
                        'table' => 'garages_distributors_shortcuts',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Shortcut.id = GarageDistributorShortcut.shortcut_id',
                        ),
                    ),
                    array(
                        'alias' => 'ShortcutNetwork',
                        'table' => 'shortcuts_networks',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'ShortcutNetwork.shortcut_id = Shortcut.id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'GarageDistributorShortcut.fav' => ConstantsBooleans::YES,
                    'Shortcut.active' => ConstantsBooleans::YES,
                    'GarageDistributorShortcut.user_id' => $user_id,
                    'OR' => array(
                        array(
                            'ShortcutNetwork.network_id' => null,
                        ),
                        array(
                            'ShortcutNetwork.network_id' => $network_id,
                        )
                    ),
                    array(
                        'OR' => array(
                            'start_date' => null,
                            'start_date <=' => date('Y-m-d', strtotime(date('Y-m-d'))),
                        )
                    ),
                    array(
                        'OR' => array(
                            'end_date' => null,
                            'end_date >' => date('Y-m-d')
                        )
                    ),
                ),
                'fields' => array(
                    'Shortcut.*',

                ),
                'group' => array(
                    'Shortcut.id',
                ),
                'order' => array(
                    'Shortcut.title'
                )
            )
        );
    }

    public function findsMyFavoritesShortcuts($user_id, $network_id)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'GarageDistributorShortcut',
                        'table' => 'garages_distributors_shortcuts',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Shortcut.id = GarageDistributorShortcut.shortcut_id',
                        ),
                    ),
                    array(
                        'alias' => 'ShortcutNetwork',
                        'table' => 'shortcuts_networks',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'ShortcutNetwork.shortcut_id = Shortcut.id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'GarageDistributorShortcut.fav' => ConstantsBooleans::YES,
                    'Shortcut.active' => ConstantsBooleans::YES,
                    'GarageDistributorShortcut.user_id' => $user_id,
                    'OR' => array(
                        array(
                            'ShortcutNetwork.network_id' => null,
                        ),
                        array(
                            'ShortcutNetwork.network_id' => $network_id,
                        )
                    ),
                    array(
                        'OR' => array(
                            'start_date' => null,
                            'start_date <=' => date('Y-m-d', strtotime(date('Y-m-d'))),
                        )
                    ),
                    array(
                        'OR' => array(
                            'end_date' => null,
                            'end_date >' => date('Y-m-d')
                        )
                    ),
                ),
                'fields' => array(
                    'Shortcut.*',

                ),
                'group' => array(
                    'Shortcut.id',
                ),
                'order' => array(
                    'Shortcut.title'
                )
            )
        );
    }

    public function getAllByShortcutTypeId($shortcut_type_id, $aag_region_id)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'ShortcutNetwork',
                        'table' => 'shortcuts_networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'ShortcutNetwork.shortcut_id = Shortcut.id'
                        )
                    ),
                    array(
                        'alias' => 'Network',
                        'table' => 'networks',
                        'type' => 'INNER',
                        'conditions' => array(
                            'ShortcutNetwork.network_id = Network.id'
                        )
                    ),
                ),
                'conditions' => array(
                    'Shortcut.shortcut_type_id' => $shortcut_type_id,
                    'Shortcut.active' => ConstantsBooleans::YES,
                    'Network.aag_region_id' => $aag_region_id,
                    array(
                        'OR' => array(
                            'start_date' => null,
                            'start_date <=' => date('Y-m-d', strtotime(date('Y-m-d'))),
                        )
                    ),
                    array(
                        'OR' => array(
                            'end_date' => null,
                            'end_date >' => date('Y-m-d')
                        )
                    ),
                ),
                'group' => array(
                    'Shortcut.id',
                ),
            )
        );
    }

    public function check_delete($shortcut_id)
    {
        $class_array = array(
            $GarageDistributorShortcut = ClassRegistry::init('GarageDistributorShortcut'),
            $ShortcutNetwork = ClassRegistry::init('ShortcutNetwork'),
            $ShortcutRole = ClassRegistry::init('ShortcutRole'),
        );

        foreach ($class_array as $Class) {
            $tmp = $Class->findByShortcutId($shortcut_id);
            if (!empty($tmp)) {
                return false;
            }
        }

        return true;
    }

    public function getShortcutsByPosition($shortcut_type, $networks, $distributors_networks, $activities, $user, $aag_region_id)
    {

        $query = array(
            'joins' => array(
                array(
                    'alias' => 'ShortcutPosition',
                    'table' => 'shortcuts_positions',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Shortcut.id = ShortcutPosition.shortcut_id'
                    )
                ),
            ),
            'conditions' => array(
                'Shortcut.active' => ConstantsBooleans::YES,
                'Shortcut.shortcut_type_id' => $shortcut_type,
                'Shortcut.start_date <' => date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' +1 day')),
                array(
                    'OR' => array(
                        'Shortcut.end_date' => null,
                        'Shortcut.end_date >=' => date('Y-m-d')
                    )
                ),
                'ShortcutPosition.position_id' => $user['Contact']['position_id'],
            ),
            'order' => array(
                'Shortcut.title desc'
            ),
            'fields' => array(
                'Shortcut.*',
            ),
            'group' => array(
                'Shortcut.id',
            ),
        );

        if ($user['Contact']['garage_id']) {
            if ($networks) {
                $query['joins'][] = array(
                    'alias' => 'ShortcutNetwork',
                    'table' => 'shortcuts_networks',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Shortcut.id = ShortcutNetwork.shortcut_id'
                    )
                );
                $query['conditions'][] = array('ShortcutNetwork.network_id' => $networks);
            } else {
                $query['conditions'][] = array('Shortcut.without_networks' => ConstantsBooleans::YES);
            }
        }

        if ($user['Contact']['distributor_id']) {
            $this->Distributor = ClassRegistry::init('Distributor');
            $distributor = $this->Distributor->findById($user['Contact']['distributor_id']);

            if ($distributor['Distributor']['aag_member']) {
                $query['conditions'][] = array('Shortcut.aag_member_yes' => ConstantsBooleans::YES);
            } else {
                $query['conditions'][] = array('Shortcut.aag_member_no' => ConstantsBooleans::YES);
            }

            $query['joins'][] = array(
                'alias' => 'ShortcutTradingGroup',
                'table' => 'shortcuts_trading_groups',
                'type' => 'INNER',
                'conditions' => array(
                    'Shortcut.id = ShortcutTradingGroup.shortcut_id'
                )
            );
            $query['conditions'][] = array('ShortcutTradingGroup.trading_group_id' => $distributor['Distributor']['trading_group_id']);

            if ($distributors_networks) {
                $query['joins'][] = array(
                    'alias' => 'ShortcutDistributionNetwork',
                    'table' => 'shortcuts_distributors_networks',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Shortcut.id = ShortcutDistributionNetwork.shortcut_id'
                    )
                );
                $query['conditions'][] = array('ShortcutDistributionNetwork.distributor_network_id' => $distributors_networks);
            } else {
                $query['conditions'][] = array('Shortcut.without_distributor_networks' => ConstantsBooleans::YES);
            }
        }

        if ($activities) {
            $query['joins'][] = array(
                'alias' => 'ShortcutCustomerActivity',
                'table' => 'shortcuts_customers_activities',
                'type' => 'INNER',
                'conditions' => array(
                    'Shortcut.id = ShortcutCustomerActivity.shortcut_id'
                )
            );
            $query['conditions'][] = array('ShortcutCustomerActivity.customer_activity_id' => $activities);
        } else {
            $query['conditions'][] = array('Shortcut.without_activity' => ConstantsBooleans::YES);
        }

        return $this->find('all', $query);
    }
}

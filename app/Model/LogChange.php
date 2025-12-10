<?php

class LogChange extends AppModel
{
    public $useTable = 'logs_changes';

    public $validate = array(
        'contact' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR_CORTO),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'old_value' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'new_value' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
    );

    var $hasOne = array(
        'LogTable',
        'LogField',
        'User',
        'Garage'
    );

    private $_queries = array(
        'search' => array(
            'joins' => array(
                array(
                    'alias' => 'LogTable',
                    'table' => 'logs_tables',
                    'type' => 'INNER',
                    'conditions' => array(
                        'LogTable.id = LogChange.table_id',
                    ),
                ),
                array(
                    'alias' => 'LogField',
                    'table' => 'logs_fields',
                    'type' => 'INNER',
                    'conditions' => array(
                        'LogField.id = LogChange.field_id',
                    ),
                ),
                array(
                    'alias' => 'User',
                    'table' => 'users',
                    'type' => 'INNER',
                    'conditions' => array(
                        'User.id = LogChange.user_id',
                    ),
                ),
                array(
                    'alias' => 'GarageTable',
                    'table' => 'garages',
                    'type' => 'INNER',
                    'conditions' => array(
                        'GarageTable.id = LogChange.garage_id',
                    ),
                ),
            ),
            'fields' => array(
                'LogTable.*',
                'LogField.*',
                'User.name',
                'User.surname',
                'LogChange.*'
            ),
            'order' => 'LogChange.id desc'
        ),
        'search_distributors' => array(
            'joins' => array(
                array(
                    'alias' => 'LogTable',
                    'table' => 'logs_tables',
                    'type' => 'INNER',
                    'conditions' => array(
                        'LogTable.id = LogChange.table_id',
                    ),
                ),
                array(
                    'alias' => 'LogField',
                    'table' => 'logs_fields',
                    'type' => 'INNER',
                    'conditions' => array(
                        'LogField.id = LogChange.field_id',
                    ),
                ),
                array(
                    'alias' => 'User',
                    'table' => 'users',
                    'type' => 'INNER',
                    'conditions' => array(
                        'User.id = LogChange.user_id',
                    ),
                ),
                array(
                    'alias' => 'Distributor',
                    'table' => 'distributors',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Distributor.id = LogChange.distributor_id',
                    ),
                ),
            ),
            'fields' => array(
                'LogTable.*',
                'LogField.*',
                'User.name',
                'LogChange.*'
            ),
            'order' => 'LogChange.id desc'
        ),
        'search_table_position' => array(
            'joins' => array(
                array(
                    'alias' => 'User',
                    'table' => 'users',
                    'type' => 'INNER',
                    'conditions' => array(
                        'User.id = LogChange.user_id',
                    ),
                ),
            ),
            'fields' => array(
                'User.name',
                'LogChange.*'
            ),
            'order' => 'LogChange.id desc'
        ),
        'search_table_permission' => array(
            'joins' => array(
                array(
                    'alias' => 'LogTable',
                    'table' => 'logs_tables',
                    'type' => 'INNER',
                    'conditions' => array(
                        'LogTable.id = LogChange.table_id',
                    ),
                ),
                array(
                    'alias' => 'LogField',
                    'table' => 'logs_fields',
                    'type' => 'INNER',
                    'conditions' => array(
                        'LogField.id = LogChange.field_id',
                    ),
                ),
                array(
                    'alias' => 'User',
                    'table' => 'users',
                    'type' => 'INNER',
                    'conditions' => array(
                        'User.id = LogChange.user_id',
                    ),
                ),
                array(
                    'alias' => 'GroupPermission',
                    'table' => 'groups_permissions',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'GroupPermission.id = LogChange.group_permission_id',
                    ),
                ),
            ),
            'fields' => array(
                'LogTable.*',
                'LogField.*',
                'User.name',
                'GroupPermission.*',
                'LogChange.*'
            ),
            'order' => 'LogChange.id desc'
        ),
        'search_table_group_permission' => array(
            'joins' => array(
                array(
                    'alias' => 'LogTable',
                    'table' => 'logs_tables',
                    'type' => 'INNER',
                    'conditions' => array(
                        'LogTable.id = LogChange.table_id',
                    ),
                ),
                array(
                    'alias' => 'LogField',
                    'table' => 'logs_fields',
                    'type' => 'INNER',
                    'conditions' => array(
                        'LogField.id = LogChange.field_id',
                    ),
                ),
                array(
                    'alias' => 'User',
                    'table' => 'users',
                    'type' => 'INNER',
                    'conditions' => array(
                        'User.id = LogChange.user_id',
                    ),
                ),
                array(
                    'alias' => 'Position',
                    'table' => 'positions',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Position.id = LogChange.position_id',
                    ),
                ),
            ),
            'fields' => array(
                'LogTable.*',
                'LogField.*',
                'User.name',
                'LogChange.*',
                'Position.*'
            ),
            'order' => 'LogChange.id desc'
        ),
    );

    public function conditions($fields)
    {
        $conditions = array();
        if (!empty($fields['table'])) {
            $conditions[] = $this->_conditionTable($fields['table']);
        }
        if (!empty($fields['field'])) {
            $conditions[] = $this->_conditionField($fields['field']);
        }
        if (!empty($fields['user'])) {
            $conditions[] = $this->_conditionUser($fields['user']);
        }
        if (!empty($fields['old_value'])) {
            $conditions[] = $this->_conditionOldValue($fields['old_value']);
        }
        if (!empty($fields['new_value'])) {
            $conditions[] = $this->_conditionNewValue($fields['new_value']);
        }
        if (!empty($fields['from'])) {
            $conditions[] = $this->_conditionDateFrom($fields['from']);
        }
        if (!empty($fields['to'])) {
            $conditions[] = $this->_conditionDateTo($fields['to']);
        }
        if (!empty($fields['contact'])) {
            $conditions[] = $this->_conditionContact($fields['contact']);
        }
        if (!empty($fields['group_permission'])) {
            $conditions[] = $this->_conditionGroupPermission($fields['group_permission']);
        }
        if (!empty($fields['position'])) {
            $conditions[] = $this->_conditionPosition($fields['position']);
        }
        return $conditions;
    }

    private function _conditionTable($table)
    {
        return array('LogTable.name LIKE' => '%' . $table . '%');
    }

    private function _conditionField($field)
    {
        return array('LogField.name LIKE' => '%' . $field . '%');
    }

    private function _conditionUser($user)
    {
        return array('User.name LIKE' => '%' . $user . '%');
    }

    private function _conditionOldValue($old_value)
    {
        return array('LogChange.old_value LIKE' => '%' . $old_value . '%');
    }

    private function _conditionNewValue($new_value)
    {
        return array('LogChange.new_value LIKE' => '%' . $new_value . '%');
    }

    private function _conditionDateFrom($from_date)
    {
        return array('LogChange.date >=' => Fecha::toFormatoBd($from_date));
    }

    private function _conditionDateTo($to_date)
    {
        return array('LogChange.date <=' => Fecha::toFormatoBd($to_date));
    }

    private function _conditionContact($contact)
    {
        return array('LogChange.contact LIKE' => '%' . $contact . '%');
    }

    private function _conditionGroupPermission($group_permission)
    {
        return array('GroupPermission.name' . __s() . '  LIKE' => '%' . $group_permission . '%');
    }
    private function _conditionPosition($position)
    {
        return array('Position.name' . __s() . '  LIKE' => '%' . $position . '%');
    }

    public function _query($index)
    {
        return $this->_queries[$index];
    }

    public function save_log($table_id, $field_id, $user_id, $customer_id, $old_value, $new_value, $customer_type)
    {
        if ($old_value == '') {
            $old_value = 'Logs.Not_created';
        }
        if ($new_value == '') {
            $new_value = 'Logs.Deleted';
        }
        $new_log = array(
            'LogChange' => array(
                'table_id' => $table_id,
                'field_id' => $field_id,
                'user_id' => $user_id,
                'date' => date('Y-m-d H:i:s'),
                'old_value' => $old_value,
                'new_value' => $new_value,
            )
        );
        if ($customer_type == ConstantsLogType::GARAGE) {
            $new_log['LogChange']['garage_id'] = $customer_id;
        } elseif ($customer_type == ConstantsLogType::DISTRIBUTOR) {
            $new_log['LogChange']['distributor_id'] = $customer_id;
        } elseif ($customer_type == ConstantsLogType::CONTACT) {
            $new_log['LogChange']['contact'] = $customer_id;
        } elseif ($customer_type == ConstantsLogType::GROUP_PERMISSION) {
            $new_log['LogChange']['group_permission_id'] = $customer_id;
        } elseif ($customer_type == ConstantsLogType::PERMISSION) {
            $new_log['LogChange']['position_id'] = $customer_id;
        }

        $this->create();
        $this->save($new_log);
    }

    public function get_last_changes_by_garage($garage_id)
    {
        $tmp = $this->_query('search');
        $tmp['conditions'] = array(
            'LogChange.garage_id' => $garage_id,
        );
        $tmp['order'] = array(
            'LogChange.date' => 'desc'
        );
        $tmp['limit'] = ConstantsPagination::SIZE_PAGE_SMALL;
        return $this->find('all', $tmp);
    }

    public function get_last_changes_by_distributor($distributor_id)
    {
        $tmp = $this->_query('search_distributors');
        $tmp['conditions'] = array(
            'LogChange.distributor_id' => $distributor_id,
        );
        $tmp['order'] = array(
            'LogChange.date' => 'desc'
        );
        $tmp['limit'] = ConstantsPagination::SIZE_PAGE_SMALL;
        return $this->find('all', $tmp);
    }

    public function get_params_create_log_add($new_data, $table_name, $user, $customer_id, $customer_type)
    {
        foreach ($new_data as $key => $new_value) {
            if (!is_array($new_value)) {
                if ($key != ConstantsExclude::MODIFICATION_GARAGE && $key != ConstantsExclude::ID && $key != ConstantsExclude::GARAGE_ID && $key != ConstantsExclude::LOCO) {
                    $table = $this->LogTable->findByName($table_name);

                    $field = $this->LogField->find('first', array(
                        'conditions' => array(
                            'name' => $key,
                            'table_id' => $table['LogTable']['id']
                        )
                    ));
                    if (isset($field) && !empty($field)) {
                        $value = $this->check_value($table_name, $key, null, $new_value);
                        $new_value = $value[1];

                        $this->save_log(
                            $table['LogTable']['id'],
                            $field['LogField']['id'],
                            $user['User']['id'],
                            $customer_id,
                            "Logs.Not_created",
                            $new_value,
                            $customer_type
                        );
                    }
                }
            }
        }
    }

    public function get_params_create_log_edit($old_data, $new_data, $table_name, $user, $customer_id, $customer_type, $field_name = null)
    {
        foreach ($new_data as $key => $new_value) {
            if (!in_array($key, Configure::read('Exclude_logs'))) {
                //We compare string values instead of numeric values
                if (isset($old_data[$key]) && strval($old_data[$key]) != 'Principal' && strval($old_data[$key]) != 'Not Principal') {
                    $old_value = $old_data[$key];
                } else {
                    $old_value = '';
                }
                if ($old_value == null) {
                    $old_value = '';
                }

                if ($old_value != $new_value && $key != 'country') {
                    $table = $this->LogTable->findByName($table_name);
                    if ($table) {
                        if ($field_name != null) {
                            $field = $this->LogField->find('first', array(
                                'conditions' => array(
                                    'name' => $field_name,
                                    'table_id' => $table['LogTable']['id']
                                )
                            ));
                            $check_value = $field_name;
                        } else {
                            $field = $this->LogField->find('first', array(
                                'conditions' => array(
                                    'name' => $key,
                                    'table_id' => $table['LogTable']['id']
                                )
                            ));
                            $check_value = $key;
                        }
                        if ($field) {
                            $value = $this->check_value($table_name, $check_value, $old_value, $new_value);
                            $old_value = $value[0];
                            $new_value = $value[1];
                            if ($old_value != $new_value) {
                                $this->save_log(
                                    $table['LogTable']['id'],
                                    $field['LogField']['id'],
                                    $user['User']['id'],
                                    $customer_id,
                                    $old_value,
                                    $new_value,
                                    $customer_type
                                );
                            }
                        }
                    }
                }
            }
        }
        if ($field_name != null) {
            $old_ones = array_diff($old_data, $new_data);
            foreach ($old_ones as $old_one) {
                $table = $this->LogTable->findByName($table_name);
                $field = $this->LogField->find('first', array(
                    'conditions' => array(
                        'name' => $field_name,
                        'table_id' => $table['LogTable']['id']
                    )
                ));
                if ($field) {
                    $value = $this->check_value($table_name, $field_name, $old_one, null);
                    $old_one = $value[0];

                    $this->save_log(
                        $table['LogTable']['id'],
                        $field['LogField']['id'],
                        $user['User']['id'],
                        $customer_id,
                        $old_one,
                        "Logs.Deleted",
                        $customer_type
                    );
                }
            }
        }
    }

    public function get_params_create_log_delete($old_data, $table_name, $user, $customer_id, $customer_type)
    {
        foreach ($old_data as $key => $old_value) {
            if ($key != ConstantsExclude::MODIFICATION_GARAGE && $key != ConstantsExclude::ID && $key != ConstantsExclude::GARAGE_ID && $key != ConstantsExclude::LOCO) {
                $table = $this->LogTable->findByName($table_name);

                $field = $this->LogField->find('first', array(
                    'conditions' => array(
                        'name' => $key,
                        'table_id' => $table['LogTable']['id']
                    )
                ));

                if ($field) {
                    $value = $this->check_value($table_name, $key, $old_value, null);
                    $old_value = $value[0];

                    $this->save_log(
                        $table['LogTable']['id'],
                        $field['LogField']['id'],
                        $user['User']['id'],
                        $customer_id,
                        $old_value,
                        "Logs.Deleted",
                        $customer_type
                    );
                }
            }
        }
    }

    public function get_params_create_log_service($table_name, $field_name, $user, $customer_id, $old_data, $new_data, $customer_type)
    {
        $this->begin();
        $table = $this->LogTable->findByName($table_name);
        $field = $this->LogField->find('first', array(
            'conditions' => array(
                'name' => $field_name,
                'table_id' => $table['LogTable']['id']
            )
        ));

        $this->save_log(
            $table['LogTable']['id'],
            $field['LogField']['id'],
            $user['User']['id'],
            $customer_id,
            $old_data,
            $new_data,
            $customer_type
        );

        $this->commit();
    }

    public function add_contact_log($table_name, $user, $customer_id, $customer_type, $contact_id)
    {
        $table = $this->LogTable->findByName($table_name);
        $field = $this->LogField->find('first', array(
            'conditions' => array(
                'name' => 'contact_id',
                'table_id' => $table['LogTable']['id']
            )
        ));

        $this->Contact = ClassRegistry::init('Contact');
        $contact = $this->Contact->findById($contact_id);

        $this->save_log(
            $table['LogTable']['id'],
            $field['LogField']['id'],
            $user['User']['id'],
            $customer_id,
            $contact['Contact']['full_name'] . ' - ' . __t("Logs.Not_associated"),
            $contact['Contact']['full_name'] . ' - ' . __t("Logs.Associated"),
            $customer_type
        );
    }

    public function remove_contact_log($table_name, $user, $customer_id, $customer_type, $contact_id)
    {
        $table = $this->LogTable->findByName($table_name);
        $field = $this->LogField->find('first', array(
            'conditions' => array(
                'name' => 'contact_id',
                'table_id' => $table['LogTable']['id']
            )
        ));

        $this->Contact = ClassRegistry::init('Contact');
        $contact = $this->Contact->findById($contact_id);

        $this->save_log(
            $table['LogTable']['id'],
            $field['LogField']['id'],
            $user['User']['id'],
            $customer_id,
            $contact['Contact']['full_name'] . ' - ' . __t("Logs.Associated"),
            $contact['Contact']['full_name'] . ' - ' . __t("Logs.Not_associated"),
            $customer_type
        );
    }

    public function save_logs_contact_add($new_position, $user_id, $positions_logs, $position_id)
    {
        $this->Position = ClassRegistry::init('Position');
        unset($new_position['Position']['id']);
        foreach ($new_position['Position'] as $key => $new_value) {
            if ($key != ConstantsExclude::LOCO) {
                if (!is_array($new_value) && isset($field['LogField'])) {
                    $table = $this->LogTable->findByName($this->Position->table);

                    $field = $this->LogField->find('first', array(
                        'conditions' => array(
                            'name' => $key,
                            'table_id' => $table['LogTable']['id']
                        )
                    ));

                    $value = $this->check_value($this->Position->table, $key, null, $new_value);
                    $old_value = 'Logs.Not_created';
                    $new_value = $value[1];

                    $this->save_log(
                        $table['LogTable']['id'],
                        $field['LogField']['id'],
                        $user_id,
                        null,
                        $old_value,
                        $new_value,
                        null
                    );
                }
            }
        }

        foreach ($positions_logs as $position_log) {
            $table = $this->LogTable->findByName($this->Position->table);
            $this->save_log(
                $table['LogTable']['id'],
                29,
                $user_id,
                $position_id,
                'Logs.Not_created',
                $position_log,
                null
            );
        }
    }

    public function save_logs_contact_edit($old_position, $new_position, $user_id, $positions_logs, $position_id)
    {
        $this->Position = ClassRegistry::init('Position');
        unset($old_position['Position']['config_length']);
        foreach ($old_position['Position'] as $key => $old_value) {
            if ($key != ConstantsExclude::LOCO) {
                if (!is_array($old_value) && $new_position['Position'][$key] != $old_value) {
                    $table = $this->LogTable->findByName($this->Position->table);

                    $field = $this->LogField->find('first', array(
                        'conditions' => array(
                            'name' => $key,
                            'table_id' => $table['LogTable']['id']
                        )
                    ));

                    $value = $this->check_value($this->Position->table, $key, $old_value, $new_position['Position'][$key]);
                    $old_value = $value[0];
                    $new_value = $value[1];
                    $this->save_log(
                        $table['LogTable']['id'],
                        $field['LogField']['id'],
                        $user_id,
                        $position_id,
                        $old_value,
                        $new_value,
                        ConstantsLogType::PERMISSION
                    );
                }
            }
        }

        foreach ($positions_logs as $position_log) {
            $table = $this->LogTable->findByName($this->Position->table);

            $this->save_log(
                $table['LogTable']['id'],
                29,
                $user_id,
                $position_id,
                'Logs.Not_created',
                $position_log,
                ConstantsLogType::PERMISSION
            );
        }
    }

    public function save_logs_contact_delete($old_position, $new_position, $user_id)
    {
        $this->Position = ClassRegistry::init('Position');
        unset($old_position['Position']['id']);
        foreach ($old_position['Position'] as $key => $old_value) {
            if ($key != ConstantsExclude::LOCO) {
                if (!is_array($old_value)) {
                    $table = $this->LogTable->findByName($this->Position->table);

                    $field = $this->LogField->find('first', array(
                        'conditions' => array(
                            'name' => $key,
                            'table_id' => $table['LogTable']['id']
                        )
                    ));
                    $value = $this->check_value($this->Position->table, $key, $old_value, null);
                    $old_value = $value[0];
                    $new_value = $value[1];

                    $this->save_log(
                        $table['LogTable']['id'],
                        $field['LogField']['id'],
                        $user_id,
                        null,
                        $old_value,
                        'Logs.Deleted',
                        null
                    );
                }
            }
        }
    }

    public function check_value($table_name, $field_name, $old_value, $new_value)
    {
        $log_options = Configure::read('Constants_Logs_Options');
        $log_options_translations = Configure::read('Constants_Logs_Options_Translations');

        if (array_key_exists($field_name, Configure::read('Constants_Logs_Options_Translations'))) {
            $model = ClassRegistry::init($log_options_translations[$field_name]);
            if (!is_null($old_value) && !empty($old_value)) {
                $old_value_name = $model->findById($old_value);
                $old_value = $old_value_name[$log_options_translations[$field_name]]['name' . __s()];
            }
            if (!is_null($new_value) && !empty($new_value)) {
                $new_value_name = $model->findById($new_value);
                $new_value = $new_value_name[$log_options_translations[$field_name]]['name' . __s()];
            }
        } elseif (array_key_exists($field_name, Configure::read('Constants_Logs_Options'))) {
            $model = ClassRegistry::init($log_options[$field_name]);
            if (!is_null($old_value) && !empty($old_value)) {
                $old_value_name = $model->findById($old_value);
                $old_value = $old_value_name[$log_options[$field_name]]['name'];
            }
            if (!is_null($new_value) && !empty($new_value)) {
                $new_value_name = $model->findById($new_value);
                $new_value = $new_value_name[$log_options[$field_name]]['name'];
            }
        } elseif (array_key_exists($table_name, Configure::read('Constants_Logs_Options'))) {
            $model = ClassRegistry::init($log_options[$table_name]);
            if (!is_null($old_value) && !empty($old_value)) {
                $old_value_name = $model->findById($old_value);
                $old_value = $old_value_name[$log_options[$table_name]]['name' . __s()];
            }
            if (!is_null($new_value) && !empty($new_value)) {
                $new_value_name = $model->findById($new_value);
                $new_value = $new_value_name[$log_options[$table_name]]['name' . __s()];
            }
        } elseif ($field_name == ConstantsLogsOptions::TYPE && $table_name == 'distributors_customer_activities') {

            if (!is_null($new_value) && $new_value == ConstantsBooleans::YES) {
                $new_value = __t('Distributor.Workshop');
            } elseif (!is_null($new_value) && $new_value == ConstantsBooleans::NO) {
                $new_value = __t('Distributor.Distributor');
            }

            if (!is_null($old_value) && $old_value == ConstantsBooleans::YES) {
                $old_value = __t('Distributor.Workshop');
            } elseif (!is_null($old_value) && $old_value == ConstantsBooleans::NO) {
                $old_value = __t('Distributor.Distributor');
            }
        } elseif ($field_name == ConstantsLogsOptions::STATUS && ($table_name == 'garages_networks' || $table_name == 'garages_agreements')) {
            $status = Configure::read('Network_Status');
            foreach ($status as $key => $network_status) {
                $status[$key] = __t($network_status);
            }
            $new_value = !is_null($new_value) && !empty($new_value) ? $status[$new_value] : null;
            $old_value = !is_null($old_value) && !empty($old_value) ? $status[$old_value] : null;
        } elseif ($field_name == ConstantsLogsOptions::STATUS) {
            $status = Configure::read('Garage_Status');
            foreach ($status as $key => $garage_status) {
                $status[$key] = __t($garage_status);
            }

            $new_value = !is_null($new_value) && !empty($new_value) ? $status[$new_value] : null;
            $old_value = !is_null($old_value) && !empty($old_value) ? $status[$old_value] : null;
        } elseif ($field_name == ConstantsLogsOptions::IS_CV) {
            $new_value = !is_null($new_value) && !empty($new_value) ? ConstantsLogsTranslations::CV : ConstantsLogsTranslations::LV;
            $old_value = !is_null($old_value) && !empty($old_value) ? ConstantsLogsTranslations::CV : ConstantsLogsTranslations::LV;
        } elseif ($field_name == ConstantsLogsOptions::PRINCIPAL) {
            $new_value = !is_null($new_value) && !empty($new_value) ? __t(ConstantsLogsTranslations::PRINCIPAL) : __t(ConstantsLogsTranslations::NOT_PRINCIPAL);
            $old_value = !is_null($old_value) && !empty($old_value) ? __t(ConstantsLogsTranslations::PRINCIPAL) : __t(ConstantsLogsTranslations::NOT_PRINCIPAL);
        } elseif (in_array($field_name, Configure::read('ConstantsLogsOptionsChecks'))) {
            $new_value = !is_null($new_value) && !empty($new_value) ? __t('General.Active') : __t('General.No_active');
            $old_value = !is_null($old_value) && !empty($old_value) ? __t('General.Active') : __t('General.No_active');
        } elseif ($field_name == ConstantsLogsOptions::LEAD_SOURCE) {
            $lead_sources = Configure::read('lead_source');
            foreach ($lead_sources as $key => $lead_source) {
                $lead_sources[$key] = __t($lead_source);
            }

            $new_value = !is_null($new_value) && !empty($new_value) ? $lead_sources[$new_value] : null;
            $old_value = !is_null($old_value) && !empty($old_value) ? $lead_sources[$old_value] : null;
        }

        return array($old_value, $new_value);
    }
}

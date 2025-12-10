<?php

class RequestedChange extends AppModel
{
    public $useTable = 'requested_changes';

    public $hasOne = array(
        'LogTable',
        'LogField',
        'User',
        'Garage',
        'Distributor'
    );

    public $hasMany = array(
        'RequestedChangeImage',
    );

    public $validate = array(
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
        'section' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'description' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_TEXT),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
    );

    private $_queries = array(
        'search' => array(
            'joins' => array(
                array(
                    'alias' => 'LogTable',
                    'table' => 'logs_tables',
                    'type' => 'INNER',
                    'conditions' => array(
                        'LogTable.id = RequestedChange.table_id',
                    ),
                ),
                array(
                    'alias' => 'LogField',
                    'table' => 'logs_fields',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'LogField.id = RequestedChange.field_id',
                    ),
                ),
                array(
                    'alias' => 'User',
                    'table' => 'users',
                    'type' => 'INNER',
                    'conditions' => array(
                        'User.id = RequestedChange.user_id',
                    ),
                ),
                array(
                    'alias' => 'GarageTable',
                    'table' => 'garages',
                    'type' => 'INNER',
                    'conditions' => array(
                        'GarageTable.id = RequestedChange.garage_id',
                    ),
                ),
            ),
            'fields' => array(
                'LogTable.*',
                'LogField.*',
                'User.name',
                'RequestedChange.*'
            ),
            'order' => 'RequestedChange.id desc'
        ),
        'garage_changes_email' => array(
            'joins' => array(
                array(
                    'alias' => 'LogTable',
                    'table' => 'logs_tables',
                    'type' => 'INNER',
                    'conditions' => array(
                        'LogTable.id = RequestedChange.table_id',
                    ),
                ),
                array(
                    'alias' => 'LogField',
                    'table' => 'logs_fields',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'LogField.id = RequestedChange.field_id',
                    ),
                ),
                array(
                    'alias' => 'User',
                    'table' => 'users',
                    'type' => 'INNER',
                    'conditions' => array(
                        'User.id = RequestedChange.user_id',
                    ),
                ),
                array(
                    'alias' => 'GarageTable',
                    'table' => 'garages',
                    'type' => 'INNER',
                    'conditions' => array(
                        'GarageTable.id = RequestedChange.garage_id',
                    ),
                ),
            ),
            'fields' => array(
                'LogTable.*',
                'LogField.*',
                'User.name',
                'RequestedChange.*',
                'GarageTable.name'
            ),
            'order' => array(
                'COALESCE(RequestedChange.field_id, RequestedChange.section) ASC',
                'RequestedChange.date DESC'
            ),
        ),
        'search_distributors' => array(
            'joins' => array(
                array(
                    'alias' => 'LogTable',
                    'table' => 'logs_tables',
                    'type' => 'INNER',
                    'conditions' => array(
                        'LogTable.id = RequestedChange.table_id',
                    ),
                ),
                array(
                    'alias' => 'LogField',
                    'table' => 'logs_fields',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'LogField.id = RequestedChange.field_id',
                    ),
                ),
                array(
                    'alias' => 'User',
                    'table' => 'users',
                    'type' => 'INNER',
                    'conditions' => array(
                        'User.id = RequestedChange.user_id',
                    ),
                ),
                array(
                    'alias' => 'Distributor',
                    'table' => 'distributors',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Distributor.id = RequestedChange.distributor_id',
                    ),
                ),
            ),
            'fields' => array(
                'LogTable.*',
                'LogField.*',
                'User.name',
                'RequestedChange.*'
            ),
            'order' => 'RequestedChange.id desc'
        ),
        'distributor_changes_email' => array(
            'joins' => array(
                array(
                    'alias' => 'LogTable',
                    'table' => 'logs_tables',
                    'type' => 'INNER',
                    'conditions' => array(
                        'LogTable.id = RequestedChange.table_id',
                    ),
                ),
                array(
                    'alias' => 'LogField',
                    'table' => 'logs_fields',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'LogField.id = RequestedChange.field_id',
                    ),
                ),
                array(
                    'alias' => 'User',
                    'table' => 'users',
                    'type' => 'INNER',
                    'conditions' => array(
                        'User.id = RequestedChange.user_id',
                    ),
                ),
                array(
                    'alias' => 'Distributor',
                    'table' => 'distributors',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Distributor.id = RequestedChange.distributor_id',
                    ),
                ),
            ),
            'fields' => array(
                'LogTable.*',
                'LogField.*',
                'User.name',
                'RequestedChange.*',
                'Distributor.name'
            ),
            'order' => array(
                'COALESCE(RequestedChange.field_id, RequestedChange.section) ASC',
                'RequestedChange.date DESC'
            ),
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
        return array('RequestedChange.old_value LIKE' => '%' . $old_value . '%');
    }

    private function _conditionNewValue($new_value)
    {
        return array('RequestedChange.new_value LIKE' => '%' . $new_value . '%');
    }

    private function _conditionDateFrom($from_date)
    {
        return array('RequestedChange.date >=' => Fecha::toFormatoBd($from_date));
    }

    private function _conditionDateTo($to_date)
    {
        return array('RequestedChange.date <=' => Fecha::toFormatoBd($to_date));
    }

    public function _query($index)
    {
        return $this->_queries[$index];
    }

    // public function create_request_edit_garage_multiples( $old_data, $new_data, $table_name, $user, $customer_id, $customer_type, $field_name ){
    //     $this->begin();
    //     $result = true;
    //     $table = $this->LogTable->findByName($table_name);
    //     $field = $this->LogField->find('first', array(
    //         'conditions' => array(
    //             'name' => $field_name,
    //             'table_id' => $table['LogTable']['id']
    //         )
    //     ));
    //     $diff = array_merge(
    //         array_diff($old_data, $new_data),
    //         array_diff($new_data, $old_data)
    //     );
    //     if(!empty($diff)){
    //         $result = $this->save_request(
    //             $table['LogTable']['id'],
    //             $field['LogField']['id'],
    //             $user['User']['id'],
    //             $customer_id,
    //             implode(';',$old_data),
    //             implode(';',$new_data),
    //             $customer_type
    //         );
    //         $this->commit();
    //     }
    //     return $result;
    // }

    public function create_request_edit_description($table_name, $section, $user_id, $customer_id, $description, $customer_type)
    {
        $result = true;
        if ($description == '') {
            return $result;
        }
        $table = $this->LogTable->findByName($table_name);
        $new_request = array(
            'RequestedChange' => array(
                'table_id' => $table['LogTable']['id'],
                'section' => $section,
                'user_id' => $user_id,
                'date' => date('Y-m-d H:i:s'),
                'description' => $description,
                'sent' => ConstantsBooleans::NO,
            )
        );
        if ($customer_type == ConstantsLogType::GARAGE) {
            $new_request['RequestedChange']['garage_id'] = $customer_id;
        } else if ($customer_type == ConstantsLogType::DISTRIBUTOR) {
            $new_request['RequestedChange']['distributor_id'] = $customer_id;
        }

        $this->create();
        return $this->save($new_request);
    }

    public function create_request_edit($old_data, $new_data, $table_name, $user, $customer_id, $customer_type, $field_name = null)
    {
        $result = array(true);
        foreach ($new_data as $key => $new_value) {
            if (!in_array($key, Configure::read('Exclude_requested_change'))) {
                if (isset($old_data[$key]) && $old_data[$key] != 'Principal' && $old_data[$key] != 'Not Principal') {
                    $old_value = $old_data[$key];
                } else {
                    $old_value = '';
                }
                if ($old_value == null) {
                    $old_value = '';
                }

                $this->begin();
                if ($old_value != $new_value && $key != 'country') {
                    $table = $this->LogTable->findByName($table_name);
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
                    $value = $this->check_value($table_name, $check_value, $old_value, $new_value);
                    $old_value = $value[0];
                    $new_value = $value[1];
                    if ($old_value != $new_value) {
                        $result[] = $this->save_request(
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
                $this->commit();
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

                $value = $this->check_value($table_name, $field_name, $old_one, null);
                $old_one = $value[0];

                $result[] = $this->save_request(
                    $table['LogTable']['id'],
                    $field['LogField']['id'],
                    $user['User']['id'],
                    $customer_id,
                    $old_one,
                    "RequestedChanges.Deleted",
                    $customer_type
                );
            }
        }
        return !in_array(false, $result);
    }

    public function save_request($table_id, $field_id, $user_id, $customer_id, $old_value, $new_value, $customer_type)
    {
        if ($old_value == '') {
            $old_value = 'RequestedChanges.Not_created';
        }
        if ($new_value == '') {
            $new_value = 'RequestedChanges.Deleted';
        }
        $new_request = array(
            'RequestedChange' => array(
                'table_id' => $table_id,
                'field_id' => $field_id,
                'user_id' => $user_id,
                'date' => date('Y-m-d H:i:s'),
                'old_value' => $old_value,
                'new_value' => $new_value,
                'sent' => ConstantsBooleans::NO,
            )
        );
        if ($customer_type == ConstantsLogType::GARAGE) {
            $new_request['RequestedChange']['garage_id'] = $customer_id;
        } else if ($customer_type == ConstantsLogType::DISTRIBUTOR) {
            $new_request['RequestedChange']['distributor_id'] = $customer_id;
        }

        $this->create();
        return $this->save($new_request);
    }

    public function get_last_changes_by_garage($garage_id)
    {
        $tmp = $this->_query('search');
        $tmp['conditions'] = array(
            'RequestedChange.garage_id' => $garage_id,
        );
        $tmp['order'] = array(
            'RequestedChange.date' => 'desc'
        );
        $tmp['limit'] = ConstantsPagination::SIZE_PAGE_SMALL;
        return $this->find('all', $tmp);
    }

    public function get_last_changes_by_distributor($distributor_id)
    {
        $tmp = $this->_query('search_distributors');
        $tmp['conditions'] = array(
            'RequestedChange.distributor_id' => $distributor_id,
        );
        $tmp['order'] = array(
            'RequestedChange.date' => 'desc'
        );
        $tmp['limit'] = ConstantsPagination::SIZE_PAGE_SMALL;
        return $this->find('all', $tmp);
    }

    public function get_all_changes_by_garage($garage_id)
    {
        $tmp = $this->_query('garage_changes_email');
        $tmp['conditions'] = array(
            'RequestedChange.garage_id' => $garage_id,
            'RequestedChange.sent' => ConstantsBooleans::NO,
        );
        $changes_tmp = $this->find('all', $tmp);
        $changes = array();
        foreach ($changes_tmp as $change) {
            if (!is_null($change['RequestedChange']['field_id'])) {
                if (!Hash::check($changes, '{n}.RequestedChange[field_id=' . $change['RequestedChange']['field_id'] . ']') || in_array($change['RequestedChange']['field_id'], Configure::read('request_fields_send_all'))) {
                    $changes[] = $change;
                }
            } elseif (!Hash::check($changes, '{n}.RequestedChange[section=' . $change['RequestedChange']['section'] . ']')) {
                $changes[] = $change;
            }
        }
        return $changes;
    }

    public function get_all_changes_by_distributor($distributor_id)
    {
        $tmp = $this->_query('distributor_changes_email');
        $tmp['conditions'] = array(
            'RequestedChange.distributor_id' => $distributor_id,
            'RequestedChange.sent' => ConstantsBooleans::NO,
        );
        $changes_tmp = $this->find('all', $tmp);
        $changes = array();
        foreach ($changes_tmp as $change) {
            if (!is_null($change['RequestedChange']['field_id'])) {
                if (!Hash::check($changes, '{n}.RequestedChange[field_id=' . $change['RequestedChange']['field_id'] . ']') || in_array($change['RequestedChange']['field_id'], Configure::read('request_fields_send_all'))) {
                    $changes[] = $change;
                }
            } elseif (!Hash::check($changes, '{n}.RequestedChange[section=' . $change['RequestedChange']['section'] . ']')) {
                $changes[] = $change;
            }
        }
        return $changes;
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
        } else if (array_key_exists($field_name, Configure::read('Constants_Logs_Options'))) {
            $model = ClassRegistry::init($log_options[$field_name]);
            if (!is_null($old_value) && !empty($old_value)) {
                $old_value_name = $model->findById($old_value);
                $old_value = $old_value_name[$log_options[$field_name]]['name'];
            }
            if (!is_null($new_value) && !empty($new_value)) {
                $new_value_name = $model->findById($new_value);
                $new_value = $new_value_name[$log_options[$field_name]]['name'];
            }
        } else if (array_key_exists($table_name, Configure::read('Constants_Logs_Options'))) {
            $model = ClassRegistry::init($log_options[$table_name]);
            if (!is_null($old_value) && !empty($old_value)) {
                $old_value_name = $model->findById($old_value);
                $old_value = $old_value_name[$log_options[$table_name]]['name' . __s()];
            }
            if (!is_null($new_value) && !empty($new_value)) {
                $new_value_name = $model->findById($new_value);
                $new_value = $new_value_name[$log_options[$table_name]]['name' . __s()];
            }
        }

        if ($field_name == ConstantsLogsOptions::STATUS && $table_name == 'garages_networks') {
            $status = Configure::read('Network_Status');
            foreach ($status as $key => $network_status) {
                $status[$key] = __t($network_status);
            }
            $new_value = !is_null($new_value) && !empty($new_value) ? $status[$new_value] : null;
            $old_value = !is_null($old_value) && !empty($old_value) ? $status[$old_value] : null;
        } else if ($field_name == ConstantsLogsOptions::STATUS) {
            $status = Configure::read('Garage_Status');
            foreach ($status as $key => $garage_status) {
                $status[$key] = __t($garage_status);
            }

            $new_value = !is_null($new_value) && !empty($new_value) ? $status[$new_value] : null;
            $old_value = !is_null($old_value) && !empty($old_value) ? $status[$old_value] : null;
        } else if ($field_name == ConstantsLogsOptions::IS_CV) {
            $new_value = !is_null($new_value) && !empty($new_value) ? ConstantsLogsTranslations::CV : ConstantsLogsTranslations::LV;
            $old_value = !is_null($old_value) && !empty($old_value) ? ConstantsLogsTranslations::CV : ConstantsLogsTranslations::LV;
        } else if ($field_name == ConstantsLogsOptions::PRINCIPAL) {
            $new_value = !is_null($new_value) && !empty($new_value) ? __t(ConstantsLogsTranslations::PRINCIPAL) : __t(ConstantsLogsTranslations::NOT_PRINCIPAL);
            $old_value = !is_null($old_value) && !empty($old_value) ? __t(ConstantsLogsTranslations::PRINCIPAL) : __t(ConstantsLogsTranslations::NOT_PRINCIPAL);
        } else if (in_array($field_name, Configure::read('ConstantsLogsOptionsChecks'))) {
            $new_value = !is_null($new_value) && !empty($new_value) ? __t('General.Active') : __t('General.No_active');
            $old_value = !is_null($old_value) && !empty($old_value) ? __t('General.Active') : __t('General.No_active');
        } else if ($field_name == ConstantsLogsOptions::LEAD_SOURCE) {
            $lead_sources = Configure::read('lead_source');
            foreach ($lead_sources as $key => $lead_source) {
                $lead_sources[$key] = __t($lead_source);
            }
            $new_value = !is_null($new_value) && !empty($new_value) ? $lead_source[$new_value] : null;
            $old_value = !is_null($old_value) && !empty($old_value) ? $lead_source[$old_value] : null;
        }

        return array($old_value, $new_value);
    }


    public function createEmails()
    {
        $this->Email = ClassRegistry::init('Email');
        $this->GarageContactBdm = ClassRegistry::init('GarageContactBdm');
        $this->DistributorContactBdm = ClassRegistry::init('DistributorContactBdm');
        $this->Garage = ClassRegistry::init('Garage');
        $garages_id = $this->find(
            'all',
            array(
                'fields' => array('DISTINCT RequestedChange.garage_id'),
                'conditions' => array(
                    'RequestedChange.garage_id IS NOT NULL'
                )
            )
        );
        $distributors_id = $this->find(
            'all',
            array(
                'fields' => array('DISTINCT RequestedChange.distributor_id'),
                'conditions' => array(
                    'RequestedChange.distributor_id IS NOT NULL'
                )
            )
        );
        foreach ($garages_id as $garage_id) {
            $this->begin();
            $this->unbindModel(
                array('hasOne' => array('LogTable', 'LogField', 'Garage', 'Distributor', 'User'))
            );
            $this->updateAll(
                array('RequestedChange.sent' => ConstantsBooleans::YES),
                array(
                    'RequestedChange.sent' => ConstantsBooleans::NO,
                    'RequestedChange.garage_id' => $garage_id['RequestedChange']['garage_id'],
                )
            );
            $this->commit();
        }
        foreach ($distributors_id as $distributor_id) {
            $this->begin();
            $this->unbindModel(
                array('hasOne' => array('LogTable', 'LogField', 'Garage', 'Distributor', 'User'))
            );
            $this->updateAll(
                array('RequestedChange.sent' => ConstantsBooleans::YES),
                array(
                    'RequestedChange.sent' => ConstantsBooleans::NO,
                    'RequestedChange.distributor_id' => $distributor_id['RequestedChange']['distributor_id'],
                )
            );
            $this->commit();
        }
    }
}

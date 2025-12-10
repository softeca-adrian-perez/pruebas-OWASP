<?php

class DistributorCustomerActivity extends AppModel
{

    public $useTable = 'distributors_customer_activities';


    public $validate = array(
        'activity_details' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
    );

    public function add_activity($activity, $distributor_id)
    {
        $fields = array(
            'DistributorCustomerActivity' => array(
                'customer_activity_id',
                'distributor_id',
                'type',
                'start_date',
                'end_date',
                'modification_date',
            )
        );

        $activity['DistributorCustomerActivity']['distributor_id'] = $distributor_id;
        $activity['DistributorCustomerActivity']['start_date'] = Fecha::toFormatoBD($activity['DistributorCustomerActivity']['start_date']);
        $activity['DistributorCustomerActivity']['end_date'] = Fecha::toFormatoBD($activity['DistributorCustomerActivity']['end_date']);

        $activity['DistributorCustomerActivity']['modification_date'] = date('Y-m-d H:i:s');

        $this->create();
        $activity_bd = $this->guardar($activity, $fields);
        if (!$activity_bd) {
            return false;
        }

        $this->commit();
        return $activity_bd;
    }

    public function edit_activity($activity, $distributor_id)
    {
        $fields = array(
            'DistributorCustomerActivity' => array(
                'id',
                'customer_activity_id',
                'distributor_id',
                'type',
                'start_date',
                'end_date',
                'modification_date',
            )
        );

        $activity['DistributorCustomerActivity']['distributor_id'] = $distributor_id;
        $activity['DistributorCustomerActivity']['start_date'] = Fecha::toFormatoBD($activity['DistributorCustomerActivity']['start_date']);
        $activity['DistributorCustomerActivity']['end_date'] = Fecha::toFormatoBD($activity['DistributorCustomerActivity']['end_date']);

        $activity['DistributorCustomerActivity']['modification_date'] = date('Y-m-d H:i:s');

        $activity_bd = $this->guardar($activity, $fields);
        if (!$activity_bd) {
            return false;
        }

        $this->commit();
        return $activity_bd;
    }

    public function getActivitiesByDistributor($distributor_id)
    {
        return $this->find('list', array(
            'fields' => array(
                'id',
                'customer_activity_id',
            ),
            'conditions' => array(
                'DistributorCustomerActivity.distributor_id' => $distributor_id
            ),
        ));
    }

    public function createDisActivity($distributor_json, $distributor_id)
    { // New DistributorCustomerActivity comes from FRANCE JSON

        if (isset($distributor_json['activite'])) {
            foreach ($distributor_json['activite'] as $activity => $value) {
                if ($value == true) {
                    $customer_activity_bd = '';
                    if (
                        ($activity == 'vl') ||
                        ($activity == 'pl') ||
                        ($activity == 'peinture') ||
                        ($activity == 'industrie') ||
                        ($activity == 'carrosserie')

                    ) {
                        $this->CustomerActivity = ClassRegistry::init('CustomerActivity');
                        $customer_activity_bd = $this->CustomerActivity->findByNameFr($activity);

                        //Create $customer_activity
                        if (empty($customer_activity_bd)) {
                            //If the activity doesn't exist, we create it.
                            $activity_new = array(
                                'CustomerActivity' => array(
                                    'name_en' => $activity,
                                    'name_fr' => $activity,
                                    'name_de' => $activity,
                                )
                            );
                            $this->CustomerActivity->create();
                            $customer_activity_bd = $this->CustomerActivity->save($activity_new);
                            if (!$customer_activity_bd) {
                                CakeLog::write('updates-france', 'The customer activity could not be created.' . PHP_EOL);
                            }
                        }
                        if ($distributor_json['gefa'] == true && $activity == 'peinture') {
                            $customer_activity_bd = $this->findByNameFr('carrosserie');
                        }

                        //Create distributor_customer_activity
                        $distributor_customer_activity_tmp = array(
                            'DistributorCustomerActivity' => array(
                                'customer_activity_id' => $customer_activity_bd['CustomerActivity']['id'],
                                'distributor_id' => $distributor_id,
                                'type' => ConstantsBooleans::NO, //Distributor
                            )
                        );

                        $this->create();
                        $distributor_customer_activity_bd = $this->save($distributor_customer_activity_tmp);
                        if (!$distributor_customer_activity_bd) {
                            CakeLog::write('updates-france', 'The distributor customer activity could not be created.' . PHP_EOL);
                        }
                    }

                    if ($activity == 'atelier') {
                        if ($distributor_json['activite']['pl']) {
                            $customer_activity_bd =  $this->CustomerActivity->findByNameFr('pl');
                            $distributor_customer_activity_tmp = array(
                                'DistributorCustomerActivity' => array(
                                    'customer_activity_id' => $customer_activity_bd['CustomerActivity']['id'],
                                    'distributor_id' => $distributor_id,
                                    'type' => ConstantsBooleans::YES, //ATELIER
                                )
                            );

                            $this->create();
                            $distributor_customer_activity_bd = $this->save($distributor_customer_activity_tmp);
                            if (!$distributor_customer_activity_bd) {
                                CakeLog::write('updates-france', 'The distributor customer activity could not be created.' . PHP_EOL);
                            }
                        } else {
                            $customer_activity_bd = $customer_activity_model->findByNameFr('vl');
                            $distributor_customer_activity_tmp = array(
                                'DistributorCustomerActivity' => array(
                                    'customer_activity_id' => $customer_activity_bd['CustomerActivity']['id'],
                                    'distributor_id' => $distributor_id,
                                    'type' => ConstantsBooleans::YES, //ATELIER
                                )
                            );

                            $this->create();
                            $distributor_customer_activity_bd = $this->save($distributor_customer_activity_tmp);
                            if (!$distributor_customer_activity_bd) {
                                CakeLog::write('updates-france', 'The distributor customer activity could not be created.' . PHP_EOL);
                            }
                        }
                    }
                }
            }
            $this->commit();
        }

        return true;
    }

    public function updateDisActivity($distributor_json, $distributor_exist)
    { // New DistributorCustomerActivity comes from FRANCE JSON

        if (isset($distributor_json['activite'])) {
            foreach ($distributor_json['activite'] as $activity => $value) {
                if ($value == true) {
                    $customer_activity_bd = '';
                    if (
                        ($activity == 'vl') ||
                        ($activity == 'pl') ||
                        ($activity == 'peinture') ||
                        ($activity == 'industrie') ||
                        ($activity == 'carrosserie')

                    ) {
                        $this->CustomerActivity = ClassRegistry::init('CustomerActivity');
                        $customer_activity_bd = $this->CustomerActivity->findByNameFr($activity);

                        //Create $customer_activity
                        if (empty($customer_activity_bd)) {
                            //If the activity doesn't exist, we create it.
                            $activity_new = array(
                                'CustomerActivity' => array(
                                    'name_en' => $activity,
                                    'name_fr' => $activity,
                                    'name_de' => $activity,
                                )
                            );
                            $this->CustomerActivity->create();
                            $customer_activity_bd = $this->CustomerActivity->save($activity_new);
                            if (!$customer_activity_bd) {
                                CakeLog::write('updates-france', 'The customer activity could not be created.' . PHP_EOL);
                            }
                        }

                        if ($distributor_json['gefa'] == true && $activity == 'peinture') {
                            $customer_activity_bd = $this->findByNameFr('carrosserie');
                        }


                        $distributor_customer_activity_exist = $this->findByDistributorIdAndCustomerActivityIdAndType($distributor_exist['Distributor']['id'], $customer_activity_bd['CustomerActivity']['id'], 0);
                        if (!$distributor_customer_activity_exist) {
                            //Create distributor_customer_activity
                            $distributor_customer_activity_tmp = array(
                                'DistributorCustomerActivity' => array(
                                    'customer_activity_id' => $customer_activity_bd['CustomerActivity']['id'],
                                    'distributor_id' => $distributor_exist['Distributor']['id'],
                                    'type' => ConstantsBooleans::NO, //Distributor
                                )
                            );

                            $this->create();
                            $distributor_customer_activity_bd = $this->save($distributor_customer_activity_tmp);
                            if (!$distributor_customer_activity_bd) {
                                CakeLog::write('updates-france', 'The distributor customer activity could not be created.' . PHP_EOL);
                            }
                        }
                    }

                    if ($activity == 'atelier') {

                        if ($distributor_json['activite']['pl']) {

                            $customer_activity_bd =  $this->CustomerActivity->findByNameFr('pl');
                            $distributor_customer_activity_exist = $this->findByDistributorIdAndCustomerActivityIdAndType($distributor_exist['Distributor']['id'], $customer_activity_bd['CustomerActivity']['id'], 1);
                            if (!$distributor_customer_activity_exist) {
                                $distributor_customer_activity_tmp = array(
                                    'DistributorCustomerActivity' => array(
                                        'customer_activity_id' => $customer_activity_bd['CustomerActivity']['id'],
                                        'distributor_id' => $distributor_exist['Distributor']['id'],
                                        'type' => ConstantsBooleans::YES, //ATELIER
                                    )
                                );

                                $this->create();
                                $distributor_customer_activity_bd = $this->save($distributor_customer_activity_tmp);
                                if (!$distributor_customer_activity_bd) {
                                    CakeLog::write('updates-france', 'The distributor customer activity could not be created.' . PHP_EOL);
                                }
                            }
                        } else {

                            $customer_activity_bd = $customer_activity_model->findByNameFr('vl');
                            $distributor_customer_activity_exist = $this->findByDistributorIdAndCustomerActivityIdAndType($distributor_exist['Distributor']['id'], $customer_activity_bd['CustomerActivity']['id'], 1);
                            if (!$distributor_customer_activity_exist) {
                                $distributor_customer_activity_tmp = array(
                                    'DistributorCustomerActivity' => array(
                                        'customer_activity_id' => $customer_activity_bd['CustomerActivity']['id'],
                                        'distributor_id' => $distributor_exist['Distributor']['id'],
                                        'type' => ConstantsBooleans::YES, //ATELIER
                                    )
                                );

                                $this->create();
                                $distributor_customer_activity_bd = $this->save($distributor_customer_activity_tmp);
                                if (!$distributor_customer_activity_bd) {
                                    CakeLog::write('updates-france', 'The distributor customer activity could not be created.' . PHP_EOL);
                                }
                            }
                        }
                    }
                }
            }
            $this->commit();
        }
        return true;
    }

    /**
     * The Distributor Activities comes from a UK JSON.
     */
    public function createDistributorCustomerActivity($distributor_json, $distributor_id)
    {
        if ($distributor_json['LV'] == true) { //Automotive
            $activity_id = 1;
            $distributor_distributor_activity_tmp = array(
                'DistributorCustomerActivity' => array(
                    'distributor_id' => $distributor_id,
                    'customer_activity_id' => $activity_id,
                    'start_date' => isset($distributor_json['LVJoinDate']) ? substr($distributor_json['LVJoinDate'], 0, 10) : null,
                    'end_date' => isset($distributor_json['LVLeftDate']) ? substr($distributor_json['LVLeftDate'], 0, 10) : null,
                )
            );
            $this->create();
            if (!$this->save($distributor_distributor_activity_tmp)) {
                CakeLog::write('updates', 'LV activity creation failed' . PHP_EOL);
            }
        }

        if ($distributor_json['CV'] == true) { //Commercial Vehicle
            $activity_id = 2;
            $distributor_distributor_activity_tmp = array(
                'DistributorCustomerActivity' => array(
                    'distributor_id' => $distributor_id,
                    'customer_activity_id' => $activity_id,
                    'start_date' => isset($distributor_json['CVJoinDate']) ? substr($distributor_json['CVJoinDate'], 0, 10) : null,
                    'end_date' => isset($distributor_json['CVLeftDate']) ? substr($distributor_json['CVLeftDate'], 0, 10) : null,
                )
            );
            $this->create();
            if (!$this->save($distributor_distributor_activity_tmp)) {
                CakeLog::write('updates', 'CV activity creation failed' . PHP_EOL);
            }
        }

        if ($distributor_json['Refinish'] == true) { //Refinish
            $activity_id = 3;
            $distributor_distributor_activity_tmp = array(
                'DistributorCustomerActivity' => array(
                    'distributor_id' => $distributor_id,
                    'customer_activity_id' => $activity_id,
                    'start_date' => isset($distributor_json['RefinishJoinDate']) ? substr($distributor_json['RefinishJoinDate'], 0, 10) : null,
                    'end_date' => isset($distributor_json['RefinishDate']) ? substr($distributor_json['RefinishDate'], 0, 10) : null,
                )
            );
            $this->create();
            if (!$this->save($distributor_distributor_activity_tmp)) {
                CakeLog::write('updates', 'Refinish activity creation failed' . PHP_EOL);
            }
        }

        if ($distributor_json['Retail'] == true) { //Retail
            $activity_id = 4;
            $distributor_distributor_activity_tmp = array(
                'DistributorCustomerActivity' => array(
                    'distributor_id' => $distributor_id,
                    'customer_activity_id' => $activity_id,
                    'start_date' => isset($distributor_json['RetailJoinDate']) ? substr($distributor_json['RetailJoinDate'], 0, 10) : null,
                    'end_date' => isset($distributor_json['RetailLeftDate']) ? substr($distributor_json['RetailLeftDate'], 0, 10) : null,
                )
            );
            $this->create();
            if (!$this->save($distributor_distributor_activity_tmp)) {
                CakeLog::write('updates', 'Retail activity creation failed' . PHP_EOL);
            }
        }

        if ($distributor_json['Service'] == true) { //Service
            $activity_id = 5;
            $distributor_distributor_activity_tmp = array(
                'DistributorCustomerActivity' => array(
                    'distributor_id' => $distributor_id,
                    'customer_activity_id' => $activity_id,
                    'start_date' => isset($distributor_json['ServiceJoinDate']) ? substr($distributor_json['ServiceJoinDate'], 0, 10) : null,
                    'end_date' => isset($distributor_json['ServiceLeftDate']) ? substr($distributor_json['ServiceLeftDate'], 0, 10) : null,
                )
            );
            $this->create();
            if (!$this->save($distributor_distributor_activity_tmp)) {
                CakeLog::write('updates', 'Service activity creation failed' . PHP_EOL);
            }
        }
    }

    /**
     * The Distributor Activities comes from a UK JSON
     */
    public function updateDistributorCustomerActivity($distributor_json, $distributor_id)
    {
        $all_activities = $this->findAllByDistributorId($distributor_id);

        foreach ($all_activities as $activity) { //Delete all activities for this distributor
            $this->delete($activity['DistributorCustomerActivity']['id']);
        }

        if ($distributor_json['LV'] == true) { //Automotive
            $activity_id = 1;
            $distributor_distributor_activity_tmp = array(
                'DistributorCustomerActivity' => array(
                    'distributor_id' => $distributor_id,
                    'customer_activity_id' => $activity_id,
                    'start_date' => isset($distributor_json['LVJoinDate']) ? substr($distributor_json['LVJoinDate'], 0, 10) : null,
                    'end_date' => isset($distributor_json['LVLeftDate']) ? substr($distributor_json['LVLeftDate'], 0, 10) : null,
                )
            );
            $this->create();
            if (!$this->save($distributor_distributor_activity_tmp)) {
                CakeLog::write('updates', 'LV activity creation failed' . PHP_EOL);
            }
        }

        if ($distributor_json['CV'] == true) { //Commercial Vehicle
            $activity_id = 2;
            $distributor_distributor_activity_tmp = array(
                'DistributorCustomerActivity' => array(
                    'distributor_id' => $distributor_id,
                    'customer_activity_id' => $activity_id,
                    'start_date' => isset($distributor_json['CVJoinDate']) ? substr($distributor_json['CVJoinDate'], 0, 10) : null,
                    'end_date' => isset($distributor_json['CVLeftDate']) ? substr($distributor_json['CVLeftDate'], 0, 10) : null,
                )
            );
            $this->create();
            if (!$this->save($distributor_distributor_activity_tmp)) {
                CakeLog::write('updates', 'CV activity creation failed' . PHP_EOL);
            }
        }

        if ($distributor_json['Refinish'] == true) { //Refinish
            $activity_id = 3;
            $distributor_distributor_activity_tmp = array(
                'DistributorCustomerActivity' => array(
                    'distributor_id' => $distributor_id,
                    'customer_activity_id' => $activity_id,
                    'start_date' => isset($distributor_json['RefinishJoinDate']) ? substr($distributor_json['RefinishJoinDate'], 0, 10) : null,
                    'end_date' => isset($distributor_json['RefinishDate']) ? substr($distributor_json['RefinishDate'], 0, 10) : null,
                )
            );
            $this->create();
            if (!$this->save($distributor_distributor_activity_tmp)) {
                CakeLog::write('updates', 'Refinish activity creation failed' . PHP_EOL);
            }
        }

        if ($distributor_json['Retail'] == true) { //Retail
            $activity_id = 4;
            $distributor_distributor_activity_tmp = array(
                'DistributorCustomerActivity' => array(
                    'distributor_id' => $distributor_id,
                    'customer_activity_id' => $activity_id,
                    'start_date' => isset($distributor_json['RetailJoinDate']) ? substr($distributor_json['RetailJoinDate'], 0, 10) : null,
                    'end_date' => isset($distributor_json['RetailLeftDate']) ? substr($distributor_json['RetailLeftDate'], 0, 10) : null,
                )
            );
            $this->create();
            if (!$this->save($distributor_distributor_activity_tmp)) {
                CakeLog::write('updates', 'Retail activity creation failed' . PHP_EOL);
            }
        }

        if ($distributor_json['Service'] == true) { //Service
            $activity_id = 5;
            $distributor_distributor_activity_tmp = array(
                'DistributorCustomerActivity' => array(
                    'distributor_id' => $distributor_id,
                    'customer_activity_id' => $activity_id,
                    'start_date' => isset($distributor_json['ServiceJoinDate']) ? substr($distributor_json['ServiceJoinDate'], 0, 10) : null,
                    'end_date' => isset($distributor_json['ServiceLeftDate']) ? substr($distributor_json['ServiceLeftDate'], 0, 10) : null,
                )
            );
            $this->create();
            if (!$this->save($distributor_distributor_activity_tmp)) {
                CakeLog::write('updates', 'Service activity creation failed' . PHP_EOL);
            }
        }
    }

	/**
	 * T001 SECURITY - It is not changed because it does not receive variable parameters per call.
	*/
    public function resetAutoIncrement()
    {
        $sql = "SET @count = 0;UPDATE distributors_customer_activities SET distributors_customer_activities.id = @count:= @count + 1;";
        $this->query($sql);
        $sql2 = "ALTER TABLE `distributors_customer_activities` AUTO_INCREMENT = 1;";
        $this->query($sql2);
    }
}

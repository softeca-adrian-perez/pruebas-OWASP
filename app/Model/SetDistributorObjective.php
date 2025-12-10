<?php
App::uses('DistributorObjective', 'Model');

class SetDistributorObjective extends AppModel{
    public $useTable = 'set_distributors_objectives';

    public function add($data) 
    {
		$fields = array(
			'SetDistributorObjective' => array(
				'distributor_array',
				'distributor_array_updated',
				'objective_id',
				'from',
				'to',
				'creation_date',
				'modification_date',
				'is_set',
				'user_id',
				'email_to',
			)
		);
		$this->create();

		$set_dis_obj_bd = $this->guardar($data, $fields);
		if (!$set_dis_obj_bd) {
			return false;
		}

		$this->commit();
		return $set_dis_obj_bd;
	}

    public function edit($data)
	{
		$fields = array(
			'SetDistributorObjective' => array(
				'distributor_array_updated',
				'modification_date',
				'is_set',
			)
		);

		$set_dis_obj_bd = $this->guardar($data, $fields);
		if (!$set_dis_obj_bd) {
			return false;
		}

		$this->commit();
		return true;
	}

	public function setObjectives()
	{
		$DistributorObjective = ClassRegistry::init('DistributorObjective');

		$array_not_set = $this->find('first', array(
			'conditions' => array(
                'SetDistributorObjective.is_set' => ConstantsBooleans::NO
            ),
            'fields' => array(
                'SetDistributorObjective.*',
            ),
        ));

		if ($array_not_set) {
			$distributorArray = json_decode($array_not_set['SetDistributorObjective']['distributor_array_updated']);
			if (count((array)$distributorArray) < ConstantsObjectives::LIMIT_CREATE) {
				$foreach_array = $distributorArray;
			}else {
				$foreach_array = array_slice((array)$distributorArray, 0, ConstantsObjectives::LIMIT_CREATE);
				$remaining_array = array_slice((array)$distributorArray, ConstantsObjectives::LIMIT_CREATE);
			}

			foreach ($foreach_array as $distributor_id) {
				$DistributorObjective->add($distributor_id, $array_not_set['SetDistributorObjective']['objective_id'], Fecha::toFormatoVista($array_not_set['SetDistributorObjective']['from']), Fecha::toFormatoVista($array_not_set['SetDistributorObjective']['to']));
			}

			if (!isset($remaining_array) || count($remaining_array) === 0) {
				$data = array();
				$data['SetDistributorObjective']['id'] = $array_not_set['SetDistributorObjective']['id'];
				$data['SetDistributorObjective']['distributor_array_updated'] = NULL;
				$data['SetDistributorObjective']['modification_date'] = date('Y-m-d H:i:s');
				$data['SetDistributorObjective']['is_set'] = ConstantsBooleans::YES;
				$this->edit($data);

				if ($data['SetDistributorObjective']['is_set'] == ConstantsBooleans::YES) {
					$this->generate_objectives_csv($array_not_set);
				}

			}else {
				$data = array();
				$data['SetDistributorObjective']['id'] = $array_not_set['SetDistributorObjective']['id'];
				$data['SetDistributorObjective']['distributor_array_updated'] = json_encode($remaining_array);
				$data['SetDistributorObjective']['modification_date'] = date('Y-m-d H:i:s');
				$data['SetDistributorObjective']['is_set'] = ConstantsBooleans::NO;
				$this->edit($data);
			}
		}

		return true;
	}

	/**
     * Create a new Objective Completed email csv.
     */

    public function generate_objectives_csv($data)
    {
        $this->User = ClassRegistry::init('User');
        $this->Distributor = ClassRegistry::init('Distributor');
        $this->Email = ClassRegistry::init('Email');
        $this->AppointmentObjective = ClassRegistry::init('AppointmentObjective');

		$user = $this->User->findById($data['SetDistributorObjective']['user_id']);
        $objective = $this->AppointmentObjective->findById($data['SetDistributorObjective']['objective_id']);
        $objectiveName = $objective['AppointmentObjective']['name'];
        $userCountryId = $user['User']['country_id'];
        $userAagRegionId = $user['User']['aag_region_id'];
        $userName = $user['User']['full_name'];

		$languageCode = ConstantsLanguages::ENGLISH_CODE;
		if (isset($user['User']['language_id'])) {
			$language = $this->User->Language->findById($user['User']['language_id']);
			if ($language) {
				$languageCode = $language['Language']['code'];
			}
		}

        $complete_date = date('Ymd_') . time();

        ini_set('memory_limit', '3G');
        set_time_limit(60 * 60 * 24);

        $filename = 'Distributors_Objectives_' . $complete_date. '.csv';
        $fileFullName = ConstantsPath::DIR_CSV_OBJECTIVES_FILES_ABSOLUTE . DS . $filename;

        $page = 1;
        $limit = ($page * ConstantesPaginacion::TAM_100) . '';
        if ($page != 1) {
            $limit = (($page * ConstantesPaginacion::TAM_100) - ConstantesPaginacion::TAM_100) . '';
            $limit = ConstantesPaginacion::TAM_100 . ',' . $limit;
        }

        $file = fopen($fileFullName, 'a');

        $table = array(
            __t('General.Name', $languageCode),
        );

        fputcsv($file, $table);

		$distributors_array = json_decode($data['SetDistributorObjective']['distributor_array']);
		foreach ($distributors_array as $key => $distributor_id) {
			$distributor = $this->Distributor->findById($distributor_id);
            $distributor_row = array();
            $distributor_row[] = $distributor['Distributor']['name'];
            fputcsv($file, $distributor_row);
        }
        fclose($file);

        $this->Email->newEmailObjectivesCompleted($filename, serialize(array($fileFullName)), $data['SetDistributorObjective']['email_to'], $userCountryId, $userAagRegionId, $userName, $objectiveName, $languageCode);
    }

}
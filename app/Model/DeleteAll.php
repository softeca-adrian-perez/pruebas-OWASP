<?php
App::uses('DistributorObjective', 'Model');

class DeleteAll extends AppModel{
    public $useTable = 'delete_all';

    public function add($data) 
    {
		$fields = array(
			'DeleteAll' => array(
				'id_to_delete',
				'last_deleted_id',
				'table',
				'creation_date',
				'modification_date',
				'is_completed',
			)
		);
		$this->create();

		$delete_all_bd = $this->guardar($data, $fields);
		if (!$delete_all_bd) {
			return false;
		}

		$this->commit();
		return $delete_all_bd;
	}

    public function edit($data)
	{
		$fields = array(
			'DeleteAll' => array(
				'last_deleted_id',
				'modification_date',
				'is_completed',
			)
		);

		$delete_all_bd = $this->guardar($data, $fields);
		if (!$delete_all_bd) {
			return false;
		}

		$this->commit();
		return true;
	}

	public function delete_all_distributors_objectives()
	{
		$DistributorObjective = ClassRegistry::init('DistributorObjective');

		$array_not_set = $this->find('first', array(
			'conditions' => array(
                'DeleteAll.is_completed' => ConstantsBooleans::NO,
                'DeleteAll.table' => ConstantsDeleteAll::DISTRIBUTOR_OBJECTIVES
            ),
            'fields' => array(
                'DeleteAll.*',
            ),
        ));

		if ($array_not_set) {
			$dis_objectives_ids = $DistributorObjective->search_list_by_last_id($array_not_set['DeleteAll']['id_to_delete']);
			if ($dis_objectives_ids) {
				$last_id = null;
				foreach ($dis_objectives_ids as $dis_objective_id) {
					$DistributorObjective->delete_objective($dis_objective_id);
					$last_id = $dis_objective_id;
				}

				if (isset($last_id) && !empty($last_id)) {
					if ($last_id == $array_not_set['DeleteAll']['id_to_delete']) {
						$data = array();
						$data['DeleteAll']['id'] = $array_not_set['DeleteAll']['id'];
						$data['DeleteAll']['last_deleted_id'] = $last_id;
						$data['DeleteAll']['modification_date'] = date('Y-m-d H:i:s');
						$data['DeleteAll']['is_completed'] = ConstantsBooleans::YES;
						$this->edit($data);
					}else {
						$data = array();
						$data['DeleteAll']['id'] = $array_not_set['DeleteAll']['id'];
						$data['DeleteAll']['last_deleted_id'] = $last_id;
						$data['DeleteAll']['modification_date'] = date('Y-m-d H:i:s');
						$data['DeleteAll']['is_completed'] = ConstantsBooleans::NO;
						$this->edit($data);
					}
				}
			}
		}

		return true;
	}

}
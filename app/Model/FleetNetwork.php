<?php

class FleetNetwork extends AppModel{
    public $useTable = 'fleets_networks';

    public $belongsTo = array(
        'Network' => array(
            'className' => 'Network',
            'foreignKey' => 'network_id'
        )
    );

    public function setFleetNetworks($fleetId, $data) {
		$fields = array(
			'FleetNetwork' => array(
				'fleet_id',
				'network_id',
                'creation_date'
			)
		);

        $this->deleteAll(array(
            'fleet_id' => $fleetId
        ));

		if (!empty($data) && count($data)>0 ) {
			foreach ($data as $value) {
				$fleetNetwork = array(
					'FleetNetwork' => array(
						'fleet_id' => $fleetId,
                        'network_id' => $value,
                        'creation_date' => date('Y-m-d H:i:s')
					),
				);
				$this->create();
				$result = $this->guardar($fleetNetwork, $fields);
				if (!$result) {
					return false;
				}
			}
		}
        if ($this->findCountByFleetId($fleetId) < 1) {
            $fleetNetwork = array(
                'FleetNetwork' => array(
                    'fleet_id' => $fleetId,
                    'network_id' => DEFAULT_NETWORK_FLEETS,
                    'creation_date' => date('Y-m-d H:i:s')
                ),
            );
            $this->create();
            $result = $this->guardar($fleetNetwork, $fields);
        }

        if (!$result) {
            $this->rollback();
            return false;
        }
        $this->commit();
        return true;
	}

    public function get_list($fleet_id) {
		return $this->find(
            'list',
            array(
                'joins' => array(
                    array(
                        'table' => 'networks',
                        'alias' => 'Network',
                        'type' => 'Inner',
                        'conditions' => array(
                            'Network.id = FleetNetwork.network_id',
							'FleetNetwork.fleet_id' => $fleet_id,
                        )
                    ),
                ),
                'fields' => array(
                    'Network.id',
                ),
            )
        );
	}
}

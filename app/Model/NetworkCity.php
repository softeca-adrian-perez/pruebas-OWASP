<?php
class NetworkCity extends AppModel
{
    public $useTable = 'network_cities';

	public $validate = array();

	public function add_network_city($data)
    {
		$fields = array(
			'NetworkCity' => array(
				'network_id',
                'city_id'
			)
		);
		$this->create();

		$network_city_bd = $this->guardar($data, $fields);
		if (!$network_city_bd) {
			return false;
		}

		$this->commit();
		return $network_city_bd;
	}

	public function getData($networkId) {
		return $this->find(
			'all',
			array(
				'joins' => array(
					array(
                        'table' => 'cities',
                        'alias' => 'City',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'City.id = NetworkCity.city_id'
						),
						'fields' => array(
							'City.id',
							'City.name'
						)
					),
					array(
						'table' => 'provinces',
                        'alias' => 'Province',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Province.id = City.province_id'
						),
						'fields' => array(
							'Province.id',
							'Province.name'
						)
					),
					array(
						'table' => 'countries',
                        'alias' => 'Country',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Country.id = Province.country_id'
						),
						'fields' => array(
							'Country.id',
							'Country.name'
						)
					),
				),
				'conditions' => array(
                    'NetworkCity.network_id' => $networkId,
                ),
				'fields' => array(
					'City.name',
					'Province.name',
					'Country.name',
					'NetworkCity.id'
				),
				'order' => array(
					'City.name' => 'asc'
				)
			)
		);
	}
}

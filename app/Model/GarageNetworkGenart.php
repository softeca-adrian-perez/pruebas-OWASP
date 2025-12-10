<?php
class GarageNetworkGenart extends AppModel{
    public $useTable = 'garages_networks_genarts';

	public $validate = array(
		'discount' => array(
			array(
				'rule' => array('comparison', '>=', 0),
				'required' => true,
				'message' => 'Validation.Mandatory_to_numeric_positive',
			),
		),
		'markup' => array(
			array(
				'rule' => array('comparison', '>=', 0),
				'required' => true,
				'message' => 'Validation.Mandatory_to_numeric_positive',
			),
		),
		'surcharge' => array(
			array(
				'rule' => array('comparison', '>=', 0),
				'required' => true,
				'message' => 'Validation.Mandatory_to_numeric_positive',
			),
		)
	);
}

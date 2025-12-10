<?php

class SalesArea extends AppModel
{
    public $useTable = 'sales_areas';

	public $validate = array(
		'name_en' => array(
			'maxLength' => array(
				'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
				'message' => 'Validation.Name_is_too_long',
			),
		),
		'name_fr' => array(
			'maxLength' => array(
				'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
				'message' => 'Validation.Name_is_too_long',
			),
		),
		'name_de' => array(
			'maxLength' => array(
				'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
				'message' => 'Validation.Name_is_too_long',
			),
		),
		'name_nl' => array(
			'maxLength' => array(
				'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
				'message' => 'Validation.Name_is_too_long',
			),
		),
	);

    public function add_sales_area($data)
    {
        $fields = array(
            'SalesArea' => array(
				'name_en',
				'name_fr',
				'name_de',
				'name_nl',
				'name_es',
				'aag_region_id',
            )
        );
        $this->create();

        $sales_area_bd = $this->guardar($data, $fields);
        if (!$sales_area_bd) {
            return false;
        }

        $this->commit();
        return true;
    }

    public function edit_sales_area($data)
	{
		$fields = array(
			'SalesArea' => array(
				'name_en',
				'name_fr',
				'name_de',
				'name_nl',
				'name_es',
			)
		);

		$sales_area_bd = $this->guardar($data, $fields);
		if (!$sales_area_bd) {
			return false;
		}

		$this->commit();
		return true;
	}

    public function getData($aagRegionId)
	{
		return $this->find(
			'all',
			array(
				'conditions' => array(
					'aag_region_id' => $aagRegionId
				),
				'order' => array(
					'name_' . __l() => 'asc'
				)
			)
		);
	}

	public function searchListByRegion($aagRegionId)
	{
		return $this->find('list', array(
			'conditions' => array(
				'aag_region_id' => $aagRegionId
			),
			'fields' => array(
				'id',
				'name_' . __l()
			),
			'order' => array(
				'name_' . __l()
			)
		));
	}
}

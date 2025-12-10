<?php

class CampaignEntry extends AppModel{
    public $useTable = 'campaign_entries';

	public $hasOne = array(
        'AagRegion',
    );


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

    public function search_list($aag_region_id){
        return $this->find('list', array(
			'conditions' => array(
				'aag_region_id' => $aag_region_id
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

    public function add_campaign_entries($data)
    {
		$fields = array(
			'CampaignEntry' => array(
				'name_en',
				'name_fr',
				'name_de',
				'name_nl',
				'name_es',
				'aag_region_id',
			)
		);
		$this->create();

		$campaign_entries_bd = $this->guardar($data, $fields);
		if (!$campaign_entries_bd) {
			return false;
		}

		$this->commit();
		return true;
	}

    public function edit_campaign_entries($data)
	{
		$fields = array(
			'CampaignEntry' => array(
				'name_en',
				'name_fr',
				'name_de',
				'name_nl',
				'name_es',
			)
		);

		$campaign_entries_bd = $this->guardar($data, $fields);
		if (!$campaign_entries_bd) {
			return false;
		}

		$this->commit();
		return true;
	}

	public function getData($aag_region_id) {
		return $this->find('all', array(
			'conditions' => array(
				'aag_region_id' => $aag_region_id
			),
			'order' => array('name_' . __l() => 'asc')
		));
	}

	public function getDataByGarage($garage_id, $aag_region_id) {
        return $this->find(
            'list',
            array(
                'joins' => array(
                    array(
                        'table' => 'garages_campaign',
                        'alias' => 'GarageCampaign',
                        'type' => 'Left',
                        'conditions' => array(
                            'GarageCampaign.garage_campaign_id = CampaignEntry.id',
							'GarageCampaign.garage_id' => $garage_id
                        )
                    ),
                ),
				'conditions' => array(
					'GarageCampaign.garage_campaign_id IS NULL',
					'CampaignEntry.aag_region_id' => $aag_region_id
				),
                'fields' => array(
                    'CampaignEntry.id',
                    'CampaignEntry.name_' . __l(),
                )
            )
        );
    }

}
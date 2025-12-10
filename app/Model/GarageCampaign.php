<?php

class GarageCampaign extends AppModel{
    public $useTable = 'garages_campaign';

    public $validate = array(
        'number' => array(
            array(
                'rule' => 'numeric',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_number',
            ),
        ),
    );

    public function add_garage_campaign( $garage_campaign , $garage_id ){
        $fields = array(
            'GarageCampaign' => array(
                'garage_id',
                'garage_campaign_id',
                'number',
            )
        );

        $garage_campaign['GarageCampaign']['garage_id'] = $garage_id;

        $this->create();

        $garage_campaign_bd = $this->guardar( $garage_campaign, $fields );

        if(!$garage_campaign_bd){
            return false;
        }

        $this->commit();
        return $garage_campaign_bd;
    }

    public function edit_garage_campaign( $garage_campaign ){
        $fields = array(
            'GarageEquipment' => array(
                'garage_id',
                'garage_campaign_id',
                'number',
            )
        );

        $this->create();

        $garage_campaign_bd = $this->guardar( $garage_campaign, $fields );

        if( !$garage_campaign_bd ){
            return false;
        }

        $this->commit();
        return $garage_campaign_bd;
    }

    public function getDataByGarage($garage_id) {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'table' => 'campaign_entries',
                        'alias' => 'CampaignEntry',
                        'type' => 'Inner',
                        'conditions' => array(
                            'CampaignEntry.id = GarageCampaign.garage_campaign_id',
							'GarageCampaign.garage_id' => $garage_id
                        )
                    ),
                ),
                'fields' => array(
                    'GarageCampaign.id',
                    'CampaignEntry.name_' . __l(),
                    'GarageCampaign.number',
                )
            )
        );
    }

}
<?php

class GarageWebsite extends AppModel{

    public $useTable = 'garages_websites';

    public $validate = array(
        'website_id' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_website',
            ),
        ),
        'url' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
        'tagline' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
        'description' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
    );


    public function add_garage_website( $garage_website , $garage_id ){
        $fields = array(
            'GarageWebsite' => array(
                'garage_id',
                'website_id',
                'url',
                'tagline',
                'description',
            )
        );

        $garage_website['GarageWebsite']['garage_id'] = $garage_id;
        $this->create();

        $garage_website_bd = $this->guardar( $garage_website, $fields );
        if(!$garage_website_bd){
            return false;
        }

        $this->Garage = ClassRegistry::init("Garage");
        $this->Garage->edit_modification_date_garage( $garage_id );

        $this->commit();
        return $garage_website_bd;
    }

    public function edit_garage_website( $garage_website ){
        $fields = array(
            'GarageWebsite' => array(
                'website_id',
                'url',
                'tagline',
                'description',
            )
        );

        $this->create();
        $garage_website_bd = $this->guardar( $garage_website, $fields );

        if(!$garage_website_bd){
            return false;
        }

        $this->Garage = ClassRegistry::init("Garage");
        $this->Garage->edit_modification_date_garage( $garage_website_bd['GarageWebsite']['garage_id'] );

        $this->commit();
        return $garage_website_bd;
    }

    public function removeWebsiteFromGarages( $website_id ){
        $garages = $this->findAllByWebsiteId( $website_id );

        foreach ($garages as $garage) {
            $this->delete($garage['GarageWebsite']['id']);
        }
    }

    public function findWebsiteExport($garage_id){
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Website',
                        'table' => 'websites',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Website.id = GarageWebsite.website_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'GarageWebsite.garage_id' => $garage_id,
                ),
                'fields' => array(
                    'Website.name',
                    'GarageWebsite.*',
                ),
            )
        );
    }
}
?>
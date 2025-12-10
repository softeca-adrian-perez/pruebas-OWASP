<?php

class Website extends AppModel{
    public $useTable = 'websites';

    public $hasOne = array(
        'AagRegion',
    );

    public function new_website($website){
        $fields = array(
            'Website' => array(
                'name',
                'aag_region_id',
            )
        );
        $this->create();

        $service_bd = $this->guardar($website, $fields);
        if ( !$service_bd ){
            return false;
        }

        $this->commit();
        return true;
    }

    private $_queries = array(
        'search' =>
            array(
                'fields' => array(
                    'Website.*',
                )
            ),
    );

    public function _query( $index ){
        return $this->_queries[$index];
    }

    public function search_list($aag_region_id){
        return $this->find('list',array(
            'fields' => array(
                'id',
                'name'
            ),
            'conditions' => array(
                'aag_region_id' => $aag_region_id,
            ),
        ));
    }

    public function edit_website($data)
	{
		$fields = array(
			'Website' => array(
				'name',
			)
		);

		$website_bd = $this->guardar($data, $fields);
		if (!$website_bd) {
			return false;
		}

		$this->commit();
		return true;
	}

	public function getData($aag_region_id) {
		return $this->find('all', array(
            'conditions' => array(
                'aag_region_id' => $aag_region_id,
            ),
			'order' => array(
				'name' => 'asc'
				)
			)
		);
	}

}
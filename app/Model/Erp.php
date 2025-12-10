<?php
class Erp extends AppModel{
    public $useTable = 'erp';
    public $displayField = 'erp_code';

    public $belongsTo = array(
        'AagRegion',
    );

    public function get_list(){
        return $this->find('list', array(
            'fields' => array(
                'id',
                'erp_code'
            )
        ));
	}

    public function getErpsByAagRegionId($aag_region_id){
        return $this->find('list', array(
            'conditions' => array(
                'aag_region_id =' => $aag_region_id
            )
        ));
    }
}

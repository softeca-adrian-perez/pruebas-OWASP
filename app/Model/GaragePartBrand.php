<?php

class GaragePartBrand extends AppModel{

    public $useTable = 'garages_parts_brands';

    public function addRelationGaragePartBrand( $garage_id , $part_brand_id ){
        $model = array(
            'garage_id' => $garage_id,
            'part_brand_id' => $part_brand_id
        );

        $this->create();
        if($this->save($model)){
            return true;
        }else{
            return false;
        }
    }

    public function removeGaragePartBrand( $garage_id ){
        $parts_brands = $this->findAllByGarageId( $garage_id );

        foreach ($parts_brands as $part_brand) {
            $this->delete($part_brand['GaragePartBrand']['id']);
        }
    }

    public function findPartBrands( $garage_id ){
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'PartBrand',
                        'table' => 'parts_brands',
                        'type' => 'INNER',
                        'conditions' => array(
                            'PartBrand.id = GaragePartBrand.part_brand_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'GaragePartBrand.garage_id' => $garage_id,
                ),
                'fields' => array(
                    'GaragePartBrand.*, PartBrand.*'
                ),
            )
        );
    }
    public function findPartBrandByGarage( $garage_id ){
        return $this->find(
            'list',
            array(
                'joins' => array(
                    array(
                        'alias' => 'PartBrand',
                        'table' => 'parts_brands',
                        'type' => 'INNER',
                        'conditions' => array(
                            'PartBrand.id = GaragePartBrand.part_brand_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'GaragePartBrand.garage_id' => $garage_id,
                ),
                'fields' => array(
                    'PartBrand.id'
                ),
            )
        );
    }

}
?>
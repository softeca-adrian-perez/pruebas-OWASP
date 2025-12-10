<?php

class GarageBrand extends AppModel{

    public $useTable = 'garages_brands';

    public function addRelationGaragePartBrand( $garage_id , $part_brand_id ){
        $model = array(
            'garage_id' => $garage_id,
            'brand_id' => $part_brand_id
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
            $this->delete($part_brand['GarageBrand']['id']);
        }
    }

    public function findPartBrands( $garage_id ){
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'Brand',
                        'table' => 'brands',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Brand.id = GarageBrand.brand_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'GarageBrand.garage_id' => $garage_id,
                ),
                'fields' => array(
                    'GarageBrand.*, Brand.*'
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
                        'alias' => 'Brand',
                        'table' => 'brands',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Brand.id = GarageBrand.brand_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'GarageBrand.garage_id' => $garage_id,
                ),
                'fields' => array(
                    'Brand.id'
                ),
            )
        );
    }

}
?>
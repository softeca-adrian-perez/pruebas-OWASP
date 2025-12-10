<?php

class GarageCustomerActivity extends AppModel{

    public $useTable = 'garages_customers_activities';

    public $validate = array(
		'order' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
	);

    public function new_garage_customer_activity( $garage_customer_activity ){
        $fields = array(
            'GarageCustomerActivity' => array(
                'garage_id',
                'customer_activity_id',
                'order',
            )
        );
        $this->create();
        $garage_customer_activity_bd = $this->guardar($garage_customer_activity, $fields);
        if ( !$garage_customer_activity_bd ){
            return false;
        }

        $this->commit();
        return true;
    }

    public function edit_garage_customer_activity( $garage_customer_activity ){
        $fields = array(
            'GarageCustomerActivity' => array(
                'id',
                'garage_id',
                'customer_activity_id',
                'order',
            )
        );
        $garage_customer_activity_bd = $this->guardar($garage_customer_activity, $fields);
        if ( !$garage_customer_activity_bd ){
            return false;
        }

        $this->commit();
        return true;
    }

    public function change_order( $garages_customers_activities ){
        $fields = array(
            'GarageCustomerActivity' => array(
                'id',
                'order',
            )
        );

        $garages_customers_activities_bd = $this->guardar( $garages_customers_activities , $fields );

        if( !$garages_customers_activities_bd ){
            return false;
        }

        $this->commit();
        return $garages_customers_activities_bd;
    }

    public function getAllByGarageIdByOrder( $garage_id ){
        return $this->find('all',array(
                'conditions' => array(
                    'garage_id' => $garage_id
                ),
                'order' => array('order'),
            )
        );
    }

    public function findLastOrderByGarageId( $garage_id ){
        return $this->find('all',array(
                'conditions' => array(
                    'garage_id' => $garage_id,
                ),
                'fields' => array(
                    'MAX(GarageCustomerActivity.order) as max_tmp'
                )
            )
        );
    }

    public function getActivitiesByGarage( $garage_id ) {
        return $this->find('list', array(
            'fields' => array(
                'id',
                'customer_activity_id',
            ),
            'conditions' => array(
                'GarageCustomerActivity.garage_id' => $garage_id
            ),
        ));
    }

}
?>
<?php
    class GarageNetworkFluid extends AppModel
    {
        public $useTable = "garages_networks_fluids";
        public $belongsTo = array("Fluid");

        public $validate = array(
            'price' => array(
                'numeric' => array(
                    'rule' => 'numeric',
                    'allowEmpty' => true,
                    'message' => 'Validation.Must_be_a_number',
                ),
                'range' => array(
                    'rule' => array('range', -1, ConstantsValidation::MAX_VALUE_INT),
                    'message' => 'Validation.Invalid_range',
                ),
            )
        );
    }

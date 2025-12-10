<?php

class UserSearch extends AppModel{
    public $useTable = 'users_searches';
    public $displayField = 'name';
    public $order = 'UserSearch.name';

    public $validate = array(
        'name' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'city' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'location' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'last_visit' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
    );

    public function getListSearchesByLastUseAndGarages(){
        return $this->find('list',
            array(
                'conditions' => array(
                   'is_garages' => ConstantsBooleans::YES
                ),
                'fields' => array(
                    'id',
                    'name'
                ),
                'order' => 'last_use'
            )
        );
    }

    public function getListSearchesByLastUseAndDistributors(){
        return $this->find('list',
            array(
                'conditions' => array(
                    'is_garages' => ConstantsBooleans::NO
                ),
                'fields' => array(
                    'id',
                    'name'
                ),
                'order' => 'last_use'
            )
        );
    }

    public function new_search( $search ){
        $fields = array(
            'UserSearch' => array(
                'name',
                'user_id',
                'route_id',
                'network_status_id',
                'is_garages',
                'location',
                'city',
                'lat',
                'lng',
                'distance',
                'last_visit',
                'my_customers',
                'last_use',
                'creation_date'
            )
        );

        if(isset($search['UserSearch']['my_customers']) && !empty($search['UserSearch']['my_customers'])){
            if($search['UserSearch']['my_customers'] == 'true'){
                $search['UserSearch']['my_customers'] = 1;
            }else{
                $search['UserSearch']['my_customers'] = 0;
            }
        }

        $search['UserSearch']['user_id'] = CakeSession::read('Auth.User.id');
        $search['UserSearch']['last_use'] = date('Y-m-d H:i:s');
        $search['UserSearch']['creation_date'] = date('Y-m-d H:i:s');

        $this->create();
        $search_bd = $this->guardar($search, $fields);
        if ( !$search_bd ){
            return false;
        }

        $this->commit();
        return true;
    }

    public function edit_search( $search_id ){
        $fields = array(
            'UserSearch' => array(
                'id',
                'last_use'
            )
        );

        $search['UserSearch']['id'] = $search_id;
        $search['UserSearch']['last_use'] = date('Y-m-d H:i:s');

        $search_bd = $this->guardar($search, $fields);
        if ( !$search_bd ){
            return false;
        }

        $this->commit();
        return true;
    }

}
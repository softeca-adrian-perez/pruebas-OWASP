<?php

class UserPreference extends AppModel{

    public $useTable = 'users_preferences';

    public $belongsTo = array(
        'User'
    );

    public $validate = array(
		'value' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
	);

    public function save_preferences( $user_id, $preference_id, $value ){ 
        $user_preference = $this->findByUserIdAndPreferenceId($user_id, $preference_id);

        $fields = array(
            'UserPreference' => array(
                'user_id',
                'preference_id',
                'value'
            )
        );
        
        $preference = array(
            'UserPreference' => array(
                'user_id' => $user_id,
                'preference_id' => $preference_id,
                'value' => $value
            )
        );

        if(!empty($user_preference)){
            array_unshift($fields['UserPreference'], 'id');
            $preference['UserPreference']['id'] = $user_preference['UserPreference']['id'];
            $this->create();
        }

        if($this->guardar($preference, $fields)){
            return true;
        }

    }


}
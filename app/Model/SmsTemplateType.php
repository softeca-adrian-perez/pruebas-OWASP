<?php

class SmsTemplateType extends AppModel {

    public $useTable = 'sms_template_types';

	public function search_list(){
        return $this->find('list',array(
			'fields' => array(
                'id',
                'name'. __s()
            ),
            'order' => array(
                'name'. __s()
            )
        ));
    }
}

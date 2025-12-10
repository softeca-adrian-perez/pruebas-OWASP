<?php

class CommunicationUser extends AppModel{

    public $useTable = 'communications_users';

    public $belongsTo = array(
        'Communication',
        'User',
    );

    public function add($user_id, $communication_id){
        $fields = array(
            'CommunicationUser' => array(
                'user_id',
                'communication_id',
                'date_read',
            )
        );
        $communication_user = $this->findByUserIdAndCommunicationId($user_id, $communication_id);
        if(!$communication_user){
            $communication_user = array(
                'CommunicationUser' => array(
                    'user_id' => $user_id,
                    'communication_id' => $communication_id,
                    'date_read' => date('Y-m-d H:i:s'),
                )
            );
            $this->create();
        }else{
            $communication_user['CommunicationUser']['date_read'] = date('Y-m-d H:i:s');
        }

        return $this->guardar($communication_user, $fields);
    }

    public function getPopUpsRead( $user ){
        return $this->find('list',
            array(
                'conditions' => array(
                    'CommunicationUser.user_id' => $user['id']
                ),
                'fields' => array(
                    'id',
                    'CommunicationUser.communication_id'
                )
            )
        );
    }

}
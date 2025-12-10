<?php

class MessageDistributor extends AppModel{
    public $useTable = 'messages_distributors';

    public $belongsTo = array(
        'Message', 'Distributor'
    );

    public function addRelationMessageDistributor( $message_id, $distributor_id){
        $mesage_distributor = array(
            'distributor_id' => $distributor_id,
            'message_id' => $message_id,
            'date_read' => null,
        );

        $this->create();
        if($this->save($mesage_distributor)){
            return true;
        }else{
            return false;
        }
    }

    public function getListByMessageId($message_id){
        return $this->Distributor->getListByMessageId($message_id);
    }

    public function readMessage($message_id, $distributor_id){
        $fields = array(
            'MessageDistributor' => array(
                'date_read',
            ),
        );
        $message = $this->findByMessageIdAndDistributorId($message_id, $distributor_id);
        $message['MessageDistributor']['date_read'] = date('Y-m-d H:i:s');
        return $this->guardar($message, $fields);
    }
}

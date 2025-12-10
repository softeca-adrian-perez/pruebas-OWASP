<?php

class MessageGarage extends AppModel{
    public $useTable = 'messages_garages';

    public $belongsTo = array(
        'Message', 'Garage'
    );

    public function addRelationMessageGarage( $message_id, $garage_id){
        $mesage_garage = array(
            'garage_id' => $garage_id,
            'message_id' => $message_id,
            'date_read' => null,
        );

        $this->create();
        if($this->save($mesage_garage)){
            return true;
        }else{
            return false;
        }
    }

    public function getListByMessageId($message_id){
        return $this->Garage->getListByMessageId($message_id);
    }

    public function readMessage($message_id, $garage_id){
        $fields = array(
            'MessageGarage' => array(
                'date_read',
            ),
        );
        $message = $this->findByMessageIdAndGarageId($message_id, $garage_id);
        $message['MessageGarage']['date_read'] = date('Y-m-d H:i:s');
        return $this->guardar($message, $fields);
    }
}

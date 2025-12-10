<?php

class Message extends AppModel 
{
    public $useTable = 'messages';

    public $belongsTo = array(
        'User',
        'MessageType' => array(
            'foreign_key' => 'type'
        ),
    );

    public $hasMany = array(
        'MessageGarage',
        'MessageDistributor',
        'MessageFile',
    );

    public $validate = array(
        'subject' => array(
            'notBlank' => array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_fill_the_subject',
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Subject_too_long',
            ),
        ),
        'body' => array(
            'notBlank' => array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_fill_the_body',
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_TEXT),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
    );

    public function query($index)
    {
        return $this->_queries[$index];
    }

    private $_queries = array(
        'list' => array(
            'order' => array(
                'Message.creation_date DESC'
            ),
        )
    );

    public function conditions($fields){
        $conditions = array();
        if(!empty($fields['subject'])){
            $conditions[] = $this->_conditionSubject($fields['subject']);
        }
        if(!empty($fields['body'])){
            $conditions[] = $this->_conditionBody($fields['body']);
        }
        if(!empty($fields['type'])){
            $conditions[] = $this->_conditionType($fields['type']);
        }
        if(!empty($fields['sent'])){
            $conditions[] = $this->_conditionSent($fields['sent']);
        }
        if(!empty($fields['created-from'])){
            $conditions[] = $this->_conditionCreatedFrom($fields['created-from']);
        }
        if(!empty($fields['created-to'])){
            $conditions[] = $this->_conditionCreatedTo($fields['created-to']);
        }
        if(!empty($fields['sent-from'])){
            $conditions[] = $this->_conditionSentFrom($fields['sent-from']);
        }
        if(!empty($fields['sent-to'])){
            $conditions[] = $this->_conditionSentTo($fields['sent-to']);
        }
        return $conditions;
    }

    public function _conditionSubject($subject){
        return array('Message.subject LIKE' => '%'.$subject.'%');
    }

    public function _conditionBody($body){
        return array('Message.body LIKE' => '%'.$body.'%');
    }

    public function _conditionType($type){
        return array('Message.type' => $type);
    }

    public function _conditionSent($sent){
        if($sent == ConstantsBooleans::NO){
            return array('Message.date_sent IS NULL');
        }else{
            return array('Message.date_sent IS NOT NULL');
        }
    }

    public function _conditionCreatedFrom($from_date){
        $from_date = Fecha::toFormatoBd($from_date);
        return array('Message.creation_date >=' => $from_date);
    }

    public function _conditionCreatedTo($to_date){
        $to_date = Fecha::toFormatoBd($to_date);
        return array('Message.creation_date <=' => $to_date);
    }

    public function _conditionSentFrom($from_date){
        $from_date = Fecha::toFormatoBd($from_date);
        return array('Message.date_sent >=' => $from_date);
    }

    public function _conditionSentTo($to_date){
        $to_date = Fecha::toFormatoBd($to_date);
        return array('Message.date_sent <=' => $to_date);
    }

    public function new_message($type, $from, $message){
        $this->create();
        $fields = array('Message' => array(
            'subject',
            'body',
            'creation_date',
            'sent',
            'user_id',
            'type',
        ));
        $message_new = array('Message' => array(
            'subject' => $message['Message']['subject'],
            'body' => $message['Message']['body'],
            'creation_date' => date('Y-m-d H:i:s'),
            'sent' => null,
            'user_id' => $from,
            'type' => $type,
        ));
        return $this->guardar($message_new, $fields);
    }

    public function edit_message($message){
        $result = array();
        $fields = array(
            'Message' => array(
                'subject',
                'body',
            ),
        );

        $result[] = $message = $this->guardar($message, $fields);
        foreach($message['Message']['files'] as $file){
            if($file['error'] == 0){
                $check_file = FileManager::check_file($file);
                if($check_file == ConstantsFileErrorTypes::OK){
                $result[] = $this->MessageFile->saveFile($file, $message['Message']['id'], ConstantsFileType::FILE);
                } else {
                    $result[] = false;
                }
            }
        }
        if($message['Message']['type'] == ConstantsMessagesTypes::GARAGE){
            $this->MessageGarage->deleteAll(array('MessageGarage.message_id' => $message['Message']['id']));
            $to = $message['Message']['recipients'];
            if(!empty($to)){
                foreach($to as $recipient){
                    $result[] = $this->MessageGarage->addRelationMessageGarage($message['Message']['id'], $recipient);
                }
            }
        }elseif($message['Message']['type'] == ConstantsMessagesTypes::DISTRIBUTOR){
            $this->MessageDistributor->deleteAll(array('MessageDistributor.message_id' => $message['Message']['id']));
            $to = $message['Message']['recipients'];
            if(!empty($to)){
                foreach($to as $recipient){
                    $result[] = $this->MessageDistributor->addRelationMessageDistributor($message['Message']['id'], $recipient);
                }
            }
        }

        return !in_array(false, $result);
    }

    public function send_message($message){
        $result = array();
        $fields = array(
            'Message' => array(
                'subject',
                'body',
                'date_sent',
            ),
        );
        $message['Message']['date_sent'] = date('Y-m-d H:i:s');
        $result[] = $message = $this->guardar($message, $fields);
        foreach($message['Message']['files'] as $file){
            if($file['error'] == 0){
                $check_file = FileManager::check_file($file);
                if($check_file == ConstantsFileErrorTypes::OK){
                $result[] = $this->MessageFile->saveFile($file, $message['Message']['id'], ConstantsFileType::FILE);
                }else{
                    $result[] = false;
                }
            }
        }
        if($message['Message']['type'] == ConstantsMessagesTypes::GARAGE){
            $this->MessageGarage->deleteAll(array('MessageGarage.message_id' => $message['Message']['id']));
            $to = $message['Message']['recipients'];
            if(!empty($to)){
                foreach($to as $recipient){
                    $result[] = $this->MessageGarage->addRelationMessageGarage($message['Message']['id'], $recipient);
                }
            }
        }elseif($message['Message']['type'] == ConstantsMessagesTypes::DISTRIBUTOR){
            $this->MessageDistributor->deleteAll(array('MessageDistributor.message_id' => $message['Message']['id']));
            $to = $message['Message']['recipients'];
            if(!empty($to)){
                foreach($to as $recipient){
                    $result[] = $this->MessageDistributor->addRelationMessageDistributor($message['Message']['id'], $recipient);
                }
            }
        }

        return !in_array(false, $result);
    }

    public function delete_message($message_id){
        $result = array();
        $result[] = $this->MessageFile->deleteAll(array('message_id' => $message_id));
        $result[] = $this->MessageGarage->deleteAll(array('message_id' => $message_id));
        $result[] = $this->MessageDistributor->deleteAll(array('message_id' => $message_id));
        $result[] = $this->delete($message_id);
        return !in_array(false, $result);
    }

    public function findAllMessagesBySenderId($user_id){
        $query = array(
            'contain' => array(
                'MessageGarage' => array(
                    'Garage'
                ),
                'MessageDistributor' => array(
                    'Distributor'
                ),
            ),
            'conditions' => array(
                'Message.user_id' => $user_id
            ),
            'order' => array(
                'Message.creation_date DESC'
            ),
        );
        return $query;
    }

    public function findAllMessagesByGarageRecipient($garage_id){
        $query = array(
            'joins' => array(
                array(
                    'alias' => 'MessageGarage',
                    'table' => 'messages_garages',
                    'type' => 'LEFT',
                    'conditions' => 'Message.id = MessageGarage.message_id'
                ),
            ),
            'conditions' => array(
                'MessageGarage.garage_id' => $garage_id,
                'NOT' => array('Message.date_sent' => null),
            ),
            'order' => array(
                'Message.sent_date DESC'
            ),
            'fields' => array(
                'Message.*', 'MessageGarage.date_read'
            ),
            'group' => array(
                'Message.id'
            ),
        );
        return $query;
    }

    public function findAllMessagesByDistributorRecipient($distributor_id){
        $query = array(
            'joins' => array(
                array(
                    'alias' => 'MessageDistributor',
                    'table' => 'messages_distributors',
                    'type' => 'LEFT',
                    'conditions' => 'Message.id = MessageDistributor.message_id'
                ),
            ),
            'conditions' => array(
                'MessageDistributor.distributor_id' => $distributor_id,
                'NOT' => array('Message.date_sent' => null),
            ),
            'order' => array(
                'Message.sent_date DESC'
            ),
            'fields' => array(
                'Message.*', 'MessageDistributor.date_read'
            ),
            'group' => array(
                'Message.id'
            ),
        );
        return $query;
    }

    public function getCountUnreadByGarage($garage_id){
        return $this->MessageGarage->find('count', array(
            'joins' => array(
                array(
                    'alias' => 'Message',
                    'table' => 'messages',
                    'type' => 'LEFT',
                    'conditions' => 'Message.id = MessageGarage.message_id'
                ),
            ),
            'fields' => 'DISTINCT MessageGarage.message_id',
            'conditions' => array(
                'MessageGarage.garage_id' => $garage_id,
                'NOT' => array('Message.date_sent' => null),
                'MessageGarage.date_read' => null
            ),
        ));
    }

    public function getCountUnreadByDistributor($distributor_id){
        return $this->MessageDistributor->find('count', array(
            'joins' => array(
                array(
                    'alias' => 'Message',
                    'table' => 'messages',
                    'type' => 'LEFT',
                    'conditions' => 'Message.id = MessageDistributor.message_id'
                ),
            ),
            'fields' => 'DISTINCT MessageDistributor.message_id',
            'conditions' => array(
                'MessageDistributor.distributor_id' => $distributor_id,
                'NOT' => array('Message.date_sent' => null),
                'MessageDistributor.date_read' => null
            ),
        ));
    }
}

<?php

class GarageComment extends AppModel
{
    public $useTable = 'garages_comments';

    public $validate = array(
        'body' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_TEXT),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
    );

    private $_queries = array(
        'Search' => array(
            'joins' => array(
                array(
                    'alias' => 'User',
                    'table' => 'users',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'User.id = GarageComment.user_id',
                    ),
                ),
            ),
            'fields' => array(
                'GarageComment.*',
                'User.name'
            ),
            'order' => 'GarageComment.id desc'
        ),
    );

    public function _query($index)
    {
        return $this->_queries[$index];
    }

    public function add_garage_comment($garage_comment, $garage_id, $user_id)
    {
        $fields = array(
            'GarageComment' => array(
                'garage_id',
                'body',
                'creation_date',
                'user_id',
                'subject',
                'origin',
            )
        );
        $garage_comment['GarageComment']['garage_id'] = $garage_id;
        $garage_comment['GarageComment']['creation_date'] = date('Y-m-d H:i:s');
        $garage_comment['GarageComment']['user_id'] = $user_id;
        $this->create();

        $garage_comment_bd = $this->guardar($garage_comment, $fields);
        if (!$garage_comment_bd) {
            return false;
        }

        $this->commit();
        return $garage_comment_bd;
    }

    public function edit_garage_comment($garage_comment, $user_id)
    {
        $fields = array(
            'GarageComment' => array(
                'id',
                'garage_id',
                'body',
                'creation_date',
                'user_id',
                'subject',
                'origin',
            )
        );

        $garage_comment['GarageComment']['user_id'] = $user_id;

        $this->create();

        $garage_comment_bd = $this->guardar($garage_comment, $fields);
        if (!$garage_comment_bd) {
            return false;
        }

        $this->commit();
        return $garage_comment_bd;
    }

    public function getAllByGarageWithUsers($garage_id)
    {
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'User',
                    'table' => 'users',
                    'type' => 'INNER',
                    'conditions' => array(
                        'User.id = GarageComment.user_id',
                    ),
                ),
            ),
            'conditions' => array(
                'GarageComment.garage_id' => $garage_id
            ),
            'fields' => array(
                'GarageComment.*',
                'User.name',
                'User.surname'
            )
        ));
    }

    public function getAllByGarageWithUsersLimited($garage_id, $limit)
    {
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'User',
                    'table' => 'users',
                    'type' => 'INNER',
                    'conditions' => array(
                        'User.id = GarageComment.user_id',
                    ),
                ),
            ),
            'conditions' => array(
                'GarageComment.garage_id' => $garage_id
            ),
            'limit' => $limit,
            'fields' => array(
                'GarageComment.*',
                'User.name'
            ),
            'order' => array('GarageComment.creation_date' => 'DESC')
        ));
    }

    public function createGarageNotes($garage_json, $garage_id, &$errors)
    { //The data comes from a Json left on the server
        foreach ($garage_json['Notes'] as $note) {
            $garage_comment_tmp = array(
                'GarageComment' => array(
                    'garage_id' => $garage_id,
                    'body' => ($note['Body']) ? $note['Body'] : '--',
                    'creation_date' => ($note['CreatedOn']) ? date('Y-m-d', substr(substr(substr($note['CreatedOn'], 0, -2), 6), 0, 10)) : null,
                    'user_id' =>  null,
                )
            );

            if ($note['CreatedByName'] != '') {
                $user = ClassRegistry::init('User');
                $name = explode(" ", $note['CreatedByName']);
                $exist_user = $user->findByNameAndSurname($name[0], $name[1]);
                if ($exist_user) {
                    $garage_comment_tmp['GarageComment']['user_id'] =  $exist_user['User']['id'];
                } else {
                    $garage_comment_tmp['GarageComment']['user_id'] = 5; //Imported Data
                }
            }

            $this->create();
            if (!$this->save($garage_comment_tmp)) {
                CakeLog::write('updates', 'Couldn\'t create comment about the garage' . PHP_EOL);
                $errors['Couldn\'t create comment about the garage'] = translateDataErrors($this->validationErrors);
            }
        }
        return true;
    }

    public function findListByGarageId($garage_id)
    {
        return $this->find(
            'list',
            array(
                'conditions' => array(
                    'GarageComment.garage_id' => $garage_id,
                ),
                'fields' => array(
                    'GarageComment.id'
                ),
            )
        );
    }

    public function updateGarageNotes($garage_json, $exist_garage_id, &$errors)
    { //The data comes from a Json left on the server
        $user = ClassRegistry::init('User');
        $garage_notes_id_exist = $this->findListByGarageId($exist_garage_id);
        foreach ($garage_json['Notes'] as $note) {
            if ($note['CreatedOn']) {
                if ($garage_note = $this->findByGarageIdAndCreationDateAndBody($exist_garage_id, date('Y-m-d', substr(substr(substr($note['CreatedOn'], 0, -2), 6), 0, 10)), $note['Body'])) {
                    $name = explode(" ", $note['CreatedByName']);
                    $exist_user = $user->findByNameAndSurname($name[0], $name[1]);
                    $garage_comment_tmp = array(
                        'GarageComment' => array(
                            'id' => $garage_note['GarageComment']['id'],
                            'user_id' =>  null,
                        )
                    );
                    if ($exist_user) {
                        $garage_comment_tmp['GarageComment']['user_id'] =  $exist_user['User']['id'];
                    } else {
                        $garage_comment_tmp['GarageComment']['user_id'] = 5; //Imported Data
                    }


                    if (!$this->save($this->save($garage_comment_tmp))) {
                        CakeLog::write('updates', 'Comments on the workshop could not be created' . PHP_EOL);
                        $errors['Comments on the workshop could not be created'] = translateDataErrors($this->validationErrors);
                    }

                    unset($garage_notes_id_exist[$garage_note['GarageComment']['id']]);
                } else {
                    $garage_comment_tmp = array(
                        'GarageComment' => array(
                            'garage_id' => $exist_garage_id,
                            'body' => ($note['Body']) ? $note['Body'] : '--',
                            'creation_date' => date('Y-m-d', substr(substr(substr($note['CreatedOn'], 0, -2), 6), 0, 10)),
                            'user_id' =>  null,
                        )
                    );
                    if ($note['CreatedByName'] != '') {
                        $user = ClassRegistry::init('User');
                        $name = explode(" ", $note['CreatedByName']);
                        $exist_user = $user->findByNameAndSurname($name[0], $name[1]);
                        if ($exist_user) {
                            $garage_comment_tmp['GarageComment']['user_id'] =  $exist_user['User']['id'];
                        } else {
                            $garage_comment_tmp['GarageComment']['user_id'] = 5; //Imported Data
                        }
                    }
                    $this->create();
                    if (!$this->save($garage_comment_tmp)) {
                        CakeLog::write('updates', 'Comments on the workshop could not be created' . PHP_EOL);
                        $errors['Comments on the workshop could not be created'] = translateDataErrors($this->validationErrors);
                    }
                }
            }
        }

        //remove garages notes that have not arrived through JSON
        foreach ($garage_notes_id_exist as $key => $garage_note_id) {
            $this->delete($key);
        }

        $this->commit();
        return true;
    }

    public function findByGarageIdAndOrder($garageId)
    {
        return $this->find(
            'all',
            array(
                'conditions' => array(
                    'GarageComment.garage_id' => $garageId,
                ),
                'order' => array(
                    'creation_date DESC'
                ),
            )
        );
    }
}

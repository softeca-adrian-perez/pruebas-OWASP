<?php

class DistributorComment extends AppModel
{
    public $useTable = 'distributors_comments';

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
                    'type' => 'INNER',
                    'conditions' => array(
                        'User.id = DistributorComment.user_id',
                    ),
                ),
            ),
            'fields' => array(
                'DistributorComment.*',
                'User.name'
            ),
            'order' => 'DistributorComment.id desc'
        ),
    );

    public function _query($index)
    {
        return $this->_queries[$index];
    }

    public function add_distributor_comment($distributor_comment, $distributor_id, $user_id)
    {
        $fields = array(
            'DistributorComment' => array(
                'distributor_id',
                'body',
                'creation_date',
                'user_id',
                'subject',
                'origin',
            )
        );
        $distributor_comment['DistributorComment']['distributor_id'] = $distributor_id;
        $distributor_comment['DistributorComment']['creation_date'] = date('Y-m-d H:i:s');
        $distributor_comment['DistributorComment']['user_id'] = $user_id;
        $this->create();

        $distributor_comment_bd = $this->guardar($distributor_comment, $fields);
        if (!$distributor_comment_bd) {
            return false;
        }

        $this->commit();
        return $distributor_comment_bd;
    }

    public function edit_distributor_comment($distributor_comment, $user_id)
    {
        $fields = array(
            'DistributorComment' => array(
                'id',
                'distributor_id',
                'body',
                'creation_date',
                'user_id',
                'subject',
                'origin',
            )
        );

        $distributor_comment['DistributorComment']['user_id'] = $user_id;

        $this->create();

        $distributor_comment_bd = $this->guardar($distributor_comment, $fields);
        if (!$distributor_comment_bd) {
            return false;
        }

        $this->commit();
        return $distributor_comment_bd;
    }

    public function getAllByDistributorWithUsers($distributor_id)
    {
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'User',
                    'table' => 'users',
                    'type' => 'INNER',
                    'conditions' => array(
                        'User.id = DistributorComment.user_id',
                    ),
                ),
            ),
            'conditions' => array(
                'DistributorComment.distributor_id' => $distributor_id
            ),
            'fields' => array(
                'DistributorComment.*',
                'User.name',
                'User.surname'
            )
        ));
    }

    public function getAllByDistributorWithUsersLimited($distributor_id, $limit)
    {
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'User',
                    'table' => 'users',
                    'type' => 'INNER',
                    'conditions' => array(
                        'User.id = DistributorComment.user_id',
                    ),
                ),
            ),
            'conditions' => array(
                'DistributorComment.distributor_id' => $distributor_id
            ),
            'limit' => $limit,
            'fields' => array(
                'DistributorComment.*',
                'User.name'
            ),
            'order' => array('DistributorComment.creation_date' => 'DESC')
        ));
    }
}

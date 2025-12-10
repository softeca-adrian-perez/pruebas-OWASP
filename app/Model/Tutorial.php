<?php

class Tutorial extends AppModel
{
    public $useTable = 'tutorials';

    public $hasOne = array(
        'User',
    );

    public $validate = array(
        'title' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_name',
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'url' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_an_url',
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
    );

    public function _query($index)
    {
        return $this->_queries[$index];
    }

    private $_queries = array(
        'search_with_user' => array(
            'joins' => array(
                array(
                    'alias' => 'User',
                    'table' => 'users',
                    'type' => 'INNER',
                    'conditions' => 'User.id = Tutorial.user_id'
                ),
                array(
                    'alias' => 'TutorialRole',
                    'table' => 'tutorials_roles',
                    'type' => 'LEFT',
                    'conditions' => 'TutorialRole.tutorial_id = Tutorial.id'
                ),
            ),
            'fields' => array(
                'Tutorial.*',
                'User.name',
                'User.surname',
            ),
            'order' => array(
                'Tutorial.order' => 'asc',
                'Tutorial.id' => 'desc'
            ),
            'group' => array(
                'Tutorial.id',
            )
        )

    );

    public function getAllTutorialsWithUser()
    {
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'User',
                    'table' => 'users',
                    'type' => 'INNER',
                    'conditions' => 'User.id = Tutorial.user_id'
                ),
                array(
                    'alias' => 'TutorialRole',
                    'table' => 'tutorials_roles',
                    'type' => 'LEFT',
                    'conditions' => 'TutorialRole.tutorial_id = Tutorial.id'
                ),
            ),
            'fields' => array(
                'Tutorial.*',
                'User.name',
                'User.surname',
            ),
            'order' => array(
                'Tutorial.order' => 'asc',
                'Tutorial.id' => 'desc',
            )
        ));
    }

    public function getTutorialsByUser($conditions, $page)
    {
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'User',
                    'table' => 'users',
                    'type' => 'INNER',
                    'conditions' => 'User.id = Tutorial.user_id'
                ),
                array(
                    'alias' => 'TutorialRole',
                    'table' => 'tutorials_roles',
                    'type' => 'LEFT',
                    'conditions' => 'TutorialRole.tutorial_id = Tutorial.id'
                ),
            ),
            'fields' => array(
                'Tutorial.*',
                'User.name',
                'User.surname',
            ),
            'conditions' => $conditions,
            'group' => array(
                'Tutorial.id',
            ),
            'order' => array(
                'Tutorial.order' => 'asc',
                'Tutorial.id' => 'desc',
            ),
            'page' => $page,
            'limit' => ConstantsPagination::SIZE_TUTORIALS
        ));
    }

    public function getMaxOrder()
    {
        return $this->find('first', array(
            'fields' => array(
                'MAX(Tutorial.order) as max_order',
            ),
        ));
    }

    public function getOneTutorialsWithUser($tutorial_id)
    {
        return $this->find('first', array(
            'joins' => array(
                array(
                    'alias' => 'User',
                    'table' => 'users',
                    'type' => 'LEFT',
                    'conditions' => 'User.id = Tutorial.user_id'
                ),
            ),
            'conditions' => array(
                'Tutorial.id' => $tutorial_id,
            ),
            'fields' => array(
                'Tutorial.*',
                'User.name',
                'User.surname',
            ),
            'order' => array(
                'Tutorial.order' => 'asc',
                'Tutorial.id' => 'desc'
            )
        ));
    }

    public function edit_tutorial($tutorial)
    {
        $fields = array(
            'Tutorial' => array(
                'id',
                'title',
                'url',
                'creation_date',
                'user_id',
                'order',
                'aag_region_id',
            )
        );

        $tutorial_bd = $this->guardar($tutorial, $fields);

        if (!$tutorial_bd) {
            return false;
        }

        $this->commit();
        return $tutorial_bd;
    }

    public function add_tutorial($tutorial)
    {
        $fields = array(
            'Tutorial' => array(
                'title',
                'url',
                'creation_date',
                'user_id',
                'order',
                'aag_region_id',
            )
        );

        $tutorial_bd = $this->save($tutorial, $fields);

        if (!$tutorial_bd) {
            return false;
        }

        return $tutorial_bd;
    }

    public function viewUserTutorials($user)
    {
        $role_id = $user['role_id'];
        $this->TutorialRole = ClassRegistry::init('TutorialRole');
        if ($role_id == ConstantsRoles::ADMIN) {
            $tutorials = $this->find('all');
            return array('Tutorial.id' => Hash::extract($tutorials, '{n}.Tutorial.id'));
        } else {
            $tutorials = $this->TutorialRole->findAllByRoleId($role_id);
            return array('TutorialRole.tutorial_id' => Hash::extract($tutorials, '{n}.TutorialRole.tutorial_id'));
        }
    }

    public function change_order($tutorial)
    {
        $fields = array(
            'Tutorial' => array(
                'id',
                'order',
            )
        );

        $tutorial_bd = $this->guardar($tutorial, $fields);

        if (!$tutorial_bd) {
            return false;
        }

        $this->commit();
        return $tutorial_bd;
    }
}

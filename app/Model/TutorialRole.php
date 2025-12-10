<?php

class TutorialRole extends AppModel
{
    public $useTable = 'tutorials_roles';

//    public function edit_tutorial( $tutorial ){
//        $fields = array(
//            'Tutorial' => array(
//                'id',
//                'title',
//                'url',
//                'creation_date',
//                'user_id',
//                'order',
//            )
//        );
//
//        $tutorial_bd = $this->guardar( $tutorial, $fields );
//
//        if(!$tutorial_bd){
//            return false;
//        }
//
//        $this->commit();
//        return $tutorial_bd;
//    }

    public function add( $roles , $tutorial){

        $fields = array(
            'TutorialRole' => array(
                'tutorial_id',
                'role_id',
            )
        );

        foreach($roles as $role){
            $tutorial_role = array(
                'TutorialRole' => array(
                    'tutorial_id' => $tutorial['Tutorial']['id'],
                    'role_id' => $role,
                )
            );
            $this->create();
            if(!$this->guardar( $tutorial_role, $fields )){
                return false;
            }
        }

        $this->commit();
        return true;
    }

    public function getRolesByTutorial( $tutorial_id ){
        return $this->find('list',array(
            'joins' => array(
                array(
                    'alias' => 'Tutorial',
                    'table' => 'tutorials',
                    'type' => 'LEFT',
                    'conditions' => 'TutorialRole.tutorial_id = Tutorial.id'
                ),
            ),
            'conditions' => array(
                'Tutorial.id' => $tutorial_id,
            ),
            'fields' => array(
                'TutorialRole.role_id',
            ),
            'order' => array(
                'Tutorial.id' => 'asc',
            )
        ));
    }

}
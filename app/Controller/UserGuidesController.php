<?php
class UserGuidesController extends AppController
{
    // Tutorial has been change to user guide, Model remains the same (Tutorial)
    public $uses = array(
        'Tutorial',
        'Role',
        'TutorialRole',
    );

    /**
     * User Guides home page.
     */
    public function home()
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_USER_GUIDE) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::USER_GUIDES)
            )
        )
        {
            $user = $this->Acceso->user();
            $roleId = $user['role_id'];
            $conditions = array();
            if ($roleId != ConstantsRoles::ADMIN && $roleId != ConstantsRoles::SUPER_ADMIN) {
                $conditions = array(
                    'TutorialRole.role_id' => $roleId
                );
            }
            $tutorials = $this->Tutorial->getTutorialsByUser($conditions, ConstantsPagination::FIRST_PAGE);

            $this->set(array(
                'tutorials' => $tutorials,
                'role_id' => $roleId
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Create user guide.
     */
    public function add()
    {
        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CREATE_USER_GUIDE) &&
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::ADMIN &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::USER_GUIDES)
        ) {
            if (!$this->request->is('get')) {
                $user = $this->Acceso->user();
                $tutorial = array(
                    'Tutorial' => array(
                        'user_id' => $user['id'],
                        'order' => 1,
                        'title' => $this->request->data['Tutorial']['title'],
                        'url' => $this->request->data['Tutorial']['url'],
                        'creation_date' => date('Y-m-d H:i:s'),
                    )
                );

                if ($tutorialBd = $this->Tutorial->add_tutorial($tutorial)) {
                    if ($this->TutorialRole->add($this->request->data['TutorialRole']['roles'], $tutorialBd)) {
                        $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                        $this->redirect(
                            array(
                                'controller' => 'user_guides',
                                'action' => 'edit',
                                $this->Tutorial->getLastInsertID()
                            )
                        );
                    } else {
                        $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                    }
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            }

            $this->setVarForm();
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit user guide.
     */
    public function edit($tutorial_id)
    {
        $tutorialBd = $this->Tutorial->getOneTutorialsWithUser($tutorial_id);
        if (
            $tutorialBd &&
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CREATE_USER_GUIDE) &&
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::ADMIN &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::USER_GUIDES)
        ) {
            $tutorialBd['TutorialRole']['roles'] = $this->TutorialRole->getRolesByTutorial($tutorial_id);

            if (!$this->request->is('get')) {

                $tutorial = array(
                    'Tutorial' => array(
                        'id' => $tutorial_id,
                        'title' => $this->request->data['Tutorial']['title'],
                        'url' => $this->request->data['Tutorial']['url'],
                        'creation_date' => $this->request->data['Tutorial']['creation_date'],
                        'user_id' => $this->request->data['Tutorial']['user_id'],
                        'order' => $this->request->data['Tutorial']['order'],
                    )
                );
                if ($this->Tutorial->edit_tutorial($tutorial)) {
                    //delete tutorial_roles
                    $tutorialRoles = $this->TutorialRole->findAllByTutorialId($tutorial_id);
                    foreach ($tutorialRoles as $tutorialRole) {
                        $this->TutorialRole->delete($tutorialRole['TutorialRole']['id']);
                    }

                    if ($this->TutorialRole->add($this->request->data['TutorialRole']['roles'], $tutorialBd)) {
                        $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                        $this->redirect($this->request->here);
                    } else {
                        $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                    }
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            } else {
                $this->request->data = $tutorialBd;
            }

            $this->setVarForm();
            $this->set(array(
                'tutorial_bd' => $tutorialBd,
                'tutorial_id' => $tutorial_id
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    private function setVarForm()
    {
        $cancelAction = array(
            'url_cancel' => array(
                'controller' => 'user_guides',
                'action' => 'home',
            ),
        );

        $nOrder = $this->Tutorial->getMaxOrder();
        $order = array();
        for ($x = 1; $x <= $nOrder[0]['max_order'] + 1; $x++) {
            $order[$x] = $x;
        }

        $roles = $this->Role->search_list();
        unset($roles[ConstantsRoles::ADMIN]);
        unset($roles[ConstantsRoles::SUPER_ADMIN]);
        $this->set(array(
            'cancel_action' => $cancelAction,
            'order' => $order,
            'roles' => $roles,
        ));
    }

    /**
     * Delete user guide.
     */
    public function delete_tutorial($tutorial_id)
    {
        $tutorialRoles = $this->TutorialRole->findAllByTutorialId($tutorial_id);
        if (
            $tutorialRoles &&
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CREATE_USER_GUIDE) &&
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::ADMIN &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::USER_GUIDES)
        ) {
            //delete tutorial_roles
            foreach ($tutorialRoles as $tutorialRole) {
                $this->TutorialRole->delete($tutorialRole['TutorialRole']['id']);
            }

            if ($this->Tutorial->delete($tutorial_id)) {
                $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_DELETED));
            } else {
                $this->Session->setFlashError(__t(ConstantsMessages::BAD_DELETED));
            }

            $this->redirect(array(
                'controller' => 'user_guides',
                'action' => 'home'
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX load more user guides.
     */
    public function ajax_load_more()
    {
        $this->verify_ajax($this->request);

        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_USER_GUIDE) &&
                CakeSession::read('Auth.User.role_id') != ConstantsRoles::GARAGE &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::USER_GUIDES)
            )
        ) {
            $roleId = $this->Session->read('Auth.User.role_id');
            $page = $this->request->query['page'];
            $conditions = array();
            if ($roleId != ConstantsRoles::ADMIN) {
                $conditions = array(
                    'TutorialRole.role_id' => $roleId
                );
            }
            $tutorials = $this->Tutorial->getTutorialsByUser($conditions, $page);

            $totalTutorials = $this->Tutorial->find('count');
            $loadMore = $totalTutorials / $page;

            $this->set(array(
                'tutorials' => $tutorials,
                'load_more' => $loadMore,
                'role_id' => $roleId
            ));
            $this->layout = false;
            $this->render('../Tutorials/Elements/results_table');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX change order user guides.
     */
    public function ajax_set_order()
    {
        $this->verify_ajax($this->request);
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_USER_GUIDE) &&
                CakeSession::read('Auth.User.role_id') != ConstantsRoles::GARAGE &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::USER_GUIDES)
            )
        ) {
            $tutorials = $this->request->data;
            foreach ($tutorials as $tutorialId => $order) {
                $tutorialTmp = array(
                    'Tutorial' => array(
                        'id' => $tutorialId,
                        'order' => $order,
                    )
                );
                $this->Tutorial->change_order($tutorialTmp);
            }
            $this->autoRender = false;
        } else {
            throw new UnauthorizedException();
        }
    }
}

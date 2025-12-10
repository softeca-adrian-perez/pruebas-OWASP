<?php
class GroupsPermissionsController extends AppController
{
    public $uses = array(
        'GroupPermission',
        'GroupPermissionPermission',
        'PositionConfig',
        'PositionConfigType',
        'LogChange',
    );

    private function setVarForm($group_permission_id)
    {
        $this->set(
            array(
                'group_permission_id' => $group_permission_id,
            )
        );
    }

    /**
     * Groups permissions maintenance page.
     */
    public function home()
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->haveDefaultPermission(ConstantsPermissionsGrouping::PERMISSIONS, ConstantsPermissionsGrouping::MAINTENANCE) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
            )
        ) {
            $searcher = $this->request->query;
            $this->request->data['Buscador'] = $searcher;

            $groupPermissions = $this->custom_pagination(
                $this->GroupPermission->_query('home'),
                $this->GroupPermission->conditions($searcher)
            );

            $configTypes = $this->PositionConfigType->search_list();

            $this->set(
                array(
                    'group_permissions' => $groupPermissions,
                    'config_types' => $configTypes,
                )
            );
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Get group permissions by type.
     */
    public function ajax_generate_group_permissions()
    {
        $this->verify_ajax($this->request);

        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->haveDefaultPermission(ConstantsPermissionsGrouping::PERMISSIONS, ConstantsPermissionsGrouping::MAINTENANCE) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
            )
        ) {
            $type = $this->request->data['type'];
            $permissions = $this->GroupPermission->Permission->getPermissionsByType($type);

            $this->set(
                array(
                    'permissions' => $permissions
                )
            );

            $this->layout = null;
            $this->render('/GroupsPermissions/Elements/permissions_table');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Create group permission.
     */
    public function add($group_permission_id = null)
    {
        if (
            $this->haveDefaultPermission(ConstantsPermissionsGrouping::PERMISSIONS, ConstantsPermissionsGrouping::MAINTENANCE) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
        ) {
            $this->GroupPermission->validate['name_en'] = array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_name',
            );
            $positionConfigTypes = $this->PositionConfigType->search_list();
            $url = Router::url(array(
                'controller' => 'groups_permissions',
                'action' => 'add'
            ));

            if (!$this->request->is('get')) {
                if (!empty($this->request->data['GroupPermission']['position_config_type_id_position'])) {
                    $this->request->data['GroupPermission']['position_config_type_id'] = $this->request->data['GroupPermission']['position_config_type_id_position'];
                }

                if ($this->GroupPermission->add($this->request->data)) {
                    $this->LogChange->get_params_create_log_add(
                        $this->request->data['GroupPermission'],
                        $this->GroupPermission->table,
                        $this->Session->read('Auth'),
                        null,
                        null
                    );

                    $permissionsTmp = $this->GroupPermissionPermission->getAllByGroupPermissionId($this->GroupPermission->id);
                    if (!empty($permissionsTmp)) {
                        foreach ($permissionsTmp as $permissionTmp) {
                            $permissionsLog['permission_id'] = $permissionTmp;
                        }

                        $this->LogChange->get_params_create_log_add(
                            $permissionsLog,
                            $this->GroupPermissionPermission->table,
                            $this->Session->read('Auth'),
                            null,
                            null
                        );
                    }
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    $this->redirect(
                        array(
                            'controller' => 'groups_permissions',
                            'action' => 'edit',
                            $this->GroupPermission->getLastInsertID()
                        )
                    );
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            } else {
                if ($group_permission_id != null) {
                    $this->GroupPermission->contain(
                        array(
                            'Permission'
                        )
                    );
                    $groupPermission = $this->GroupPermission->findById($group_permission_id);
                    $this->request->data = $this->GroupPermission->completeInformationMatrixPermissions($groupPermission);
                    $this->request->data['GroupPermission'] = array();
                }
            }

            $this->set(
                array(
                    'permissions' => $this->GroupPermission->Permission->getPermissionsByType(),
                    'position_config_types' => $positionConfigTypes,
                    'url' => $url
                )
            );
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX create group permission.
     */
    public function ajax_add()
    {
        $this->verify_ajax($this->request);

        if (
            $this->haveDefaultPermission(ConstantsPermissionsGrouping::PERMISSIONS, ConstantsPermissionsGrouping::MAINTENANCE) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
        ) {
            $this->GroupPermission->validate['name_en'] = array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_name',
            );

            $type = $this->request->data['type'];
            $url = Router::url(array(
                'controller' => 'groups_permissions',
                'action' => 'add'
            ));

            $permissions =  $this->GroupPermission->Permission->getPermissionsByType($type);
            $positionConfigTypes = $this->PositionConfigType->search_list();

            $this->set(
                array(
                    'permissions' => $permissions,
                    'position_config_types' => $positionConfigTypes,
                    'url' => $url
                )
            );

            $this->layout = false;
            $this->render('/GroupsPermissions/form');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get last group permission ID.
     */
    public function ajax_get_last_group_permission_id()
    {
        $this->verify_ajax($this->request);

        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->haveDefaultPermission(ConstantsPermissionsGrouping::PERMISSIONS, ConstantsPermissionsGrouping::MAINTENANCE) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
            )
        ) {
            $this->layout = $this->autoRender = false;
            $lastElement = $this->GroupPermission->find('first', array('order' => 'id DESC', 'fields' => 'id'));

            return $lastElement['GroupPermission']['id'];
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit group permission.
     */
    public function edit($group_permission_id = null)
    {
        $groupPermission = $this->GroupPermission->findById($group_permission_id);

        if (
            $groupPermission &&
            $this->haveDefaultPermission(ConstantsPermissionsGrouping::PERMISSIONS, ConstantsPermissionsGrouping::MAINTENANCE) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
        ) {
            $this->GroupPermission->validate['name_en'] = array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_name',
            );

            $positionConfigTypes = $this->PositionConfigType->search_list();
            $url = Router::url(array(
                'controller' => 'groups_permissions',
                'action' => 'edit',
                $group_permission_id
            ));

            $this->GroupPermission->contain(
                array(
                    'Permission'
                )
            );

            $permissions = $this->GroupPermissionPermission->getAllByGroupPermissionId($group_permission_id);

            if ($this->request->is('get')) {
                // Add the permissions you already have, including the permissions group
                $this->request->data = $this->GroupPermission->completeInformationMatrixPermissions($groupPermission);
            } else {
                $this->request->data['GroupPermission']['id'] = $group_permission_id;

                if ($this->GroupPermission->edit($this->request->data)) {
                    $this->LogChange->get_params_create_log_edit(
                        $groupPermission['GroupPermission'],
                        $this->request->data['GroupPermission'],
                        $this->GroupPermission->table,
                        $this->Session->read('Auth'),
                        $group_permission_id,
                        ConstantsLogType::GROUP_PERMISSION
                    );

                    $permissionsLog = $this->GroupPermissionPermission->getAllByGroupPermissionId($group_permission_id);
                    if (!empty($permissions)) {
                        $permissions = array_combine($permissions, $permissions);
                        if (!empty($permissionsLog)) {
                            $permissionsLog = array_combine($permissionsLog, $permissionsLog);
                        }
                    }

                    $this->LogChange->get_params_create_log_edit(
                        $permissions,
                        $permissionsLog,
                        $this->GroupPermissionPermission->table,
                        $this->Session->read('Auth'),
                        $group_permission_id,
                        ConstantsLogType::GROUP_PERMISSION,
                        'permission_id'
                    );

                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));

                    $this->redirect($this->request->here);
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            }

            $this->setVarForm($group_permission_id);

            $this->set(
                array(
                    'permissions' => $this->GroupPermission->Permission->getPermissionsByType($groupPermission['GroupPermission']['position_config_type_id']),
                    'position_config_types' => $positionConfigTypes,
                    'url' => $url,
                    'checked_permissions' => $permissions
                )
            );
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Delete group permission.
     */
    public function delete_group_permissions($id)
    {
        $groupPermission = $this->GroupPermission->findById($id);

        if (
            $groupPermission &&
            $this->haveDefaultPermission(ConstantsPermissionsGrouping::PERMISSIONS, ConstantsPermissionsGrouping::MAINTENANCE) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
        ) {
            $positionConfig = $this->PositionConfig->findByGroupPermissionId($id);
            if (empty($positionConfig)) {
                if ($this->GroupPermission->delete($id)) {
                    $groupsPermissionsPermissions = $this->GroupPermissionPermission->find(
                        'list',
                        array('conditions' => array('group_permission_id' => $id))
                    );
                    $this->GroupPermissionPermission->deleteAll($groupsPermissionsPermissions);

                    unset($groupPermission['GroupPermission']['is_fixed']);
                    unset($groupPermission['GroupPermission']['active']);
                    $this->LogChange->get_params_create_log_delete(
                        $groupPermission['GroupPermission'],
                        $this->GroupPermission->table,
                        $this->Session->read('Auth'),
                        null,
                        null
                    );
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_DELETED));
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_DELETED_PERMISSION_GROUP));
                }
            } else {
                $this->Session->setFlashError(__t(ConstantsMessages::BAD_DELETED_PERMISSION_GROUP));
            }

            $this->redirect(
                array(
                    'controller' => 'groups_permissions',
                    'action' => 'home',
                )
            );
        } else {
            throw new UnauthorizedException();
        }
    }
}

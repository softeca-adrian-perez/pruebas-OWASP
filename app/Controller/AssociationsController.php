<?php
class AssociationsController extends AppController
{
    public $uses = array(
        'Association',
        'Distributor',
    );

    /**
     * Associations home page.
     */
    public function home()
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->haveDefaultPermission(ConstantsPermissionsGrouping::ASSOCIATIONS, ConstantsPermissionsGrouping::MAINTENANCE) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
            )
        ) {
            $associations = $this->Association->find(
                'all',
                array(
                    'order' => 'name',
                )
            );

            $this->set(
                array(
                    'associations' => $associations,
                )
            );
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX create association.
     */
    public function ajax_add_association($search)
    {
        $this->verify_ajax($this->request);

        if (
            $this->haveDefaultPermission(ConstantsPermissionsGrouping::ASSOCIATIONS, ConstantsPermissionsGrouping::MAINTENANCE) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
        ) {
            if (!$this->request->is('get')) {
                $flag = 0;
                if (empty($this->request->data['Association']['name'])) {
                    $flag = ConstantsFlag::ERROR_NAME_EMPTY;
                }
                if ($flag == ConstantsFlag::ERROR_NO) {
                    $associationBd = array(
                        'Association' => array(
                            'name' => $this->request->data['Association']['name'],
                        )
                    );
                    $association = $this->Association->new_association($associationBd);
                    if (!$association) {
                        $error = ConstantsAlertsErrors::ERROR_GENERAL;
                    }
                } else {
                    $error = ConstantsAlertsErrors::ERROR_NAME_EMPTY;
                }

                $this->checkSearch($search);

                if (isset($error)) {
                    $this->Session->setFlashError(__t($error));
                } else {
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                }

                $this->layout = null;
                $this->render('../Associations/Elements/table_associations');
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX edit association.
     */
    public function ajax_edit_association($association_id, $search)
    {
        $this->verify_ajax($this->request);

        $association = $this->Association->findById($association_id, 'id');
        if (
            $association &&
            $this->haveDefaultPermission(ConstantsPermissionsGrouping::ASSOCIATIONS, ConstantsPermissionsGrouping::MAINTENANCE) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
        ) {
            if (!$this->request->is('get')) {
                $flag = 0;
                if (empty($this->request->data['Association']['name'])) {
                    $flag = ConstantsFlag::ERROR_NAME_EMPTY;
                }
                if ($flag != ConstantsFlag::ERROR_NAME_EMPTY) {
                    $data = array(
                        'Association' => array(
                            'id' => $association_id,
                            'name' => $this->request->data['Association']['name'],
                            'international_code' => null,
                        )
                    );
                }
                if ($flag == ConstantsFlag::ERROR_NO) {
                    $edit = $this->Association->save($data);
                    if (!$edit) {
                        $error = ConstantsAlertsErrors::ERROR_GENERAL;
                    }
                } elseif ($flag == ConstantsFlag::ERROR_NAME_EMPTY) {
                    $error = ConstantsAlertsErrors::ERROR_NAME_EMPTY;
                } elseif ($flag == ConstantsFlag::ERROR_DIMENSIONS) {
                    $error = ConstantsAlertsErrors::ERROR_DIMENSIONS;
                } elseif ($flag == ConstantsFlag::ERROR_EXTENSION) {
                    $error = ConstantsAlertsErrors::ERROR_EXTENSION;
                }
            }

            $this->checkSearch($search);

            if (isset($error)) {
                $this->Session->setFlashError(__t($error));
            } else {
                $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
            }

            $this->layout = null;
            $this->render('../Associations/Elements/table_associations');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX delete association.
     */
    public function ajax_delete_association($association_id, $search)
    {
        $this->verify_ajax($this->request);

        $association = $this->Association->findById($association_id, 'id');
        if (
            $association &&
            $this->haveDefaultPermission(ConstantsPermissionsGrouping::ASSOCIATIONS, ConstantsPermissionsGrouping::MAINTENANCE) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
        ) {
            if (!$this->request->is('get')) {
                //Check if there is a distributor with this association_id
                $distributorTmp = $this->Distributor->findByAssociationId($association_id);
                if (empty($distributorTmp)) {
                    $delete = $this->Association->delete($association_id);
                    if (!$delete) {
                        $error = ConstantsAlertsErrors::ERROR_GENERAL;
                    }
                } else {
                    $error = ConstantsAlertsErrors::ERROR_DELETE_ASSOCIATIONS . ' ' . $distributorTmp['Distributor']['name'];
                }

                $this->checkSearch($search);

                if (isset($error)) {
                    $this->Session->setFlashError(__t($error));
                } else {
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                }

                $this->layout = null;
                $this->render('../Associations/Elements/table_associations');
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Load search association.
     */
    public function search_association($search)
    {
        $this->verify_ajax($this->request);

        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->haveDefaultPermission(ConstantsPermissionsGrouping::ASSOCIATIONS, ConstantsPermissionsGrouping::MAINTENANCE) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
            )
        ) {
            $this->checkSearch($search);

            $this->layout = null;
            $this->render('../Associations/Elements/table_associations');
        } else {
            throw new UnauthorizedException();
        }
    }

    private function checkSearch($search)
    {
        if ($search == 'all') {
            $associations = $this->Association->find(
                'all',
                array(
                    'order' => 'name',
                )
            );
        } else {
            $associations = $this->Association->find('all', array(
                'conditions' => array(
                    'name' . ' LIKE' => '%' . $search . '%'
                ),
                'order' => 'name',
            ));
        }

        $this->set(
            array(
                'associations' => $associations,
            )
        );
    }
}

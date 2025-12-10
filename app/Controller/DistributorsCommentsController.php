<?php
class DistributorsCommentsController extends AppController
{
    public $uses = array(
        'Distributor',
        'DistributorComment',
        'User',
        'LogChange'
    );

    /**
     * DistributorComments view page.
     */
    public function view($distributor_comment_id)
    {
        $distributorComment = $this->DistributorComment->findById($distributor_comment_id);

        if (
            $distributorComment &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                (
                    $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR) &&
                    CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS)
                )
            )
        ) {
            $users = $this->User->find('list');
            $cancelAction = array(
                'url_cancel' => array(
                    'controller' => 'distributors',
                    'action' => 'add_comment',
                    $distributorComment['DistributorComment']['distributor_id']
                ),
            );

            $this->setVarForm();
            $this->set(array(
                'user' => $this->Session->read('Auth'),
                'distributor_comments' => $distributorComment,
                'users' => $users,
                'cancel_action' => $cancelAction,
                'distributor_id' => $distributor_comment_id
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Create DistributorComment.
     */
    public function add($distributor_id)
    {
        $distributor = $this->Distributor->findByIdAndAagRegionId($distributor_id, CakeSession::read('Auth.User.aag_region_id'));

        if (
            $distributor &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR) &&
            CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS) &&
            $this->Acceso->haveTradingGroupPermission(ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR, $distributor['Distributor']['trading_group_id'])
        ) {
            $cancelAction = array(
                'url_cancel' => array(
                    'controller' => 'distributors',
                    'action' => 'add_comments',
                    $distributor_id
                ),
            );

            if (!$this->request->is('get')) {
                $distributorComment = $this->DistributorComment->add_distributor_comment($this->request->data, $distributor_id, CakeSession::read('Auth.User.id'));
                if ($distributorComment) {
                    $this->LogChange->get_params_create_log_add(
                        $distributorComment['DistributorComment'],
                        $this->DistributorComment->table,
                        $this->Session->read('Auth'),
                        $distributor_id,
                        ConstantsLogType::DISTRIBUTOR
                    );

                    $this->Session->setFlashSuccess(__t('Distributor.Comment_added'));
                    $this->redirect(
                        array(
                            'controller' => 'distributors_comments',
                            'action' => 'edit',
                            $this->DistributorComment->getLastInsertID(),
                        )
                    );
                } else {
                    $this->Session->setFlashError(__t('Distributor.Comment_error_add'));
                }
            }

            $this->setVarForm();
            $this->set(array(
                'cancel_action' => $cancelAction,
                'distributor_id' => $distributor_id
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit DistributorComment.
     */
    public function edit($distributor_comment_id)
    {
        $distributorComment = $this->DistributorComment->findById($distributor_comment_id);

        if ($distributorComment) {
            $distributor = $this->Distributor->findByIdAndAagRegionId($distributorComment['DistributorComment']['distributor_id'], CakeSession::read('Auth.User.aag_region_id'));

            if (
                $distributor &&
                $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR) &&
                CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS) &&
                $this->Acceso->haveTradingGroupPermission(ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR, $distributor['Distributor']['trading_group_id'])
            ) {
                $cancelAction = array(
                    'url_cancel' => array(
                        'controller' => 'distributors_comments',
                        'action' => 'view',
                        $distributorComment['DistributorComment']['id']
                    ),
                );

                if (!$this->request->is('get')) {
                    $distributorBd = $this->DistributorComment->edit_distributor_comment($this->request->data, CakeSession::read('Auth.User.id'));
                    if ($distributorBd) {
                        $this->LogChange->get_params_create_log_edit(
                            $distributorComment['DistributorComment'],
                            $distributorBd['DistributorComment'],
                            $this->DistributorComment->table,
                            $this->Session->read('Auth'),
                            $distributorComment['DistributorComment']['distributor_id'],
                            ConstantsLogType::DISTRIBUTOR
                        );
                        $this->Session->setFlashSuccess(__t('Distributor.Comment_edited'));
                        $this->redirect($this->request->here);
                    } else {
                        $this->Session->setFlashError(__t('Distributor.Comment_error_edit'));
                    }
                } else {
                    $this->request->data = $distributorComment;
                }

                $this->setVarForm();
                $this->set(array(
                    'distributor_comments' => $distributorComment,
                    'distributor_id' =>  $distributorComment['DistributorComment']['distributor_id'],
                    'cancel_action' => $cancelAction,
                ));
            } else {
                header('HTTP/1.0 401 Unauthorized');
                exit;
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Delete DistributorComment.
     */
    public function delete($distributor_id, $distributor_comment_id)
    {
        $distributorComment = $this->DistributorComment->findById($distributor_comment_id);
        $distributor = $this->Distributor->findByIdAndAagRegionId($distributor_id, CakeSession::read('Auth.User.aag_region_id'));

        if (
            $distributorComment && $distributor &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR) &&
            CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS) &&
            $this->Acceso->haveTradingGroupPermission(ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR, $distributor['Distributor']['trading_group_id'])
        ) {
            if ($this->DistributorComment->delete($distributor_comment_id)) {
                $this->LogChange->get_params_create_log_delete(
                    $distributorComment['DistributorComment'],
                    $this->DistributorComment->table,
                    $this->Session->read('Auth'),
                    $distributor_id,
                    ConstantsLogType::DISTRIBUTOR
                );
                $this->Session->setFlashSuccess(__t('Distributor.Comment_deleted'));
            } else {
                $this->Session->setFlashError(__t('Distributor.Comment_error_delete'));
            }

            $this->redirect(array(
                'controller' => 'distributors',
                'action' => 'add_comments',
                $distributor_id
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    private function setVarForm()
    {
        $comment = $this->DistributorComment->find('list');

        $this->set(array(
            'comment' => $comment,
        ));
    }
}

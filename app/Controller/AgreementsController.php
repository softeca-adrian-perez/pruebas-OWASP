<?php

set_time_limit(60 * 60);

class AgreementsController extends AppController
{
    public $uses = array(
        'Agreement',
        'GarageAgreement',
        'AagRegion'
    );

    /**
     * Agreements home page.
     */
    public function home()
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_AGREEMENTS) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::AGREEMENTS)
            )
        ) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];

            $externalAgreements = $this->Agreement->find(
                'all',
                array(
                    'conditions' => array('Agreement.aag_region_id' => $aagRegionId)
                )
            );

            $this->ApiRm = ClassRegistry::init("ApiRm.ApiRm");
            $internalAgreements = $this->ApiRm->get_internal_agreements();

            $this->set(array(
                'external_agreements' => $externalAgreements,
                'internal_agreements' => $internalAgreements,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Add new agreement.
     */
    public function add()
    {
        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CREATE_AGREEMENTS) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::AGREEMENTS)
        ) {
            $this->setVarDate();

            if (!$this->request->is('get')) {
                $agreement = $this->Agreement->add_agreement($this->request->data);
                if ($agreement) {
                    $msg = h(sprintf(__t('Agreement.Well_add'), $agreement['Agreement']['nombre']));
                    $this->Session->setFlashSuccess($msg);
                    $this->redirect(
                        array(
                            'controller' => 'agreements',
                            'action' => 'home',
                        )
                    );
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
     * Edit agreement with the specified ID.
     */
    public function edit($agreement_id)
    {
        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $agreement = $this->Agreement->findByIdAndAagRegionId($agreement_id, $aagRegionId);
        if (
            $agreement &&
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CREATE_AGREEMENTS) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::AGREEMENTS)
        ) {
            if (!$this->request->is('get')) {
                $agreement_bd = $this->Agreement->edit_agreement($this->request->data);
                if ($agreement_bd) {
                    $msg = h(sprintf(__t('Agreement.Well_edit'), $agreement['Agreement']['nombre']));
                    $this->Session->setFlashSuccess($msg);
                    $this->redirect(
                        array(
                            'controller' => 'agreements',
                            'action' => 'home',
                        )
                    );
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            } else {
                $this->request->data = $agreement;
            }

            $this->setVarDate();
            $this->setVarForm();
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX delete agreement with the specified ID.
     */
    public function ajax_delete($agreement_id)
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $agreement = $this->Agreement->findByIdAndAagRegionId($agreement_id, $aagRegionId);
        if (
            $agreement &&
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CREATE_AGREEMENTS) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::AGREEMENTS)
        ) {
            $canDelete = true;
            $canDeleteText = 'true';
            $errorText = '';

            $models_relationship = $this->getModels();

            if (!empty($agreement)) {
                foreach ($models_relationship as $model => $translation) {
                    $object = $this->$model->findByAgreementId($agreement_id);
                    if (!empty($object)) {
                        $canDelete = false;
                        $errorText = $translation;
                        break;
                    }
                }
            }

            if ($canDelete) {
                $conditions = array(
                    'agreement_id' => $agreement_id
                );
                //Delete the relationship between Agreement and GarageAgreement
                $garageAgreement = $this->GarageAgreement->deleteAll($conditions);

                if ($garageAgreement) {
                    $agreementBd = $this->Agreement->delete($agreement_id);
                    if (!$agreementBd) {
                        $canDeleteText = 'false';
                    }
                } else {
                    $canDeleteText = 'false';
                }
            } else {
                $canDeleteText = 'false';
            }

            $ret = array(
                'precess' => $canDeleteText,
                'error_text' => $errorText
            );

            $js_array = json_encode($ret);
            echo $js_array;

            $this->layout = $this->autoRender = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    private function getModels()
    {
        return array(
            'GarageAgreement' =>  __t('General.Relation_with') . ' ' . __t('GarageAgreement.GarageAgreement'),
        );
    }

    private function setVarForm()
    {
        $user = $this->Acceso->user();
        $userAagRegionId = $user['aag_region_id'];
        $userRoleId = $user['role_id'];
        $aagRegions = $this->AagRegion->region_list();

        $cancel_action = array(
            'url_cancel' => array(
                'controller' => 'agreements',
                'action' => 'home',
            ),
        );

        $this->set(array(
            'cancel_action' => $cancel_action,
            'aag_regions' => $aagRegions,
            'user_aag_region_id' => $userAagRegionId,
            'user_role_id' => $userRoleId
        ));
    }

    private function setVarDate()
    {
        $date = getdate();
        $dayWeek = array(
            '1' => __t('Garage.Monday'),
            '2' => __t('Garage.Tuesday'),
            '3' => __t('Garage.Wednesday'),
            '4' => __t('Garage.Thursday'),
            '5' => __t('Garage.Friday'),
            '6' => __t('Garage.Saturday'),
            '7' => __t('Garage.Sunday'),
        );
        $date = $date['mday'] . '/' . $date['mon'] . '/' . $date['year'];

        $this->set(array(
            'date' => $date,
            'day_week' => $dayWeek[date('N')],
        ));
    }
}

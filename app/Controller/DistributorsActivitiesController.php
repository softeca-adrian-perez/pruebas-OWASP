<?php
class DistributorsActivitiesController extends AppController
{
    public $uses = array(
        'Distributor',
        'DistributorCustomerActivity',
        'DistributorCustomerActivityWorkshop',
        'CustomerActivity',
        'WorkshopActivity',
        'LogChange',
    );

    /**
     * Create distributor activity.
     */
    public function add($distributor_id)
    {
        $distributor = $this->Distributor->findByIdAndAagRegionId($distributor_id, CakeSession::read('Auth.User.aag_region_id'), 'id');

        if (
            $distributor &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR) &&
            CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS)
        ) {
            $customerActivities = $this->CustomerActivity->search_list();

            $activitiesUnavailable = $this->DistributorCustomerActivity->getActivitiesByDistributor($distributor_id);
            foreach ($activitiesUnavailable as $activityUnavailable) {
                unset($customerActivities[$activityUnavailable]);
            }

            $workshopActivities = $this->WorkshopActivity->search_list();
            $cancelAction = array(
                'url_cancel' => array(
                    'controller' => 'distributors',
                    'action' => 'add_activity',
                    $distributor_id
                ),
            );

            if (!$this->request->is('get')) {
                $distributorActivityBd = $this->DistributorCustomerActivity->add_activity($this->request->data, $distributor_id);
                if ($distributorActivityBd) {
                    if (isset($this->request->data['DistributorCustomerActivity']['workshop_activity_id']) && ($this->request->data['DistributorCustomerActivity']['workshop_activity_id'])) {
                        foreach ($this->request->data['DistributorCustomerActivity']['workshop_activity_id'] as $key => $workshop_activity_id) {
                            $this->DistributorCustomerActivityWorkshop->add_activity_workshop($distributorActivityBd['DistributorCustomerActivity']['id'], $workshop_activity_id, $this->request->data['DistributorCustomerActivity']['activity_details'][$key]);
                        }
                    }
                    unset($distributorActivityBd['DistributorCustomerActivity']['distributor_id']);
                    $this->LogChange->get_params_create_log_add(
                        $distributorActivityBd['DistributorCustomerActivity'],
                        $this->DistributorCustomerActivity->table,
                        $this->Session->read('Auth'),
                        $distributor_id,
                        ConstantsLogType::DISTRIBUTOR
                    );

                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    $this->redirect(
                        array(
                            'controller' => 'distributors_activities',
                            'action' => 'edit',
                            $this->DistributorCustomerActivity->getLastInsertID(),
                            $distributor_id
                        )
                    );
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            }

            $this->set(array(
                'cancel_action' => $cancelAction,
                'distributor_id' => $distributor_id,
                'customer_activities' => $customerActivities,
                'workshop_activities' => $workshopActivities
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit distributor activity.
     */
    public function edit($activity_id, $distributor_id)
    {
        $distributorCustomerActivity = $this->DistributorCustomerActivity->findById($activity_id);

        if (
            $distributorCustomerActivity &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR) &&
            CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS)
        ) {
            $customerActivities = $this->CustomerActivity->search_list();
            $activitiesUnavailable = $this->DistributorCustomerActivity->getActivitiesByDistributor($distributor_id);
            unset($activitiesUnavailable[$activity_id]);
            foreach ($activitiesUnavailable as $activityUnavailable) {
                unset($customerActivities[$activityUnavailable]);
            }

            $workshopActivities = $this->WorkshopActivity->search_list();
            $activitiesWorkshopsTmp = $this->DistributorCustomerActivityWorkshop->findAllByDistributorCustomerActivityId($distributorCustomerActivity['DistributorCustomerActivity']['id']);
            foreach ($activitiesWorkshopsTmp as $activityWorkshopTmp) {
                $distributorCustomerActivity['DistributorCustomerActivity']['workshops'][$activityWorkshopTmp['DistributorCustomerActivityWorkshop']['workshop_activity_id']] = $activityWorkshopTmp['DistributorCustomerActivityWorkshop']['activity_details'];
            }
            $cancelAction = array(
                'url_cancel' => array(
                    'controller' => 'distributors',
                    'action' => 'add_activity',
                    $distributor_id
                ),
            );

            if (!$this->request->is('get')) {
                $oldData = $this->DistributorCustomerActivity->findById($activity_id);
                $activityBd = $this->DistributorCustomerActivity->edit_activity($this->request->data, $distributor_id);
                if ($activityBd) {
                    $activitiesWorkshopsTmpDelete = $this->DistributorCustomerActivityWorkshop->findAllByDistributorCustomerActivityId($distributorCustomerActivity['DistributorCustomerActivity']['id']);
                    foreach ($activitiesWorkshopsTmpDelete as $activityWorkshopDelete) {
                        $this->DistributorCustomerActivityWorkshop->delete($activityWorkshopDelete['DistributorCustomerActivityWorkshop']['id']);
                    }
                    if (isset($this->request->data['DistributorCustomerActivity']['workshop_activity_id']) && is_array($this->request->data['DistributorCustomerActivity']['workshop_activity_id'])) {
                        foreach ($this->request->data['DistributorCustomerActivity']['workshop_activity_id'] as $key => $workshop_activity_id) {
                            $this->DistributorCustomerActivityWorkshop->add_activity_workshop($activityBd['DistributorCustomerActivity']['id'], $workshop_activity_id, $this->request->data['DistributorCustomerActivity']['activity_details'][$key]);
                        }
                    }
                    if ($oldData != $activityBd) {
                        unset($activityBd['DistributorCustomerActivity']['distributor_id']);
                        $this->LogChange->get_params_create_log_edit(
                            $oldData['DistributorCustomerActivity'],
                            $activityBd['DistributorCustomerActivity'],
                            $this->DistributorCustomerActivity->table,
                            $this->Session->read('Auth'),
                            $distributor_id,
                            ConstantsLogType::DISTRIBUTOR
                        );
                    }
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    $this->redirect($this->request->here);
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            } else {
                $distributorCustomerActivity['DistributorCustomerActivity']['start_date'] = Fecha::toFormatoVistaFecha($distributorCustomerActivity['DistributorCustomerActivity']['start_date']);
                $distributorCustomerActivity['DistributorCustomerActivity']['end_date'] = Fecha::toFormatoVistaFecha($distributorCustomerActivity['DistributorCustomerActivity']['end_date']);
                $this->request->data = $distributorCustomerActivity;
            }
            $this->set(array(
                'cancel_action' => $cancelAction,
                'distributor_id' => $distributor_id,
                'activity' => $distributorCustomerActivity,
                'customer_activities' => $customerActivities,
                'workshop_activities' => $workshopActivities
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Delete distributor activity.
     */
    public function delete($distributor_activity_id, $distributor_id)
    {
        $distributorCustomerActivity = $this->DistributorCustomerActivity->findById($distributor_activity_id);

        if (
            $distributorCustomerActivity &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR) &&
            CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS)
        ) {
            $workshopActivities = $this->DistributorCustomerActivityWorkshop->findAllByDistributorCustomerActivityId($distributor_activity_id);
            foreach ($workshopActivities as $workshop_activity) {
                $this->DistributorCustomerActivityWorkshop->delete($workshop_activity['DistributorCustomerActivityWorkshop']['id']);
            }
            $this->DistributorCustomerActivity->delete($distributor_activity_id);

            $user = $this->Session->read('Auth');
            $this->LogChange->get_params_create_log_delete(
                $distributorCustomerActivity['DistributorCustomerActivity'],
                $this->DistributorCustomerActivity->table,
                $user,
                $distributor_id,
                ConstantsLogType::DISTRIBUTOR
            );

            $this->redirect(
                array(
                    'controller' => 'distributors',
                    'action' => 'add_activity',
                    $distributor_id
                )
            );
        } else {
            throw new UnauthorizedException();
        }
    }
}

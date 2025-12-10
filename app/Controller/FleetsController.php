<?php
App::uses('PaginatorOrderCustomComponent', 'Controller/Component');
class FleetsController extends AppController
{
    public $uses = array(
        'Fleet',
        'Country',
        'Province',
        'User',
        'Permission',
        'Role',
        'Erp',
        'AagRegion',
        'Network',
        'FleetNetwork'
    );

    /**
     * Fleets home page.
     */
    public function home()
    {
        $user = $this->Acceso->User();
        $roleId = $user['role_id'];
        $aagRegionId = $user['aag_region_id'];

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            $this->Acceso->haveModulePermission(ConstantsConfigModules::FLEET)
        ) {
            $fleets = $this->custom_pagination(
                $this->Fleet->_query('home'),
                array('Fleet.aag_region_id' => $aagRegionId),
                ConstantsPagination::SIZE_PAGE_SMALL,
                'Fleet',
                null,
                'PaginatorOrderCustom'
            );

            $this->set(array(
                'fleets' => $fleets
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Create Fleet.
     */
    public function add()
    {
        $user = $this->Acceso->User();
        $aagRegionId = $user['aag_region_id'];

        if ($this->Acceso->haveModulePermission(ConstantsConfigModules::FLEET)) {
            $conditions = array('id' => $aagRegionId);
            $aagRegionName = $this->AagRegion->region_list_conditions($conditions);

            $countries = $this->Country->get_country_by_region($aagRegionId);
            $provinces = $this->Province->getListByAagRegion($aagRegionId);
            $erpProviders = $this->Erp->getErpsByAagRegionId($aagRegionId);

            if (!$this->request->is('get')) {
                $fleetBd = $this->request->data;
                $fleetBd['Fleet']['aag_region_id'] = $aagRegionId;
                if (!isset($this->request->data['Fleet']['networks'][0])) {
                    $this->Session->setFlashError(__t('Fleet.Select_network'));
                } else {
                    $fleetBd = $this->Fleet->add_fleet($fleetBd);
                    if ($fleetBd) {
                        $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                        $this->redirect($this->request->here);
                    } else {
                        $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                    }
                }
            }

            $this->setVarForm();

            $this->set(array(
                'provinces' => $provinces,
                'countries' => $countries,
                'erp_providers' => $erpProviders,
                'user_aag_region_name' => $aagRegionName,
                'networks' => $this->Network->getListByRegion($aagRegionId)
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit Fleet.
     */
    public function edit($fleet_guid)
    {
        $user = $this->Acceso->User();
        $aagRegionId = $user['aag_region_id'];

        $fleet = $this->Fleet->findByGuidAndAagRegionId($fleet_guid, $aagRegionId);

        if ($fleet && $this->Acceso->haveModulePermission(ConstantsConfigModules::FLEET)) {
            $conditions = array('id' => $aagRegionId);
            $aagRegionName = $this->AagRegion->region_list_conditions($conditions);

            $countries = $this->Country->get_country_by_region($aagRegionId);
            $provinces = $this->Province->getListByAagRegion($aagRegionId);
            $erpProviders = $this->Erp->getErpsByAagRegionId($aagRegionId);

            if ($this->request->is('get')) {
                $this->request->data = $fleet;
            } else {
                $this->request->data['Fleet']['id'] = $fleet['Fleet']['id'];
                if (!isset($this->request->data['Fleet']['networks'][0])) {
                    $this->Session->setFlashError(__t('Fleet.Select_network'));
                } else {
                    $resp = $this->Fleet->edit_fleet($this->request->data);
                    if ($resp) {
                        $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                        $this->redirect($this->request->here);
                    } else {
                        $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                    }
                }
            }

            $this->setVarForm();

            $this->request->data['Fleet']['networks'] = $this->FleetNetwork->get_list($fleet['Fleet']['id']);
            $this->set(array(
                'fleet' => $fleet,
                'provinces' => $provinces,
                'countries' => $countries,
                'erp_providers' => $erpProviders,
                'user_aag_region_name' => $aagRegionName,
                'networks' => $this->Network->getListByRegion($aagRegionId)
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    private function setVarForm()
    {
        $cancel_action = array(
            'url_cancel' => array(
                'controller' => 'fleets',
                'action' => 'home',
            ),
        );

        $this->set(array(
            'cancel_action' => $cancel_action,
        ));
    }
}

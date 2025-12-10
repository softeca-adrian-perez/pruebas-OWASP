<?php
class GaragesContactsGeneralBranchManagerController extends AppController
{
    public $uses = array(
        'GarageContactGeneralBranchManager',
        'LogChange',
        'Config',
        'Garage',
        'GarageNetwork'
    );

    /**
     * AJAX create GarageContactGeneralBranchManager.
     */
    public function ajax_add_garage_contact_general_branch_manager($garage_id, $contact_id)
    {
        $this->verify_ajax($this->request);

        $garage = $this->Garage->findByIdAndAagRegionId($garage_id, CakeSession::read('Auth.User.aag_region_id'));
        $garageNetworks = $this->GarageNetwork->findAllByGarageId($garage_id);
        if (empty($garageNetworks)) {
            $garageNetworks[] = array('GarageNetwork' => array('garage_id' => $garage_id, 'network_id' => '-1',));
        }

        $getListConfigTabs = $this->Config->get_list_config_tabs();

        if (
            $garage &&
            $getListConfigTabs[ConstantsTabs::GENERAL_BRANCH_MANAGER] == ConstantsBooleans::ACTIVE &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN |
                (
                    $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
                    CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
                    (
                        !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GENERIC_STAFF)) ||
                        (
                            CakeSession::read('Auth.User.role_id') == ConstantsRoles::DISTRIBUTOR &&
                            $this->Acceso->haveGarageDistributor(CakeSession::read('Auth.User.distributor_id'))
                        )
                    ) &&
                    $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)
                )
            )
        ) {
            if (!$this->request->is('get')) {
                $garage_contact_tmp = array(
                    'garage_id' => $garage_id,
                    'contact_id' => $contact_id
                );

                if (!$this->GarageContactGeneralBranchManager->findByContactIdAndGarageId($contact_id, $garage_id)) {
                    $garage_contact = $this->GarageContactGeneralBranchManager->new_garage_contact_general_branch_manager($garage_contact_tmp);
                    if ($garage_contact) {
                        $this->LogChange->add_contact_log(
                            $this->GarageContactGeneralBranchManager->table,
                            $this->Session->read('Auth'),
                            $garage_id,
                            ConstantsLogType::GARAGE,
                            $contact_id
                        );
                        $success = array(
                            'success' => 'ok'
                        );
                        $js_array = json_encode($success);
                        echo $js_array;
                    }
                }
            }
            $this->layout = false;
            $this->render(false);
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX delete GarageContactGeneralBranchManager.
     */
    public function ajax_remove_garage_contact_general_branch_manager($garage_id, $contact_id)
    {
        $this->verify_ajax($this->request);

        $garage = $this->Garage->findByIdAndAagRegionId($garage_id, CakeSession::read('Auth.User.aag_region_id'));
        $garageNetworks = $this->GarageNetwork->findAllByGarageId($garage_id);
        if (empty($garageNetworks)) {
            $garageNetworks[] = array('GarageNetwork' => array('garage_id' => $garage_id, 'network_id' => '-1',));
        }

        $getListConfigTabs = $this->Config->get_list_config_tabs();

        if (
            $garage &&
            $getListConfigTabs[ConstantsTabs::GENERAL_BRANCH_MANAGER] == ConstantsBooleans::ACTIVE &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN |
                (
                    $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
                    CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
                    (
                        !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GENERIC_STAFF)) ||
                        (
                            CakeSession::read('Auth.User.role_id') == ConstantsRoles::DISTRIBUTOR &&
                            $this->Acceso->haveGarageDistributor(CakeSession::read('Auth.User.distributor_id'))
                        )
                    ) &&
                    $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)
                )
            )
        ) {
            if (!$this->request->is('get')) {
                $garage_contact_id = $this->GarageContactGeneralBranchManager->find(
                    'first',
                    array(
                        'conditions' => array(
                            'garage_id' => $garage_id,
                            'contact_id' => $contact_id
                        )
                    )
                );
                if (isset($garage_contact_id['GarageContactGeneralBranchManager'])) {
                    $garage_contact = $this->GarageContactGeneralBranchManager->delete($garage_contact_id['GarageContactGeneralBranchManager']['id']);
                    if ($garage_contact) {
                        $this->LogChange->remove_contact_log(
                            $this->GarageContactGeneralBranchManager->table,
                            $this->Session->read('Auth'),
                            $garage_id,
                            ConstantsLogType::GARAGE,
                            $contact_id
                        );
                        $success = array(
                            'success' => 'ok'
                        );
                        $js_array = json_encode($success);
                        echo $js_array;
                    }
                }
            }
            $this->layout = false;
            $this->render(false);
        } else {
            throw new UnauthorizedException();
        }
    }
}

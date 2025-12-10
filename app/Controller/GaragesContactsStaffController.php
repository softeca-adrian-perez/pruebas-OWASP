<?php
class GaragesContactsStaffController extends AppController
{
    public $uses = array(
        'GarageContactStaff',
        'LogChange',
        'TrainingDelegate',
        'Garage',
        'GarageNetwork'
    );

    /**
     * AJAX create GarageContactStaff.
     */
    public function ajax_add_garage_contact_staff($garage_id, $contact_id)
    {
        $this->verify_ajax($this->request);

        $garage = $this->Garage->findByIdAndAagRegionId($garage_id, CakeSession::read('Auth.User.aag_region_id'));
        $garageNetworks = $this->GarageNetwork->findAllByGarageId($garage_id);
        if (empty($garageNetworks)) {
            $garageNetworks[] = array('GarageNetwork' => array('garage_id' => $garage_id, 'network_id' => '-1',));
        }

        if (
            $garage &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                (
                    $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
                    CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
                    !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GENERIC_STAFF)) &&
                    $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)
                )
            )
        ) {
            if (!$this->request->is('get')) {
                $garageContactTmp = array(
                    'garage_id' => $garage_id,
                    'contact_id' => $contact_id
                );

                if (!$this->GarageContactStaff->findByContactIdAndGarageId($contact_id, $garage_id)) {
                    $garageContactStaff = $this->GarageContactStaff->new_garage_contact_staff($garageContactTmp);
                    if ($garageContactStaff) {
                        $this->LogChange->add_contact_log(
                            $this->GarageContactStaff->table,
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
     * AJAX delete GarageContactStaff.
     */
    public function ajax_remove_garage_contact_staff($garage_id, $contact_id)
    {
        $this->verify_ajax($this->request);

        $garage = $this->Garage->findByIdAndAagRegionId($garage_id, CakeSession::read('Auth.User.aag_region_id'));
        $garageNetworks = $this->GarageNetwork->findAllByGarageId($garage_id);
        if (empty($garageNetworks)) {
            $garageNetworks[] = array('GarageNetwork' => array('garage_id' => $garage_id, 'network_id' => '-1',));
        }

        if (
            $garage &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                (
                    $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
                    CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
                    !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GENERIC_STAFF)) &&
                    $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)
                )
            )
        ) {
            if (!$this->request->is('get')) {
                $garageContactStaff = $this->GarageContactStaff->find(
                    'first',
                    array(
                        'conditions' => array(
                            'garage_id' => $garage_id,
                            'contact_id' => $contact_id
                        )
                    )
                );
                if (isset($garageContactStaff['GarageContactStaff'])) {
                    $trainingDelegate = $this->TrainingDelegate->findAllByGarageContactStaffId($garageContactStaff['GarageContactStaff']['id']);
                    if (!isset($trainingDelegate) || empty($trainingDelegate)) {
                        $garageContactStaff = $this->GarageContactStaff->delete($garageContactStaff['GarageContactStaff']['id']);
                        if ($garageContactStaff) {
                            $this->LogChange->remove_contact_log(
                                $this->GarageContactStaff->table,
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
                    } else {
                        $error = array(
                            'error' => 'ko'
                        );
                        $js_array = json_encode($error);
                        echo $js_array;
                        $this->Session->setFlashError(__t('GarageContactStaff.Error_message'));
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

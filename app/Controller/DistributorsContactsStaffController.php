<?php

class DistributorsContactsStaffController extends AppController
{
    public $uses = array(
        'Distributor',
        'DistributorContactStaff',
        'LogChange'
    );

    /**
     * AJAX create DistributorContactStaff.
     */
    public function ajax_add_distributor_contact_staff($distributor_id, $contact_id)
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $distributor = $this->Distributor->findByIdAndAagRegionId($distributor_id, $aagRegionId);

        if (
            $distributor &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR) &&
            CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS) &&
            $this->Acceso->haveTradingGroupPermission(ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR, $distributor['Distributor']['trading_group_id'])
        ) {
            if (!$this->request->is('get')) {
                $distributorContactTmp = array(
                    'distributor_id' => $distributor_id,
                    'contact_id' => $contact_id
                );

                if (!$this->DistributorContactStaff->findByContactIdAndDistributorId($contact_id, $distributor_id)) {
                    $distributorContact = $this->DistributorContactStaff->new_distributor_contact_staff($distributorContactTmp);
                    if ($distributorContact) {
                        $this->LogChange->add_contact_log(
                            $this->DistributorContactStaff->table,
                            $this->Session->read('Auth'),
                            $distributor_id,
                            ConstantsLogType::DISTRIBUTOR,
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
     * AJAX delete DistributorContactStaff.
     */
    public function ajax_remove_distributor_contact_staff($distributor_id, $contact_id)
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $distributor = $this->Distributor->findByIdAndAagRegionId($distributor_id, $aagRegionId);
        $distributorContactStaff = $this->DistributorContactStaff->findByDistributorIdAndContactId($distributor_id, $contact_id);

        if (
            $distributor && $distributorContactStaff &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR) &&
            CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS) &&
            $this->Acceso->haveTradingGroupPermission(ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR, $distributor['Distributor']['trading_group_id'])
        ) {
            if (!$this->request->is('get')) {
                $distributorContact = $this->DistributorContactStaff->delete($distributorContactStaff['DistributorContactStaff']['id']);
                if ($distributorContact) {
                    $this->LogChange->remove_contact_log(
                        $this->DistributorContactStaff->table,
                        $this->Session->read('Auth'),
                        $distributor_id,
                        ConstantsLogType::DISTRIBUTOR,
                        $contact_id
                    );
                    $success = array(
                        'success' => 'ok'
                    );
                    echo json_encode($success);
                }
            }
            $this->layout = false;
            $this->render(false);
        } else {
            throw new UnauthorizedException();
        }
    }
}

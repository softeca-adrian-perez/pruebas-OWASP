<?php
class DistributorsContactsBdmController extends AppController
{
    public $uses = array(
        'Distributor',
        'DistributorContactBdm',
        'LogChange'
    );

    /**
     * AJAX create DistributorContactBdm.
     */
    public function ajax_add_distributor_contact_bdm($distributor_id, $contact_id)
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
                $distributorContact = $this->DistributorContactBdm->addDistributorContactBdm($distributor_id, $contact_id);
                if ($distributorContact) {
                    $this->LogChange->add_contact_log(
                        $this->DistributorContactBdm->table,
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
            $this->layout = false;
            $this->render(false);
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX delete DistributorContactBdm.
     */
    public function ajax_remove_distributor_contact_bdm($distributor_id, $contact_id)
    {
        $this->verify_ajax($this->request);

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $distributor = $this->Distributor->findByIdAndAagRegionId($distributor_id, $aagRegionId);
        $distributorContactBdm = $this->DistributorContactBdm->findByDistributorIdAndContactId($distributor_id, $contact_id);

        if (
            $distributor && $distributorContactBdm &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR) &&
            CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS) &&
            $this->Acceso->haveTradingGroupPermission(ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR, $distributor['Distributor']['trading_group_id'])
        ) {
            if (!$this->request->is('get')) {
                $distributorContact = $this->DistributorContactBdm->delete($distributorContactBdm['DistributorContactBdm']['id']);
                if ($distributorContact) {
                    $this->LogChange->remove_contact_log(
                        $this->DistributorContactBdm->table,
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

    /**
     * AJAX get DistributorContactBdm names.
     * Used for dynamic selects.
     */
    public function get_contacts_bdm_name()
    {
        $this->verify_ajax($this->request);
        $this->layout = false;
        $this->autoRender = false;

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $contactsName = $this->DistributorContactBdm->getContactsBdmAjax($this->request->query, $aagRegionId);

        $listContacts = array();
        foreach ($contactsName as $key => $contact) {
            $listContacts[] = array(
                'id' => $key,
                'text' => $contact,
            );
        }

        $listContactsComplete['items'] = $listContacts;

        return json_encode($listContactsComplete);
    }
}

<?php
class GaragesContactsBdmController extends AppController
{
    public $uses = array(
        'GarageContactBdm',
        'LogChange'
    );

    /**
     * AJAX get GarageContactBdm names.
     * Used for dynamic selects.
     */
    public function get_contacts_bdm_name()
    {
        $this->verify_ajax($this->request);

        $this->layout = false;
        $this->autoRender = false;

        $user = $this->Acceso->user();
        $aagRegionId = $user['aag_region_id'];

        $contactsName = $this->GarageContactBdm->getContactsBdmAjax($this->request->query, $aagRegionId);

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

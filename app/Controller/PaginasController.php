<?php
App::uses('AppController', 'Controller');

class PaginasController extends AppController
{
    public $uses = array();

    /**
     * Paginas home page.
     */
    public function home($network_id = null, $region_id = null, $country_id = null)
    {
        if ($network_id == null && CakeSession::read('Auth.User.networks')) {
            $networks = CakeSession::read('Auth.User.networks');
            CakeSession::write('Auth.User.current_network', Numero::validateSessionNumeric($networks[0]));
        } else {
            CakeSession::write('Auth.User.current_network', Numero::validateSessionNumeric($network_id));
        }

        if ($region_id != null) {
            CakeSession::write('Auth.User.aag_region_id', Numero::validateSessionNumeric($region_id));
        } else {
            $user = ClassRegistry::init('User');
            $user = $user->findById(CakeSession::read('Auth.User.id'));
            if (isset($user['User']['aag_region_id'])) {
                CakeSession::write('Auth.User.aag_region_id', Numero::validateSessionNumeric($user['User']['aag_region_id']));
            } else {
                CakeSession::write('Auth.User.aag_region_id', null);
            }
        }

        if ($country_id == null && CakeSession::read('Auth.User.countries')) {
            $countries = CakeSession::read('Auth.User.countries');
            CakeSession::write('Auth.User.current_country', Numero::validateSessionNumeric($countries[0]));
        } else {
            CakeSession::write('Auth.User.current_country', Numero::validateSessionNumeric($country_id));
        }

        $user = $this->Acceso->user();

        if (
            $user['role_id'] == ConstantsRoles::BDM_AAG ||
            $user['role_id'] == ConstantsRoles::BDM_TG ||
            $user['role_id'] == ConstantsRoles::GPC_LOGISTICS_BDM ||
            $user['Contact']['position_id'] == ConstantsPositions::NATIONAL_SALES_MANAGER_CV_ID ||
            $user['Contact']['position_id'] == ConstantsPositions::NATIONAL_SALES_MANAGER_LV_ID ||
            $user['Contact']['position_id'] == ConstantsPositions::REGIONAL_SALES_MANAGER_LV_ID ||
            $user['Contact']['position_id'] == ConstantsPositions::REGIONAL_SALES_MANAGER_CV_ID
        ) {
            $this->redirect(array(
                'controller' => 'dashboard',
                'action' => 'home',
            ));
        } else {
            $this->redirect(array(
                'controller' => 'home',
                'action' => 'home_page2',
            ));
        }
    }

    public function flash_message()
    {
        $this->layout = false;
    }
}

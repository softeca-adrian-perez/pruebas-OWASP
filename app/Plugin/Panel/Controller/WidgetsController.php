<?php

/**
 * @property PanelWidget $PanelWidget
 */
class WidgetsController extends AppController
{

    public $uses = array(
        'Panel.PanelWidget',
        'Network',
        'Garage',
        'GarageNetwork',
        'AagRegion',
        'Country',
        'TradingGroup',
        'Distributor'
    );

    /**
     * Home/Dashboard.
     * Only available for SuperAdmin.
     */
    public function index()
    {

        $user = $this->Acceso->user();
        $roleId = $user['role_id'];
        if ($roleId == ConstantsRoles::SUPER_ADMIN) {
            $current_network_id = CakeSession::read('Auth.User.current_network');
            $current_network = $this->Network->find(
                'first',
                array(
                    'conditions' => array(
                        'Network.id =' => $current_network_id
                    )
                )
            );
            $current_region_id = CakeSession::read('Auth.User.aag_region_id');

            $allowed_widgets = $this->PanelWidget->getWidgetsRole($user['Role']['id']);
            $displayed_widgets = $this->PanelWidget->getWidgetsUser($user['id']);
            $hidden_widgets = $this->PanelWidget->getWidgetsAvailable($displayed_widgets, $user['Role']['id']);

            if (!$displayed_widgets) {
                $displayed_widgets = $allowed_widgets;
            }

            $title_widgets = array();
            foreach ($allowed_widgets as $widget) {
                $title_widgets[$widget['PanelWidget']['id']] = $widget['PanelWidget']['display_name'];
            }

            $networks = $this->Network->find('all');
            $regions_tmp =  $this->AagRegion->find('all');

            $conditions = $this->request->query;
            $this->request->data['Search'] = $conditions;

            foreach ($regions_tmp as $key => $region) {
                $garage_networks = array();
                foreach ($networks as $network) {
                    $garage_networks[$network['Network']['name']]['id'] = $network['Network']['id'];
                    $garage_networks[$network['Network']['name']]['associates'] = $this->GarageNetwork->getGarageRegionsByNetwork($region['AagRegion']['id'], $network['Network']['id'], $conditions);
                    $network = $this->Network->findById($network['Network']['id']);
                    $garage_networks[$network['Network']['name']]['image'] = $network['Network']['image'];
                }
                $regions[$key] = $region;
                $regions[$key]['Networks'] = $garage_networks;
            }

            //network in header
            if (empty($this->request->query)) {
                $networks_availables = $this->Network->getNetworksById($current_network_id);
            } else {
                //networks selected
                if (empty($conditions['network_id'])) {
                    $networks_availables = $this->Network->getNetworksByRegion($current_region_id);
                }

                //networks by region
                if (!empty($conditions['network_id'])) {
                    $networks_availables = $this->Network->getNetworksByIds($conditions['network_id']);
                }
            }

            $regions_list = $this->AagRegion->region_list();
            $countries = $this->Country->get_country_by_region($current_region_id);
            $network_list = $this->Network->getNetworkListByRegion($current_region_id);

            $current_country =  CakeSession::read('Auth.User.current_country');
            $current_region = CakeSession::read('Auth.User.current_region');

            $this->set(
                array(
                    'widgets' => $displayed_widgets,
                    'title_widgets' => $title_widgets,
                    'widgets_available' => $hidden_widgets,
                    'current_network' => $current_network,
                    'networks_availables' => $networks_availables,
                    'user' => $user,
                    'networks' => $networks,
                    'regions' => $regions,
                    'regions_list' => $regions_list,
                    'countries' => $countries,
                    'network_list' => $network_list,
                    'current_country' => $current_country,
                    'current_region' => $current_region,
                    'active_page' => ConstantsActiveHomePage::PAGE_5,
                    'current_region_id' => $current_region_id,
                )
            );
        } else {
            throw new UnauthorizedException();
        }
    }

    public function get_data_widget($logic_model, $url_view, $width_initial)
    {
        $user = $this->Acceso->user();
        $this->loadModel('Panel.' . $logic_model);
        $this->set(
            array(
                'data' => $this->$logic_model->GetDataWidget($user),
                'w_initial' => $width_initial
            )
        );
        $this->render('Widgets/' . $url_view);
    }

    public function get_data_widget_by_id($id)
    {
        $user = $this->Acceso->user();
        $widget = $this->PanelWidget->findById($id);
        $this->loadModel('Panel.' . $widget['PanelWidget']['logic_model']);
        $this->set(
            array(
                'data' => $this->$widget['PanelWidget']['logic_model']->GetDataWidget($user),
                'w_initial' => 1
            )
        );
        $this->layout = false;
        $this->render('Widgets/' . $widget['PanelWidget']['url_view']);
    }

    public function save_ajax()
    {
        $user = $this->Acceso->user();
        $this->autoRender = false;
        $retorno = false;
        if ($this->request->is('post')) {
            $this->begin();
            $this->loadModel('Panel.PanelWidgetUser');
            if ($this->PanelWidgetUser->save_ajax($this->request->data, $user)) {
                $retorno = true;
                $this->commit();
            }
        }
        return json_encode($retorno);
    }

    public function get_widgets_available_from_db()
    {
        $user = $this->Acceso->user();
        $widgets = $this->PanelWidget->getWidgetsUser($user['id']);
        if (empty($widgets)) {
            $widgets = $this->PanelWidget->getWidgetsRole($user['Role']['id']);
        }
        $this->layout = false;
        $this->set(
            array(
                'available_widgets' => $this->PanelWidget->getWidgetsAvailable($widgets, $user['Role']['id'])
            )
        );
    }

    public function get_widgets_from_db()
    {
        $user = $this->Acceso->user();
        $widgets = $this->PanelWidget->getWidgetsUser($user['id']);
        if (empty($widgets)) {
            $widgets = $this->PanelWidget->getWidgetsRole($user['Role']['id']);
        }
        $this->layout = false;
        $this->set(
            array(
                'widgets' => $widgets,
            )
        );
    }

    public function get_data_widget_by_network($logic_model, $url_view, $width_initial, $network_id)
    {
        $this->loadModel('Panel.' . $logic_model);
        $data = $this->$logic_model->GetDataWidgetByNetwork($network_id);
        $this->set(
            array(
                'data' => $data,
                'w_initial' => $width_initial,
                'network_id' => $network_id
            )
        );
        $this->render('Widgets/' . $url_view);
    }

    public function location_by_network()
    {

        $conditions = $this->request->query;
        $this->request->data['Search'] = $conditions;

        $condition_region = [];
        $condition_country = [];
        $condition_network = [];

        if (!empty($conditions)) {
            if (!empty($conditions['region_id'])) {
                $condition_region[] = ['AagRegion.id' => $conditions['region_id']];
            }
            if (!empty($conditions['country_id'])) {
                $condition_country[] = ['Country.id' => $conditions['country_id']];
            }
            if (!empty($conditions['network_id'])) {
                $condition_network[] = ['Network.id' => $conditions['network_id']];
            }
        }

        $params = array(
            'joins' => array(
                array(
                    'alias' => 'GarageNetwork',
                    'table' => 'garages_networks',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'GarageNetwork.garage_id = Garage.id',
                    ),
                ),
                array(
                    'alias' => 'Network',
                    'table' => 'networks',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Network.id = GarageNetwork.network_id',
                    ),
                ),
                array(
                    'alias' => 'City',
                    'table' => 'cities',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Garage.city_id = City.id',
                    ),
                ),
                array(
                    'alias' => 'Province',
                    'table' => 'provinces',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Province.id = City.province_id',
                    ),
                ),
                array(
                    'alias' => 'Country',
                    'table' => 'countries',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Country.id = Province.country_id',
                    ),
                ),
                array(
                    'alias' => 'AagRegion',
                    'table' => 'aag_regions',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'AagRegion.id = Country.aag_region_id',
                    ),
                )
            ),
            'conditions' => array(
                $condition_region,
                $condition_country,
                $condition_network
            ),
            'fields' => array(
                'Garage.id',
                'Garage.name',
                'Garage.address1',
                'Garage.phone',
                'Garage.latitude',
                'Garage.longitude',
                'Province.name AS province',
                'Network.network_type'
            )
        );

        $garages_network = $this->Garage->find('all', $params);

        foreach ($garages_network as $key => $garage) {
            $garage['Garage']['province'] = $garage['Province']['province'];
            $garage['Garage']['address_str'] = __t('Garage.Address');
            $garage['Garage']['phone_str'] = __t('Garage.Phone');
            $garage['Garage']['province_str'] = __t('Garage.Province');
            $garage['Garage']['is_garage'] = true;
            $garages_network[$key] = $garage;
        }

        $garages_network = Hash::extract($garages_network, '{n}.Garage');
        $garages_network = $this->unique_multidim_array($garages_network, 'id');

        $this->autoRender = false;
        $this->layout = null;
        return json_encode($garages_network);
    }

    private function unique_multidim_array($array, $key)
    {
        $temp_array = array();
        $i = 0;
        $key_array = array();

        foreach ($array as $val) {
            if (!in_array($val[$key], $key_array)) {
                $key_array[$i] = $val[$key];
                $temp_array[$i] = $val;
            }
            $i++;
        }
        $reindexed_array = array_values($temp_array);
        return $reindexed_array;
    }
}

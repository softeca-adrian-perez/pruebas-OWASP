<?php
class GaragesCustomersActivitiesController extends AppController
{
    public $uses = array(
        'GarageCustomerActivity',
        'LogChange',
        'Garage',
        'GarageNetwork'
    );

    /**
     * AJAX create GarageCustomerActivity.
     */
    public function ajax_add_activities_garage()
    {
        $this->verify_ajax($this->request);

        $garageId = $this->request->data['garage_id'];
        $garage = $this->Garage->findByIdAndAagRegionId($garageId, CakeSession::read('Auth.User.aag_region_id'));
        $garage_networks = $this->GarageNetwork->findAllByGarageId($garageId);
        if (empty($garage_networks)) {
            $garage_networks[] = array('GarageNetwork' => array('garage_id' => $garageId, 'network_id' => '-1',));
        }

        if (
            $garage &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
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
            $garage_customer_activity = array(
                'GarageCustomerActivity' => array(
                    'garage_id' => $this->request->data['garage_id'],
                    'customer_activity_id' => $this->request->data['customer_activity_id'],
                    'order' => $this->request->data['order'],
                )
            );

            $this->GarageCustomerActivity->new_garage_customer_activity($garage_customer_activity);

            $this->LogChange->get_params_create_log_add(
                $garage_customer_activity['GarageCustomerActivity'],
                $this->GarageCustomerActivity->table,
                $this->Session->read('Auth'),
                $this->request->data['garage_id'],
                ConstantsLogType::GARAGE
            );

            $this->autoRender = false;
            return $this->GarageCustomerActivity->getLastInsertID();
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX set GarageCustomerActivity order.
     */
    public function ajax_set_order()
    {
        $this->verify_ajax($this->request);

        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
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
                )
            )
        ) {
            $activities = $this->request->data;
            foreach ($activities as $activity_id => $order) {
                $garage_customer_activity_tmp = array(
                    'GarageCustomerActivity' => array(
                        'id' => $activity_id,
                        'order' => $order,
                    )
                );
                $this->GarageCustomerActivity->change_order($garage_customer_activity_tmp);
            }
            $this->autoRender = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX delete GarageCustomerActivity.
     */
    public function ajax_delete_garage_customer_activity()
    {
        $this->verify_ajax($this->request);

        $garage_id = $this->request->data['garage_id'];
        $garage = $this->Garage->findByIdAndAagRegionId($garage_id, CakeSession::read('Auth.User.aag_region_id'));
        $garage_networks = $this->GarageNetwork->findAllByGarageId($garage_id);
        if (empty($garage_networks)) {
            $garage_networks[] = array('GarageNetwork' => array('garage_id' => $garage_id, 'network_id' => '-1',));
        }

        $garage_customer_activity_id = $this->request->data['garage_customer_activity_id'];
        $garage_customer_activity = $this->GarageCustomerActivity->findById($garage_customer_activity_id);

        if (
            $garage && $garage_customer_activity &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
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
            $this->GarageCustomerActivity->delete($garage_customer_activity_id);

            $this->LogChange->get_params_create_log_delete(
                $garage_customer_activity['GarageCustomerActivity'],
                $this->GarageCustomerActivity->table,
                $this->Session->read('Auth'),
                $garage_id,
                ConstantsLogType::GARAGE
            );

            $garage_customer_activities = $this->GarageCustomerActivity->getAllByGarageIdByOrder($garage_id);
            $counter = 1;
            foreach ($garage_customer_activities as $garage_customer_activity) {
                $garage_customer_activity['GarageCustomerActivity']['order'] = $counter;
                $counter++;
                $this->GarageCustomerActivity->edit_garage_customer_activity($garage_customer_activity);
            }

            $this->autoRender = false;
        } else {
            throw new UnauthorizedException();
        }
    }
}

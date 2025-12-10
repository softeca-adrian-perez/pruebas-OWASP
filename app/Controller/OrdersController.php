<?php
App::uses('PaginatorOrderCustomComponent', 'Controller/Component');

class OrdersController extends AppController
{
    public $uses = array(
        'Order',
        'Garage',
        'GarageNetwork',
        'GarageEquipment',
        'Equipment',
        'LogChange',
        'EquipmentType',
        'Supplier',
        'Brand',
        'OrderProduct',
        'GarageProduct',
        'Country',
        'OrderType',
        'OrderTypeProduct'
    );

    /**
     * Order view.
     */
    // public function view($order_id)
    // {
    //     if (
    //         CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
    //         (
    //             $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
    //             CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
    //             $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
    //             CakeSession::read('Auth.User.role_id') != ConstantsRoles::DISTRIBUTOR
    //         )
    //     ) {
    //         $order = $this->Order->findById($order_id);

    //         $order['Order']['invoice_date'] = Fecha::toFormatoVista($order['Order']['invoice_date']);
    //         $order['Order']['order_date'] = Fecha::toFormatoVista($order['Order']['order_date']);
    //         $order['Order']['completed_date'] = Fecha::toFormatoVista($order['Order']['completed_date']);

    //         $this->setVarForm(CakeSession::read('Auth.User.aag_region_id'));
    //         $this->set(array(
    //             'order' => $order,
    //             'garage_id' =>  $order['Order']['garage_id'],
    //             'products' => $this->OrderProduct->search_list(CakeSession::read('Auth.User.aag_region_id')),
    //             'products_array' => $this->OrderProduct->getProductsFromOrderId($order['Order']['id'], CakeSession::read('Auth.User.aag_region_id')),
    //         ));
    //     } else {
    //         header('HTTP/1.0 401 Unauthorized');
    //         exit;
    //     }
    // }

    /**
     * Create Order.
     */
    public function add($garage_id)
    {
        $garage = $this->Garage->findByIdAndAagRegionId($garage_id, CakeSession::read('Auth.User.aag_region_id'));
        $garageNetworks = $this->GarageNetwork->findAllByGarageId($garage_id);

        if (
            $garage &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
            CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GENERIC_STAFF)) &&
            $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)
        ) {
            $country = array();
            if (isset($garage['Garage']['province_id'])) {
                $country = $this->Country->get_country_by_province($garage['Garage']['province_id']);
            } elseif (isset($garage['Garage']['city_id'])) {
                $country = $this->Country->get_country_by_city($garage['Garage']['city_id']);
            }

            $cancelAction = array(
                'url_cancel' => array(
                    'controller' => 'garages',
                    'action' => 'add_orders',
                    $garage_id
                ),
            );

            if ($this->request->is('post')) {
                $orders = $this->Order->add_orders($this->request->data, $garage_id);
                if ($orders) {
                    $garagesProducts = $this->request->data['Order']['GaragesProducts'];
                    if ($garagesProducts) {
                        $this->GarageProduct->add_products_orders($garagesProducts, $orders['Order']['id']);
                    }
                    $this->LogChange->get_params_create_log_add(
                        $orders['Order'],
                        $this->Order->table,
                        $this->Session->read('Auth'),
                        $garage_id,
                        ConstantsLogType::GARAGE
                    );
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    $this->redirect(
                        array(
                            'controller' => 'orders',
                            'action' => 'edit',
                            $this->Order->getLastInsertID()
                        )
                    );
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            }

            $this->setVarForm($garage['Garage']['aag_region_id']);
            $this->set(array(
                'garage_id' => $garage_id,
                'cancel_action' => $cancelAction,
                'products' => $this->OrderProduct->search_list($garage['Garage']['aag_region_id']),
                'country' => $country,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit Order.
     */
    public function edit($orders_id)
    {
        $orders = $this->Order->findById($orders_id);
        $garageId = $orders['Order']['garage_id'];
        $garage = $this->Garage->findByIdAndAagRegionId($garageId, CakeSession::read('Auth.User.aag_region_id'));
        $garageNetworks = $this->GarageNetwork->findAllByGarageId($garageId);

        if (
            $garage &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
            CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GENERIC_STAFF)) &&
            $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)
        ) {
            $country = array();
            if (isset($garage['Garage']['province_id'])) {
                $country = $this->Country->get_country_by_province($garage['Garage']['province_id']);
            } elseif (isset($garage['Garage']['city_id'])) {
                $country = $this->Country->get_country_by_city($garage['Garage']['city_id']);
            }

            $cancelAction = array(
                'url_cancel' => array(
                    'controller' => 'orders',
                    'action' => 'view',
                    $orders['Order']['id']
                ),
            );

            $orders['Order']['invoice_date'] = Fecha::toFormatoVista($orders['Order']['invoice_date']);
            $orders['Order']['order_date'] = Fecha::toFormatoVista($orders['Order']['order_date']);
            $orders['Order']['completed_date'] = Fecha::toFormatoVista($orders['Order']['completed_date']);

            $productsArray = $this->OrderProduct->getProductsFromOrderId($orders['Order']['id'], CakeSession::read('Auth.User.aag_region_id'));

            if (!$this->request->is('get')) {
                $oldData = $this->Order->findById($orders['Order']['id']);
                $ordersBd = $this->Order->edit_orders($this->request->data, $orders['Order']['garage_id']);
                if ($ordersBd) {
                    $isCorrect = true;
                    $deleteGaragesProducts = $this->request->data['Order']['DeleteGaragesProducts'];
                    foreach ($productsArray as $product) {
                        if (!$deleteGaragesProducts || ($deleteGaragesProducts && !in_array($product['Product']['id'], $deleteGaragesProducts))) {
                            $exist = $this->OrderTypeProduct->findByOrderProductIdAndOrderTypeIdAndAagRegionId($product['Product']['product_id'], $this->request->data['Order']['order_type_id'], CakeSession::read('Auth.User.aag_region_id'));
                            if (empty($exist)) {
                                $isCorrect = false;
                            }
                        }
                    }
                    if ($isCorrect) {
                        $garagesProducts = $this->request->data['Order']['GaragesProducts'];
                        if ($garagesProducts) {
                            $this->GarageProduct->add_products_orders($garagesProducts, $orders['Order']['id']);
                        }
                        if ($deleteGaragesProducts) {
                            $this->GarageProduct->deleteAll(['GarageProduct.id' => $deleteGaragesProducts], false);
                        }
                        $this->LogChange->get_params_create_log_edit(
                            $oldData['Order'],
                            $ordersBd['Order'],
                            $this->Order->table,
                            $this->Session->read('Auth'),
                            $orders['Order']['garage_id'],
                            ConstantsLogType::GARAGE
                        );
                        $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                        $this->redirect($this->request->here);
                    } else {
                        $this->Session->setFlashError(__t('Orden.Incompatibility_product_order_type'));
                    }
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            } else {
                $this->request->data = $orders;
            }

            $this->setVarForm($garage['Garage']['aag_region_id']);
            $this->set(array(
                'orders' => $orders,
                'garage_id' =>  $orders['Order']['garage_id'],
                'cancel_action' => $cancelAction,
                'products' => $this->OrderProduct->search_list($garage['Garage']['aag_region_id']),
                'products_array' => $productsArray,
                'country' => $country,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Delete Order.
     */
    public function delete($garage_id, $order_id)
    {
        $garage = $this->Garage->findByIdAndAagRegionId($garage_id, CakeSession::read('Auth.User.aag_region_id'));
        $garageNetworks = $this->GarageNetwork->findAllByGarageId($garage_id);

        if (
            $garage &&
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
            CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GENERIC_STAFF)) &&
            $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)
        ) {
            $orders = $this->Order->findById($order_id);
            $user = $this->Session->read('Auth');

            $products = $this->OrderProduct->getProductsFromOrderId($order_id, CakeSession::read('Auth.User.aag_region_id'));
            if ($products) {
                $this->GarageProduct->deleteAll(['GarageProduct.id' => Hash::extract($products, '{n}.Product.id')], false);
            }
            $delete = $this->Order->delete($order_id);
            if ($delete) {
                $this->LogChange->get_params_create_log_delete(
                    $orders['Order'],
                    $this->Order->table,
                    $user,
                    $garage_id,
                    ConstantsLogType::GARAGE
                );
                $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_DELETED));
            } else {
                $this->Session->setFlashError(__t(ConstantsMessages::BAD_DELETED));
            }

            $this->redirect(array(
                'controller' => 'garages',
                'action' => 'add_orders',
                $garage_id
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    private function setVarForm($aag_region_id)
    {
        $equipmentTypes = $this->EquipmentType->search_list($aag_region_id);
        $equipments = $this->Equipment->search_list($aag_region_id);
        $brands = $this->Brand->search_list();
        $suppliers = $this->Supplier->search_list();

        $this->set(array(
            'equipments' => $equipments,
            'equipment_types' => $equipmentTypes,
            'suppliers' => $suppliers,
            'brands' => $brands,
            'order_types' => $this->OrderType->search_list(),
        ));
    }

    /**
     * AJAX charge OrderProducts.
     */
    public function ajax_charge_products($order_id)
    {
        $this->verify_ajax($this->request);
        $orderProducts = $this->OrderProduct->getProductsFromOrderId($order_id, CakeSession::read('Auth.User.aag_region_id'));

        if (
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
            CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
            CakeSession::read('Auth.User.role_id') != ConstantsRoles::DISTRIBUTOR
        ) {
            $this->autoRender = false;
            $arrayBd = [];
            foreach ($orderProducts as $key => $value) {
                $arrayBd[$key]['id'] = $value['OrderProduct']['id'];
                $arrayBd[$key]['name'] = $value['OrderProduct']['name_' . __l()];
                $arrayBd[$key]['id_assoc'] = $value['Product']['id'];
                $arrayBd[$key]['quantity'] = $value['Product']['quantity'];
            };
            return json_encode($arrayBd);
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX delete OrderProduct.
     */
    public function ajax_delete_products()
    {
        $this->verify_ajax($this->request);

        if (
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
            CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
            CakeSession::read('Auth.User.role_id') != ConstantsRoles::DISTRIBUTOR
        ) {
            $this->autoRender = false;
            $this->GarageProduct->delete($this->request->data['id']);
            return true;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX get OrderTypeProduct.
     */
    public function ajax_get_products()
    {
        $this->verify_ajax($this->request);

        if (
            $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
            CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
            CakeSession::read('Auth.User.role_id') != ConstantsRoles::DISTRIBUTOR
        ) {
            $this->autoRender = false;
            $result = $this->OrderTypeProduct->search_list_by_order_type($this->request->data['id']);
            return json_encode($result);
        } else {
            throw new UnauthorizedException();
        }
    }
}

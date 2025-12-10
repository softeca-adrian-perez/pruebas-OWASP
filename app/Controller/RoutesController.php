<?php
class RoutesController extends AppController
{
    /**
     * AJAX create GarageRoute CRM visits.
     */
    public function ajax_create_route()
    {
        $this->verify_ajax($this->request);

        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE, ConstantsRoles::SUPER_ADMIN))
        ) {
            $data = $this->request->data;
            $route = array(
                'Route' => array(
                    'type' => $data['type'],
                    'name' => $data['name'],
                    'user_creation_id' => CakeSession::read('Auth.User.id'),
                    'user_assigned_id' => CakeSession::read('Auth.User.id')
                )
            );

            if ($this->Route->new_route($route)) {
                $routeId = $this->Route->getLastInsertId();

                foreach ($data['garages'] as $garage) {
                    $this->Route->GarageRoute->addRelationGarageRoute($garage, $routeId);
                }
            }

            $routes = $this->Route->search_list_garage();

            $this->set(
                array(
                    'routes' => $routes
                )
            );

            $this->layout = false;
            $this->autoRender = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX edit GarageRoute CRM visits.
     */
    public function ajax_edit_route()
    {
        $this->verify_ajax($this->request);

        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE, ConstantsRoles::SUPER_ADMIN))
        ) {
            $data = $this->request->data;
            $route = array(
                'Route' => array(
                    'id' => $data['route'],
                    'type' => $data['type'],
                    'name' => $data['name'],
                )
            );

            if ($this->Route->edit_route($route)) {
                $this->Route->GarageRoute->removeGarageRoutes($data['route']);
                foreach ($data['garages'] as $garage) {
                    $this->Route->GarageRoute->addRelationGarageRoute($garage, $data['route']);
                }
            }

            $routes = $this->Route->search_list_garage();

            $this->set(
                array(
                    'routes' => $routes
                )
            );

            $this->autoRender = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX create DistributorRoute CRM visits.
     */
    public function ajax_create_route_distributor()
    {
        $this->verify_ajax($this->request);

        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE, ConstantsRoles::SUPER_ADMIN))
        ) {
            $data = $this->request->data;

            $route = array(
                'Route' => array(
                    'type' => $data['type'],
                    'name' => $data['name'],
                    'user_creation_id' => CakeSession::read('Auth.User.id'),
                    'user_assigned_id' => CakeSession::read('Auth.User.id'),
                )
            );

            if ($this->Route->new_route($route)) {
                $routeId = $this->Route->getLastInsertId();

                foreach ($data['distributors'] as $distributor_id) {
                    $this->Route->DistributorRoute->addRelationDistributorRoute($distributor_id, $routeId);
                }
            }

            $routes = $this->Route->search_list_distributor();

            $this->set(
                array(
                    'routes' => $routes
                )
            );

            $this->layout = false;
            $this->render('../Visits/Elements/ajax_route_select');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX edit DistributorRoute CRM visits.
     */
    public function ajax_edit_route_distributor()
    {
        $this->verify_ajax($this->request);


        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE, ConstantsRoles::SUPER_ADMIN))
        ) {
            $data = $this->request->data;
            $route = array(
                'Route' => array(
                    'id' => $data['route'],
                    'type' => $data['type'],
                    'name' => $data['name'],
                )
            );

            if ($this->Route->edit_route($route)) {
                $this->Route->DistributorRoute->removeDistributorRoutes($data['route']);
                foreach ($data['distributors'] as $distributor_id) {
                    $this->Route->DistributorRoute->addRelationDistributorRoute($distributor_id, $data['route']);
                }
            }

            $routes = $this->Route->search_list_distributor();

            $this->set(
                array(
                    'routes' => $routes
                )
            );

            $this->layout = false;
            $this->render('../Visits/Elements/ajax_route_select');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX delete GarageRoute or DistributorRoute CRM visits.
     */
    public function ajax_delete_route()
    {
        $this->verify_ajax($this->request);

        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE, ConstantsRoles::SUPER_ADMIN))
        ) {
            $routeOptions = array(
                ConstantsVisitType::GARAGE => __t('Garage.Garage'),
                ConstantsVisitType::DISTRIBUTOR => __t('Distributor.Distributor'),
            );

            $routeId = $this->request->data['route_id'];
            $routeType = $this->request->data['route_type'];

            if ($routeType == ConstantsVisitType::GARAGE) {
                $garageRoutes = $this->Route->GarageRoute->findAllByRouteId($routeId);
                foreach ($garageRoutes as $garage_route) {
                    $this->Route->GarageRoute->delete($garage_route['GarageRoute']['id']);
                }
            } elseif ($routeType == ConstantsVisitType::DISTRIBUTOR) {
                $distributorRoutes = $this->Route->DistributorRoute->findAllByRouteId($routeId);
                foreach ($distributorRoutes as $distributor_route) {
                    $this->Route->DistributorRoute->delete($distributor_route['DistributorRoute']['id']);
                }
            }
            $this->Route->delete($routeId);

            $search = $this->request->query;
            $this->request->data['Search'] = $search;

            $conditions = $this->Route->conditions($search);
            $conditions['user_creation_id'] = CakeSession::read('Auth.User.id');

            $routes = $this->custom_pagination(
                array(),
                $conditions,
                ConstantsPagination::SIZE_PAGE_SMALL,
                $this->Route
            );
            foreach ($routes as $key => $route) {
                $conditions = array('route_id' => $route['Route']['id']);
                if ($route['Route']['type'] == ConstantsVisitType::GARAGE) {
                    $routes[$key]['Route']['customer_number'] = $this->Route->GarageRoute->find('count', array('conditions' => $conditions));
                } elseif ($route['Route']['type'] == ConstantsVisitType::DISTRIBUTOR) {
                    $routes[$key]['Route']['customer_number'] = $this->Route->DistributorRoute->find('count', array('conditions' => $conditions));
                }
            }

            $this->set(array(
                'route_options' => $routeOptions,
                'routes' => $routes
            ));

            $this->layout = false;
            $this->render('../Visits/Elements/ajax_routes_list');
        } else {
            throw new UnauthorizedException();
        }
    }
}

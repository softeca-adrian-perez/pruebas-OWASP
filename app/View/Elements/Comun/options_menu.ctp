<?php
$controller = $this->request->controller;
$action = $this->request->action;
$user = $this->Acceso->user();
if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::GARAGE) {
?>
    <li class="<?php echo ($controller == 'home' || ($controller == 'communications' && in_array($action, array('section', 'communication_searcher', 'home_section')))  || ($controller == 'suppliers' &&  $action == 'index')) ? 'active' : ''; ?>">
        <?php
        if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN) {
            echo $this->Html->link(
                '<span class="aag-icon-casa"></span>' .
                    __t('General.Home'),
                array(
                    'plugin' => false,
                    'controller' => 'home',
                    'action' => 'home_dashboard',
                ),
                array('escape' => false)
            );
        } else {
            echo $this->Html->link(
                '<span class="aag-icon-casa"></span>' .
                    __t('General.Home'),
                array(
                    'plugin' => false,
                    'controller' => 'home',
                    'action' => 'home_page2',
                ),
                array('escape' => false)
            );
        }
        ?>
    </li>
<?php } else { ?>
    <li class="<?php echo $controller == 'home' ? 'active' : ''; ?>">
        <?php
        echo $this->Html->link(
            '<span class="icon-dashboard" style="font-size: 14px;"></span>' .
                __t('Menu.Dashboard'),
            array(
                'plugin' => false,
                'controller' => 'home',
                'action' => 'home_page2',
            ),
            array(
                'escape' => false
            )
        );
        ?>
    </li>
<?php
}
if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::GPC_LOGISTICS_RM) {
?>
    <li class="<?php echo in_array($controller, array('appointments', 'events')) ? 'active' : ''; ?>">
        <?php
        echo $this->Html->link(
            '<span class="icon-calendar_sbd"></span>' .
                __t('Menu.Agenda'),
            array(
                'plugin' => false,
                'controller' => 'appointments',
                'action' => 'home',
            ),
            array('escape' => false)
        );
        ?>
    </li>
<?php
}
if (
    (
        $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_TRADING_GROUP) &&
        $this->Acceso->haveModulePermission(ConstantsConfigModules::TRADING_GROUPS)
    ) ||
    CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN
) {
?>
    <li class="<?php echo $controller == 'trading_groups' ? 'active' : ''; ?>">
        <?php
        echo $this->Html->link(
            '<span class="aag-icon-reunion"></span>' .
                __t('Menu.Trading_groups'),
            array(
                'plugin' => false,
                'controller' => 'trading_groups',
                'action' => 'home',
            ),
            array('escape' => false)
        );
        ?>
    </li>
    <?php
}
if (
    (
        $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
        CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
        $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES)
    ) ||
    CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN
) {
    if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::GARAGE) {
    ?>
        <li class="<?php echo $controller == 'garages_networks' && $action == 'general' ? 'active' : ''; ?>">
            <?php
            echo $this->Html->link(
                '<span class="aag-icon-garage"></span>' .
                    __t('General.My_garage'),
                array(
                    'plugin' => false,
                    'controller' => 'garages_networks',
                    'action' => 'general',
                    CakeSession::read('Auth.User.garage_id')
                ),
                array('escape' => false)
            );
            ?>
        </li>
    <?php } elseif (CakeSession::read('Auth.User.role_id') != ConstantsRoles::DISTRIBUTOR) { ?>
        <li class="<?php echo in_array($controller, array('garages', 'garages_software', 'garages_equipments', 'garages_employees', 'garages_customers_activities', 'garages_comments', 'garages_networks', 'garages_images', 'garages_websites', 'orders', 'garages_values_adds', 'bookings', 'enquiries', 'reviews', 'reporting', 'quotations')) ? 'active' : ''; ?>">
            <?php
            echo $this->Html->link(
                '<span class="aag-icon-garage"></span>' .
                    __t('Menu.Garages'),
                array(
                    'plugin' => false,
                    'controller' => 'garages',
                    'action' => 'home',
                ),
                array('escape' => false)
            );
            ?>
        </li>
    <?php
    }
}
if (
    (
        $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_NETWORK) &&
        $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES_NETWORKS)
    ) ||
    CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN
) {
    ?>
    <li class="<?php echo $controller == 'networks' ? 'active' : ''; ?>">
        <?php
        echo $this->Html->link(
            '<span class="aag-icon-garaje-redes"></span>' .
                __t('Menu.Garages') . ' ' . __t('Menu.Networks'),
            array(
                'plugin' => false,
                'controller' => 'networks',
                'action' => 'home',
            ),
            array('escape' => false)
        );
        ?>
    </li>
    <?php
}
if (
    (
        $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR) &&
        CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
        $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS)
    ) ||
    CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN
) {
    if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::DISTRIBUTOR) {
    ?>
        <li class="<?php echo $controller == 'distributors' && !in_array($action, array('branches', 'my_branch')) ? 'active' : ''; ?>">
            <?php
            echo $this->Html->link(
                '<span class="aag-icon-distribuidores"></span>' .
                    __t('Menu.My_data'),
                array(
                    'plugin' => false,
                    'controller' => 'distributors',
                    'action' => 'my_data',
                    CakeSession::read('Auth.User.distributor_id')
                ),
                array('escape' => false)
            );
            ?>
        </li>
    <?php
    } else {
        $controllersActive = array('distributors_software', 'distributors_services', 'distributors_contracts', 'distributors_comments', 'distributors_labels', 'distributors_activities', 'distributors_images',  'distributors_distributors_networks');
        ?>
        <li class="<?php echo ($controller == 'distributors' && !in_array($action, array('branches', 'my_branch')) || in_array($controller, $controllersActive)) ? 'active' : ''; ?>">
            <?php
            echo $this->Html->link(
                '<span class="aag-icon-distribuidores"></span>' .
                    __t('Menu.Distributors'),
                array(
                    'plugin' => false,
                    'controller' => 'distributors',
                    'action' => 'home',
                ),
                array('escape' => false)
            );
            ?>
        </li>
    <?php
    }
}
if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::DISTRIBUTOR) {
    $branches = $this->Acceso->haveBranches(CakeSession::read('Auth.User.distributor_id'));
    if ($branches) {
    ?>
        <li class="<?php echo $controller == 'distributors' && in_array($action, array('branches', 'my_branch')) ? 'active' : ''; ?>">
            <?php
            echo $this->Html->link(
                '<span class="ion-merge"></span>' .
                    __t('Menu.Branches'),
                array(
                    'plugin' => false,
                    'controller' => 'distributors',
                    'action' => 'branches',
                    CakeSession::read('Auth.User.distributor_id')
                ),
                array('escape' => false)
            );
            ?>
        </li>
    <?php
    }
}
if (
    $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
    CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
    CakeSession::read('Auth.User.role_id') == ConstantsRoles::DISTRIBUTOR &&
    $this->Acceso->haveGarageDistributor(CakeSession::read('Auth.User.distributor_id')) &&
    $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES)
) {
    ?>
    <li class="<?php echo in_array($controller, array('garages', 'garages_software', 'garages_equipments', 'garages_employees', 'garages_customers_activities', 'garages_comments', 'garages_networks', 'garages_images', 'garages_websites')) ? 'active' : ''; ?>">
        <?php
        echo $this->Html->link(
            '<span class="icon-garages"></span>' .
                __t('Menu.Garages'),
            array(
                'plugin' => false,
                'controller' => 'garages',
                'action' => 'home',
            ),
            array('escape' => false)
        );
        ?>
    </li>
<?php
}
if (
    (
        $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_DISTRIBUTOR_NETWORK) &&
        $this->Acceso->haveModulePermission(ConstantsConfigModules::DISTRIBUTORS_NETWORKS)
    ) ||
    CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN
) {
?>
    <li class="<?php echo $controller == 'distributors_networks' ? 'active' : ''; ?>">
        <?php
        echo $this->Html->link(
            '<span class="aag-icon-distribuidores-redes"></span>' .
                __t('Menu.Distributors') . ' ' . __t('Menu.Networks'),
            array(
                'plugin' => false,
                'controller' => 'distributors_networks',
                'action' => 'home',
            ),
            array('escape' => false)
        );
        ?>
    </li>
<?php
}

if (
    $this->Acceso->haveModulePermission(ConstantsConfigModules::FLEET) ||
    CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN
) {
?>
    <li class="<?php echo $controller == 'fleets' ? 'active' : ''; ?>">
        <?php
        echo $this->Html->link(
            '<span class="aag-icon-245283304" style="transform: scale(1.25);"></span>' .
                __t('Menu.Fleets'),
            array(
                'plugin' => false,
                'controller' => 'fleets',
                'action' => 'home',
            ),
            array('escape' => false)
        );
        ?>
    </li>
<?php
}

if (
    (
        $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_SMS) &&
        $this->Acceso->haveModulePermission(ConstantsConfigModules::SMS)
    ) ||
    CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN
) {
?>
    <li class="<?php echo $controller == 'sms' ? 'active' : ''; ?>">
        <?php
        echo $this->Html->link(
            '<span class="aag-icon-correo-aviso"></span>' .
                __t('Menu.Sms'),
            array(
                'plugin' => false,
                'controller' => 'sms',
                'action' => (CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN) ? 'home' : 'list',
            ),
            array('escape' => false)
        );
        ?>
    </li>
<?php
}
if (
    (
        $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_AGREEMENTS) &&
        $this->Acceso->haveModulePermission(ConstantsConfigModules::AGREEMENTS)
    ) ||
    CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN
) {
?>
    <li class="<?php echo $controller == 'agreements' ? 'active' : ''; ?>">
        <?php
        echo $this->Html->link(
            '<span class="aag-icon-acuerdo"></span>' .
                __t('Menu.Agreement'),
            array(
                'plugin' => false,
                'controller' => 'agreements',
                'action' => 'home',
            ),
            array('escape' => false)
        );
        ?>
    </li>
<?php
}
?>
<?php
if (
    (
        $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_USER) &&
        $this->Acceso->haveModulePermission(ConstantsConfigModules::USERS)
    ) ||
    CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN
) {
?>
    <li class="<?php echo $controller == 'users' && $action != 'reassignments' ? 'active' : ''; ?>">
        <?php
        echo $this->Html->link(
            '<span class="aag-icon-mas-usuarios"></span>' .
                __t('Menu.Users'),
            array(
                'plugin' => false,
                'controller' => 'users',
                'action' => 'listing',
            ),
            array('escape' => false)
        );
        ?>
    </li>
<?php
}
if (
    (
        $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_ALERT) &&
        CakeSession::read('Auth.User.role_id') != ConstantsRoles::GARAGE &&
        $this->Acceso->haveModulePermission(ConstantsConfigModules::ALERTS)
    ) ||
    CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN
) {
?>
    <li class="<?php echo $controller == 'alerts' ? 'active' : ''; ?>">
        <?php
        echo $this->Html->link(
            '<span class="aag-icon-campana"></span>' .
                __t('Menu.Alerts'),
            array(
                'plugin' => false,
                'controller' => 'alerts',
                'action' => 'home',
            ),
            array('escape' => false)
        );
        ?>
    </li>
<?php
}
if (
    (
        $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
        $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
        !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
    ) ||
    CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN
) {
?>
    <li class="<?php echo $controller == 'appointments' && $action != 'maintenance_home_events' ? 'active' : ''; ?>">
        <?php
        echo $this->Html->link(
            '<span class="aag-icon-crm-computer"></span>' .
                __t('Menu.CRM'),
            array(
                'plugin' => false,
                'controller' => 'dashboard',
                'action' => 'home',
            ),
            array('escape' => false)
        );
        ?>
    </li>
<?php
}

if (
    $this->Acceso->haveModulePermission(ConstantsConfigModules::VENUES) ||
    CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN
) {
?>
    <li class="<?php echo $controller == 'venues' ? 'active' : ''; ?>">
        <?php
        echo $this->Html->link(
            '<span class="aag-icon-pin"></span>' .
                __t('Menu.Venue'),
            array(
                'plugin' => false,
                'controller' => 'venues',
                'action' => 'home',
            ),
            array('escape' => false)
        );
        ?>
    </li>
<?php
}

if (
    $this->Acceso->haveModulePermission(ConstantsConfigModules::CONFERENCES) ||
    CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN
) {
?>
    <li class="<?php echo ($controller == 'conferences' || $controller == 'conferences_delegates') ? 'active' : '' ?>">
        <?php
        echo $this->Html->link(
            '<span class="aag-icon-mundo"></span>' .
                __t('Menu.Conference'),
            array(
                'plugin' => false,
                'controller' => 'conferences',
                'action' => 'home',
            ),
            array('escape' => false)
        );
        ?>
    </li>
    <?php
}
if (CakeSession::read('Auth.User.aag_region_id') == ConstantsAAGRegionId::UK) {
    // Admin role can see training providers
    if (
        CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
        $this->Acceso->haveModulePermission(ConstantsConfigModules::TRAINING)
    ) {
    ?>
        <li class="<?php echo in_array($controller, array('trainings_providers', 'trainings_trainers', 'trainings_courses', 'trainings_planned_courses', 'trainings_delegates', 'trainings_credits_networks', 'trainings_credits_movements')) ? 'active' : ''; ?>">
            <?php
            echo $this->Html->link(
                '<span class="ion-university"></span>' .
                    __t('Menu.Training'),
                array(
                    'plugin' => false,
                    'controller' => 'trainings_providers',
                    'action' => 'home',
                ),
                array('escape' => false)
            );
            ?>
        </li>
    <?php
    }
    // Network access without Admin role can access training list
    if (
        CakeSession::read('Auth.User.role_id') == ConstantsRoles::GARAGE_NETWORK_MANAGER &&
        $this->Acceso->haveModulePermission(ConstantsConfigModules::TRAINING)
    ) {
    ?>
        <li class="<?php echo in_array($controller, array('trainings_courses', 'trainings_planned_courses', 'trainings_delegates', 'trainings_credits_networks', 'trainings_credits_movements')) ? 'active' : ''; ?>">
            <?php
            echo $this->Html->link(
                '<span class="ion-university"></span>' .
                    __t('Menu.Training'),
                array(
                    'plugin' => false,
                    'controller' => 'trainings_courses',
                    'action' => 'home',
                ),
                array('escape' => false)
            );
            ?>
        </li>
    <?php
    }
}
if (
    (
        $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::MAINTENANCE) &&
        $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
    ) ||
    CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN
) {
    $active = (
        in_array($controller, array('maintenance', 'positions', 'config', 'regions', 'sales_families', 'shortcuts', 'groups_permissions')) ||
        (in_array($controller, array('services', 'vehicles', 'vehicle_types', 'software', 'websites', 'contracts', 'permission', 'associations')) && $action == 'home') ||
        ($controller == 'appointments' && $action == 'maintenance_home_events') ||
        ($controller == 'users' && $action == 'reassignments') ||
        ($controller == 'communications' && !in_array($action, array('section', 'communication_searcher', 'home_section'))) ||
        ($controller == 'tasks' && in_array($action, array('maintenance_tasks', 'add_debrief_task', 'edit_debrief_task', 'maintenance_topics', 'add_debrief_topic', 'edit_debrief_topic'))) ||
        ($controller == 'emails' && $action == 'maintenance')
    ) ? 'active' : '';
    ?>
    <li class="<?php echo $active; ?>">
        <?php
        echo $this->Html->link(
            '<span class="aag-icon-rodillo"></span>' .
                __t('Menu.Maintenance'),
            array(
                'plugin' => false,
                'controller' => 'maintenance',
                'action' => 'home',
            ),
            array('escape' => false)
        );
        ?>
    </li>
<?php
}
if (
    (
        $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_USER_GUIDE) &&
        CakeSession::read('Auth.User.role_id') != ConstantsRoles::GARAGE &&
        $this->Acceso->haveModulePermission(ConstantsConfigModules::USER_GUIDES)
    ) ||
    CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN
) {
?>
    <li class="<?php echo ($controller == 'user_guides') ? 'active' : '' ?>">
        <?php
        echo $this->Html->link(
            '<span class="aag-icon-birrete"></span>' .
                __t('Menu.User_guides'),
            array(
                'plugin' => false,
                'controller' => 'user_guides',
                'action' => 'home',
            ),
            array('escape' => false)
        );
        ?>
    </li>
    <?php
}
if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::GARAGE) {
    $garageNetworkClass = ClassRegistry::init('GarageNetwork');
    $garage_networks = $garageNetworkClass->find(
        'all',
        array('conditions' => array(
            'garage_id =' => CakeSession::read('Auth.User.garage_id'),
        ))
    );
    foreach ($garage_networks as $key => $value) {
        if (
            $value['GarageNetwork']['status'] == ConstantsNetworksStatus::LIVE &&
            in_array($value['GarageNetwork']['network_id'], CakeSession::read('Auth.User.networks'))
        ) {
            $Network = ClassRegistry::init('Network');
            $network = $Network->findById($value['GarageNetwork']['network_id']);
            $controllersNetworkActive = array('garages_networks', 'bookings', 'quotations', 'enquiries', 'reporting', 'reviews');
    ?>
            <li class="<?php echo ($network['Network']['id'] == $user['current_network'] && in_array($controller, $controllersNetworkActive) && $action != 'general') ? 'active' : ''; ?>">
                <?php
                echo $this->Html->link(
                    '<span class="aag-icon-garaje-redes"></span>' . $network['Network']['name'],
                    array(
                        'plugin' => false,
                        'controller' => 'garages_networks',
                        'action' => 'network_dashboard',
                        $value['GarageNetwork']['id']
                    ),
                    array('escape' => false)
                );
                ?>
            </li>
    <?php
        }
    }
}
if (
    $this->Acceso->haveModulePermission(ConstantsConfigModules::VALUE_ADDED_SUPPLIER) ||
    CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN
) {
    ?>
    <li class="<?php echo ($controller == 'value_added_suppliers') ? 'active' : '' ?>">
        <?php
        $icon = '<svg width="29" height="23" style="width: 29px !important; object-fit: contain; min-height: 23px !important;" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" data-name="Layer 1"><path d="m19 4h-1.101c-.465-2.279-2.484-4-4.899-4h-2c-2.414 0-4.434 1.721-4.899 4h-1.101c-2.757 0-5 2.243-5 5v10c0 2.757 2.243 5 5 5h8c.553 0 1-.447 1-1s-.447-1-1-1h-8c-1.654 0-3-1.346-3-3v-5h9c.553 0 1-.447 1-1s-.447-1-1-1h-9v-3c0-1.654 1.346-3 3-3h14c1.654 0 3 1.346 3 3v2c0 .552.447 1 1 1s1-.448 1-1v-2c0-2.757-2.243-5-5-5zm-8-2h2c1.302 0 2.402.839 2.816 2h-7.631c.414-1.161 1.514-2 2.816-2zm12.65 13.877c.492.656.462 1.565-.071 2.188l-5.568 5.935 2.06-6h-4.121l2.06 6-5.566-5.935c-.534-.622-.563-1.532-.071-2.188l1.558-2.077c.378-.504.971-.8 1.6-.8h1.605l-1.126 3h4l-1.126-3h1.606c.63 0 1.222.296 1.6.8l1.558 2.077z"/></svg>';

        if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::ADMIN) {
            echo $this->Html->link(
                $icon .
                    __t('ValueAddedSuppliers.ValueAddedSuppliers'),
                array(
                    'plugin' => false,
                    'controller' => 'value_added_suppliers',
                    'action' => 'management_home',
                ),
                array('escape' => false)
            );
        } else {
            echo $this->Html->link(
                $icon .
                    __t('ValueAddedSuppliers.ValueAddedSuppliers'),
                array(
                    'plugin' => false,
                    'controller' => 'value_added_suppliers',
                    'action' => 'home',
                ),
                array('escape' => false)
            );
        }
        ?>
    </li>
    <?php
}
if (
    $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::SUPPLIERS) ||
    CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN
) {
    if (
        $this->Acceso->haveModulePermission(ConstantsConfigModules::SUPPLIERS) ||
        CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN
    ) {
    ?>
        <li class="<?php echo $controller == 'suppliers' && !in_array($action, array('index', 'maintenance_suppliers_categories', 'add_category', 'edit_category')) ? 'active' : ''; ?>">
            <?php
            echo $this->Html->link(
                '<span class="aag-icon-maletin"></span>' .
                    __t('Suppliers.Suppliers'),
                array(
                    'plugin' => false,
                    'controller' => 'suppliers',
                    'action' => 'maintenance_suppliers',
                ),
                array('escape' => false)
            );
            ?>
        </li>
    <?php
    }
    if (
        $this->Acceso->haveModulePermission(ConstantsConfigModules::SUPPLIERS_CATEGORIES) ||
        CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN
    ) {
    ?>
        <li class="<?php echo $controller == 'suppliers' && in_array($action, array('maintenance_suppliers_categories', 'add_category', 'edit_category')) ? 'active' : ''; ?>">
            <?php
            echo $this->Html->link(
                '<span class="aag-icon-maletin-reloj"></span>' .
                    __t('Maintenance.Suppliers_categories'),
                array(
                    'plugin' => false,
                    'controller' => 'suppliers',
                    'action' => 'maintenance_suppliers_categories',
                ),
                array('escape' => false)
            );
            ?>
        </li>
    <?php
    }
    if (
        $this->Acceso->haveModulePermission(ConstantsConfigModules::BRANDS) ||
        CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN
    ) {
    ?>
        <li class="<?php echo ($controller == 'brands') ? 'active' : '' ?>">
            <?php
            echo $this->Html->link(
                '<span class="aag-icon-medalla"></span>' .
                    __t('Suppliers.Brands'),
                array(
                    'plugin' => false,
                    'controller' => 'brands',
                    'action' => 'maintenance_brands',
                ),
                array('escape' => false)
            );
            ?>
        </li>
    <?php
    }
    if (
        $this->Acceso->haveModulePermission(ConstantsConfigModules::PRODUCTS) ||
        CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN
    ) {
    ?>
        <li class="<?php echo ($controller == 'products') ? 'active' : '' ?>">
            <?php
            echo $this->Html->link(
                '<span class="aag-icon-caja"></span>' .
                    __t('Suppliers.Products'),
                array(
                    'plugin' => false,
                    'controller' => 'products',
                    'action' => 'maintenance_products',
                ),
                array('escape' => false)
            );
            ?>
        </li>
    <?php
    }
}
if (
    (
        $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_EMAILS) &&
        $this->Acceso->haveModulePermission(ConstantsConfigModules::EMAILS)
    ) ||
    CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN
) {
    ?>
    <li class="<?php echo $controller == 'emails' && $action != 'maintenance' ? 'active' : ''; ?>">
        <?php
        echo $this->Html->link(
            '<span class="aag-icon-correo-aviso"></span>' .
                __t('Menu.Emails'),
            array(
                'plugin' => false,
                'controller' => 'emails',
                'action' => 'home',
            ),
            array('escape' => false)
        );
        ?>
    </li>
<?php
}
if (
    (
        $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_CONTACT) &&
        $this->Acceso->haveModulePermission(ConstantsConfigModules::CONTACTS)
    ) ||
    CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN
) {
?>
    <li class="<?php echo $controller == 'contacts' ? 'active' : ''; ?>">
        <?php
        echo $this->Html->link(
            '<span class="aag-icon-comentario"></span>' .
                __t('Menu.Contacts'),
            array(
                'plugin' => false,
                'controller' => 'contacts',
                'action' => 'home',
            ),
            array('escape' => false)
        );
        ?>
    </li>
<?php
}
if (
    (
        $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_CONTACT_LIST) &&
        $this->Acceso->haveModulePermission(ConstantsConfigModules::CONTACTS_LIST)
    ) ||
    CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN
) {
?>
    <li class="<?php echo $controller == 'contacts_lists' ? 'active' : ''; ?>">
        <?php
        echo $this->Html->link(
            '<span class="aag-icon-agenda"></span>' .
                __t('Menu.Contacts_lists'),
            array(
                'plugin' => false,
                'controller' => 'contacts_lists',
                'action' => 'home',
            ),
            array('escape' => false)
        );
        ?>
    </li>
<?php
}
if (
    (
        $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::STATISTICS) &&
        $this->Acceso->haveModulePermission(ConstantsConfigModules::STATISTICS)
    ) ||
    CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN
) {
?>
    <li class="<?php echo $controller == 'statistics' ? 'active' : ''; ?>">
        <?php
        echo $this->Html->link(
            '<span class="aag-icon-grafica-con-barras"></span>' .
                __t('Menu.Statistics'),
            array(
                'plugin' => false,
                'controller' => 'statistics',
                'action' => 'home',
            ),
            array('escape' => false)
        );
        ?>
    </li>
<?php } ?>
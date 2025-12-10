<?php
$controller = $this->request->controller;
$action = $this->request->action;
$role = $this->Acceso->rol();
$user = $this->Acceso->user();
?>
<li class="<?php echo $controller == 'dashboard' ? 'active' : ''; ?>">
    <?php echo $this->Html->link(
        '<span class="aag-icon-casa"></span>' .
            __t('General.Dashboard'),
        array(
            'controller' => 'dashboard',
            'action' => 'home',
        ),
        array(
            'title' =>  __t('Menu.Dashboard'),
            'escape' => false
        )
    ); ?>
</li>
<li class="<?php echo in_array($controller, array('appointments', 'events')) ? 'active' : ''; ?>">
    <?php echo $this->Html->link(
        '<span class="aag-icon-calendarop"></span>' .
            __t('Menu.Agenda'),
        array(
            'controller' => 'appointments',
            'action' => 'home',
        ),
        array(
            'title' =>  __t('Menu.Agenda'),
            'escape' => false
        )
    ); ?>
</li>
<?php
if (
    CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
    !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GPC_LOGISTICS_BDM, ConstantsRoles::GPC_LOGISTICS_RM))
) {
?>
    <li class="<?php echo $controller == 'clients' && in_array($action, array('home', 'tracking', 'tracking_task', 'report', 'resume', 'statistic')) ? 'active' : ''; ?>">
        <?php echo $this->Html->link(
            '<span class="aag-icon-garage"></span>' .
                __t('Menu.Garages'),
            array(
                'controller' => 'clients',
                'action' => 'home',
            ),
            array(
                'title' =>  __t('Menu.Garages'),
                'escape' => false
            )
        ); ?>
    </li>
<?php
}
if (CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO)) {  ?>
    <li class="<?php echo $controller == 'clients' && in_array($action, array('home_distributors', 'tracking_distributor', 'tracking_distributor_task', 'report_distributor', 'resume_distributor', 'distributor_sales')) ? 'active' : ''; ?>">
        <?php echo $this->Html->link(
            '<span class="aag-icon-distribuidores"></span>' .
                __t('Menu.Distributors'),
            array(
                'controller' => 'clients',
                'action' => 'home_distributors',
            ),
            array(
                'title' =>  __t('Menu.Distributors'),
                'escape' => false
            )
        ); ?>
    </li>
<?php
}
if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
    $action_visits = CakeSession::read('Auth.User.role_id') == ConstantsRoles::GPC_LOGISTICS_BDM ? 'home_distributor' : 'home';
?>
    <li class="<?php echo $controller == 'visits' ? 'active' : ''; ?>">
        <?php echo $this->Html->link(
            '<span class="icon-planning"></span>' .
                __t('Menu.Planning_visits'),
            array(
                'controller' => 'visits',
                'action' => $action_visits,
            ),
            array(
                'title' =>  __t('Menu.Planning_visits'),
                'escape' => false
            )
        ); ?>
    </li>
<?php } ?>
<li class="<?php echo $controller == 'tasks' ? 'active' : ''; ?>">
    <?php echo $this->Html->link(
        '<span class="icon-tasks"></span>' .
            __t('Menu.Tasks'),
        array(
            'controller' => 'tasks',
            'action' => 'home',
            '?' => array(
                'assigned_to' => null,
            ),
        ),
        array(
            'title' => __t('Menu.Tasks'),
            'escape' => false
        )
    );
    ?>
</li>
<li style="flex-direction: column;">
    <?php echo '<a href="/" class="oc-dropdown"><span class="icon-crm"></span>' . __t('Crm.Management_area') . '</a>'; ?>
    <ul style="margin-top: 0px !important;">
        <?php
        if (
            in_array($role, array(ConstantsRoles::ADMIN, ConstantsRoles::GPC_LOGISTICS_RM, ConstantsRoles::TG_DIRECTOR, ConstantsRoles::AAG_DIRECTOR))
        ) {
        ?>
            <li class="<?php echo in_array($action, array('maintenance')) ? 'active' : ''; ?>">
                <?php
                echo $this->Html->link(
                    '<span class="ion-settings" style="font-size: 18px !important;"></span>' . __t('Crm.Management_maintenance'),
                    array(
                        'controller' => 'appointments_objectives',
                        'action' => 'maintenance',
                    ),
                    array(
                        'title' => __t('Crm.Management_maintenance'),
                        'escape' => false,
                        'style' => 'font-size: 1.3rem !important'
                    )
                );
                ?>
            </li>
        <?php } ?>
        <li class="<?php echo in_array($action, array('reporting')) ? 'active' : ''; ?>">
            <?php
            echo $this->Html->link(
                '<span class="ion-wrench" style="font-size: 18px !important;"></span>' . __t('Crm.Management_reporting'),
                array(
                    'controller' => 'appointments_objectives',
                    'action' => 'reporting',
                ),
                array(
                    'title' => __t('Crm.Management_reporting'),
                    'escape' => false,
                    'style' => 'font-size: 1.3rem !important'
                )
            );
            ?>
        </li>
        <?php if (in_array($role, array(ConstantsRoles::ADMIN, ConstantsRoles::GPC_LOGISTICS_RM, ConstantsRoles::TG_DIRECTOR, ConstantsRoles::AAG_DIRECTOR))) { ?>
            <li class="<?php echo in_array($action, array('objectives')) ? 'active' : ''; ?>">
                <?php
                echo $this->Html->link(
                    '<span class="ion-hammer" style="font-size: 18px !important;"></span>' . __t('Crm.Management_set_objectives'),
                    array(
                        'controller' => 'appointments_objectives',
                        'action' => 'objectives',
                    ),
                    array(
                        'title' => __t('Crm.Management_set_objectives'),
                        'escape' => false,
                        'style' => 'font-size: 1.3rem !important'
                    )
                );
                ?>
            </li>
            <li class="<?php echo in_array($action, array('view_objectives')) ? 'active' : ''; ?>">
                <?php
                echo $this->Html->link(
                    '<span class="ion-android-list" style="font-size: 18px !important;"></span>' . __t('Crm.Management_view_objectives'),
                    array(
                        'controller' => 'appointments_objectives',
                        'action' => 'view_objectives',
                    ),
                    array(
                        'title' => __t('Crm.Management_view_objectives'),
                        'escape' => false,
                        'style' => 'font-size: 1.3rem !important'
                    )
                );
                ?>
            </li>
        <?php } ?>
    </ul>
</li>
<?php
if (
    !in_array($role, array(ConstantsRoles::BDM_AAG, ConstantsRoles::BDM_TG, ConstantsRoles::GPC_LOGISTICS_BDM, ConstantsRoles::GPC_LOGISTICS_RM)) &&
    !in_array($user['Contact']['position_id'], array(ConstantsPositions::NATIONAL_SALES_MANAGER_CV_ID, ConstantsPositions::NATIONAL_SALES_MANAGER_LV_ID, ConstantsPositions::REGIONAL_SALES_MANAGER_LV_ID, ConstantsPositions::REGIONAL_SALES_MANAGER_CV_ID))
) {
?>
    <li>
        <?php
        echo $this->Html->link(
            '<span class="ion-android-exit"></span>' .
                __t('Menu.GNM'),
            array(
                'controller' => 'home',
                'action' => 'home_page2',
            ),
            array(
                'title' =>  __t('Menu.GNM'),
                'escape' => false
            )
        );
        ?>
    </li>
<?php } ?>

<script>
    $('#logo_home').attr('href', '/dashboard/home');
</script>
<style>
    header#header .oc-dropdown {
        cursor: pointer;
    }

    header#header .oc-dropdown+ul {
        display: none;
    }

    header#header .oc-dropdown.active+ul {
        display: block;
    }

    header#header .oc-dropdown.active i {
        transform: rotate(180deg);
    }
</style>
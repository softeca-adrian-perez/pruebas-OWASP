<?php
$controller = $this->request->controller;
$action = $this->request->action;
$role = $this->Acceso->rol();
$user = $this->Acceso->user();
?>
<nav>
    <div>
        <ul>
            <?php
            if (
                (
                    in_array($role, array(ConstantsRoles::BDM_AAG, ConstantsRoles::BDM_TG, ConstantsRoles::GPC_LOGISTICS_BDM, ConstantsRoles::AAG_MANAGER)) ||
                    in_array($user['Contact']['position_id'], array(ConstantsPositions::NATIONAL_SALES_MANAGER_CV_ID, ConstantsPositions::NATIONAL_SALES_MANAGER_LV_ID, ConstantsPositions::REGIONAL_SALES_MANAGER_CV_ID, ConstantsPositions::REGIONAL_SALES_MANAGER_LV_ID))
                ) &&
                (
                    ($controller == 'users' && in_array($action, array('my_data', 'my_preferences'))) ||
                    (in_array($controller, array('alerts', 'messages')) && $action == 'home') ||
                    ($controller == 'contacts' && $action == 'directory')
                )
            ) {
                echo $this->Element(
                    'Comun' . DS . 'menu_options_crm',
                    array(
                        'plugin' => false,
                        'role' => $role,
                        'controller' => $controller,
                        'action' => $action,
                    )
                );
            } elseif ($role == ConstantsRoles::GPC_LOGISTICS_RM) {
                echo $this->Element(
                    'Comun' . DS . 'options_menu',
                    array(
                        'plugin' => false,
                        'role' => $role,
                        'controller' => $controller,
                        'action' => $action,
                    )
                );
            } elseif (
                !in_array($controller, array('appointments', 'dashboard', 'clients', 'visits', 'tasks', 'events', 'portfolio', 'appointments_objectives', 'distributors_objectives')) ||
                ($controller == 'appointments' && $action == 'maintenance_home_events') ||
                ($controller == 'tasks' && in_array($action, array('maintenance_tasks', 'maintenance_topics', 'view_debrief_task', 'add_debrief_task', 'edit_debrief_task', 'add_debrief_topic', 'edit_debrief_topic')))
            ) {
                echo $this->Element(
                    'Comun' . DS . 'options_menu',
                    array(
                        'plugin' => false,
                        'role' => $role,
                        'controller' => $controller,
                        'action' => $action,
                    )
                );
            } else {
                echo $this->Element(
                    'Comun' . DS . 'menu_options_crm',
                    array(
                        'plugin' => false,
                        'role' => $role,
                        'controller' => $controller,
                        'action' => $action,
                    )
                );
            }
            ?>
        </ul>
    </div>
</nav>
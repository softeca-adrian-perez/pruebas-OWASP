<?php
$config = CakeSession::read('Auth.User.Config');
$classActive = 'class="active"';
$isRoleGarage = CakeSession::read('Auth.User.role_id') == ConstantsRoles::GARAGE;
?>
<div class="menu_garage aag-tabs">
    <ul>
        <?php if (!$isRoleGarage) { ?>
            <li <?php if ($selected == 'datas_garage') {
                    echo $classActive;
                } ?>>
                <?php
                // If exist "garage_id", is called to edit, otherwise, is called to add
                if (isset($garage_id)) {
                    echo $this->Html->link(
                        __t('Garage.Garage'),
                        array(
                            'controller' => 'garages',
                            'action' => 'edit',
                            $garage_id
                        )
                    );
                } else {
                    echo $this->Html->link(
                        __t('Garage.Garage'),
                        array(
                            'controller' => 'garages',
                            'action' => 'add',
                        )
                    );
                }
                ?>
            </li>
        <?php } ?>
        <?php if (isset($garage_id)) { ?>
            <?php if (!$isRoleGarage) { ?>
                <li <?php if ($selected == 'd&n_garage') {
                        echo $classActive;
                    } ?>>
                    <?php
                    echo $this->Html->link(
                        __t('Network.Distributors_networks'),
                        array(
                            'controller' => 'garages',
                            'action' => 'add_dis_and_net_garage',
                            $garage_id,
                        )
                    );
                    ?>
                </li>
            <?php } ?>
            <?php if (!$isRoleGarage) { ?>
                <li <?php if ($selected == 'activities_and_services_garage') {
                        echo $classActive;
                    } ?>>
                    <?php
                    echo $this->Html->link(
                        __t('Garage.Activity') . ' / ' . __t('Garage.Services'),
                        array(
                            'controller' => 'garages',
                            'action' => 'add_activities_and_services_garage',
                            $garage_id,
                        )
                    );
                    ?>
                </li>
            <?php } ?>
            <?php if (!$isRoleGarage) { ?>
                <li <?php if ($selected == 'aditional_ingo_garage') {
                        echo $classActive;
                    } ?>>
                    <?php
                    echo $this->Html->link(
                        __t('Garage.Aditional_info'),
                        array(
                            'controller' => 'garages',
                            'action' => 'add_aditional_info_and_other_details_garage',
                            $garage_id,
                        )
                    );
                    ?>
                </li>
            <?php } ?>
            <?php if (!$isRoleGarage) { ?>
                <li <?php if ($selected == 'opening_garage') {
                        echo $classActive;
                    } ?>>
                    <?php
                    echo $this->Html->link(
                        __t('Garage.Opening_times'),
                        array(
                            'controller' => 'garages',
                            'action' => 'add_opening_garage',
                            $garage_id,
                        )
                    );
                    ?>
                </li>
                <li <?php if ($selected == 'equipment_and_software_garage') {
                        echo $classActive;
                    } ?>>
                    <?php
                    echo $this->Html->link(
                        __t('Equipment.Equipment') . ' / ' . __t('Garage.Software'),
                        array(
                            'controller' => 'garages',
                            'action' => 'add_equipment_and_software_garage',
                            $garage_id,
                        )
                    );
                    ?>
                </li>
                <li <?php if ($selected == 'orders') {
                        echo $classActive;
                    } ?>>
                    <?php
                    echo $this->Html->link(
                        __t('Garage.Orders'),
                        array(
                            'controller' => 'garages',
                            'action' => 'add_orders',
                            $garage_id,
                        )
                    );
                    ?>
                </li>
                <li <?php if ($selected == 'marketing_and_image_garage') {
                        echo $classActive;
                    } ?>>
                    <?php
                    echo $this->Html->link(
                        __t('Garage.Marketing'),
                        array(
                            'controller' => 'garages',
                            'action' => 'add_marketing_and_image_garage',
                            $garage_id,
                        )
                    );
                    ?>
                </li>
            <?php } ?>
            <?php if (!$isRoleGarage && $get_list_config_tabs[ConstantsTabs::GENERAL_BRANCH_MANAGER] == ConstantsBooleans::ACTIVE) { ?>
                <li <?php if ($selected == 'contacts_general_branch_manager') {
                        echo $classActive;
                    } ?>>
                    <?php
                    echo $this->Html->link(
                        __t('Contact.General_branch_manager'),
                        array(
                            'controller' => 'garages',
                            'action' => 'add_contacts_general_branch_manager',
                            $garage_id,
                        )
                    );
                    ?>
                </li>
            <?php } ?>
            <?php if (!$isRoleGarage) { ?>
                <li <?php if ($selected == 'contact_garage_staff') {
                        echo $classActive;
                    } ?>>
                    <?php
                    echo $this->Html->link(
                        __t('Contact.Staff'),
                        array(
                            'controller' => 'garages',
                            'action' => 'add_contacts_staff',
                            $garage_id,
                        )
                    );
                    ?>
                </li>
            <?php } ?>
            <?php if (!$isRoleGarage && $get_list_config_tabs[ConstantsTabs::EMPLOYEES] == ConstantsBooleans::ACTIVE) { ?>
                <li <?php if ($selected == 'employee_garage') {
                        echo $classActive;
                    } ?>>
                    <?php
                    echo $this->Html->link(
                        __t('Garage.Employees'),
                        array(
                            'controller' => 'garages',
                            'action' => 'add_employee_garage',
                            $garage_id,
                        )
                    );
                    ?>
                </li>
            <?php } ?>
            <?php if (!$isRoleGarage) { ?>
                <li <?php if ($selected == 'admin') {
                        echo $classActive;
                    } ?>>
                    <?php
                    echo $this->Html->link(
                        __t('General.Admin'),
                        array(
                            'controller' => 'garages',
                            'action' => 'add_admin',
                            $garage_id,
                        )
                    );
                    ?>
                </li>
                <li <?php if ($selected == 'requested_changes') {
                        echo $classActive;
                    } ?>>
                    <?php
                    echo $this->Html->link(
                        __t('RequestedChanges.Requested_changes'),
                        array(
                            'controller' => 'garages',
                            'action' => 'requested_changes',
                            $garage_id,
                        )
                    );
                    ?>
                </li>
                <li <?php if ($selected == 'training_credits') {
                        echo 'class="active"';
                    } ?>>
                    <?php
                    echo $this->Html->link(
                        __t('Training.Trainings_credits'),
                        array(
                            'controller' => 'garages',
                            'action' => 'training_credits',
                            $garage_id,
                        )
                    );
                    ?>
                </li>
                <li <?php if ($selected == 'values_adds') {
                        echo 'class="active"';
                    } ?>>
                    <?php
                    echo $this->Html->link(
                        __t('Garage.Value_add'),
                        array(
                            'controller' => 'garages',
                            'action' => 'add_value_add',
                            $garage_id,
                        )
                    );
                    ?>
                </li>
            <?php } ?>
        <?php } ?>
    </ul>
</div>
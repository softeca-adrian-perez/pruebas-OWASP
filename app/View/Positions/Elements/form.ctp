<?php
$action = $this->request->action;
echo $this->Html->script('positions_maintenance.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('permissions_group.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Form->create(
    'Position',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data',
        'id' => 'position-form'
    )
);
echo $this->Form->hidden(
    'Position.id',
    array(
        'id' => 'position-page',
        'val' => true
    )
);
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        if ($action == ConstantsActionsNames::ADD) {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Maintenance.Maintenance'),
                    array(
                        'controller' => 'maintenance',
                        'action' => 'home'
                    )
                ),
                $this->Html->link(
                    __t('Maintenance.Positions'),
                    array(
                        'controller' => 'positions',
                        'action' => 'home'
                    )
                ),
                __t('General.Add'),
            ));
        } else {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Maintenance.Maintenance'),
                    array(
                        'controller' => 'maintenance',
                        'action' => 'home'
                    )
                ),
                $this->Html->link(
                    __t('Maintenance.Positions'),
                    array(
                        'controller' => 'positions',
                        'action' => 'home'
                    )
                ),
                __t('General.Edit'),
            ));
        }
        ?>
    </div>
    <div>
        <?php echo $this->element('Comun/form_actions', $cancel_action); ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="aag-title p-bottom-1">
        <?php echo __t('Maintenance.Positions'); ?>
    </div>
    <div class="cnt-form-inputs">
        <?php
        echo $this->Form->input(
            'Position.name_en',
            array(
                'label' => __t('Maintenance.Position_name_en'),
                'type' => 'text',
                'required' => true,
            )
        );
        echo $this->Form->input(
            'Position.name_fr',
            array(
                'label' => __t('Maintenance.Position_name_fr'),
                'type' => 'text',
                'required' => true,
            )
        );
        echo $this->Form->input(
            'Position.name_de',
            array(
                'label' => __t('Maintenance.Position_name_de'),
                'type' => 'text',
                'required' => true,
            )
        );
        echo $this->Form->input(
            'Position.role_id',
            array(
                'label' => __t('Maintenance.Role'),
                'class' => 'select2-multiple',
                'type' => 'select',
                'multiple' => false,
                'empty' => true,
                'options' => $roles,
                'id' => 'position_role',
                'data-url' => Router::url(array(
                    'controller' => 'positions',
                    'action' => 'ajax_position_config_type_role'
                ))
            )
        );
        ?>
    </div>
    <div id="role_position_type" class="d-none">
        <div class="aag-subtitle p-top-1"><?php echo __t('General.Type'); ?></div>
        <div class="cnt-form-inputs">
            <div id="position_type_cnt">
                <?php echo $this->element('../Positions/Elements/position_type'); ?>
            </div>
        </div>
        <div class="aag-subtitle p-top-1"><?php echo __t('Maintenance.Configuration'); ?></div>
        <div class="cnt-form-inputs">
            <div class="d-none filter_config" id="cnt_networks">
                <?php echo $this->Form->input(
                    'FormInput',
                    array(
                        'type' => 'select',
                        'options' => $networks,
                        'class' => 'select2-multiple clear_field',
                        'required' => true,
                        'multiple' => true,
                        'id' => 'input_networks',
                        'label' => __t('Network.Network'),
                    )
                ); ?>
            </div>
            <div class="d-none filter_config" id="cnt_regions">
                <?php echo $this->Form->input(
                    'FormInput',
                    array(
                        'type' => 'select',
                        'options' => $regions,
                        'class' => 'select2-multiple clear_field',
                        'required' => true,
                        'multiple' => true,
                        'id' => 'input_regions',
                        'label' => __t('General.Region'),
                    )
                ); ?>
            </div>
            <div class="d-none filter_config" id="cnt_trading_groups">
                <?php echo $this->Form->input(
                    'FormInput',
                    array(
                        'type' => 'select',
                        'options' => $trading_groups,
                        'class' => 'select2-multiple clear_field',
                        'required' => true,
                        'multiple' => true,
                        'id' => 'input_trading_groups',
                        'label' => __t('TradingGroup.Trading_groups'),
                    )
                ); ?>
            </div>
            <div class="d-none filter_config" id="cnt_distributor_networks">
                <?php echo $this->Form->input(
                    'FormInput',
                    array(
                        'type' => 'select',
                        'options' => $distributor_networks,
                        'class' => 'select2-multiple clear_field',
                        'required' => true,
                        'multiple' => true,
                        'id' => 'input_distributor_networks',
                        'label' => __t('Network.Distributor_networks'),
                    )
                ); ?>
            </div>
            <div class="d-none filter_config" id="cnt_aag_members">
                <?php echo $this->Form->input(
                    'FormInput',
                    array(
                        'type' => 'select',
                        'options' => $aag_members,
                        'class' => 'select2-multiple clear_field',
                        'required' => true,
                        'multiple' => true,
                        'id' => 'input_aag_members',
                        'label' => __t('Distributor.Aag_member'),
                    )
                ); ?>
            </div>
            <div class="d-none filter_config" id="cnt_profiles">
                <?php echo $this->Form->input(
                    'FormInput',
                    array(
                        'type' => 'select',
                        'options' => $profiles,
                        'class' => 'select2-multiple clear_field',
                        'required' => true,
                        'multiple' => true,
                        'id' => 'input_profiles',
                        'label' => __t('Distributor.Profile'),
                    )
                ); ?>
            </div>
            <div class="d-none filter_config" id="cnt_positions">
                <?php echo $this->Form->input(
                    'FormInput',
                    array(
                        'type' => 'select',
                        'options' => $positions ?? array(),
                        'class' => 'select2-multiple clear_field',
                        'required' => true,
                        'multiple' => true,
                        'id' => 'input_positions',
                        'label' => __t('General.Position'),
                    )
                ); ?>
            </div>
            <div class="d-none filter_config" id="cnt_customer_activities">
                <?php echo $this->Form->input(
                    'FormInput',
                    array(
                        'type' => 'select',
                        'options' => $customer_activities,
                        'class' => 'select2-multiple clear_field',
                        'required' => true,
                        'multiple' => true,
                        'id' => 'input_customer_activities',
                        'label' => __t('Garage.Customer_activities'),
                    )
                ); ?>
            </div>
            <div class="d-none filter_config" id="cnt_bdms">
                <?php echo $this->Form->input(
                    'FormInput',
                    array(
                        'type' => 'select',
                        'options' => $bdms,
                        'class' => 'select2-multiple clear_field',
                        'required' => true,
                        'multiple' => true,
                        'id' => 'input_bdms',
                        'label' => __t('Position.BDM_associated'),
                    )
                ); ?>
            </div>
            <div class="d-none filter_config" id="cnt_suppliers_categories">
                <?php echo $this->Form->input(
                    'FormInput',
                    array(
                        'type' => 'select',
                        'options' => $suppliers_categories,
                        'class' => 'select2-multiple clear_field',
                        'required' => true,
                        'multiple' => true,
                        'id' => 'input_suppliers_categories',
                        'label' => __t('Suppliers.Categories'),
                    )
                ); ?>
            </div>
            <div class="filter_config" id="group_permission_cnt">
                <?php echo $this->element('../Positions/Elements/group_permission'); ?>
            </div>
        </div>
        <div class="ta-right m-vertical-1">
            <?php
            echo $this->Html->link(
                __t('General.Add'),
                array(),
                array(
                    'escape' => false,
                    'class' => 'aag-button small green',
                    'id' => 'button_add'
                )
            );
            ?>
            <div class="d-none f-right" id="ctn_new_permission_group-js">
                <?php echo $this->Html->link(
                    __t('General.New_group_of_permissions'),
                    array(),
                    array(
                        'escape' => false,
                        'class' => 'aag-button small green m-0-i',
                        'id' => 'button_new_group_permission-js',
                        'data-open' => "myModal",
                        'data-url' => Router::url(array(
                            'controller' => 'groups_permissions',
                            'action' => 'ajax_add',
                        )),
                        'data-url_get_group_permissions' => Router::url(array(
                            'controller' => 'positions',
                            'action' => 'ajax_position_group_permissions',
                        ))
                    )
                ); ?>
            </div>
        </div>

    </div>

    <div class="o-auto clear p-vertical-1">
        <table class="table-tracking">
            <thead>
                <tr id="table_position_header">
                    <th class="ta-center"><?php echo __t('General.Type'); ?></th>
                    <th class="ta-center"><?php echo __t('Network.Network'); ?></th>
                    <th class="ta-center"><?php echo __t('General.Region'); ?></th>
                    <th class="ta-center"><?php echo __t('TradingGroup.Trading_groups'); ?></th>
                    <th class="ta-center"><?php echo __t('Network.Distributor_networks'); ?></th>
                    <th class="ta-center"><?php echo __t('Distributor.Aag_member'); ?></th>
                    <th class="ta-center"><?php echo __t('Distributor.Profile'); ?></th>
                    <th class="ta-center"><?php echo __t('Garage.Customer_activities'); ?></th>
                    <th class="ta-center"><?php echo __t('Contact.BDM'); ?></th>
                    <th class="ta-center"><?php echo __t('Suppliers.Categories'); ?></th>
                    <th class="ta-center"><?php echo __t('Config.Group_permissions'); ?></th>
                    <th class="ta-center"><?php echo __t('General.Actions'); ?></th>
                </tr>
            </thead>
            <tbody id="table_position">
                <?php
                $counter = 0;
                if (isset($position)) {
                    for ($i = 0; $i < $position['Position']['config_length']; $i++) { ?>
                        <tr data-row="<?php echo $counter; ?>">
                            <td class="ta-center position_type_select">
                                <?php echo "<span data-id='" . $position['Position']['position_types'][$i] . "'>" . h($position_config_types[$position['Position']['position_types'][$i]]) . "</span>"; ?>
                            </td>
                            <td class="ta-center network_select">
                                <?php if ($position['Position']['all_networks'][$i]) {
                                    echo __t('General.All');
                                } ?>
                                <?php foreach ($position['Position']['networks'][$i] as $network_id) {
                                    echo h($networks[$network_id]) . '<br>';
                                } ?>
                            </td>
                            <td class="ta-center region_select">
                                <?php if ($position['Position']['all_regions'][$i]) {
                                    echo __t('General.All');
                                } ?>
                                <?php foreach ($position['Position']['regions'][$i] as $region_id) {
                                    echo h($regions[$region_id]) . '<br>';
                                } ?>
                            </td>
                            <td class="ta-center trading_group_select">
                                <?php if ($position['Position']['all_trading_groups'][$i]) {
                                    echo __t('General.All');
                                } ?>
                                <?php foreach ($position['Position']['trading_groups'][$i] as $trading_group_id) {
                                    echo h($trading_groups[$trading_group_id]) . '<br>';
                                } ?>
                            </td>
                            <td class="ta-center distributor_network_select">
                                <?php if ($position['Position']['all_distributor_networks'][$i]) {
                                    echo __t('General.All');
                                } ?>
                                <?php foreach ($position['Position']['distributor_networks'][$i] as $distributor_network_id) {
                                    echo h($distributor_networks[$distributor_network_id]) . '<br>';
                                } ?>
                            </td>
                            <td class="ta-center aag_member_select">
                                <?php if ($position['Position']['all_aag_members'][$i]) {
                                    echo __t('General.All');
                                } ?>
                                <?php foreach ($position['Position']['aag_members'][$i] as $aag_member) {
                                    echo h($aag_members[$aag_member]) . '<br>';
                                } ?>
                            </td>
                            <td class="ta-center profiles_select">
                                <?php if ($position['Position']['all_profiles'][$i]) {
                                    echo __t('General.All');
                                } ?>
                                <?php foreach ($position['Position']['profiles'][$i] as $profile_id) {
                                    echo h($profiles[$profile_id]) . '<br>';
                                } ?>
                            </td>
                            <td class="ta-center customer_activity_select">
                                <?php if ($position['Position']['all_customer_activities'][$i]) {
                                    echo __t('General.All');
                                } ?>
                                <?php foreach ($position['Position']['customer_activities'][$i] as $customer_activity_id) {
                                    echo h($customer_activities[$customer_activity_id]) . '<br>';
                                } ?>
                            </td>
                            <td class="ta-center bdm_select">
                                <?php foreach ($position['Position']['bdms'][$i] as $user_id) {
                                    echo h($bdms[$user_id]) . '<br>';
                                } ?>
                            </td>
                            <td class="ta-center category_select">
                                <?php if ($position['Position']['all_suppliers_categories'][$i]) {
                                    echo __t('General.All');
                                } ?>
                                <?php foreach ($position['Position']['suppliers_categories'][$i] as $supplier_category_id) {
                                    echo h($suppliers_categories[$supplier_category_id]) . '<br>';
                                } ?>
                            </td>
                            <td class="ta-center permission_select">
                                <?php echo "<span data-id='" . $position['Position']['group_permissions'][$i] . "'>" . h($group_permissions[$position['Position']['group_permissions'][$i]]) . "</span>"; ?>
                            </td>
                            <td class="ta-center">
                                <span class="ion-android-cancel cursor-pointer c-fallo delete-row"></span>
                            </td>
                        </tr>
                <?php $counter++;
                    }
                } ?>
            </tbody>
        </table>
    </div>
</div>

<?php
if ($this->action == ConstantsActionsNames::EDIT && !$position_uses) {
?>
    <div class="row">
        <div class="columns medium-12 ta-right m-top-1">
            <?php echo $this->Html->link(
                "<span class='icon-delete'></span>" .
                    __t('General.Delete'),
                array(
                    'controller' => 'positions',
                    'action' => 'delete',
                    $position_id
                ),
                array(
                    'escape' => false,
                    'class' => 'button-general conImg cuatro',
                )
            ); ?>
        </div>
    </div>
<?php
}
echo $this->Form->end();
if (isset($position)) {
    if (isset($position['Position']['all_networks'])) {
        foreach ($position['Position']['all_networks'] as $key => $all_networks) {
            if ($all_networks) {
                $position['Position']['networks'][$key] = array(0 => ConstantsConfigSelect::ALL);
            }
        }
    }
    if (isset($position['Position']['all_regions'])) {
        foreach ($position['Position']['all_regions'] as $key => $all_regions) {
            if ($all_regions) {
                $position['Position']['regions'][$key] = array(0 => ConstantsConfigSelect::ALL);
            }
        }
    }
    if (isset($position['Position']['all_trading_groups'])) {
        foreach ($position['Position']['all_trading_groups'] as $key => $all_trading_groups) {
            if ($all_trading_groups) {
                $position['Position']['trading_groups'][$key] = array(0 => ConstantsConfigSelect::ALL);
            }
        }
    }
    if (isset($position['Position']['all_distributor_networks'])) {
        foreach ($position['Position']['all_distributor_networks'] as $key => $all_networks) {
            if ($all_networks) {
                $position['Position']['distributor_networks'][$key] = array(0 => ConstantsConfigSelect::ALL);
            }
        }
    }
    if (isset($position['Position']['all_aag_members'])) {
        foreach ($position['Position']['all_aag_members'] as $key => $all_aag_members) {
            if ($all_aag_members) {
                $position['Position']['aag_members'][$key] = array(0 => ConstantsConfigSelect::ALL);
            }
        }
    }
    if (isset($position['Position']['all_profiles'])) {
        foreach ($position['Position']['all_profiles'] as $key => $all_profiles) {
            if ($all_profiles) {
                $position['Position']['profiles'][$key] = array(0 => ConstantsConfigSelect::ALL);
            }
        }
    }
    if (isset($position['Position']['all_customer_activities'])) {
        foreach ($position['Position']['all_customer_activities'] as $key => $all_customer_activities) {
            if ($all_customer_activities) {
                $position['Position']['customer_activities'][$key] = array(0 => ConstantsConfigSelect::ALL);
            }
        }
    }
    if (isset($position['Position']['all_suppliers_categories'])) {
        foreach ($position['Position']['all_suppliers_categories'] as $key => $all_suppliers_categories) {
            if ($all_suppliers_categories) {
                $position['Position']['suppliers_categories'][$key] = array(0 => ConstantsConfigSelect::ALL);
            }
        }
    }
}
?>
<div style="display: none;" id="myModal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
    <div id="modal_new_permission_group-js" class="medium-12 columns p-right-0">
    </div>
    <a class="close-modal" data-close aria-label="Close">&#215;</a>
</div>
<script>
    <?php
    if (isset($position['Position']['regions'])) {
    ?> regions = <?php echo json_encode($position['Position']['regions']); ?>;
    <?php
    } else {
    ?> regions = {};
    <?php
    }
    if (isset($position['Position']['networks'])) {
    ?> networks = <?php echo json_encode($position['Position']['networks']); ?>;
    <?php
    } else {
    ?> networks = {};
    <?php
    }
    if (isset($position['Position']['trading_groups'])) {
    ?> trading_groups = <?php echo json_encode($position['Position']['trading_groups']); ?>;
    <?php
    } else {
    ?> trading_groups = {};
    <?php
    }
    if (isset($position['Position']['distributor_networks'])) {
    ?> distributor_networks = <?php echo json_encode($position['Position']['distributor_networks']); ?>;
    <?php
    } else {
    ?> distributor_networks = {};
    <?php
    }
    if (isset($position['Position']['aag_members'])) {
    ?> aag_members = <?php echo json_encode($position['Position']['aag_members']); ?>;
    <?php
    } else {
    ?> aag_members = {};
    <?php
    }
    if (isset($position['Position']['profiles'])) {
    ?> profiles = <?php echo json_encode($position['Position']['profiles']); ?>;
    <?php
    } else {
    ?> profiles = {};
    <?php
    }
    if (isset($position['Position']['customer_activities'])) {
    ?> customer_activities = <?php echo json_encode($position['Position']['customer_activities']); ?>;
    <?php
    } else {
    ?> customer_activities = {};
    <?php
    }
    if (isset($position['Position']['bdms'])) {
    ?> bdms = <?php echo json_encode($position['Position']['bdms']); ?>;
    <?php
    } else {
    ?> bdms = {};
    <?php
    }
    if (isset($position['Position']['suppliers_categories'])) {
    ?> suppliers_categories = <?php echo json_encode($position['Position']['suppliers_categories']); ?>;
    <?php
    } else {
    ?> suppliers_categories = {};
    <?php
    }
    if (isset($position['Position']['group_permissions'])) {
    ?> group_permissions = <?php echo json_encode($position['Position']['group_permissions']); ?>;
    <?php
    } else {
    ?> group_permissions = {};
    <?php
    }
    if (isset($position['Position']['position_types'])) {
    ?> position_types = <?php echo json_encode($position['Position']['position_types']); ?>;
    <?php
    } else {
    ?> position_types = {};
    <?php
    }
    ?>
    positions_length = <?php echo $counter; ?>;
</script>
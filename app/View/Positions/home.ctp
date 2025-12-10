<?php echo $this->Html->script('positions.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script')); ?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Maintenance.Maintenance'),
                array(
                    'controller' => 'maintenance',
                    'action' => 'home'
                )
            ),
            __t('Maintenance.Positions')
        ));
        ?>
    </div>
    <?php if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) { ?>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back')?></a>
        <?php
        echo $this->Html->link(
            __t('Position.New_position'),
            array(
                'controller' => 'positions',
                'action' => 'add'
            ),
            array('class' => 'aag-button medium green')
        );
        ?>
    </div>
    <?php } ?>
</div>
<div class="cnt-data">
    <?php echo $this->element('../Positions/Elements/search'); ?>
    <div class="o-auto">
        <table class="table-tracking table-position-maintenance">
            <thead>
            <tr>
                <th><?php echo $this->Paginator->sort('Position.name'.__s(), __t('Position.Position')); ?></th>
                <th><?php echo $this->Paginator->sort('Position.role_id', __t('Role.Role')); ?></th>
                <th><?php echo __t('Config.Type'); ?></th>
                <th><?php echo __t('Config.Network'); ?></th>
                <th><?php echo __t('Config.Regions'); ?></th>
                <th><?php echo __t('Config.Tg'); ?></th>
                <th><?php echo __t('Network.Distributor_networks'); ?></th>
                <th><?php echo __t('Distributor.Aag_member'); ?></th>
                <th><?php echo __t('Distributor.Profile'); ?></th>
                <th><?php echo __t('Garage.Customer_activities'); ?></th>
                <th><?php echo __t('Suppliers.Categories'); ?></th>
                <th><?php echo __t('Contact.BDM'); ?></th>
                <th><?php echo __t('Config.Group_permissions'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $row_counter = 0;
            foreach($positions_config as $position){
                if(isset($position['Config'])){
                    $config_count = count($position['Config']);
                    $counter = 0;
                    foreach($position['Config'] as $config){ ?>
                        <tr>
                            <?php
                            if($row_counter%2 == 0)
                            {
                                $row_background = 'background: var(--container-color)';
                            }
                            else
                            {
                                $row_background = 'background: var(--container-elements-color)';
                            }
                            $class = '';
                            if( !$counter ){
                                $class = ' class="with-border"'; ?>
                                <td class="with-border" rowspan="<?php echo $config_count;?>" style="<?php echo $row_background; ?>">
                                <?php if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
                                    echo $this->Html->link(
                                        $positions_list[$position['position_id']],
                                        array(
                                            'controller' => 'positions',
                                            'action' => 'edit',
                                            $position['position_id'],
                                        ),
                                        array(
                                            'class' => 'c-primary'
                                        )
                                    );
                                } else {
                                    echo h($positions_list[$position['position_id']]);
                                }?>
                                </td>
                                <td class="with-border" rowspan="<?php echo $config_count;?>" style="<?php echo $row_background; ?>">
                                    <?php echo h($roles[$position['role_id']]); ?>
                                </td>
                                <?php $row_counter++; ?>
                            <?php } ?>
                            <td <?php echo $class; ?>>
                                <?php echo h($position_config_types[$config['PositionConfig']['position_config_type_id']]); ?>
                            </td>
                            <td <?php echo $class; ?>>
                                <?php if( $config['PositionConfig']['all_networks'] ) {
                                    echo __t('General.All') . '<br>';
                                } else if( isset($config['PositionConfig']['Networks']) && $config['PositionConfig']['Networks'] ){
                                    foreach($config['PositionConfig']['Networks'] as $network){
                                        echo h($networks[$network['PositionConfigNetwork']['network_id']]) . '<br>';
                                    }
                                } ?>
                            </td>
                            <td <?php echo $class; ?>>
                                <?php if( $config['PositionConfig']['all_regions'] ) {
                                    echo __t('General.All') . '<br>';
                                } else if( isset($config['PositionConfig']['Regions']) && $config['PositionConfig']['Regions'] ){
                                    foreach($config['PositionConfig']['Regions'] as $region){
                                        echo h($regions[$region['PositionConfigRegion']['region_id']]) . '<br>';
                                    }
                                } ?>
                            </td>
                            <td <?php echo $class; ?>>
                                <?php if( $config['PositionConfig']['all_trading_groups'] ) {
                                    echo __t('General.All') . '<br>';
                                } else if( isset($config['PositionConfig']['TradingGroups']) && $config['PositionConfig']['TradingGroups'] ){
                                    foreach($config['PositionConfig']['TradingGroups'] as $trading_group){
                                        echo h($trading_groups[$trading_group['PositionConfigTradingGroup']['trading_group_id']]) . '<br>';
                                    }
                                } ?>
                            </td>
                            <td <?php echo $class; ?>>
                                <?php if( $config['PositionConfig']['all_distributor_networks'] ) {
                                    echo __t('General.All') . '<br>';
                                } else if( isset($config['PositionConfig']['DistributorNetworks']) && $config['PositionConfig']['DistributorNetworks'] ){
                                    foreach($config['PositionConfig']['DistributorNetworks'] as $distributor_network){
                                        echo h($distributor_networks[$distributor_network['PositionConfigDistributorNetwork']['distributor_network_id']]) . '<br>';
                                    }
                                } ?>
                            </td>
                            <td <?php echo $class; ?>>
                                <?php if( $config['PositionConfig']['all_aag_members'] ) {
                                    echo __t('General.All') . '<br>';
                                } elseif( isset($config['PositionConfig']['AagMembers']) && $config['PositionConfig']['AagMembers'] ){
                                    foreach($config['PositionConfig']['AagMembers'] as $aag_member){
                                        echo h($aag_members[$aag_member['PositionConfigAagMember']['aag_member']]) . '<br>';
                                    }
                                } ?>
                            </td>
                            <td <?php echo $class; ?>>
                                <?php if( $config['PositionConfig']['all_profiles'] ) {
                                    echo __t('General.All') . '<br>';
                                } elseif( isset($config['PositionConfig']['Profiles']) && $config['PositionConfig']['Profiles'] ){
                                    foreach($config['PositionConfig']['Profiles'] as $profile){
                                        echo h($profiles[$profile['PositionConfigProfile']['profile_id']]) . '<br>';
                                    }
                                } ?>
                            </td>
                            <td <?php echo $class; ?>>
                                <?php if( $config['PositionConfig']['all_customer_activities'] ) {
                                    echo __t('General.All') . '<br>';
                                } elseif( isset($config['PositionConfig']['CustomerActivities']) && $config['PositionConfig']['CustomerActivities'] ){
                                    foreach($config['PositionConfig']['CustomerActivities'] as $customer_activity){
                                        echo h($customer_activities[$customer_activity['PositionConfigCustomerActivity']['customer_activity_id']]) . '<br>';
                                    }
                                } ?>
                            </td>
                            <td <?php echo $class; ?>>
                                <?php if( $config['PositionConfig']['all_suppliers_categories'] ) {
                                    echo __t('General.All') . '<br>';
                                } elseif( isset($config['PositionConfig']['SuppliersCategories']) && $config['PositionConfig']['SuppliersCategories'] ){
                                    foreach($config['PositionConfig']['SuppliersCategories'] as $supplier_category){
                                        echo h($suppliers_categories[$supplier_category['PositionConfigSupplierCategory']['supplier_category_id']]) . '<br>';
                                    }
                                } ?>
                            </td>
                            <td <?php echo $class; ?>>
                                <?php if( isset($config['PositionConfig']['Bdms']) && $config['PositionConfig']['Bdms'] ){
                                    foreach($config['PositionConfig']['Bdms'] as $bdm){
                                        echo $user_list[$bdm['PositionConfigBdm']['user_id']];
                                    }
                                } ?>
                            </td>
                            <td <?php echo $class; ?>>
                                <?php echo h($group_permissions[$config['PositionConfig']['group_permission_id']]); ?>
                            </td>
                        </tr>
                        <?php $counter++;
                    }
                }else{?>
                    <tr>
                        <?php
                        if($row_counter%2 == 0){
                            $row_background = 'background: #FFFFFF';
                        } else {
                            $row_background = 'background: #F9F9F9';
                        }

                        $class = '';

                        $class = ' class="with-border"'; ?>

                        <td class="with-border" style="<?php echo $row_background; ?>">
                        <?php if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
                            echo $this->Html->link(
                                $positions_list[$position['position_id']],
                                array(
                                    'controller' => 'positions',
                                    'action' => 'edit',
                                    $position['position_id'],
                                ),
                                array(
                                    'class' => 'c-primary'
                                )
                            );} else {
                                echo h($positions_list[$position['position_id']]);
                            }?>
                        </td>
                        <td class="with-border" style="<?php echo $row_background; ?>">
                            <?php echo h($roles[$position['role_id']]); ?>
                        </td>

                        <td <?php echo $class; ?>>

                        </td>
                        <td <?php echo $class; ?>>

                        </td>
                        <td <?php echo $class; ?>>

                        </td>
                        <td <?php echo $class; ?>>

                        </td>
                        <td <?php echo $class; ?>>

                        </td>
                        <td <?php echo $class; ?>>

                        </td>
                        <td <?php echo $class; ?>>

                        </td>
                        <td <?php echo $class; ?>>

                        </td>
                        <td <?php echo $class; ?>>

                        </td>
                        <td <?php echo $class; ?>>

                        </td>
                        <td <?php echo $class; ?>>

                        </td>
                    </tr>
                    <?php
                    $row_counter++;
                }
            } ?>
            </div>
            </tbody>
        </table>
    </div>
    <?php echo $this->element('Comun/paginacion'); ?>
</div>
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
            $this->Html->link(
                __t('Maintenance.Permissions'),
                array(
                    'controller' => 'permissions',
                    'action' => 'home'
                )
            ),
            __t('General.View'),
        ));
        ?>
    </div>
    <div>
        <?php
        echo $this->Html->link(
            __t('General.Back'),
            CakeSession::read('url_referer'),
            array('class' => 'aag-button medium four')
        );
        ?>
    </div>
</div>
<div class="cnt-data">
    <?php echo $this->element('../Permissions/Elements/search_permission'); ?>
    <div class="o-auto">
        <table class="table-tracking">
            <thead>
                <tr>
                    <th><?php echo $this->Paginator->sort('User.full_user_name', __t('User.Name')); ?></th>
                    <th><?php echo $this->Paginator->sort('User.role_id', __t('User.Role')); ?></th>
                    <th><?php echo $this->Paginator->sort('PositionConfig.position_id', __t('Maintenance.Position')); ?></th>
                    <th><?php echo $this->Paginator->sort('PositionConfig.group_permission_id', __t('GroupPermission.Group_permission')); ?></th>
                    <th><?php echo __t('Contact.BDM'); ?></th>
                    <th><?php echo __t('Network.Networks'); ?></th>
                    <th><?php echo __t('General.Regions'); ?></th>
                    <th><?php echo __t('Network.Trading_groups'); ?></th>

                    <th><?php echo __t('Network.Distributor_networks'); ?></th>
                    <th><?php echo __t('Distributor.Aag_member'); ?></th>
                    <th><?php echo __t('Distributor.Profile'); ?></th>
                    <th><?php echo __t('Garage.Customer_activities'); ?></th>
                    <th><?php echo __t('Suppliers.Categories'); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ( $permission_users as $permission ){ ?>
                <tr>
                    <td>
                        <?php echo h($permission[0]['full_user_name']); ?>
                    </td>
                    <td>
                        <?php echo ($permission['User']['role_id'] != null) ? h($roles[$permission['User']['role_id']]) : ''?>
                    </td>
                    <td>
                        <?php echo ($permission['PositionConfig']['position_id'] != null) ? h($positions[$permission['PositionConfig']['position_id']]) : ''?>
                    </td>
                    <td>
                        <?php echo ($permission['PositionConfig']['group_permission_id'] != null) ? h($group_permissions[$permission['PositionConfig']['group_permission_id']]) : ''?>
                    </td>
                    <td>
                        <?php if( !empty($permission['bdms']) ) {
                            foreach($permission['bdms'] as $bdm ){
                                echo h($users[$bdm['PositionConfigBdm']['user_id']]) . ' / ';
                            }
                        }?>
                    </td>
                    <td>
                        <?php if( $permission['PositionConfig']['all_networks'] ){
                            echo __t('General.All');
                        } else if( !empty($permission['networks']) ) {
                            foreach($permission['networks'] as $network ){
                                echo h($networks[$network['PositionConfigNetwork']['network_id']]) . ' / ';
                            }
                        }?>
                    </td>
                    <td>
                        <?php if( $permission['PositionConfig']['all_regions'] ){
                            echo __t('General.All');
                        } else if( !empty($permission['regions']) ) {
                            foreach($permission['regions'] as $region ){
                                echo h($regions[$region['PositionConfigRegion']['region_id']]) . ' / ';
                            }
                        }?>
                    </td>
                    <td>
                        <?php if( $permission['PositionConfig']['all_trading_groups'] ){
                            echo __t('General.All');
                        } else if( !empty($permission['trading_groups']) ) {
                            foreach($permission['trading_groups'] as $trading_group ){
                                echo h($trading_groups[$trading_group['PositionConfigTradingGroup']['trading_group_id']]) . ' / ';
                            }
                        }?>
                    </td>

                    <td>
                        <?php if( $permission['PositionConfig']['all_distributor_networks'] ){
                            echo __t('General.All');
                        } else if( !empty($permission['distributor_networks']) ) {
                            foreach($permission['distributor_networks'] as $distributor_networks_id ){
                                echo h($trading_groups[$distributor_networks_id['PositionConfigDistributorNetwork']['distributor_network_id']]) . ' / ';
                            }
                        }?>
                    </td>
                    <td>
                        <?php if( $permission['PositionConfig']['all_aag_members'] ){
                            echo __t('General.All');
                        } else if( !empty($permission['aag_members']) ) {
                            foreach($permission['aag_members'] as $aag_member ){
                                echo h($aag_members[$aag_member['PositionConfigAagMember']['aag_member']]) . ' / ';
                            }
                        }?>
                    </td>
                    <td>
                        <?php if( $permission['PositionConfig']['all_customer_activities'] ){
                            echo __t('General.All');
                        } else if( !empty($permission['customer_activities']) ) {
                            foreach($permission['customer_activities'] as $customer_activity ){
                                echo h($customer_activities[$customer_activity['PositionConfigCustomerActivity']['customer_activity_id']]) . ' / ';
                            }
                        }?>
                    </td>
                    <td>
                        <?php if( $permission['PositionConfig']['all_profiles'] ){
                            echo __t('General.All');
                        } else if( !empty($permission['profiles']) ) {
                            foreach($permission['profiles'] as $profile ){
                                echo h($profiles[$profile['PositionConfigProfile']['profile_id']]) . ' / ';
                            }
                        }?>
                    </td>
                    <td>
                        <?php if( $permission['PositionConfig']['all_suppliers_categories'] ){
                            echo __t('General.All');
                        } else if( !empty($permission['supplier_categories']) ) {
                            foreach($permission['supplier_categories'] as $supplier_category ){
                                echo h($suppliers_categories[$supplier_category['PositionConfigSupplierCategory']['supplier_category_id']]) . ' / ';
                            }
                        }?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <?php echo $this->element('Comun/paginacion'); ?>
</div>
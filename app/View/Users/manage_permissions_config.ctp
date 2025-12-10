<?php 
echo $this->Html->script('permissions_group.js?v=' . Configure::read('VERSION_CACHE')); 
echo $this->Form->create('Permissions', array('class' => 'form-horizontal custom',));
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('User.Users'),
                array(
                    'controller' => 'users',
                    'action' => 'listing'
                )
            ),
            __t('User.Permission'),
        ));
        ?>
    </div>
    <div>
        <?php 
        echo $this->element(
            'Comun/form_actions',
            array(
                'url_cancel' => array(
                    'controller' => 'users',
                    'action' => 'listing',
                ),
            )
        );
        ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="aag-title">
        <?php echo __t('User.Permissions_list') .' / '. trim(h($user['User']['name']) . ' ' . h($user['User']['surname'])); ?>
    </div>
    <div class="o-auto p-top-1">
        <table class="table-tracking">
            <thead>
            <tr>
                <th><?php echo __t('General.Type'); ?></th>
                <th><?php echo __t('Network.Networks'); ?></th>
                <th><?php echo __t('General.Regions'); ?></th>
                <th><?php echo __t('TradingGroup.Trading_groups'); ?></th>
                <th><?php echo __t('Config.Group_permissions'); ?></th>
                <th class="ta-center"><?php echo __t('General.Actions'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            if(isset( $positions_config[0]['Config'] )){
            foreach( $positions_config[0]['Config'] as $config){ ?>
                <tr>
                    <td>
                    <?php echo $positions_config_types[$config['PositionConfig']['position_config_type_id']]; ?>
                    </td>
                    <td>
                        <?php if( $config['PositionConfig']['all_networks'] ){ ?>
                            <?php echo __t('General.All'); ?>
                        <?php }
                        else{
                        ?>
                            <?php foreach( $config['PositionConfig']['Networks'] as $network){ ?>
                                <div>
                                    <?php echo h($networks[$network['PositionConfigNetwork']['network_id']]); ?>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </td>
                    <td>
                        <?php if( $config['PositionConfig']['all_regions'] ){ ?>
                            <?php echo __t('General.All'); ?>
                        <?php }
                        else{
                        ?>
                        <?php foreach( $config['PositionConfig']['Regions'] as $region){ ?>
                            <div>
                                <?php echo h($regions[$region['PositionConfigRegion']['region_id']]); ?>
                            </div>
                        <?php } ?>
                        <?php } ?>
                    </td>
                    <td>
                        <?php if( $config['PositionConfig']['all_trading_groups'] ){ ?>
                            <?php echo __t('General.All'); ?>
                        <?php }
                        else{
                        ?>
                        <?php foreach( $config['PositionConfig']['TradingGroups'] as $trading_group){ ?>
                            <div>
                                <?php echo h($trading_groups[$trading_group['PositionConfigTradingGroup']['trading_group_id']]); ?>
                            </div>
                        <?php } ?>
                        <?php } ?>
                    </td>
                    <td>
                        <?php echo $group_permissions[$config['PositionConfig']['group_permission_id']]; ?>
                    </td>
                    <td class="ta-center">
                        <?php
                        echo $this->Html->link(
                            '<span class="aag-icon-ojo c-primary"></span>',
                            array(),
                            array(
                                'escape' => false,
                                'class' => 'view-group-permission-js',
                                'data-permission_url' => Router::url(array(
                                    'controller' => 'users',
                                    'action' => 'ajax_get_permission_by_group',
                                )),
                                'data-user_id' => $user['User']['id'],
                                'data-group_permission_id' => $config['PositionConfig']['group_permission_id'],
                                'title' => __t('User.View_permissions')
                            )
                        );
                        ?>
                    </td>
                </tr>
            <?php }
            } ?>
            </tbody>
        </table>
    </div>
    <br/>
    <div class="row d-none" id='permission-group'>
        <?php echo $this->element('../Users/Elements/matriz_permissions'); ?>
    </div>
</div>
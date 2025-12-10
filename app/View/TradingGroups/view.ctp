<?php echo $this->Html->script('trading-groups.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script')); ?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('TradingGroup.Trading_groups'),
                array(
                    'controller' => 'trading_groups',
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
            array(
                'class' => 'aag-button medium four',
                'title' => __t('General.Back')
            )
        );

        if( $this->Acceso->haveTradingGroupPermission( ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR , $trading_group['TradingGroup']['id'] ) ){
            echo $this->Html->link(
                __t('General.Edit'),
                array(
                    'controller' => 'trading_groups',
                    'action' => 'edit',
                    $trading_group['TradingGroup']['id'],
                ),
                array(
                    'escape' => false,
                    'title' => __t('General.Edit'),
                    'class' => 'aag-button medium'
                )
            );
        }
        ?>
    </div>
</div>

<div class="cnt-data aag-padding fg-0">
    <div class="aag-title m-bottom-1">
        <?php echo __t('TradingGroup.Trading_groups'); ?>
    </div>
    <div class="cnt-two-columns">
        <div class="cnt-form-inputs">
            <div>
                <strong class="d-block"><?php echo __t('TradingGroup.Name') ?> </strong>
                <?php echo h($trading_group['TradingGroup']['name']); ?>
            </div>
            <div>
                <strong class="d-block"><?php echo __t('General.Region') ?>: </strong>
                <?php echo h($aag_regions[$trading_group['TradingGroup']['aag_region_id']]); ?>
            </div>
            <div>
                <strong class="d-block"><?php echo __t('TradingGroup.Web') ?>: </strong>
                <a target="_blank" title="<?php echo h($trading_group['TradingGroup']['web']); ?>"
                    href="<?php echo h($trading_group['TradingGroup']['web']); ?>"><?php echo h($trading_group['TradingGroup']['web']); ?></a>
            </div>
            <div>
                <strong class="d-block"><?php echo __t('General.Type') ?>: </strong>
                <?php
                if ($trading_group['TradingGroup']['is_cv'] === null)
                {
                    echo __t('General.Lv_cv');
                }
                elseif ($trading_group['TradingGroup']['is_cv'] == ConstantsBooleans::NO)
                {
                    echo __t('General.LV');
                }
                elseif ($trading_group['TradingGroup']['is_cv'] == ConstantsBooleans::YES)
                {
                    echo __t('General.CV');
                }
                ?>
            </div>
            <div>
                <strong class="d-block"><?php echo __t('TradingGroup.Creation_date') ?>: </strong>
                <?php echo Fecha::toFormatoVistaFecha(h($trading_group['TradingGroup']['creation_date'])); ?>
            </div>
        </div>
        <div>
            <strong class="d-block"><?php echo __t('TradingGroup.Image') ?>: </strong>
            <img title="<?php echo h($trading_group['TradingGroup']['name']); ?>"
                    src="<?php echo FileManager::get_url(FilePaths::TRADING_GROUP_IMAGES_RELATIVE . $trading_group['TradingGroup']['image']); ?>"
                    class="img_table">
        </div>
    </div>
    <div class="aag-subtitle">
        <?php echo __t('Network.Networks') ?>
    </div>
    <div class="medium-12 columns background-color-primary p-0">
        <div class="columns medium-12 p-1">
            <div id="alert_trading_group"></div>
        </div>
        <?php if($this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CREATE_TRADING_GROUP)) { ?>
        <div class="columns medium-12 p-0">
            <div class="columns medium-8 m-top-1">
                <?php echo $this->Form->input(
                    'TradingGroup.network_id',
                    array(
                        'label' => false,
                        'class' => 'select2-multiple',
                        'type' => 'select',
                        'empty' => true,
                        'options' => $networks_availables,
                        'id' => 'selected_network'
                    )
                ); ?>
            </div>
            <div class="columns medium-4 ta-right cnt-buttons-v2">
                <?php echo $this->Html->link(
                    __t('Network.Add_network'),
                    array(),
                    array(
                        'escape' => false,
                        'class' => 'btn-add btn-guardar',
                        'title' => __t('Network.Add_network'),
                        'id' => 'btn_add_network',
                        'data-trading-group' => $trading_group['TradingGroup']['id'],
                        'data-url' => Router::url(array(
                            'controller' => 'trading_groups',
                            'action' => 'ajax_add_network',
                        )),
                    )
                ); ?>
            </div>
        </div>
        <?php } ?>
        <div class="columns medium-12 p-1">
            <div class="o-auto">
                <table class="table-tracking">
                    <thead>
                    <tr>
                        <th class="ta-center" width="165"><?php echo __t('Network.Image'); ?></th>
                        <th><?php echo __t('Network.Name'); ?></th>
                        <th><?php echo __t('Network.Web'); ?></th>
                        <th class='ta-center'><?php echo __t('Network.Type'); ?></th>
                        <?php if($this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CREATE_TRADING_GROUP)) { ?>
                        <th class="ta-center" width="60"><?php echo __t('General.Actions'); ?></th>
                        <?php } ?>
                    </tr>
                    </thead>
                    <tbody id="table_networks">
                    <?php
                    foreach ($related_networks as $related_network) {
                        ?>
                        <tr id="<?php echo $related_network['Network']['id'] ?>">
                            <td class="ta-center">
                                <img title="<?php echo $related_network['Network']['name']; ?>"
                                     src="<?php echo FileManager::get_url(FilePaths::NETWORKS_IMAGES_RELATIVE . $related_network['Network']['image']); ?>"
                                     class="img_table">
                            </td>
                            <td>
                                <?php echo h($related_network['Network']['name']); ?>
                            </td>
                            <td>
                                <?php echo h($related_network['Network']['web']); ?>
                            </td>
                            <td class='ta-center'>
                                <?php echo h($networks_types[$related_network['Network']['network_type']]); ?>
                            </td>
                            <?php if($this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CREATE_TRADING_GROUP)) { ?>
                            <td class="ta-center">
                                <?php
                                echo $this->Html->link(
                                    '<span class="ion-trash-b c-fallo"></span>',
                                    'javascript:;',
                                    array(
                                        'class' => 'delete-network-js',
                                        'data-confirmmsg' => __t('General.Delete_relation?'),
                                        'data-url' => Router::url(array(
                                            'controller' => 'trading_groups',
                                            'action' => 'ajax_delete_network',
                                            $trading_group['TradingGroup']['id'],
                                            $related_network['Network']['id']
                                        )),
                                        'data-id' => $related_network['Network']['id'],
                                        'data-name' => $related_network['Network']['name'],
                                        'data-yes' => __t('General.Yes'),
                                        'data-no' => __t('General.No'),
                                        'escape' => false,
                                        'title' => __t('General.Delete'),
                                    )
                                );
                                ?>
                            </td>
                            <?php } ?>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

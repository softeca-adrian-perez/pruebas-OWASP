<?php
echo $this->Html->script('/js/trading_groups_users_permissions.js?v='.Configure::read('VERSION_CACHE'), array('block' => 'script'));
?>
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
            __t('General.List'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back')?></a>
        <?php
        if ($this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::CREATE_TRADING_GROUP)) {
            echo $this->Html->link(
                __t('TradingGroup.New_trading_group'),
                array(
                    'controller' => 'trading_groups',
                    'action' => 'add',
                ),
                array('class' => 'aag-button medium green')
            );
        } ?>
    </div>
</div>
<div class="cnt-data p-vertical-1">
    <div class="aag-title cnt-data-element">
        <?php echo __t('TradingGroup.Trading_groups') ?>
    </div>
    <div class="o-auto">
        <table class="table-tracking tabla-responsive">
            <thead>
            <tr>
                <th><?php echo __t('TradingGroup.Image'); ?></th>
                <th><?php echo $this->Paginator->sort('TradingGroup.name', __t('TradingGroup.Name')); ?></th>
                <th><?php echo $this->Paginator->sort('TradingGroup.web', __t('TradingGroup.Web')); ?></th>
                <th class="ta-center"><?php echo __t('Distributor.Distributors'); ?></th>
                <?php if( CakeSession::read('Auth.User.role_id') == ConstantsRoles::ADMIN ){ ?>
                    <th class="ta-center">
                        <?php echo __t('General.Actions'); ?>
                    </th>
                <?php } ?>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($trading_groups as $trading_group)
            {
                ?>
                <tr>
                    <td>
                        <img title="<?php echo $trading_group['TradingGroup']['name']; ?>"
                                src="<?php echo FileManager::get_url(FilePaths::TRADING_GROUP_IMAGES_RELATIVE . $trading_group['TradingGroup']['image']); ?>"
                                class="img_table"
                                style="max-height:42px"
                                >
                    </td>
                    <td>
                        <?php echo $this->Html->link(
                            $trading_group['TradingGroup']['name'],
                            array(
                                'controller' => 'trading_groups',
                                'action' => 'view',
                                $trading_group['TradingGroup']['id']
                            ),
                            array(
                                'class' => 'c-primary'
                            )
                        ); ?>
                    </td>
                    <td class="c-primary">
                        <a target="_blank" title="<?php echo h($trading_group['TradingGroup']['web']); ?>" href="<?php echo h($trading_group['TradingGroup']['web']); ?>">
                            <?php echo h($trading_group['TradingGroup']['web']); ?>
                        </a>
                    </td>
                    <td class="ta-center">
                        <?php echo h($trading_group['TradingGroup']['distributors']); ?>
                    </td>
                    <?php if( CakeSession::read('Auth.User.role_id') == ConstantsRoles::ADMIN ){ ?>
                        <td class="ta-center ws-nowrap">
                            <?php
                            echo $this->Html->link(
                                '<span class="aag-icon-ojo cursor-pointer"></span>',
                                '#',
                                array(
                                    'escape' => false,
                                    'onclick' => "$('.modal-permissions').html('');$('#permission_list').prop('selectedIndex',0);$('#permission_list').select2()",
                                    'data-open' => 'ModalPermissions',
                                    'title' => __t('User.Permission'),
                                    'data-trading-group-id' => $trading_group['TradingGroup']['id'],
                                    'class' => 'view_trading_groups_permission'
                                )
                            );
                            if($this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::EDIT_TRADING_GROUP))
                            {
                                echo $this->Html->Link(
                                '<span class="aag-icon-papelera c-fallo"></span>',
                                array(),
                                array(
                                    'escape' => false,
                                    'title' => __t('General.Delete'),
                                    'class' => 'delete-js',
                                    'data-url' => Router::url(array(
                                        'controller' => 'trading_groups',
                                        'action' => 'ajax_delete',
                                        $trading_group['TradingGroup']['id'],
                                    )),
                                    'data-url_redirect' => Router::url(array(
                                        'controller' => 'trading_groups',
                                        'action' => 'home',
                                    )),
                                    'data-confirmmsg' => __t('TradingGroup.Delete_trading_group?'),
                                    'data-msg_correct' => __t('TradingGroup.Correct_deleted'),
                                    'data-msg_bad' => __t('Constants.Message_bad_deleted'),
                                    'style' => 'position: relative; top: -1px; padding-left: 2px;'
                                    )
                                );
                            }
                            ?>
                        </td >
                        <?php
                    }
                    ?>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </div>
    <?php echo $this->element('Comun/paginacion'); ?>
</div>
<?php echo $this->Form->hidden(
    'TradingGroupId',
    array(
        'id' => 'trading_group_id_click',
        'value' => null
    )
); ?>
<div style="display: none;" id="ModalPermissions" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
    <a class="close-modal" data-close aria-label="Close">&#215;</a>
    <?php echo $this->element('../TradingGroups/Elements/trading_groups_permissions_search'); ?>
</div>

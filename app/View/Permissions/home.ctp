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
            __t('General.Home'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back')?></a>
    </div>
</div>
<div class="cnt-data">
    <?php echo $this->element('../Permissions/Elements/search'); ?>
    <div class="o-auto">
        <table class="table-tracking">
            <thead>
                <tr>
                    <th><?php echo __t('Maintenance.Position_config_type'); ?></th>
                    <th><?php echo __t('GroupPermission.Group_permission'); ?></th>
                    <th><?php echo $this->Paginator->sort('Permission.name_en', __t('General.Name_en')); ?></th>
                    <th><?php echo $this->Paginator->sort('Permission.name_fr', __t('General.Name_fr')); ?></th>
                    <th><?php echo $this->Paginator->sort('Permission.name_de', __t('General.Name_de')); ?></th>
                    <th class="ta-center"><?php echo __t('General.Actions'); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ( $permissions as $permission ){ ?>
                <tr>
                    <td>
                        <?php echo ($permission['Permission']['position_config_type_id'] != null) ? $positions_config_types[$permission['Permission']['position_config_type_id']] : ''?>
                    </td>
                    <td>
                        <?php echo ($permission['Permission']['grouping_permission_id'] != null) ? $group_permissions[$permission['Permission']['grouping_permission_id']] : ''?>
                    </td>
                    <td>
                        <?php echo h($permission['Permission']['name_en']); ?>
                    </td>
                    <td>
                        <?php echo h($permission['Permission']['name_fr']); ?>
                    </td>
                    <td>
                        <?php echo h($permission['Permission']['name_de']); ?>
                    </td>
                    <td class="ta-center">
                        <?php echo $this->Html->link(
                            '<span class="aag-icon-ojo c-primary"></span>',
                            array(
                                'controller' => 'permissions',
                                'action' => 'view',
                                $permission['Permission']['id'],
                            ),
                            array(
                                'escape' => false,
                                'title' => __t('User.View_permissions')
                            )
                        ); ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <?php echo $this->element('Comun/paginacion'); ?>
</div>
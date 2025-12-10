<?php echo $this->Html->script('permissions_group.js?v=' . Configure::read('VERSION_CACHE')); ?>
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
                __t('General.Group_of_permissions'),
                array(
                    'controller' => 'groups_permissions',
                    'action' => 'home'
                )
            ),
            __t('General.Home'),
        ));
        ?>
    </div>
    <div>
    	<a class="aag-button medium two go-back-js"><?php echo __t('General.Back')?></a>
        <?php
        if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
            // echo $this->Html->link(__t('Maintenance.Maintenance_home'), array('controller' => 'maintenance', 'action' => 'home'));
            echo $this->Html->link(
                __t('General.New_group_of_permissions'),//New group permission
                array(
                    'controller' => 'groups_permissions',
                    'action' => 'add',
                ),
                array('class' => 'aag-button medium green')
            );
        } ?>
    </div>
</div>
<div class="cnt-data">
    <?php
    echo $this->element('GroupsPermissions'.DS.'search');
    echo $this->Session->flash();
    ?>
    <div class="o-auto p-top-1">
        <table class="table-tracking table-position-maintenance">
            <thead>
            <tr>
                <th><?php echo $this->Paginator->sort('GroupPermission.name_' . __l(), __t('General.Name')); ?></th>
                <th><?php echo __t('General.Type'); ?></th>
                <?php if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) { ?>
                    <th class="ta-center"><?php echo __t('General.Actions'); ?></th>
                <?php } ?>
            </tr>
            </thead>
            <tbody>
            <?php foreach($group_permissions as $group_permission){ ?>
                <tr>
                    <td>
                        <?php echo $this->Html->link(
                            $group_permission['GroupPermission']['name'.__s()],
                            array(
                                'controller' => 'groups_permissions',
                                'action' => 'edit',
                                $group_permission['GroupPermission']['id']
                            ),
                            array(
                                'class' => 'c-primary'
                            )
                        ); ?>
                    </td>
                    <td>
                        <?php
                        $type = $group_permission['GroupPermission']['position_config_type_id'];
                        echo h($config_types[$type]); ?>
                    </td>
                    <?php if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) { ?>
                    <td class="ta-right ws-nowrap">
                        <?php
                        echo $this->Html->link(
                            '<span class="aag-icon-editar c-primary"></span>',
                            array(
                                'controller' => 'groups_permissions',
                                'action' => 'edit',
                                $group_permission['GroupPermission']['id'],
                            ),
                            array(
                                'escape' => false,
                                'title' => __t('General.Edit'),
                            )
                        );
                        ?>
                        <?php
                        echo $this->Html->link(
                            '<span class="icon-clipboard-pencil c-exito"></span>',
                            array(
                                'controller' => 'groups_permissions',
                                'action' => 'add',
                                $group_permission['GroupPermission']['id'],
                            ),
                            array(
                                'escape' => false,
                                'title' => __t('General.Copy_group'),
                            )
                        );
                        ?>
                        <?php
                        echo $this->Html->link(
                            '<span class="aag-icon-papelera c-fallo"></span>',
                            array(),
                            array(
                                'escape' => false,
                                'class' => 'delete_group_permission-js',
                                'title' => __t('General.Delete'),
                                'data-url_delete' => Router::url(array(
                                    'controller' => 'groups_permissions',
                                    'action' => 'delete_group_permissions',
                                )),
                                'data-confirmmsg' => __t('User.Delete_permissions_groups?'),
                                'data-group_permission_id' => $group_permission['GroupPermission']['id'],
                                'data-yes' => __t('General.Yes'),
                                'data-no' => __t('General.No')
                            )
                        );
                        ?>
                    </td>
                    <?php } ?>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <?php echo $this->element('Comun/paginacion'); ?>
</div>
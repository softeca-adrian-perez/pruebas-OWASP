<?php
$action = $this->request->action;
echo $this->Html->script('permissions_group.js?v=' . Configure::read('VERSION_CACHE'));
echo $this->Html->script('select2.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Form->create(
    'GroupPermission',
    array(
        'id' => 'GroupPermissionForm',
        'enctype' => 'multipart/form-data',
        'url' =>  $url
    )
);
echo $this->Form->hidden('GroupPermission.id');
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
                    __t('General.Group_of_permissions'),
                    array(
                        'controller' => 'groups_permissions',
                        'action' => 'home'
                    )
                ),
                __t('General.New'),
            ));
        } else {
            echo $this->Html->breadcrumb(
                array(
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
                    __t('General.Edit')
                )
            );
        }
        ?>
    </div>
    <div>
        <div class="cnt-buttons-v2" id="cnt-form_actions-js">
            <?php echo $this->element('Comun/form_actions'); ?>
        </div>
        <div class="d-none ta-right" id="btn_save_from_position-js">
            <?php
            echo $this->Html->link(
                __t('General.Save'),
                array(),
                array(
                    'escape' => false,
                    'class' => 'aag-button medium green',
                    'id' => 'button_new_group_permission_save-js',
                    'data-element_id' => 'name_en',
                    'data-url_get_last_group_permission' => Router::url(array(
                        'controller' => 'groups_permissions',
                        'action' => 'ajax_get_last_group_permission_id'
                    ))
                )
            );
            ?>
        </div>
    </div>
</div>
<div class="cnt-data p-bottom-1">
    <div class="cnt-data-element">
        <div class="aag-title p-vertical-1">
            <?php echo __t('General.List_of_permissions'); ?>
        </div>
        <div class="cnt-form-inputs m-bottom-1 p-bottom-1">
            <?php
            echo $this->Form->input(
                'GroupPermission.name_en',
                array(
                    'id' => 'name_en-js',
                    'type' => 'text',
                    'required' => true,
                    'label' => __t('General.Name_en'),
                )
            );
            echo $this->Form->input(
                'GroupPermission.name_fr',
                array(
                    'id' => 'name_fr-js',
                    'type' => 'text',
                    'required' => true,
                    'label' => __t('General.Name_fr'),
                )
            );
            echo $this->Form->input(
                'GroupPermission.name_de',
                array(
                    'id' => 'name_de-js',
                    'type' => 'text',
                    'required' => true,
                    'label' => __t('General.Name_de'),
                )
            );
            echo $this->Form->input(
                'GroupPermission.position_config_type_id',
                array(
                    'label' => __t('Garage.Type'),
                    'class' => 'select2-multiple',
                    'id' => 'group_permission_type-js',
                    'type' => 'select',
                    'multiple' => false,
                    'empty' => false,
                    'default' => 3,
                    'options' => $position_config_types,
                    'data-url' => Router::url(array(
                        'controller' => 'groups_permissions',
                        'action' => 'ajax_generate_group_permissions',
                    )),
                    'data-confirmmsg' => __t('User.Change_group_permission_type?'),
                    'data-confirmmsg_text' => __t('User.Change_group_permission_list'),
                    'data-yes' => __t('General.Yes'),
                    'data-no' => __t('General.No')
                )
            );
            echo $this->Form->hidden('GroupPermission.position_config_type_id_position', array('id' => 'group_permission_type_hidden-js', 'value' => ''));
            ?>
        </div>
    </div>
    <?php echo $this->element('../GroupsPermissions/Elements/permissions_table'); ?>
</div>
<?php echo $this->Form->end(); ?>
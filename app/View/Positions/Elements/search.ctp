<?php
$controller = $this->request->controller;
$action = $this->request->action;
echo $this->Form->create(
    'Search',
    array(
        'class' => 'cnt-form-search',
        'type' => 'get',
        'url' => array(
            'controller' => 'positions',
            'action' => 'home',

        ),
    )
);
?>
    <div class="cnt-form-search-title">
        <?php echo __t('Position.Positions'); ?>
    </div>
    <div class="cnt-form-inputs">
        <?php
        echo $this->Form->input(
            'name',
            array(
                'class' => 'clear_field',
                'type' => 'text',
                'required' => true,
                'label' => __t('Position.Position_name'),
            )
        );
        echo $this->Form->input(
            'role_id',
            array(
                'label' => __t('Role.Role'),
                'type' => 'select',
                'class' => 'select2-multiple clear_field',
                'options' => $roles,
                'required' => false,
                'empty' => true,
            )
        );
        echo $this->Form->input(
            'group_permission_id',
            array(
                'label' => __t('GroupPermission.Group_permission'),
                'type' => 'select',
                'class' => 'select2-multiple clear_field',
                'options' => $group_permissions,
                'required' => false,
                'empty' => true,
            )
        );
        ?>
    </div>
    <div class="cnt-form-search-buttons">
        <?php
        echo $this->Form->button(
            __t('General.Search'),
            array(
                'type' => 'submit',
                'class' => 'aag-button medium'
            )
        );
        echo $this->Form->button(
            "<span class='aag-icon-escoba'></span>",
            array(
                'id' => 'clear_field',
                'class' => 'aag-button medium four outlined',
                'escape' => false,
                'title' => __t('General.Clean_search')
            )
        );
        ?>
    </div>
<?php echo $this->Form->end(); ?>
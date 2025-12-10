<?php
echo $this->Form->create(
    'Search',
    array(
        'class' => 'cnt-form-search',
        'type' => 'get'
    )
);
    ?>
    <div class="cnt-form-search-title">
        <?php echo __t('User.Permission') . ' ' . h($permission['Permission']['name' . __s()]); ?>
    </div>
    <div class="cnt-form-inputs">
        <?php
        echo $this->Form->input(
            'user_id',
            array(
                'label' => __t('User.User'),
                'type' => 'select',
                'class' => 'select2-multiple clear_field',
                'options' => $users,
                'empty' => true,
                'required' => true
            )
        );
        echo $this->Form->input(
            'role_id',
            array(
                'label' => __t('User.Role'),
                'type' => 'select',
                'class' => 'select2-multiple clear_field',
                'options' => $roles,
                'empty' => true,
                'required' => true
            )
        );
        echo $this->Form->input(
            'position_id',
            array(
                'label' => __t('Maintenance.Positions'),
                'type' => 'select',
                'class' => 'select2-multiple clear_field',
                'options' => $positions,
                'empty' => true,
                'required' => true
            )
        );
        echo $this->Form->input(
            'group_permission_id',
            array(
                'label' => __t('GroupPermission.Group_permission'),
                'type' => 'select',
                'class' => 'select2-multiple clear_field',
                'options' => $group_permissions,
                'empty' => true,
                'required' => true
            )
        );
        ?>
    </div>
    <div class="cnt-form-search-buttons">
        <button type="submit" class="aag-button medium">
            <?php echo __t('General.Search'); ?>
        </button>
        <?php
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
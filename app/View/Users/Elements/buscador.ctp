<?php
echo $this->Html->script('/js/conferences_delegates.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('/js/dynamic_lists.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Form->create(
    'Buscador',
    array(
        'class' => 'cnt-form-search',
        'type' => 'get',
        'url' => array(
            'controller' => 'users',
            'action' => 'listing'
        ),
    )
);
?>
    <div class="cnt-form-search-title">
        <?php echo __t('User.Users_list'); ?>
    </div>
    <div class="cnt-form-inputs">
        <?php
        echo $this->Form->input(
            'name',
            array(
                'class' => 'clear_field',
                'type' => 'text',
                'required' => true,
                'label' => __t('User.Name'),
            )
        );
        echo $this->Form->input(
            'surname',
            array(
                'class' => 'clear_field',
                'type' => 'text',
                'required' => true,
                'label' => __t('User.Surname'),
            )
        );
        echo $this->Form->input(
            'username',
            array(
                'class' => 'clear_field',
                'type' => 'text',
                'required' => true,
                'label' => __t('User.Username'),
            )
        );
        echo $this->Form->input(
            'position_id',
            array(
                'label' => __t('General.Position'),
                'class' => 'select2-multiple clear_field',
                'type' => 'select',
                'options' => $positions,
                'empty' => true,
            )
        );
        echo $this->Form->input(
            'garage_id',
            array(
                'label' => __t('Training.Garage_name'),
                'class' => 'clear_field select2Dinamico_garage cargar_garages',
                'type' => 'select',
                'multiple' => false,
                'options' => isset($array_garage_name) ? $array_garage_name : array(),
                'empty' => true,
            )
        );
        echo $this->Form->input(
            'role_id',
            array(
                'label' => __t('General.Role'),
                'class' => 'select2-multiple clear_field',
                'type' => 'select',
                'options' => $roles,
                'empty' => true,
            )
        );
        echo $this->Form->input(
            'active',
            array(
                'label' => __t('User.Active'),
                'type' => 'select',
                'class' => 'select2-multiple clear_field',
                'options' => $active,
                'empty' => true,
                'id' => 'read',
                'required' => true,
                //'hiddenField' => false,
            )
        );
        echo $this->Form->input(
            'contact_id',
            array(
                'label' => __t('User.Email'),
                'class' => 'clear_field dynamicSelect2_contacts_emails contact-id-js',
                'type' => 'select',
                'multiple' => false,
                'data-selected_contacts_emails' => isset($contacts_emails) ? $contacts_emails : array(),
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

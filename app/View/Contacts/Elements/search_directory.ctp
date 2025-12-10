<?php
$controller = $this->request->controller;
$action = $this->request->action;
echo $this->Form->create(
    'Search',
    array(
        'class' => 'cnt-form-search',
        'type' => 'get',
        'url' => array(
            'controller' => 'contacts',
            'action' => 'directory'
        )
    )
);
?>
    <div class="cnt-form-search-title">
        <?php echo __t('Contact.Directory'); ?>
    </div>
    <div class="cnt-form-inputs">
        <?php
        echo $this->Form->input(
            'first_name',
            array(
                'label' => false,
                'class' => 'clear_field',
                'id' => 'first_name-js',
                'type' => 'text',
                'required' => true,
                'label' => __t('Contact.First_name'),
            )
        );
        echo $this->Form->input(
            'last_name',
            array(
                'class' => 'clear_field',
                'id' => 'last_name-js',
                'type' => 'text',
                'required' => true,
                'label' => __t('Contact.Last_name'),
            )
        );
        echo $this->Form->input(
            'position_id',
            array(
                'label' => __t('Contact.Position'),
                'type' => 'select',
                'class' => 'select2-multiple clear_field',
                'options' => $positions_list_search,
                'empty' => true,
                'id' => 'associations'
            )
        );
        echo $this->Form->input(
            'email',
            array(
                'class' => 'clear_field',
                'id' => 'email-js',
                'type' => 'text',
                'required' => true,
                'label' => __t('Contact.Email'),
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
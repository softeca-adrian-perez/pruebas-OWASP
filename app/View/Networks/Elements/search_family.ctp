<?php
$controller = $this->request->controller;
$action = $this->request->action;
echo $this->Form->create(
    'Search',
    array(
        'class' => 'cnt-form-search m-0-i buscador-js',
        'type' => 'get',
        'url' => array(
            'controller' => 'networks',
            'action' => 'families_configuration',
            $network_id
        ),
    )
);
?>
    <div class="cnt-form-inputs">
        <?php
        echo $this->Form->input(
            'name_'.__l(),
            array(
                'class' => 'clear_field',
                'type' => 'text',
                'required' => true,
                'label' => __t('Network.Name'),
            )
        );
        ?>
    </div>
    <div class="cnt-form-search-buttons p-0">
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
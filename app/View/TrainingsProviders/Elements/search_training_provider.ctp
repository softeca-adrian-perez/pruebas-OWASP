<?php
$controller = $this->request->controller;
$action = $this->request->action;
echo $this->Form->create(
    'Search',
    array(
        'class' => 'cnt-form-search buscador-js',
        'style' => 'margin-top: 0;',
        'type' => 'get',
        'url' => array(
            'controller' => 'trainings_providers',
            'action' => 'home'
        )
    )
);
    ?>
    <div class="cnt-form-search-title">
        <?php echo __t('Training.Trainings_providers'); ?>
    </div>
    <div class="cnt-form-inputs">
        <?php
        echo $this->Form->input(
            'TrainingProvider_name',
            array(
                'label' => __t('Training.Training_provider'),
                'type' => 'select',
                'class' => 'select2-multiple clear_field',
                'options' => $training_provider_list,
                'empty' => true,
                'required' => true,
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
<?php
$controller = $this->request->controller;
$action = $this->request->action;
echo $this->Form->create(
    'Search',
    array(
        'class' => 'cnt-form-search buscador-js',
        'type' => 'get',
        'url' => array(
            'controller' => 'conferences',
            'action' => 'home'
        ),
    )
);
?>
    <div class="cnt-form-search-title">
        <?php echo __t('Conference.Conferences'); ?>
    </div>
    <div class="cnt-form-inputs">
        <?php
        echo $this->Form->input(
            'name',
            array(
                'class' => 'clear_field',
                'type' => 'text',
                'required' => true,
                'label' => __t('Conference.Name'),
            )
        );
        echo $this->Form->input(
            'start_date',
            array(
                'class' => 'fecha-js from-js clear_field',
                'type' => 'text',
                'required' => true,
                'div' => array(
                    'class' => 'datepicker datepicker-label-block',
                ),
                'label' => __t('Conference.Start_date'),
            )
        );
        echo $this->Form->input(
            'duration',
            array(
                'class' => 'clear_field',
                'type' => 'text',
                'required' => true,
                'label' => __t('Conference.Duration'),
            )
        );
        echo $this->Form->input(
            'venue_id',
            array(
                'label' => __t('Conference.Venue'),
                'type' => 'select',
                'class' => 'select2-multiple clear_field',
                'options' => $venues,
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
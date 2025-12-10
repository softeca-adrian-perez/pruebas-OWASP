<?php
echo $this->Html->script('garages.js?v='.Configure::read('VERSION_CACHE'), array('block' => 'script'));


$controller = $this->request->controller;
$action = $this->request->action;

echo $this->Form->create(
    'Search',
    array(
        'class' => 'cnt-form-search m-0-i buscador-js',
        'type' => 'get',
        'url' => array(
            'controller' => 'garages',
            'action' => 'add_admin',
            $garage_id
        ),
    )
);
?>

    <div class="cnt-form-search-title">
        <?php echo __t('General.Search')?>
    </div>
    <div class="cnt-form-inputs">
        <?php 
        echo $this->Form->input(
            'table',
            array(
                'class' => 'clear_field',
                'type' => 'text',
                'required' => true,
                'label' => __t('Logs.Table'),
            )
        ); 
        echo $this->Form->input(
            'field',
            array(
                'class' => 'clear_field',
                'type' => 'text',
                'required' => true,
                'label' => __t('Logs.Field'),
            )
        );
        echo $this->Form->input(
            'user',
            array(
                'class' => 'clear_field',
                'type' => 'text',
                'required' => true,
                'label' => __t('Logs.User'),
            )
        ); 
        echo $this->Form->input(
            'old_value',
            array(
                'class' => 'clear_field',
                'type' => 'text',
                'required' => true,
                'label' => __t('Logs.Old_value'),
            )
        ); 
        echo $this->Form->input(
            'new_value',
            array(
                'class' => 'clear_field',
                'type' => 'text',
                'required' => true,
                'label' => __t('Logs.New_value'),
            )
        ); 
        echo $this->Form->input(
            'from',
            array(
                'id' => 'call_start_date',
                'class' => 'fecha-js from-js clear_field',
                'type' => 'text',
                'data-to' => '#call_end_date',
                'required' => true,
                'div' => array(
                    'class' => 'datepicker datepicker-label-block',
                ),
                'label' => __t('General.From'),
            )
        ); 
        echo $this->Form->input(
            'to',
            array(
                'id' => 'call_end_date',
                'class' => 'fecha-js to-js clear_field',
                'data-from' => '#call_start_date',
                'type' => 'text',
                'required' => true,
                'div' => array(
                    'class' => 'datepicker datepicker-label-block',
                ),
                'label' => __t('General.To'),
            )
        ); ?>
    </div>
    <div class="cnt-form-search-buttons">
        <?php
        echo $this->Form->button(
            __t('General.Search'),
            array(
                'type' => 'submit',
                'class' => 'aag-button medium',
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

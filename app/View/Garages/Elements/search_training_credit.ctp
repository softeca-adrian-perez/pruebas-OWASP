<?php
$controller = $this->request->controller;
$action = $this->request->action;

echo $this->Form->create(
    'Search',
    array(
        'class' => 'cnt-form-search buscador-js',
        'type' => 'get',
        'url' => array(
            'controller' => 'garages',
            'action' => 'training_credits',
            $garage_id
        ),
    )
);
?>
    <div class="cnt-form-search-title">
        <?php echo __t('Training.Trainings_credits'); ?>
    </div>
    <div class="cnt-form-inputs">
        <?php 
        
        echo $this->Form->input(
            'Network_name',
            array(
                'label' => __t('Training.Network_name'),
                'type' => 'select',
                'class' => 'select2-multiple clear_field',
                'options' => $network_list,
                'empty' => true,
                'required' => true,
            )
        );
        echo $this->Form->input(
            'TrainingCreditMovement_date_from',
            array(
                'class' => 'fecha-js to-js clear_field',
                'type' => 'text',
                'required' => true,
                'div' => array(
                    'class' => 'datepicker datepicker-label-block',
                ),
                'label' => __t('Training.Date_from'),
            )
        ); 
        echo $this->Form->input(
            'TrainingCreditMovement_date_to',
            array(
                'class' => 'fecha-js to-js clear_field',
                'type' => 'text',
                'required' => true,
                'div' => array(
                    'class' => 'datepicker datepicker-label-block',
                ),
                'label' => __t('Training.Date_to'),
            )
        );
        echo $this->Form->input(
            'TrainingAllowances_complete_date',
            array(
                'label' => __t('Training.Training_allowance'),
                'type' => 'select',
                'class' => 'select2-multiple clear_field',
                'options' => $trainings_allowances,
                'empty' => true,
                'required' => true,
            )
        );
        ?>
        <div class="cnt-check-visit cnt-form-inputs-max-width">
            <label class="center-check ">
                <?php echo __t('Allowance.Actual_allowance'); ?>
                <div class="aag-switch round small">
                    <?php echo $this->Form->input('is_actual', array('id'=> 'activeInput-8', 'label' => false, 'div' => false, 'type' => 'checkbox', 'class' => 'clear_field', 'checked' => $is_actual_checked)); ?>
                    <label for="activeInput-8"></label>
                </div>
            </label>
        </div>
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
<br>
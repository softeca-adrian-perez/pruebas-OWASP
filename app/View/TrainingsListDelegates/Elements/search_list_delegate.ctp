<?php
echo $this->Html->script('training_course_search.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('dynamic_lists.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
$controller = $this->request->controller;
$action = $this->request->action;

echo $this->Form->create(
    'Search',
    array(
        'class' => 'cnt-form-search buscador-js',
        'type' => 'get',
        'style' => 'margin: 0px',
        'url' => array(
            'controller' => 'trainings_list_delegates',
            'action' => 'home',
        ),
    )
);
?>
<div class="cnt-form-search-title">
    <?php echo __t('General.Search'); ?>
</div>
<div class="cnt-form-inputs">
    <?php
    echo $this->Form->input(
        'Delegate_name',
        array(
            'id' => 'name-delegates',
            'label' => __t('Training.Delegate_name'),
            'type' => 'text',
            'class' => 'search_ajax clear_field',
            'data-url' => Router::url(
                array(
                    'controller' => 'trainings_list_delegates',
                    'action' => 'ajax_search_home',
                    ConstantsTypeSearch::TRAINING_LIST_DELEGATES
                )
            ),
            'required' => true,
        )
    );
    echo $this->Form->input(
        'Network_name',
        array(
            'id' => 'network_name',
            'label' => __t('Training.Network_name'),
            'type' => 'select',
            'class' => 'select2-multiple clear_field',
            'options' => $network_list,
            'empty' => true,
            'required' => false,
        )
    );
    echo $this->Form->input(
        'Garage_name',
        array(
            'id' => 'garage_name',
            'label' => __t('Training.Garage_name'),
            'class' => 'clear_field dynamicSelect2_garages_live',
            'type' => 'select',
            'multiple' => false,
            'options' => isset($array_garage_name) ? $array_garage_name : array(),
            'empty' => true,
        )
    );
    echo $this->Form->input(
        'TrainingCourse_name',
        array(
            'id' => 'training_course_name',
            'label' => __t('Training.Course_name'),
            'type' => 'select',
            'class' => 'select2-multiple clear_field',
            'options' => $course_list,
            'empty' => true,
            'required' => true,
        )
    );
    echo $this->Form->input(
        'TrainingPlannedCourse_date_from',
        array(
            'id' => 'training_planned_course_date_from',
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
        'TrainingPlannedCourse_date_to',
        array(
            'id' => 'training_planned_course_date_to',
            'label' => __t('Training.Date_to'),
            'class' => 'fecha-js to-js clear_field',
            'type' => 'text',
            'required' => true,
            'div' => array(
                'class' => 'datepicker datepicker-label-block',
            ),
        )
    );
    echo $this->Form->input(
        'Distributor_account_number',
        array(
            'id' => 'distributor_account_number',
            'label' => __t('Distributor.Account_number'),
            'class' => 'clear_field',
            'type' => 'text',
            'required' => true,
        )
    );
    echo $this->Form->input(
        'Venue_name',
        array(
            'id' => 'venue_name',
            'label' => __t('Delegate.Venue_name'),
            'class' => 'clear_field dynamicSelect2_venues',
            'type' => 'select',
            'multiple' => false,
            'options' => isset($array_venue_name) ? $array_venue_name : array(),
            'empty' => true,
        )
    ); ?>
    <div class="p-top-1">
        <?php echo $this->Form->input(
            'TrainingCourse_price_credit',
            array(
                'id' => 'training_course_price_credit',
                'label' => __t('Training.Credits'),
                'class' => 'clear_field',
                'type' => 'range',
                'required' => true,
                'min' => 0,
                'max' => $max_value_price[0]['max_price_credit'],
                'value' => isset($price_credit_search) ? $price_credit_search : $max_value_price[0]['max_price_credit'],
            )
        ); ?>
        <span id="rangeValue"></span>
    </div>
    <?php echo $this->Form->input(
        'TrainingDelegate_credit_taken',
        array(
            'id' => 'training_delegate_credit_taken',
            'label' => __t('Training.Credits_taken'),
            'type' => 'select',
            'class' => 'select2-multiple clear_field',
            'options' => $credit_taken,
            'empty' => true,
            'required' => true,
        )
    );
    ?>
    <?php echo $this->Form->input(
        'Delegate_invoice_number',
        array(
            'id' => 'delegate_invoice_number',
            'label' => __t('Training.Invoice_number'),
            'class' => 'clear_field',
            'type' => 'number',
            'required' => true,
        )
    );
    ?>
    <?php echo $this->Form->input(
        'Delegate_order_number',
        array(
            'id' => 'delegate_order_number',
            'label' => __t('Training.Purchase_order_number'),
            'class' => 'clear_field',
            'type' => 'text',
            'required' => true,
        )
    );
    ?>
    <?php echo $this->Form->input(
        'Provider_name',
        array(
            'id' => 'provider_name',
            'label' => __t('Training.Training_provider'),
            'type' => 'select',
            'class' => 'select2-multiple clear_field',
            'options' => $training_provider_list,
            'empty' => true,
            'required' => true,
        )
    );
    echo $this->Form->input(
        'Delegate_cancelled',
        array(
            'id' => 'delegate_cancelled',
            'label' => __t('CRM.Cancelled'),
            'type' => 'select',
            'class' => 'select2-multiple clear_field',
            'options' => $cancelled,
            'empty' => true,
            'required' => true,
        )
    );
    echo $this->Form->input(
        'Delegate_reason_cancelled_id',
        array(
            'id' => 'delegate_reason_cancelled_id',
            'label' => __t('General.Cancelled_reason'),
            'type' => 'select',
            'class' => 'select2-multiple clear_field',
            'options' => $reason_cancelled_list,
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
            'class' => 'aag-button medium',
        )
    );
    echo $this->Form->button(
        "<span class='aag-icon-escoba'></span>",
        array(
            'id' => 'clear_field',
            'class' => 'aag-button medium four outlined ',
            'escape' => false,
            'title' => __t('General.Clean_search')
        )
    );
    ?>
</div>
<?php echo $this->Form->end(); ?>
<br>
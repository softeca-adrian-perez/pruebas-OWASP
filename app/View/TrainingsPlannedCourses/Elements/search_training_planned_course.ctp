<?php
echo $this->Html->script('training_planned_course_search.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('dynamic_lists.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
$controller = $this->request->controller;
$action = $this->request->action;
echo $this->Form->create(
    'Search',
    array(
        'class' => 'cnt-form-search buscador-js',
        'style' => 'margin-top: 0;',
        'type' => 'get',
        'url' => array(
            'controller' => 'trainings_planned_courses',
            'action' => 'home'
        ),
    )
);
    ?>
    <div class="cnt-form-search-title">
        <?php echo __t('Training.Planned_courses'); ?>
    </div>
    <div class="cnt-form-inputs">
        <?php
        echo $this->Form->input(
            'TrainingCourse_name',
            array(
                'type' => 'select',
                'class' => 'select2-multiple clear_field',
                'options' => $course_list,
                'empty' => true,
                'required' => true,
                'label' => __t('Training.Course_name'),
            )
        );
        echo $this->Form->input(
            'CourseType_name',
            array(
                'label' => __t('Training.Course_type'),
                'type' => 'select',
                'class' => 'select2-multiple clear_field',
                'options' => $course_type_list,
                'empty' => true,
                'required' => true,
            )
        );
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
        echo $this->Form->input(
            'TrainingPlannedCourse_date_from',
            array(
                'class' => 'fecha-js to-js clear_field',
                'type' => 'text',
                'required' => true,
                'div' => array(
                    'class' => 'datepicker datepicker-label-block',
                ),
                'label' => __t('Training.Date_from'),
                'value' => $date_from_today,
            )
        );
        echo $this->Form->input(
            'TrainingPlannedCourse_date_to',
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
            'TrainingPlannedCourse_duration',
            array(
                'class' => 'clear_field',
                'type' => 'number',
                'required' => true,
                'label' => __t('Training.Duration'),
            )
        );
        echo $this->Form->input(
            'Venue_name',
            array(
                'label' => __t('Delegate.Venue_name'),
                'class' => 'clear_field dynamicSelect2_venues',
                'type' => 'select',
                'multiple' => false,
                'options' => isset($array_venue_name) ? $array_venue_name : array(),
                'empty' => true,
            )
        );
        echo $this->Form->input(
            'TrainingPlannedCourse_status',
            array(
                'label' => __t('Training.Status'),
                'type' => 'select',
                'class' => 'select2-multiple clear_field',
                'options' => $status_list,
                'empty' => true,
                'required' => true,
            )
        );
        echo $this->Form->input(
            'TrainingPlannedCourse_availability',
            array(
                'label' => __t('Training.Availability'),
                'type' => 'select',
                'class' => 'select2-multiple clear_field',
                'options' => $availability_list,
                'empty' => true,
                'required' => true,
            )
        );
        echo $this->Form->input(
            'TrainingPlannedCourse_invoice_number',
            array(
                'class' => 'clear_field',
                'type' => 'text',
                'required' => true,
                'label' => __t('Training.Invoice_number'),
            )
        );
		echo $this->Form->input(
            'TrainingPlannedCourse_sales_area_id',
            array(
                'label' => __t('Garage.Sales_area'),
                'type' => 'select',
                'class' => 'select2-multiple clear_field',
                'options' => $sales_area,
                'empty' => true,
                'id' => 'sales-area-id',
                'multiple' => true
            )
        );
        ?>
        <div class="p-top-1">
            <?php echo $this->Form->input(
                'TrainingCourse_price_credit',
                array(
                    'class' => 'clear_field',
                    'type' => 'range',
                    'required' => true,
                    'label' => __t('Training.Credits'),
                    'id' => 'input_price_credit',
                    'min' => 0,
                    'max' => $max_value_price[0]['max_price_credit'],
                    'value' => isset($price_credit_search) ? $price_credit_search : $max_value_price[0]['max_price_credit'],
                )
            ); ?>
            <span id="rangeValue"></span>
        </div>
        <div class="p-top-1">
            <?php echo $this->Form->input(
                'TrainingCourse_cost_training',
                array(
                    'class' => 'clear_field',
                    'type' => 'range',
                    'required' => true,
                    'label' => __t('Training.Cost_of_course'),
                    'id' => 'input_cost_training',
                    'min' => 0,
                    'max' => $max_cost_training[0]['max_cost_training'],
                    'value' => isset($price_cost_training) ? $price_cost_training : $max_cost_training[0]['max_cost_training'],
                )
            ); ?>
            <span id="rangeValue2"></span>
        </div>
        <div class="cnt-check-visit cnt-form-inputs-max-width">
            <label class="center-check">
                <?php echo __t('Training.Online'); ?>
                <div class="aag-switch round small">
                    <?php echo $this->Form->input('is_online', array('id'=> 'activeInput-9', 'label' => false, 'div' => false, 'type' => 'checkbox', 'class' => 'clear_field', 'checked' => $is_checked_online)); ?>
                    <label for="activeInput-9"></label>
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

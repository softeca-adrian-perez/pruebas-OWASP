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
            'controller' => 'trainings_delegates',
            'action' => 'home',
            $training_planned_course_id
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
        'TrainingCourse_name',
        array(
            'label' => __t('Training.Course_name'),
            'type' => 'select',
            'class' => 'select2-multiple clear_field',
            'options' => $course_list,
            'empty' => true,
            'required' => true,
            'value' => $training_course_id,
            'disabled' => true,
        )
    );
    echo $this->Form->input(
        'Garage_name',
        array(
            'label' => __t('Training.Garage_name'),
            'class' => 'clear_field dynamicSelect2_garages_live',
            'type' => 'select',
            'multiple' => false,
            'options' => isset($array_garage_name) ? $array_garage_name : array(),
            'empty' => true,
        )
    );
    echo $this->Form->input(
        'Network_name',
        array(
            'label' => __t('Training.Network_name'),
            'type' => 'select',
            'class' => 'select2-multiple clear_field',
            'options' => $network_list,
            'empty' => true,
            'required' => false,
        )
    ); 
    echo $this->Form->input(
        'Delegate_name',
        array(
            'label' => __t('Training.Delegate_name'),
            'class' => 'clear_field dynamicSelect2_contacts_delegates',
            'type' => 'select',
            'multiple' => false,
            'options' => isset($array_delegate_name) ? $array_delegate_name : array(),
            'empty' => true,
        )
    );
    echo $this->Form->input(
        'Garage_employment_position',
        array(
            'label' => __t('Training.Employment_position'),
            'type' => 'select',
            'class' => 'select2-multiple clear_field',
            'options' => $employment_list,
            'empty' => true,
            'required' => true,
        )
    );
    echo $this->Form->input(
        'Delegate_order_number',
        array(
            'label' => __t('Training.Purchase_order_number'),
            'class' => 'clear_field',
            'type' => 'text',
            'required' => true,
        )
    ); 
    echo $this->Form->input(
        'Delegate_cancelled',
        array(
            'label' => __t('CRM.Cancelled'),
            'type' => 'select',
            'class' => 'select2-multiple clear_field',
            'options' => $cancelled,
            'empty' => true,
            'id' => 'read',
            'required' => true,
        )
    );
    echo $this->Form->input(
        'Delegate_reason_cancelled_id',
        array(
            'label' => __t('General.Cancelled_reason'),
            'type' => 'select',
            'class' => 'select2-multiple clear_field',
            'options' => $reason_cancelled_list,
            'empty' => true,
            'required' => true,
        )
    );?>
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
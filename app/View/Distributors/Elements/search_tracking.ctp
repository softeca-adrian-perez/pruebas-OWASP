<?php
echo $this->Form->create(
    'Search',
    array(
        'class' => 'cnt-form-search buscador-js',
        'style' => 'margin: 0px',
        'type' => 'get',
        'url' => array(
            'controller' => 'distributors',
            'action' => 'tracking_distributor',
            $distributor['Distributor']['id']
        ),
    )
);
?>
<div class="cnt-form-search-title">
    <?php echo __t('CRM.Visit_history'); ?>
</div>
<div class="cnt-form-inputs">
    <?php echo $this->Form->input(
        'from',
        array(
            'class' => 'fecha-js from-js clear_field',
            'type' => 'text',
            'data-to' => '#to',
            'required' => true,
            'id' => 'from',
            'div' => array(
                'class' => 'datepicker datepicker-label-block',
            ),
            'label' => __t('General.From'),
        )
    );
    echo $this->Form->input(
        'to',
        array(
            'class' => 'fecha-js to-js clear_field',
            'type' => 'text',
            'required' => true,
            'id' => 'to',
            'data-from' => '#from',
            'div' => array(
                'class' => 'datepicker datepicker-label-block',
            ),
            'label' => __t('General.To'),
        )
    );
    echo $this->Form->input(
        'appointment_status_id',
        array(
            'label' => __t('CRM.Status'),
            'type' => 'select',
            'options' => $status_list,
            'class' => 'select2-multiple clear_field',
            'required' => true,
            'multiple' =>  true,
        )
    );
    echo $this->Form->input(
        'appointment_feeling_id',
        array(
            'label' => __t('Appointment.Feeling'),
            'type' => 'select',
            'options' => $feelings_list,
            'class' => 'select2-multiple clear_field',
            'empty' => true,
            'required' => true
        )
    );
    echo $this->Form->input(
        'appointment_type_id',
        array(
            'label' => __t('Appointment.Type'),
            'type' => 'select',
            'options' => $appointments_visit_types,
            'class' => 'select2-multiple clear_field',
            'empty' => true,
            'required' => true
        )
    ); ?>
    <label class="center-check p-top-1">
        <?php echo __t('Appointment.Requires_follow_up'); ?>
        <div class="aag-switch round small">
            <?php echo $this->Form->input('requires_follow_up', array('label' => false, 'div' => false, 'type' => 'checkbox', 'required' => true, 'id' => 'requires_follow_up', 'class' => 'clear_field')); ?>
            <label for="requires_follow_up"></label>
        </div>
    </label>
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
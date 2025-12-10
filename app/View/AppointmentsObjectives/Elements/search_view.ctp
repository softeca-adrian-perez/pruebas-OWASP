<?php
echo $this->Html->script('appointments_objectives.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('dynamic_lists.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Form->create(
    'Search',
    array(
        'class' => 'cnt-form-search buscador-js',
        'type' => 'get',
        'url' => array(
            'controller' => 'appointments_objectives',
            'action' => 'view_objectives',
        ),
    )
);
?>
<div class="cnt-form-search-title">
	<?php echo __t('AppointmentObjective.View_objectives'); ?>
</div>
<div class="cnt-form-inputs">
	<?php echo $this->Form->input(
		'distributor',
		array(
			'class' => 'clear_field dynamicSelect2_distributors',
			'type' => 'select',
			'multiple' => true,
			'required' => true,
			'empty' => true,
			'id' => 'distributor-id-js',
			'label' => __t('AppointmentObjective.Distributor'),
			'data-texto1' =>__t('General.Min_3_characters'),
			'data-selected_distributors' => isset($distributors) ? $distributors : array(),
		)
	); 
	echo $this->Form->input(
			'objectives',
		array(
			'label' => __t('AppointmentObjective.Objective'),
			'type' => 'select',
			'class' => 'select2-multiple clear_field',
			'options' => $management_objectives,
			'empty' => true,
			'id' => 'read',
			'required' => false,
			'multiple' => true,
		)
	); 
	echo $this->Form->input(
		'date_from',
		array(
			'class' => 'fecha-js from-js clear_field',
			'type' => 'text',
			'div' => array(
				'class' => 'datepicker datepicker-label-block',
			),
			'required' => true,
			'id' => 'completed_date_from',
			'data-to' => '#completed_date_to',
			'label' => __t('AppointmentObjective.From'),
		)
	);
	echo $this->Form->input(
		'date_to',
		array(
			'class' => 'fecha-js to-js clear_field',
			'type' => 'text',
			'div' => array(
				'class' => 'datepicker datepicker-label-block',
			),
			'required' => true,
			'id' => 'creation_date_to',
			'data-from' => '#creation_date_from',
			'label' => __t('AppointmentObjective.To'),
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

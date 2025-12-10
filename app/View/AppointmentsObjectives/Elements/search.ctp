<?php
echo $this->Html->script('appointments_objectives.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('dynamic_lists.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));

echo $this->Form->create(
	'Search',
	array(
		'class' => 'cnt-form-search',
		'type' => 'get',
		'style' => 'margin: 0px',
		'url' => array(
			'controller' => 'appointments_objectives',
			'action' => 'reporting',
		),
	)
);
?>

<div class="cnt-form-search-title">
	<?php echo __t('AppointmentObjective.Reporting'); ?>
</div>
<div class="cnt-form-inputs">
	<?php
	echo $this->Form->input(
		'distributor',
		array(
			'label' => __t('AppointmentObjective.Distributor'),
			'type' => 'select',
			'class' => 'dynamicSelect2_distributors clear_field',
			'multiple' => true,
			'empty' => true,
			'id' => 'distributor-id-js',
			'data-selected_distributors' => isset($distributors) ? $distributors : array(),
		)
	);
	if (
		CakeSession::read('Auth.User.Contact.position_id') == ConstantsPositions::BDM_TG_ID ||
		CakeSession::read('Auth.User.Contact.position_id') == ConstantsPositions::BDM_AAG_ID ||
		CakeSession::read('Auth.User.Contact.position_id') == ConstantsPositions::BDM_GPC_ID
	) {
		echo $this->Form->input(
			'bdm',
			array(
				'label' => __t('AppointmentObjective.BDM'),
				'class' => 'select2-multiple',
				'type' => 'select',
				'multiple' => false,
				'options' => $contacts_bdm,
				'required' => false,
				'empty' => true,
				'value' => CakeSession::read('Auth.User.Contact.id'),
				'id' => 'search_contact',
				'disabled' => true
			)
		);
		echo $this->Form->hidden('bdm', array(
			'value' => CakeSession::read('Auth.User.Contact.id')
		));
	} else {
		echo $this->Form->input(
			'bdm',
			array(
				'label' => __t('AppointmentObjective.BDM'),
				'class' => 'select2-multiple clear_field',
				'type' => 'select',
				'multiple' => true,
				'options' => $contacts_bdm,
				'required' => false,
				'empty' => true,
				'id' => 'search_contact',
			)
		);
	}
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
		'status',
		array(
			'label' => __t('AppointmentObjective.Status'),
			'class' => 'select2-multiple clear_field',
			'type' => 'select',
			'options' => $objectives_status,
			'empty' => true,
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
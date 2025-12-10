<?php
echo $this->Html->script('submit_booking.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('/js/conferences_delegates.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
$controller = $this->request->controller;
?>
<div class="cnt-form-search-title">
	<?php echo __t('Booking.Bookings'); ?>
</div>
<div class="cnt-form-inputs">
	<?php
	echo $this->Form->input(
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
		'creation_date_from',
		array(
			'class' => 'fecha-js clear_field',
			'type' => 'text',
			'required' => true,
			'div' => array(
				'class' => 'datepicker datepicker-label-block',
			),
			'label' => __t('General.Creation_date') . ' ' . __t('General.From'),
		)
	);
	echo $this->Form->input(
		'creation_date_to',
		array(
			'class' => 'fecha-js clear_field',
			'type' => 'text',
			'required' => true,
			'div' => array(
				'class' => 'datepicker datepicker-label-block',
			),
			'label' => __t('General.Creation_date') . ' ' .  __t('General.To'),
		)
	);
	echo $this->Form->input(
		'quotation_id',
		array(
			'class' => 'clear_field',
			'type' => 'text',
			'required' => true,
			'label' => __t('General.Quotation_id'),
		)
	);
	if ($has_child_networks) {
		echo $this->Form->input(
			'child_network_id',
			array(
				'label' => __t('ChildNetworks.secondary_networks'),
				'class' => 'disabled_fields',
				'type' => 'select',
				'multiple' => false,
				'empty' => true,
				'options' => $child_networks,
				'id' => 'child_network_id'
			)
		);
	}
	if (isset($network_id)) {
		$clearSearch = $controller == 'reporting' ? 'clear_field' : '';
		echo $this->Form->input(
			'garage_id',
			array(
				'label' => __t('General.Garages'),
				'class' => 'select2Dinamico_garage cargar_garages garage_filter ' . $clearSearch,
				'type' => 'select',
				'multiple' => false,
				'options' => isset($array_garage_name) ? $array_garage_name : array(),
				'value' => (CakeSession::read('Auth.User.role_id') == ConstantsRoles::GARAGE) ? CakeSession::read('Auth.User.garage_id') : $garage_id,
				'id' => 'garage_name_reporting-js',
				'empty' => true,
				'disabled' => ($controller == 'reporting') && (CakeSession::read('Auth.User.role_id') != ConstantsRoles::GARAGE) ? false : true,
			)
		);
		echo $this->Form->input(
			'marketing_acceptance',
			array(
				'label' => __t('Bookings.Marketing_acceptance'),
				'type' => 'select',
				'class' => 'select2-multiple clear_field',
				'options' => isset($marketing_acceptance_types) ? $marketing_acceptance_types : '',
				'empty' => true,
				'required' => true,
			)
		);
		echo $this->Form->input(
			'booking_status',
			array(
				'label' => __t('Booking.Booking_status'),
				'type' => 'select',
				'class' => 'select2-multiple clear_field',
				'options' => $booking_status,
				'empty' => true,
				'required' => true,
			)
		);
	} ?>
</div>
<div class="cnt-form-search-buttons">
	<?php
	echo $this->Form->button(
		__t('General.Search'),
		array(
			'type' => 'submit',
			'class' => 'aag-button medium submit-btn-trigger',
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
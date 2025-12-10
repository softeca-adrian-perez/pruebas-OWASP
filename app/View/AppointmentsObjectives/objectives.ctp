<?php
echo $this->Html->script('appointments_objectives.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('dynamic_lists.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
$controller = $this->request->controller;
$user = $this->Acceso->user();

?>
<div class="cnt-breadcrumb">
	<div>
		<?php
		echo $this->Html->breadcrumb(array(
			$this->Html->link(
				__t('CRM.Crm'),
				array(
					'controller' => 'dashboard',
					'action' => 'home'
				)
			),
			__t('Crm.Management_area'),
			__t('AppointmentObjective.Objectives'),
		));
		?>
	</div>
	<div>
		<a class="aag-button medium two go-back-js"><?php echo __t('General.Back') ?></a>
	</div>
</div>
<div class="cnt-data aag-padding">
	<div class="aag-title">
		<?php echo __t('AppointmentObjective.Set_objectives'); ?>
	</div>
	<?php
	echo $this->Form->create(
		'AppointmentObjective',
		array(
			'class' => 'form-horizontal',
			'enctype' => 'multipart/form-data',
		)
	);
	?>
	<div class="aag-subtitle p-top-1">
		<?php echo __t('AppointmentObjective.Set_management_objectives'); ?>
	</div>
	<hr>
	<div class="background-color-primary">
		<div class="m-bottom-1" style="display: flex;">
			<label class="obligatorio">
				<?php echo __t('AppointmentObjective.Select_objectives'); ?>
			</label>
			<?php
			echo $this->Form->input(
				'Objective.tg',
				array(
					'label' => __t('AppointmentObjective.Tg'),
					'type' => 'checkbox',
					'id' => 'check_Tg_management',
				)
			);
			?>
			<?php
			echo $this->Form->input(
				'Objective.gpc',
				array(
					'label' => __t('AppointmentObjective.Gpc'),
					'type' => 'checkbox',
					'id' => 'check_Gpc_management',
				)
			);
			?>
		</div>
		<div class="m-bottom-1 d-none" id="Tg_management">
			<div class="o-auto">
				<table class="table-tracking">
					<thead>
						<tr>
							<th>
								<?php echo __t('AppointmentObjective.Name') ?>
							</th>
							<th class="ta-center">

							</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($tg_objectives as $key => $objective) { ?>
							<tr>
								<td class="ta-defecto">
									<?php echo $objective; ?>
								</td>
								<td class="ta-center">
									<?php
									echo $this->Form->input(
										'AppointmentObjective.tg.' . $key,
										array(
											'label' => false,
											'type' => 'checkbox',
											'id' => 'tg' . $key,
											'class' => 'tg_objectives',
											'data-objective-id' => $key,
										)
									);
									?>
								</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
		<div class="m-bottom-1 d-none" id="Gpc_management">
			<div class="o-auto">
				<table class="table-tracking">
					<thead>
						<tr>
							<th>
								<?php echo __t('AppointmentObjective.Name') ?>
							</th>
							<th class="ta-center">

							</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($gpc_objectives as $key2 => $objective2) { ?>
							<tr>
								<td class="ta-defecto">
									<?php echo $objective2; ?>
								</td>
								<td class="ta-center">
									<?php
									echo $this->Form->input(
										'AppointmentObjective.gpc.' . $key2,
										array(
											'label' => false,
											'type' => 'checkbox',
											'id' => 'gpc' . $key2,
											'class' => 'gpc_objectives',
											'data-objective-id' => $key2,
										)
									);
									?>
								</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
		<div class="cnt-form-search m-0-i">
			<div class="cnt-form-search-title">
				<?php echo __t('Section.Filters'); ?>
			</div>
			<div class="cnt-form-inputs">
				<div>
					<?php if ($this->Session->read('Auth.User.Contact.position_id') == ConstantsPositions::REGIONAL_SALES_MANAGER_LV_ID || $this->Session->read('Auth.User.Contact.position_id') == ConstantsPositions::REGIONAL_SALES_MANAGER_CV_ID) {
						$default_value = $this->Session->read('Auth.User.contact_id');
						$disabled_value = true;
					} else {
						$default_value = null;
						$disabled_value = false;
					} ?>
					<?php echo $this->Form->input(
						'RSM',
						array(
							'label' => __t('Garage.RSM'),
							'class' => 'select2-multiple clear_field',
							'type' => 'select',
							'multiple' => true,
							'empty' => true,
							'default' => $default_value,
							'disabled' => $disabled_value,
							'options' => $rsm,
							'id' => 'garage_filter_rsm',
						)
					); ?>
				</div>
				<div>
					<?php if ($this->Session->read('Auth.User.role_id') == ConstantsRoles::BDM_AAG || $this->Session->read('Auth.User.roles_id') == ConstantsRoles::BDM_TG) {
						$default_value = $this->Session->read('Auth.User.contact_id');
						$disabled_value = true;
					} else {
						$default_value = null;
						$disabled_value = false;
					} ?>
					<?php echo $this->Form->input(
						'BDM',
						array(
							'label' => __t('Garage.BDM'),
							'class' => 'select2-multiple clear_field',
							'type' => 'select',
							'multiple' => true,
							'empty' => true,
							'default' => $default_value,
							'disabled' => $disabled_value,
							'options' => $bdm,
							'id' => 'garage_filter_bdm',
						)
					); ?>
				</div>
				<div>
					<?php echo $this->Form->input(
						'trading_group_id',
						array(
							'label' => __t('Distributor.Trading_groups'),
							'type' => 'select',
							'class' => 'select2-multiple clear_field',
							'options' => $trading_groups,
							'multiple' => true,
							'empty' => true,
							'id' => 'trading-group-id'
						)
					); ?>
				</div>
				<div>
					<?php echo $this->Form->input(
						'customer_activity_id',
						array(
							'label' => __t('Garage.Customer_activities'),
							'type' => 'select',
							'class' => 'select2-multiple clear_field',
							'options' => $activities,
							'multiple' => true,
							'empty' => true,
							'id' => 'customer_activity_id'
						)
					); ?>
				</div>
			</div>
			<div class="cnt-form-search-buttons">
				<?php
				echo $this->Html->link(
					__t('General.Search'),
					'javascript:void(0);',
					array(
						'escape' => false,
						'class' => 'aag-button medium search_distributors_button',
						'id' => 'search_distributors',
						'data-url' => Router::url(
							array(
								'controller' => 'distributors',
								'action' => 'search_distributors_objectives'
							)
						)
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
		</div>
		<div class="medium-12 columns clear">
			<div class="aag-subtitle"><?php echo __t('Distributor.Distributors') ?><span id="count_distributors"><?php echo '(' . $distributors . ')'; ?></span></div>
			<?php
			echo $this->Form->input(
				'distributor_id',
				array(
					'label' => array(
						'text' => __t('Distributor.Distributor'),
						'style' => 'position: relative; top: -3px;'
					),
					'class' => 'select2-multiple',
					'type' => 'select',
					'multiple' => true,
					'empty' => true,
					// 'options' => $task_distributors,
					// 'value' => $value,
					'id' => 'distributor_name_assigned_to',
					'data-url' => Router::url(array(
						'controller' => 'distributors',
						'action' => 'ajax_get_distributors'
					)),
				)
			); ?>
		</div>
		<div class="columns medium-12" style="padding: 0 !important;position: relative;">
			<div class="columns medium-6" style="padding-bottom: 1em;padding-right: 0px !important;">
			</div>
			<div class="columns medium-6" style="padding-bottom: 1em;padding-right: 0px !important;">
				<div class="columns medium-6">
					<?php echo $this->Form->input(
						'DistributorObjective.from_date',
						array(
							'required' => true,
							'type' => 'text',
							'class' => 'fecha-js from-js',
							'data-to' => '#to',
							'id' => 'from',
							'label' => __t('DistributorObjective.From_date'),
						)
					); ?>
				</div>
				<div class="columns medium-6">
					<?php echo $this->Form->input(
						'DistributorObjective.to_date',
						array(
							'required' => true,
							'type' => 'text',
							'class' => 'fecha-js to-js',
							'data-from' => '#from',
							'id' => 'to',
							'label' => __t('DistributorObjective.To_date'),
						)
					); ?>
				</div>
			</div>
		</div>
		<div class="columns medium-6" style="padding: 0 !important;position: relative;">
			<div class="ta-right p-bottom-1 columns medium-12 cnt-buttons-v2">
				<?php echo $this->Form->Button(
					__t('General.Save'),
					array(
						'class' => 'aag-button medium green',
						'type' => 'submit',
						'id' => 'set-objectives-js',
						'data-time-objectives-created-scheduled-task' =>  ConstantsObjectives::TIME_SCHEDULED_TASK_CREATE,
						'data-limit-objectives-created-scheduled-task' =>  ConstantsObjectives::LIMIT_CREATE,
						'data-url' => Router::url(
							array(
								'controller' => 'appointments_objectives',
								'action' => 'objectives'
							)
						)
					)
				); ?>
			</div>
		</div>
	</div>
</div>
<?php
echo $this->Form->hidden(
	'distributor_count',
	array(
		'id' => 'distributor-count-js',
		'value' => $distributors
	)
);
echo $this->Form->hidden(
	'distributor_count_update',
	array(
		'id' => 'distributor-count-update-js',
		'value' => $distributors
	)
);
?>
<?php echo $this->Form->end(); ?>
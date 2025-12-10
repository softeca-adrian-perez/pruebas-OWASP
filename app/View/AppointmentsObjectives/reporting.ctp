<?php
echo $this->Html->script('gd_export_excel.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('jquery.fileDownload.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
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
			$this->Html->link(
				__t('AppointmentObjective.Reporting'),
				array(
					'controller' => 'appointments_objectives',
					'action' => 'reporting'
				)
			),
		));
		?>
	</div>
	<div>
		<a class="aag-button medium two go-back-js"><?php echo __t('General.Back') ?></a>
		<?php
		if ($objectives) {
			echo $this->Form->button(
				__t('General.Export'),
				array(
					'class' => 'aag-button medium gd-export-excel-buscador-js',
					'value' => 'submit',
					'escape' => false,
					'name' => 'export',
					'data-url' => Router::url(
						array(
							'controller' => 'appointments_objectives',
							'action' => 'appointments_objectives_excel',
						)
					),
				)
			);
		}
		?>
	</div>
</div>
<?php echo $this->element('../Elements/Comun/appointment_reporting_tab', array('selected' => 'visited',)); ?>
<div class="cnt-data aag-padding">
	<?php echo $this->element('../AppointmentsObjectives/Elements/search'); ?>
	<div class="aag-subtitle p-top-1">
		<?php echo __t('Appointment.Visit_objectives'); ?>
	</div>
	<hr>
	<div class="o-auto">
		<table class="table-tracking">
			<thead>
				<tr>
					<th>
						<?php echo $this->Paginator->sort('Appointment.date', __t('AppointmentObjective.Visit_date')); ?>
					</th>
					<th>
						<?php echo $this->Paginator->sort('User.name', __t('AppointmentObjective.BDM')); ?>
					</th>
					<th>
						<?php echo __t('AppointmentObjective.Objective'); ?>
					</th>
					<th class="ta-center">
						<?php echo __t('AppointmentObjective.Status'); ?>
					</th>
					<th>
						<?php echo $this->Paginator->sort('AppointmentObjectiveComment.comment', __t('AppointmentObjective.Post_visit_comment')); ?>
					</th>
					<th>
						<?php echo $this->Paginator->sort('Distributor.name', __t('AppointmentObjective.Distributor')); ?>
					</th>
					<th>

					</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($objectives as $objective) { ?>
					<tr>
						<td>
							<?php echo Fecha::toFormatoVista($objective['Appointment']['date']); ?>
						</td>
						<td>
							<?php echo $objective['User']['name'] . ' ' . $objective['User']['surname']; ?>
						</td>
						<td>
							<?php echo isset($management_objectives[$objective['AppointmentObjectiveComment']['objective_id']]) ? $management_objectives[$objective['AppointmentObjectiveComment']['objective_id']] : ''; ?>
						</td>
						<td class="ta-center">
							<?php echo $objectives_status[$objective['AppointmentObjectiveComment']['status']]; ?>
						</td>
						<td>
							<?php echo $objective['AppointmentObjectiveComment']['comment']; ?>
						</td>
						<td>
							<?php echo $objective['Distributor']['name']; ?>
						</td>
						<td class="ta-right">
							<?php
							echo $this->Html->link(
								__t('AppointmentObjective.View_visit'),
								array(
									'controller' => 'appointments',
									'action' => 'edit',
									$objective['Appointment']['id']
								),
								array(
									'class' => 'aag-button small ta-center btn-full-width',
									'target' => '_blanck'
								)
							);

							?>
						</td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
	<br />
	<?php echo $objectives ? $this->element('Comun/paginacion') : ''; ?>
</div>
<?php
echo $this->Html->script('appointments_objectives.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
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
			__t('AppointmentObjective.View_objectives'),
		));
		?>
	</div>
	<div>
		<a class="aag-button medium two go-back-js"><?php echo __t('General.Back') ?></a>
		<?php
		echo $this->Html->link(
			__t('AppointmentObjective.Delete_all'),
			array(),
			array(
				'escape' => false,
				'class' => 'delete-all-objectives aag-button medium red',
				'style' => 'margin-bottom: 1em;background-color:#fe472f !important',
				'data-time-objectives-deleted-scheduled-task' =>  ConstantsObjectives::TIME_SCHEDULED_TASK_DELETE,
				'data-limit-objectives-deleted-scheduled-task' =>  ConstantsObjectives::LIMIT_DELETE,
				'data-url' => Router::url(
					array(
						'controller' => 'appointments_objectives',
						'action' => 'delete_all_distributors_objectives'
					)
				),
			)
		);
		?>
	</div>
</div>
<div class="cnt-data agg-padding">
	<?php echo $this->element('../AppointmentsObjectives/Elements/search_view'); ?>
	<div class="o-auto">
		<table class="table-tracking">
			<thead>
				<tr>
					<th>
						<?php echo $this->Paginator->sort('AppointmentObjective.name', __t('AppointmentObjective.Objective')); ?>
					</th>
					<th class="ta-center">
						<?php echo $this->Paginator->sort('AppointmentObjective.tg_management', __t('AppointmentObjective.Tg')); ?>
					</th>
					<th class="ta-center">
						<?php echo $this->Paginator->sort('AppointmentObjective.gpc_management', __t('AppointmentObjective.Gpc')); ?>
					</th>
					<th>
						<?php echo $this->Paginator->sort('Distributor.name', __t('AppointmentObjective.Distributor')); ?>
					</th>
					<th class="ta-center">
						<?php echo $this->Paginator->sort('DistributorObjective.from', __t('AppointmentObjective.Date_from')); ?>
					</th>
					<th class="ta-center">
						<?php echo $this->Paginator->sort('DistributorObjective.to', __t('AppointmentObjective.Date_to')); ?>
					</th>
					<th class="ta-center">
						<?php echo __t('General.Actions'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($distributors_objectives as $objective) { ?>
					<tr>
						<td class="ta-defecto">
							<?php echo $objective['AppointmentObjective']['name'] ?>
						</td>
						<td class="ta-center">
							<?php
							if (isset($objective['AppointmentObjective']['tg_management'])) {
								echo '<span class="ion-ios-checkmark-outline c-exito" style="margin-right !important: .5rem;font-size: 25px;"></span>';
							}
							?>
						</td>
						<td class="ta-center">
							<?php
							if (isset($objective['AppointmentObjective']['gpc_management'])) {
								echo '<span class="ion-ios-checkmark-outline c-exito" style="margin-right !important: .5rem;font-size: 25px;"></span>';
							}
							?>
						</td>
						<td>
							<?php echo $objective['Distributor']['name'] ?>
						</td>
						<td class="ta-center">
							<?php echo Fecha::toFormatoVista($objective['DistributorObjective']['from']) ?>
						</td>
						<td class="ta-center">
							<?php echo Fecha::toFormatoVista($objective['DistributorObjective']['to']) ?>
						</td>
						<td class="ta-center">
							<?php
							$objective_id = isset($objective['DistributorObjective']['id']) ? $objective['DistributorObjective']['id'] : '';
							echo $this->Html->Link(
								'<span class="aag-icon-papelera c-fallo"></span>',
								array(),
								array(
									'escape' => false,
									'title' => __t('General.Delete'),
									'class' => 'new-delete-js',
									'data-url' => Router::url(array(
										'controller' => 'appointments_objectives',
										'action' => 'ajax_delete_distributor',
										$objective['DistributorObjective']['id']
									)),
									'data-url_redirect' => Router::url(array(
										'controller' => 'appointments_objectives',
										'action' => 'view_objectives',
									)),
									'data-confirmmsg' => __t('AppointmentObjective.Delete_objective?'),
								)
							); ?>
						</td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
	<br />
	<?php echo $this->element('Comun/paginacion'); ?>
</div>
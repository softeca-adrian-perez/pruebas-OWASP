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
			__t('Crm.Management_set_objectives'),
		));
		?>
	</div>
	<div>
		<a class="aag-button medium two go-back-js"><?php echo __t('General.Back') ?></a>
		<?php
		echo $this->Html->link(
			__t('AppointmentObjective.New_objective'),
			array(
				'controller' => 'appointments_objectives',
				'action' => 'add',
			),
			array(
				'escape' => false,
				'class' => 'aag-button medium green',
			)
		); ?>
	</div>
</div>
<div class="cnt-data aag-padding">
	<div class="aag-title">
		<?php echo __t('Crm.Management_set_objectives_title') ?>
	</div>
	<div class="o-auto p-top-1">
		<table class="table-tracking">
			<thead>
				<tr>
					<th>
						<?php echo $this->Paginator->sort('AppointmentObjective.name', __t('AppointmentObjective.Name')); ?>
					</th>
					<th class="ta-center">
						<?php echo $this->Paginator->sort('AppointmentObjective.tg_personal', __t('AppointmentObjective.Tg_personal')); ?>
					</th>
					<th class="ta-center">
						<?php echo $this->Paginator->sort('AppointmentObjective.tg_management', __t('AppointmentObjective.Tg_management')); ?>
					</th>
					<th class="ta-center">
						<?php echo $this->Paginator->sort('AppointmentObjective.gpc_personal', __t('AppointmentObjective.Gpc_personal')); ?>
					</th>
					<th class="ta-center">
						<?php echo $this->Paginator->sort('AppointmentObjective.gpc_management', __t('AppointmentObjective.Gpc_management')); ?>
					</th>
					<th class="ta-center">
						<?php echo __t('General.Actions'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach ($objectives as $objective) {
					$span = '<span class="ion-ios-checkmark-outline c-exito" style="margin-right !important: .5rem;font-size: 25px;"></span>';
				?>
					<tr>
						<td class="ta-defecto">
							<?php echo $objective['AppointmentObjective']['name'] ?>
						</td>
						<td class="ta-center">
							<?php
							if ($objective['AppointmentObjective']['tg_personal']) {
								echo $span;
							}
							?>
						</td>
						<td class="ta-center">
							<?php
							if ($objective['AppointmentObjective']['tg_management']) {
								echo $span;
							}
							?>
						</td>
						<td class="ta-center">
							<?php
							if ($objective['AppointmentObjective']['gpc_personal']) {
								echo $span;
							}
							?>
						</td>
						<td class="ta-center">
							<?php
							if ($objective['AppointmentObjective']['gpc_management']) {
								echo $span;
							}
							?>
						</td>
						<td class="ta-center">
							<?php
							echo $this->Html->link(
								'<span class="aag-icon-editar c-primary"></span>',
								array(
									'controller' => 'appointments_objectives',
									'action' => 'edit',
									$objective['AppointmentObjective']['id'],
								),
								array(
									'escape' => false,
									'title' => __t('AppointmentObjective.Edit')
								)
							);
							echo $this->Html->Link(
								'<span class="aag-icon-papelera c-fallo"></span>',
								array(),
								array(
									'escape' => false,
									'title' => __t('General.Delete'),
									'class' => 'new-delete-js',
									'data-url' => Router::url(array(
										'controller' => 'appointments_objectives',
										'action' => 'ajax_delete',
										$objective['AppointmentObjective']['id']
									)),
									'data-url_redirect' => Router::url(array(
										'controller' => 'appointments_objectives',
										'action' => 'maintenance',
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
	<?php echo $this->element('Comun/paginacion'); ?>
</div>
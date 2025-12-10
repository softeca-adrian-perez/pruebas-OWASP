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
	<a class="aag-button medium two go-back-js"><?php echo __t('General.Back') ?></a>
</div>
<?php echo $this->element('../Elements/Comun/appointment_reporting_tab', array('selected' => 'not_visited',)); ?>
<div class="cnt-data aag-padding">
	<?php echo $this->element('../DistributorsObjectives/Elements/search'); ?>
	<div class="aag-subtitle p-top-1">
		<?php echo __t('Appointment.Visit_objectives'); ?>
	</div>
	<hr>
	<div class="o-auto">
		<table class="table-tracking">
			<thead>
				<tr>
					<th>
						<?php echo $this->Paginator->sort('User.name', __t('AppointmentObjective.BDM')); ?>
					</th>
					<th>
						<?php echo $this->Paginator->sort('AppointmentObjective.name', __t('AppointmentObjective.Objective')); ?>
					</th>
					<th>
						<?php echo $this->Paginator->sort('Distributor.name', __t('AppointmentObjective.Distributor')); ?>
					</th>
					<th>
						<?php echo $this->Paginator->sort('DistributorObjective.from', __t('AppointmentObjective.From')); ?>
					</th>
					<th>
						<?php echo $this->Paginator->sort('DistributorObjective.to', __t('AppointmentObjective.To')); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($objectives as $objective) { ?>
					<tr>
						<td>
							<?php echo $objective['User']['name'] . ' ' . $objective['User']['surname']; ?>
						</td>
						<td>
							<?php echo $objective['AppointmentObjective']['name']; ?>
						</td>
						<td>
							<?php echo $objective['Distributor']['name'] . ' - ' . $objective['Distributor']['account_number'] . ' - ' . $objective['Distributor']['town']; ?>
						</td>
						<td>
							<?php echo h(Fecha::toFormatoVista($objective['DistributorObjective']['from'])); ?>
						</td>
						<td>
							<?php echo h(Fecha::toFormatoVista($objective['DistributorObjective']['to'])); ?>
						</td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
	<br />
	<?php echo $objectives ? $this->element('Comun/paginacion') : ''; ?>
</div>
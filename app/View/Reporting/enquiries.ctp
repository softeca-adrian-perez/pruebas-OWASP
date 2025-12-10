<?php
echo $this->Html->script('reporting.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('gd_export_excel.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('jquery.fileDownload.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));

$user = $this->Acceso->user();
?>
<div class="cnt-breadcrumb">
	<div>
		<?php
		echo $this->Html->breadcrumb(array(
			$this->Html->link(
				__t('Network.Networks'),
				array(
					'controller' => 'networks',
					'action' => 'home'
				)
			),
			__t('Reporting.Enquiries') . ' - ' . $network_name,
		));
		?>
	</div>
	<div>
		<?php
		if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::GARAGE) {
			echo $this->Form->button(
				__t('Garage.Marketing_emails'),
				array(
					'class' => 'gd-export-excel-marketingEmails-js aag-button medium',
					'value' => 'submit',
					'escape' => false,
					'name' => 'export',
					'data-url' => Router::url(
						array(
							'controller' => 'reporting',
							'action' => 'marketing_emails_excel',
							$network_id
						)
					)
				)
			);
		}
		?>
	</div>
</div>
<?php echo $this->element('../Reporting/Elements/tabs_reporting_network', array('selected' => 'button_enquiries')); ?>
<div class="cnt-data aag-padding">
	<?php
	echo $this->Form->create(
		'Search',
		array(
			'class' => 'cnt-form-search',
			'type' => 'get',
			'url' => array(
				'controller' => 'reporting',
				'action' => 'enquiries',
				$network_id
			),
		)
	);
	?>
	<?php echo $this->element('../Enquiries/Elements/info_search'); ?>
	<?php echo $this->Form->end(); ?>
	<div class="o-auto">
		<table class="table-tracking">
			<thead>
				<tr>
					<th><?php echo $this->Paginator->sort('Garage.name', __t('Garage.Garage')); ?></th>
					<th><?php echo __t('General.Name'); ?></th>
					<th><?php echo __t('Contact.Phone'); ?></th>
					<th><?php echo __t('Email.Email'); ?></th>
					<?php if ($has_child_networks) { ?>
						<th><?php echo $this->Paginator->sort('Enquiry.child_network_id', __t('ChildNetworks.secondary_networks')); ?></th>
                    <?php } ?>
					<th><?php echo $this->Paginator->sort('Enquiry.description', __t('General.Description')); ?></th>
					<th><?php echo $this->Paginator->sort('Enquiry.answered', __t('Enquiry.Answered')); ?></th>
					<th><?php echo $this->Paginator->sort('Enquiry.creation_date', __t('General.Date')); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($enquiries as $enquiry) : ?>
					<tr>
						<td><?php echo $enquiry['Garage']['name']; ?></td>
						<td><?php echo Texto::encryptDecryptText($enquiry['Enquiry']['name'], false); ?></td>
						<td><?php echo Texto::encryptDecryptText($enquiry['Enquiry']['phone'], false); ?></td>
						<td><?php echo Texto::encryptDecryptText($enquiry['Enquiry']['email'], false); ?></td>
						<?php if ($has_child_networks) { ?>
                            <td>
                                <?php if ($has_child_networks && isset($enquiry['Enquiry']['child_network_id'])) {
                                    echo $child_networks[$enquiry['Enquiry']['child_network_id']];
                                } ?>
                            </td>
                        <?php } ?>
						<td><?php echo $enquiry['Enquiry']['description']; ?></td>
						<td><?php echo $enquiry['Enquiry']['answered'] ? __t('General.Yes') : __t('General.No'); ?></td>
						<td>
							<?php
							$date = $enquiry['Enquiry']['creation_date'];
							if ($user['aag_region_id'] == Configure::read('AAG_REGION_ID_BENELUX')) {
								$date = date(Fecha::_FORMATO_BD_FECHA_HORA, strtotime($date . ' +1 hours'));
							}
							echo Fecha::toFormatoVistaFechaHora($date);
							?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<br>
	<?php echo $this->element('Comun/paginacion'); ?>
</div>
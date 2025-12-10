<?php
echo $this->Html->script('reporting.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('gd_export_excel.js?v='.Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('jquery.fileDownload.js?v='.Configure::read('VERSION_CACHE'), array('block' => 'script'));

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
			__t('Booking.Bookings').' - '.$network_name,
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
<?php echo $this->element('../Reporting/Elements/tabs_reporting_network', array('selected' => 'button_bookings')); ?>
<div class="cnt-data aag-padding">
	<?php
	echo $this->Form->create(
		'Search',
		array(
			'class' => 'cnt-form-search',
			'type' => 'get',
			'url' => array(
				'controller' => 'reporting',
				'action' => 'bookings',
				$network_id
			),
		)
	);
	?>
	<?php echo $this->element('../Bookings/Elements/info_search'); ?>
	<?php echo $this->Form->end(); ?>
	<div class="o-auto">
		<table class="table-tracking">
			<thead>
				<tr>
					<th><?php echo  __t('Garage.Garage'); ?></th>
					<th><?php echo __t('General.Name'); ?></th>
					<th><?php echo __t('Contact.Phone'); ?></th>
					<th><?php echo __t('Email.Email'); ?></th>
					<th><?php echo __t('General.Plate'); ?></th>
					<th><?php echo __t('General.Vin'); ?></th>
					<?php if ($has_child_networks) { ?>
                        <th><?php echo $this->Paginator->sort('Booking.child_network_id', __t('ChildNetworks.secondary_networks')); ?></th>
                    <?php } ?>
					<th><?php echo $this->Paginator->sort('Booking.date', __t('General.Date')); ?></th>
					<th><?php echo $this->Paginator->sort('Booking.time', __t('General.Time')); ?></th>
					<th><?php echo $this->Paginator->sort('Booking.time_to', __t('General.Time_to')); ?></th>
					<th><?php echo $this->Paginator->sort('Booking.quotation_id', __t('General.Quotation_id')); ?></th>
					<th><?php echo $this->Paginator->sort('Booking.additional_info', __t('General.Additional_info')); ?></th>
					<th><?php echo __t('Booking.Booking_status'); ?></th>
					<th><?php echo $this->Paginator->sort('Booking.creation_date', __t('General.Creation_date')); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($bookings as $booking) :?>
					<tr>
						<td><?php echo $booking['Garage']['name']; ?></td>
						<td><?php echo Texto::encryptDecryptText($booking['Booking']['customer_name'], false); ?></td>
						<td><?php echo Texto::encryptDecryptText($booking['Booking']['customer_phone'], false); ?></td>
						<td><?php echo Texto::encryptDecryptText($booking['Booking']['customer_email'], false); ?></td>
						<td><?php echo Texto::encryptDecryptText($booking['Booking']['plate'], false); ?></td>
						<td><?php echo Texto::encryptDecryptText($booking['Booking']['vin'], false); ?></td>
						<?php if ($has_child_networks) { ?>
                            <td>
								<?php if (isset($booking['Booking']['child_network_id'])) {
									echo $child_networks[$booking['Booking']['child_network_id']];
								} ?>
                            </td>
                        <?php } ?>
						<td><?php echo Fecha::toFormatoVista($booking['Booking']['date']); ?></td>
						<td><?php echo date("H:i", strtotime($booking['Booking']['time'])); ?></td>
						<td><?php echo date("H:i", strtotime($booking['Booking']['time_to'])); ?></td>
						<td><?php echo $booking['Booking']['quotation_id']; ?></td>
						<td><?php echo $booking['Booking']['additional_info']; ?></td>
						<td class="color-blue-text">
							<?php
								if(!empty($booking['Booking']['booking_status'])){
									if($booking['Booking']['booking_status'] != ConstantsBookingsStatus::EXPIRED){
										echo $this->Html->link(
											'<span>' . h($booking_status[$booking['Booking']['booking_status']]) . '</span>',
											array(
												'controller' => 'reporting',
												'action' => 'booking_status_edit',
												$booking['Booking']['id'],
												$network_id,
												'reporting'
											),
											array(
												'escape' => false,
											)
										);
									} else {
										echo $booking_status[$booking['Booking']['booking_status']];
									}
								}
							?>
                        </td>
						<td><?php echo Fecha::toFormatoVista($booking['Booking']['creation_date']); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<br/>
	<?php echo $this->element('Comun/paginacion'); ?>
</div>

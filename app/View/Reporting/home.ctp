<?php
echo $this->Html->script('lib/Chart.min.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('reporting.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('gd_export_excel.js?v='.Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('jquery.fileDownload.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
?>
<div class="cnt-breadcrumb">
	<div>
		<?php
		if(!$isNetwork)
		{
			echo $this->Html->breadcrumb(array(
				$this->Html->link(
					__t('GarageNetwork.Garage_network'),
					array(
						'controller' => 'garages_networks',
						'action' => 'view',
						$garage_network_id
					)
				),
				__t('General.Reporting'),
			));
		}
		else
		{
			echo $this->Html->breadcrumb(array(
				$this->Html->link(
					__t('Network.Networks'),
					array(
						'controller' => 'networks',
						'action' => 'home'
					)
				),
				__t('Reporting.ReportingDates').' - '.$network_name,
			));
		}
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
<?php
if (!$isNetwork) {
	echo $this->element('../GaragesNetworks/tabs_network', array('selected' => 'button_my_garage'));
} else {
	echo $this->element('../Reporting/Elements/tabs_reporting_network',array('selected' => 'button_reporting_dates'));
}
?>
<div class="cnt-data">
	<?php
	if(!$isNetwork)
	{
		?>
		<div class="aag-padding">
			<?php echo $this->element('../GaragesNetworks/tabs_my_garage', array('selected' => 'reporting')); ?>
		</div>
		<?php
	}
	?>
		<?php echo $this->element('../Reporting/Elements/search'); ?>
	<div class="cnt-two-columns aag-padding">
		<div class="p-0 columns medium-12">
			<img src="/img/fi-rr-download-purple.svg"
				alt="<?php echo __t('Reporting.DownloadImage'); ?>"
				title="<?php echo __t('Reporting.DownloadImage'); ?>"
				class='download-chart-js'
				data-canvas_id="chartServices">
			</img>
			<img src="/img/excel.svg"
				width="15"
				alt="<?php echo __t('Reporting.DownloadData'); ?>"
				title="<?php echo __t('Reporting.DownloadData'); ?>"
				class='download_chart_excel-js',
				data-url="<?php echo Router::url(
					array(
						'plugin' => false,
						'controller' => 'reporting',
						'action' => 'downloadExcel',
						ConstantesGraphicReports::GRAPHIC_SERVICES
					)
				) ?>",
				data-datasets_data='<?php echo json_encode($datasets_data); ?>'
				data-labels='<?php echo json_encode($labels); ?>'>
			</img>
			<div>
				<canvas id="chartServices"
					data-title='<?php echo __t('Reporting.QuotationsPerService'); ?>'
					data-currency='<?php echo $currency; ?>'
					data-datasets_data='<?php echo json_encode($datasets_data); ?>'
					data-labels='<?php echo json_encode($labels); ?>'>
				</canvas>
			</div>
		</div>
		<div>
			<div class="o-auto">
				<table class="table-tracking">
					<thead>
						<tr>
							<th><?php echo __t('Reporting.Metric');?></th>
							<th><?php echo __t('Reporting.Total');?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="fw-bold"><?php echo __t('Reporting.Quotations'); ?></td>
							<td class="fw-bold"><?php echo $totalQuotations; ?></td>
						</tr>
						<tr>
							<td class="fw-bold"><?php echo __t('Reporting.Enquiries'); ?></td>
							<td class="fw-bold"><?php echo $totalEnquiries; ?></td>
						</tr>
						<tr>
							<td class="fw-bold"><?php echo __t('Reporting.BookingsWithQuotation'); ?></td>
							<td class="fw-bold"><?php echo $totalBookingsWithQuotation; ?></td>
						</tr>
						<tr>
							<td class="fw-bold"><?php echo __t('Reporting.BookingsWithoutQuotation'); ?></td>
							<td class="fw-bold"><?php echo $totalBookingsWithoutQuotation; ?></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div>
			<canvas id="chartMetrics"
				data-datasets_data='<?php echo json_encode($metricsData); ?>'
				data-labels='<?php echo json_encode($metricLabels); ?>'
				data-titletext='<?php echo $titleText; ?>'>
			</canvas>
		</div>
	</div>
</div>
<?php
echo $this->Html->script('genarts_families.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('toggle.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('quoting_networks.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
?>

<div class="cnt-breadcrumb">
	<div>
		<?php
		echo $this->Html->breadcrumb(array(
			$this->Html->link(
				__t('Network.Networks'),
				array(
					'controller' => 'networks',
					'action' => 'families_configuration',
					$network_id
				)
			),
			__t('Configuration.Configuration')
		));
		?>
	</div>
</div>
<?php echo $this->element('../Networks/configuration_tabs', array('selected' => 'quoting')); ?>

<?php
echo $this->Html->script('garages.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Form->create(
	'Search',
	array(
		'class' => 'cnt-form-search buscador-js m-top-0',
		'type' => 'get',
		'url' => array(
			'controller' => 'networks',
			'action' => 'quoting',
			$network_id
		),
		'id' => 'search-quoting'
	)
);
?>

<div class="cnt-data aag-padding">
	<div class="p-top-1 p-bottom-1">
		<div>
			<div class="toggle-js cnt-form-toggle-title-secondary opacity1 m-0-i cursor-pointer" data-toggle-id-js="1">
				<?php echo __t('Network.Quoting'); ?>
				<i class="ion-ios-arrow-down arrow-icon-js c-blanco"></i>
			</div>
			<div class="toggle-content-js list-container cnt-dropdown-data" data-toggle-id-js="1">
				<div class="cnt-form-inputs">
					<?php
					echo $this->Form->input(
						'name',
						array(
							'type' => 'text',
							'class' => 'search_ajax clear_field',
							'data-url' => Router::url(
								array(
									'controller' => 'garages',
									'action' => 'ajax_search_home',
									ConstantsTypeSearch::GARAGE
								)
							),
							'label' => __t('Appointment.Customer'),
						)
					);
					echo $this->Form->input(
						'city_id',
						array(
							'label' => __t('Garage.City'),
							'type' => 'select',
							'class' => 'select2-multiple clear_field',
							'options' => $cities,
							'empty' => true,
							'multiple' => true
						)
					);
					echo $this->Form->input(
						'province_id',
						array(
							'label' => __t('Garage.Province'),
							'type' => 'select',
							'class' => 'select2-multiple clear_field',
							'options' => $provinces,
							'empty' => true,
							'multiple' => true
						)
					);
					echo $this->Form->input(
						'postcode',
						array(
							'type' => 'text',
							'class' => 'clear_field',
							'empty' => true,
							'label' => __t('Garage.Postcode'),
						)
					);
					echo $this->Form->input(
						'erp_id',
						array(
							'type' => 'select',
							'class' => 'select2-multiple clear_field',
							'options' => $erps,
							'empty' => true,
							'label' => __t('Garage.ERP'),
						)
					);
					echo $this->Form->input(
						'ref_code',
						array(
							'type' => 'text',
							'class' => 'clear_field',
							'label' => __t('Garage.Ref_code'),
						)
					);
					echo $this->Form->input(
						'network_id',
						array(
							'label' => __t('Network.Networks'),
							'type' => 'select',
							'class' => 'select2-multiple clear_field',
							'options' => $networks,
							'empty' => true,
						)
					);
					?>
				</div>
				<div class="cnt-form-search-buttons p-top-1">
					<?php
					echo $this->Form->button(
						__t('General.Search'),
						array(
							'type' => 'submit',
							'class' => 'aag-button medium'
						)
					);
					?>
					<?php
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
				<div class="d-none" id="config_check_networks"
					data-confirmmsg="<?php echo __t('GarageQuoting.Mark_unmark_all_garages_msg'); ?>"
					data-type="<?php echo 'warning'; ?>"
					data-yes="<?php echo __t('General.Yes'); ?>"
					data-no="<?php echo __t('General.No'); ?>"
					data-url="<?php echo Router::url(array('controller' => 'networks', 'action' => 'ajax_save_garage_quoting')); ?>">
				</div>
				<div class="o-auto">
					<table class="table-tracking">
						<thead>
							<tr>
								<th><?php echo $this->Paginator->sort('Garage.name', __t('Appointment.Customer')); ?></th>
								<th><?php echo __t('Garage.City'); ?></th>
								<th><?php echo __t('Garage.Province'); ?></th>
								<th><?php echo $this->Paginator->sort('Garage.address1', __t('Garage.Address')); ?></th>
								<th><?php echo $this->Paginator->sort('Garage.phone', __t('Garage.Phone')); ?></th>
								<th><?php echo __t('Garage.ERP'); ?></th>
								<th><?php echo $this->Paginator->sort('Garage.ref_code', __t('Garage.Ref_code')); ?></th>
								<th class="ta-center">
									<?php echo __t('GarageNetwork.ActivateQuotingViews'); ?>
									<div>
										<?php if ($globalQuotingViewsActives) { ?>
											<span class="all-options-js quoting-views-js ion-toggle-filled c-exito icono-grande"></span>
										<?php } else { ?>
											<span class="all-options-js quoting-views-js ion-toggle c-fallo icono-grande"></span>
										<?php } ?>
									</div>
								</th>
								<th class="ta-center">
									<?php echo __t('GarageNetwork.ActivateQuoting'); ?>
									<div>
										<?php if ($globalQuotingActives) { ?>
											<span class="all-options-js quoting-js ion-toggle-filled c-exito icono-grande"></span>
										<?php } else { ?>
											<span class="all-options-js quoting-js ion-toggle c-fallo icono-grande"></span>
										<?php } ?>
									</div>
								</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($garages as $key => $garage) { ?>
								<tr>
									<td>
										<?php
										echo $this->Html->link(
											$garage['Garage']['name'],
											array(
												'controller' => 'garages_networks',
												'action' => 'network_dashboard',
												$garage['GarageNetwork']['id']
											),
											array(
												'class' => 'c-primary'
											)
										);
										?>
									</td>
									<td>
										<?php if ($garage['Garage']['city_id'] && isset($cities[$garage['Garage']['city_id']])) {
											echo h($cities[$garage['Garage']['city_id']]);
										}  ?>
									</td>
									<td>
										<?php if ($garage['Garage']['province_id']) {
											echo h($provinces[$garage['Garage']['province_id']] ?? '');
										}  ?>
									</td>
									<td><?php echo h($garage['Garage']['address1']); ?></td>
									<td><?php echo h($garage['Garage']['phone']); ?></td>
									<td>
										<?php if ($garage['Garage']['erp_id']) {
											echo h($erps[$garage['Garage']['erp_id']]);
										}  ?>
									</td>
									<td><?php echo h($garage['Garage']['ref_code']); ?></td>
									<td class="ta-center">
										<?php if (in_array($garage['Garage']['id'], $cvGarageIds)) { ?>
											<span class="ion-toggle icono-grande" style="color:grey;"></span>
										<?php } elseif ($garage['GarageNetwork']['quoting_views_active']) { ?>
											<span class="ico-toggle-quoting-views-js ion-toggle-filled c-exito icono-grande" id="<?php echo 'checkbox_quoting_views_' . $key ?>" data-key="<?php echo $key ?>" data-garage_id="<?php echo $garage['GarageNetwork']['id'] ?>"></span>
										<?php } else { ?>
											<span class="ico-toggle-quoting-views-js ion-toggle c-fallo icono-grande" id="<?php echo 'checkbox_quoting_views_' . $key ?>" data-key="<?php echo $key ?>" data-garage_id="<?php echo $garage['GarageNetwork']['id'] ?>"></span>
										<?php } ?>
									</td>
									<td class="ta-center">
										<?php if (in_array($garage['Garage']['id'], $cvGarageIds)) { ?>
											<span class="ion-toggle icono-grande" style="color:grey;"></span>
										<?php } elseif ($garage['GarageNetwork']['quoting_active']) { ?>
											<span class="ico-toggle-quoting-js ion-toggle-filled c-exito icono-grande" id="<?php echo 'checkbox_quoting_' . $key ?>" data-key="<?php echo $key ?>" data-garage_id="<?php echo $garage['GarageNetwork']['id'] ?>"></span>
										<?php } else { ?>
											<span class="ico-toggle-quoting-js ion-toggle c-fallo icono-grande" id="<?php echo 'checkbox_quoting_' . $key ?>" data-key="<?php echo $key ?>" data-garage_id="<?php echo $garage['GarageNetwork']['id'] ?>"></span>
										<?php } ?>
									</td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
				<?php
				if (count($garages) > 0) {
					echo $this->element('Comun/paginacion');
				}
				?>
			</div>
		</div>
	</div>
</div>
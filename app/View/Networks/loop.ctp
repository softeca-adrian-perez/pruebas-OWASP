<?php
echo $this->Html->script('loop.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('genarts_families.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('toggle.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('dynamic_lists.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
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
			__t('Network.Loop')
		));
		?>
	</div>
</div>
<?php
echo $this->Form->create(
	'Search',
	array(
		'class' => 'cnt-form-search buscador-js m-top-0',
		'type' => 'get',
		'url' => array(
			'controller' => 'networks',
			'action' => 'loop',
			$network_id
		),
		'id' => 'search-loop'
	)
);
?>
<div class="cnt-data aag-padding">
	<div class="p-top-1">
		<div class="toggle-js cnt-form-toggle-title-secondary opacity1 m-0-i cursor-pointer" data-toggle-id-js="1">
			<?php echo __t('Network.Loop'); ?>
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
		</div>
		<?php echo $this->Form->end(); ?>
		<br />
		<div class="cnt-data fg-0 p-vertical-1">
			<div class="d-none" id="config_loop-js"
				data-confirmmsg="<?php echo __t('GarageQuoting.Mark_unmark_all_garages_msg'); ?>"
				data-type="<?php echo 'warning'; ?>"
				data-yes="<?php echo __t('General.Yes'); ?>"
				data-no="<?php echo __t('General.No'); ?>"
				data-url="<?php echo Router::url(array('controller' => 'networks', 'action' => 'ajax_save_garage_loop')); ?>">
			</div>
			<div class="o-auto">
				<table class="table-tracking" id="families_genarts">
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
								<?php echo __t('General.Activate') . ' ' . __t('Network.Loop'); ?>
								<div>
									<?php if ($globalLoopActives) { ?>
										<span class="all-options-js loop-js ion-toggle-filled c-exito icono-grande"></span>
									<?php } else { ?>
										<span class="all-options-js loop-js ion-toggle c-fallo icono-grande"></span>
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
									if ($network_id == NETWORK_ID_GV || $network_id == NETWORK_ID_GC) {
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
									} else {
										echo h($garage['Garage']['name']);
									}
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
									<?php if ($garage['GarageNetwork']['loop']) { ?>
										<span class="ico-toggle-loop-js ion-toggle-filled c-exito icono-grande" id="<?php echo 'checkbox_loop_' . $key ?>" data-key="<?php echo $key ?>" data-garage_id="<?php echo $garage['GarageNetwork']['id'] ?>"></span>
									<?php } else { ?>
										<span class="ico-toggle-loop-js ion-toggle c-fallo icono-grande" id="<?php echo 'checkbox_loop_' . $key ?>" data-key="<?php echo $key ?>" data-garage_id="<?php echo $garage['GarageNetwork']['id'] ?>"></span>
									<?php } ?>
								</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
			<br />
			<?php
			if (count($garages) > 0) {
				echo $this->element('Comun/paginacion');
			}
			?>
		</div>
	</div>
</div>
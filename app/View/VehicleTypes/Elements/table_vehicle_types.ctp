<?php  echo $this->Session->flash(); ?>
<div class="cnt-form-inputs">
    <?php
    foreach( $vehicle_types as $vehicle_type )
    {
        ?>
        <div class="item-remove-edit texto-elemento select_tr" id="vehicle_type_<?php echo $vehicle_type['VehicleType']['id'] ?>"
            data-id="<?php echo $vehicle_type['VehicleType']['id'] ?>"
			data-url="
				<?php echo Router::url(array(
					'controller' => 'vehicle_types',
					'action' => 'getDataVehicleType',
					$vehicle_type['VehicleType']['id'],
				)) ?>
			">
            <img title="<?php echo $vehicle_type['VehicleType'][$selected_language]; ?>" src="<?php echo FileManager::get_url(FilePaths::ICONS_IMAGES_RELATIVE . $vehicle_type['VehicleType']['url']); ?>"/>
			<div id="<?php echo 'language-' . $this->Session->read('Auth.User.language_code');?>" style="float: left;">
                <?php echo h($vehicle_type['VehicleType'][$selected_language]); ?>
            </div>
            <?php if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
                echo $this->Html->link(
                    '<span class="aag-icon-papelera c-fallo"></span>',
                    'javascript:;',
                    array(
                        'class' => 'delete-vehicle-type-js',
                        'data-confirmmsg' => __t('Maintenance.Vehicle_types_delete?'),
                        'data-yes' => __t('General.Yes'),
                        'data-no' => __t('General.No'),
                        'data-url' => Router::url(array(
                            'controller' => 'vehicle_types',
                            'action' => 'ajax_delete_vehicle_type',
                            $vehicle_type['VehicleType']['id'],
                        )),
                        'data-id' => $vehicle_type['VehicleType']['id'],
                        'data-name' => $vehicle_type['VehicleType'][$selected_language],
                        'escape' => false,
                        'title' => __t('General.Delete'),
                    )
                );
            } ?>
        </div>
        <?php
    }
    ?>
</div>

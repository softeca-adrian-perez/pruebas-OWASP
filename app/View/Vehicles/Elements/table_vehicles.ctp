<?php  echo $this->Session->flash(); ?>
<div class="cnt-form-inputs">
    <?php
    foreach( $vehicles as $vehicle )
    {
        // style="border-bottom: 1px solid #F5F5F7;padding-top: 3px;"
        ?>
        <div class="item-remove-edit texto-elemento select_tr" id="vehicle_<?php echo $vehicle['Vehicle']['id'] ?>"
			data-id="<?php echo $vehicle['Vehicle']['id'] ?>"
			data-url="
				<?php echo Router::url(array(
					'controller' => 'vehicles',
					'action' => 'getDataVehicle',
					$vehicle['Vehicle']['id'],
				)) ?>
			">
			<div id="<?php echo 'language-' . $this->Session->read('Auth.User.language_code');?>" style="float: left;">
                <?php echo h($vehicle['Vehicle'][$selected_language]); ?>
            </div>
            <?php if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
                echo $this->Html->link(
                    '<span class="aag-icon-papelera c-fallo"></span>',
                    'javascript:;',
                    array(
                        'class' => 'delete-vehicle-js f-right lh-1',
                        'data-confirmmsg' => __t('Maintenance.Vehicle_delete?'),
                        'data-yes' => __t('General.Yes'),
                        'data-no' => __t('General.No'),
                        'data-url' => Router::url(array(
                            'controller' => 'vehicles',
                            'action' => 'ajax_delete_vehicle',
                            $vehicle['Vehicle']['id'],
                        )),
                        'data-id' => $vehicle['Vehicle']['id'],
                        'data-name' => $vehicle['Vehicle'][$selected_language],
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

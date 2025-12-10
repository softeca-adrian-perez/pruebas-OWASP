<?php
echo $this->Html->script('vehicles.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('lib/purify.min.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Maintenance.Maintenance'),
                array(
                    'controller' => 'maintenance',
                    'action' => 'home'
                )
            ),
            __t('Maintenance.Vehicles'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back')?></a>
    </div>
</div>
<div class="aag-tabs">
    <ul>
        <li>
            <input id="tab-add" type="radio" class="d-none" name="tabs" checked>
            <label for="tab-add">
                <?php echo __t('General.Add'); ?>
            </label>
        </li>
        <?php if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) { ?>
            <li>
                <input id="tab-edit" type="radio" class="d-none" name="tabs">
                <label for="tab-edit">
                    <?php echo __t('General.Edit'); ?>
                </label>
            </li>
        <?php } ?>
    </ul>
</div>
<div class="cnt-data">
    <br />
    <?php if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) { ?>
    <div class="aag-padding" id="form_click">
        <div id="form-add">
            <?php
            echo $this->Form->create(
                'Vehicle',
                array(
                    'url' => 'ajax_add_edit_vehicle',
                    'id' => 'FormAddVehicle'
                )
            ); ?>
                <div class="aag-subtitle">
                    <?php echo __t('Maintenance.Vehicle_add'); ?>
                </div>
                <div class="cnt-form-inputs">
                    <?php
						foreach($languages as $languageCode) {
							echo $this->Form->input(
								'Vehicle.name_' . $languageCode,
								array(
									'label' => __t('Maintenance.Vehicle_name_' . $languageCode),
									'type' => 'text',
									'id' => 'name-vehicle-add-' . $languageCode,
									'required' => Configure::read('MAINTENANCES_DEFAULT_LANGUAGE') == $languageCode ? true : false
								)
							);
						}
                    ?>
                </div>
                <div class="clear ta-right m-top-1">
                    <?php
                    echo $this->Form->Button(
                        __t('General.Save'),
                        array(
                            'class' => 'aag-button medium green',
                            'type' => 'submit',
                            'id' => 'add-vehicle'
                        )
                    );
                    ?>
                </div>
            <?php echo $this->Form->end(); ?>
        </div>
        <div id="form-edit" class="d-none">
            <?php
            echo $this->Form->create(
                '',
                array(
                    'id' => 'FormEditVehicle',
                    'url' => 'ajax_add_edit_vehicle'
                )
            );
                ?>
                <div class="aag-subtitle">
                    <?php echo __t('Maintenance.Vehicle_edit'); ?>
                </div>
                <div class="cnt-form-inputs">
                    <?php
						foreach($languages as $languageCode) {
							echo $this->Form->input(
								'Vehicle.name_' . $languageCode,
								array(
									'label' => __t('Maintenance.Vehicle_name_' . $languageCode),
									'type' => 'text',
									'id' => 'name-vehicle-edit-' . $languageCode,
									'required' => Configure::read('MAINTENANCES_DEFAULT_LANGUAGE') == $languageCode ? true : false
								)
							);
						}
                    ?>
                </div>
                <div class="clear ta-right m-top-1">
                    <?php
                    echo $this->Form->button(
                        __t('General.Cancel'),
                        array(
                            'type' => 'button',
                            'class' => 'aag-button medium four',
                            'id' => 'unselect-vehicle',
                            'disabled' => true
                        )
                    );
                    echo $this->Form->Button(
                        __t('General.Edit'),
                        array(
                            'class' => 'aag-button medium green',
                            'type' => 'submit',
                            'id' => 'edit-vehicle'
                        )
                    );
                    ?>
                </div>
            <?php echo $this->Form->end(); ?>
        </div>
        <div id="form-edit-msg" class="d-none">
			<div class="aag-subtitle">
                <?php echo __t('Maintenance.Vehicle_edit'); ?>
            </div>
            <div class="cnt-form-inputs">
				<?php
					foreach($languages as $languageCode) {
						echo $this->Form->input(
							'Vehicle.name_' . $languageCode,
							array(
								'label' => __t('Maintenance.Vehicle_name_' . $languageCode),
								'type' => 'text',
								'disabled' => true
							)
						);
					}
				?>
            </div>
			<div class="row">
                <div class="columns medium-12 p-0">
                    <div class="p-vertical-1 columns medium-10" >
                        <div class="columns medium-12 ta-center" style="padding-top:3rem">
                            <div class="aag-title"><?php echo __t('Maintenance.Vehicle_select'); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
    <div id="ajax_table_vehicles" class="aag-padding">
        <?php echo $this->element('../Vehicles/Elements/table_vehicles'); ?>
    </div>
</div>

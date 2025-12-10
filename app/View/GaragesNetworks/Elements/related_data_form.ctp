<?php
echo $this->Html->script('vehicle_brands_garage_network.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Form->create('GarageNetworks', array('enctype' => 'multipart/form-data'));
echo $this->Form->hidden($garage_id);
    ?>
    <div class="buttons-fixed-double-tabs">
        <div>
            <?php echo $this->element('Comun/form_actions', $cancel_action); ?>
        </div>
    </div>

    <div class="aag-padding">
        <div class="aag-subtitle">
            <?php echo __t('GarageNetwork.Offered_works'); ?>
        </div>
        <div class="cnt-form-inputs cont-services">
            <?php
            if(empty($works_available))
            {
                echo __t('GaragesNetwork.No_works_avaiable');
            }
            else
            {
                foreach($works_available as $key => $work)
                {
                    ?>
                    <div>
                        <label>
                            <?php
                            $checked = in_array($work["Work"]["id"], $works_selected);
                            echo $this->Form->input(
                                "Works.".$work["Work"]["id"],
                                array(
                                    'type' => 'checkbox',
                                    'label' => false,
                                    'div' => false,
                                    'value' => $work["Work"]["id"],
                                    'checked' => $checked,
                                    'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                                )
                            ); ?>
                            <span class="unselectable <?php if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN) {
                                echo 'no-click';
                            }?>"><?php echo $work['Work']['name_' . __l()]; ?></span>
                        </label>
                    </div>
                    <?php
                }
            }
            ?>
        </div>

        <div class="aag-subtitle">
            <?php echo __t('Garage.Services'); ?>
        </div>
        <div class="cnt-form-inputs cont-services">
            <?php
            if(empty($services_available))
            {
                echo __t('GarageNetwork.No_services_avaiable');
            }
            else
            {
                foreach($services_available as $key => $service)
                {
                    ?>
                    <label>
                        <?php
                        $checked = in_array($service['Service']['id'], $services_selected);
                        echo $this->Form->input(
                            'Service.' . $service['Service']['id'],
                            array(
                                'type' => 'checkbox',
                                'label' => false,
                                'div' => false,
                                'value' => $service['Service']['id'],
                                'checked' => $checked,
                                'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                            )
                        ); ?>
                        <span class="unselectable <?php if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN) {
                                echo 'no-click';
                            }?>" ><?php echo $this->Html->image(FileManager::get_url(FilePaths::ICONS_IMAGES_RELATIVE . $service['Service']['url'])) . " " . $service['Service']['name_' . __l()]; ?></span>
                    </label>
                    <?php
                }
            }
            ?>
        </div>

        <div class="aag-subtitle">
            <?php echo __t('GarageNetwork.Services_drivers'); ?>
        </div>
        <div class="cnt-form-inputs cont-services">
            <?php
            if(empty($services_drivers_available))
            {
                echo __t('GarageNetwork.No_services_drivers_avaiable');
            }
            else
            {
                foreach($services_drivers_available as $key => $serviceDriver)
                {
                    ?>
                    <label>
                        <?php
                        $checked = in_array($serviceDriver["ServiceDriver"]["id"], $services_drivers_selected);
                        echo $this->Form->input(
                            "ServiceDriver.".$serviceDriver["ServiceDriver"]["id"],
                            array(
                                'type' => 'checkbox',
                                'label' => false,
                                'div' => false,
                                'value' => $serviceDriver["ServiceDriver"]["id"],
                                'checked' => $checked,
                                'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                            )
                        ); ?>
                        <span class="unselectable <?php if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN) {
                                echo 'no-click';
                            }?>"><?php echo $serviceDriver['ServiceDriver']['name_en']; ?></span>
                    </label>
                    <?php
                }
            } ?>
        </div>

        <div class="aag-subtitle">
            <?php echo __t('GarageNetwork.Vehicle_brand_specialist_black_list'); ?>
        </div>
        <div id="all-checks-vehicle" class="cont-services cnt-form-inputs">
            <?php
            if(empty($vehicles_available))
            {
                echo __t('GarageNetwork.No_vehicles_avaiable');
            }
            else
            {
                foreach($vehicles_available as $key => $vehicle)
                {
                    ?>
                    <div class="cnt-check-vehicle">
                        <div>
                            <label>
                                <?php
                                $checkedVehicle = in_array($vehicle["Vehicle"]["id"], $vehicles_selected);
                                echo $this->Form->input(
                                    "Vehicle.".$vehicle["Vehicle"]["id"],
                                    array(
                                        'type' => 'checkbox',
                                        'label' => false,
                                        'div' => false,
                                        'value' => $vehicle["Vehicle"]["id"],
                                        'checked' => $checkedVehicle,
                                        'id' => 'vehicle_' . $key,
                                        'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                                    )
                                ); ?>
                                <span class="unselectable check-specialist-js <?php if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN) {
                                echo 'no-click';
                            }?>" data-id="<?php echo $key; ?>" title="<?php echo __t('GarageNetwork.Specialist_in_this_brand'); ?>">
                                    <?php echo $this->Html->image(FileManager::get_url(FilePaths::ICONS_IMAGES_RELATIVE ."ok_circle.svg")); ?>
                                </span>
                            </label>
                            <label>
                                <?php
                                $checkedVehicleBlackList = in_array($vehicle["Vehicle"]["id"], $vehicles_black_list_selected);
                                echo $this->Form->input(
                                    "VehicleBlackList.".$vehicle["Vehicle"]["id"],
                                    array(
                                        'type' => 'checkbox',
                                        'label' => false,
                                        'div' => false,
                                        'value' => $vehicle["Vehicle"]["id"],
                                        'checked' => $checkedVehicleBlackList,
                                        'id' => 'vehicle_black_list_' . $key,
                                        'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                                    )
                                ); ?>
                                <span class="unselectable check-blacklist-js <?php if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN) {
                                echo 'no-click';
                            }?>" data-id="<?php echo $key; ?>" title="<?php echo __t('GarageNetwork.Add_brand_to_black_list'); ?>">
                                    <?php echo $this->Html->image(FileManager::get_url(FilePaths::ICONS_IMAGES_RELATIVE ."x_circle.svg")); ?>
                                </span>
                            </label>
                        </div>
                        <span><?php echo $vehicle['Vehicle']['name_en']; ?></span>
                    </div>
                    <?php
                }
            }
            ?>
        </div>

        <div class="aag-subtitle">
            <?php echo __t('Garage.Vehicle_types'); ?>
        </div>
        <div class="cnt-form-inputs cont-services">
            <?php
            if(empty($vehicle_types_available))
            {
                echo __t('GarageNetwork.No_vehicle_types_avaiable');
            }
            else
            {
                foreach($vehicle_types_available as $key => $vehicleType)
                {
                    ?>
                    <label>
                        <?php
                        $checked = in_array($vehicleType["VehicleType"]["id"], $vehicle_types_selected);
                        echo $this->Form->input(
                            "VehicleType.".$vehicleType["VehicleType"]["id"],
                            array(
                                'type' => 'checkbox',
                                'label' => false,
                                'div' => false,
                                'value' => $vehicleType["VehicleType"]["id"],
                                'checked' => $checked,
                                'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                            )
                        );
                        ?>
                        <span class="unselectable <?php if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN) {
                                echo 'no-click';
                            }?>">
                            <?php echo $this->Html->image(FileManager::get_url(FilePaths::ICONS_IMAGES_RELATIVE . $vehicleType['VehicleType']['url'])). " " . $vehicleType['VehicleType']['name_' . __l()]; ?>
                        </span>
                    </label>
                    <?php
                }
            }
            ?>
        </div>
    </div>
<?php echo $this->Form->end(); ?>

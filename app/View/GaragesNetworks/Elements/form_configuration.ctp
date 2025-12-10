<?php
echo $this->Form->create('GarageNetwork', array('enctype' => 'multipart/form-data'));
    $action = $this->request->action;
    ?>
    <div class="buttons-fixed-double-tabs">
        <div>
            <?php echo $this->element('Comun/form_actions'); ?>
        </div>
    </div>
    <div class="aag-padding">
        <div class="cnt-two-columns p-bottom-1">
            <?php if ($quoting_views_active) { ?>
                <label class="center-check">
                    <?php echo __t('GarageNetwork.ActivateQuoting'); ?>
                    <div class="aag-switch round small">
                        <input id="toggleQuoting" name="quoting_active" type="checkbox" class="presets"
                    	<?php
                            if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN) {
                            echo ' disabled ';
                            }
                            echo !empty($garage_network['GarageNetwork']['quoting_active']) ? 'checked' : ''
                        ?>/>
                    <label for="toggleQuoting"></label>
                    </div>
                </label>
            <?php } ?>
            <label class="center-check">
                <?php echo __t('GarageNetwork.ActivateEnquiries'); ?>
                <div class="aag-switch round small">
                    <input id="toggleEnquiries" name="enquiries_active" type="checkbox" class="presets"
                    <?php
                    if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN) {
                        echo ' disabled ';
                    }
                        echo !empty($garage_network['GarageNetwork']['enquiries_active']) ? 'checked' : ''
                    ?>/>
                    <label for="toggleEnquiries"></label>
                </div>
            </label>
        </div>
        <?php
        echo $this->Form->input(
            'GarageNetwork.about',
            array(
                'value' => $garage_network['GarageNetwork']['about'],
                'type' => 'textarea',
                'required' => true,
                'label' => __t('Garage.About'),
                'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
            )
        );
        ?>
        <br>
        <div class="cnt-check-visit cnt-form-inputs-max-width">
            <?php
            echo $this->Form->input(
                'GarageNetwork.usp1',
                array(
                    'value' => $garage_network['GarageNetwork']['usp1'],
                    'type' => 'text',
                    'label' => __t('GarageNetwork.USP').'1',
                    'maxlength' => 50,
                    'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                )
            );
            ?>
            <?php
            echo $this->Form->input(
                'GarageNetwork.usp2',
                array(
                    'value' => $garage_network['GarageNetwork']['usp2'],
                    'type' => 'text',
                    'label' => __t('GarageNetwork.USP').'2',
                    'maxlength' => 50,
                    'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                )
            );
            ?>
            <?php
            echo $this->Form->input(
                'GarageNetwork.usp3',
                array(
                    'value' => $garage_network['GarageNetwork']['usp3'],
                    'type' => 'text',
                    'label' => __t('GarageNetwork.USP').'3',
                    'maxlength' => 50,
                    'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                )
            );
            ?>
        </div>
    </div>
<?php echo $this->Form->end(); ?>

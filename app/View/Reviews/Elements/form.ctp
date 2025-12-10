<?php
echo $this->Html->script('garage_reviews.js?v='.Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Form->create('GarageNetwork', array('enctype' => 'multipart/form-data'));
    $action = $this->request->action;
    ?>
    <div class="buttons-fixed-double-tabs">
        <div>
            <?php echo $this->element('Comun/form_actions'); ?>
        </div>
    </div>
    <div class="aag-padding">
        <div class="medium columns p-boton">
            <label class="center-check">
                <?php echo __t('GarageNetwork.Kiyoh'); ?>
                <div class="aag-switch round small">
                    <input id="kiyoh-checkbox-js" type="checkbox" name="kiyoh_active" class="presets"
                        <?php 
                        if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN) {
                            echo ' disabled ';
                        }
                        if (isset($location_id) && !empty($location_id)) {
                            echo 'checked';
                        } ?>
                    />
                <label for="kiyoh-checkbox-js"></label>
                </div>
            </label>
        </div><br>
        <div class="medium-4 columns p-0 kiyoh_input_container-js" id="location_id-js" style="display:none;padding: 10px;">
            <?php echo $this->Form->input(
                'location_id',
                array(
                    'label' => __t('GarageNetwork.Location_id'),
                    'type' => 'text',
                    'class' => 'input-disabled',
                    'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                    'value' => $location_id,
                )
            ); ?>
        </div>
        <div class="medium-4 columns p-0 kiyoh_input_container-js" id="kiyoh_api_key-js" style="display:none;padding: 10px;">
            <?php echo $this->Form->input(
                'kiyoh_api_key',
                array(
                    'label' => __t('GarageNetwork.Kiyoh_api_key'),
                    'type' => 'text',
                    'class' => 'input-disabled',
                    'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                    'value' => $kiyoh_api_key,
                )
            ); ?>
        </div>
    </div>
<?php echo $this->Form->end(); ?>
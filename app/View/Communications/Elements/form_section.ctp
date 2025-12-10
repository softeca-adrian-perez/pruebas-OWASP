<?php
$action = $this->request->action;
echo $this->Html->script('categories.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->css('../js/lib/cropper/cropper.css');
echo $this->Html->script('lib/cropper/cropper.js?v=' . Configure::read('VERSION_CACHE'));
echo $this->Form->create(
    'CommunicationSection',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data',
    )
);

echo $this->Form->hidden(
    'CommunicationSection.id'
);
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        if ($action == 'add_section') {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Maintenance.Maintenance'),
                    array(
                        'controller' => 'maintenance',
                        'action' => 'home'
                    )
                ),
                $this->Html->link(
                    __t('Communication.Communications') . ' ' . __t('Communication.Communications_sections'),
                    array(
                        'controller' => 'communications',
                        'action' => 'maintenance_communications_sections'
                    )
                ),
                __t('General.Add'),
            ));
        } else {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Maintenance.Maintenance'),
                    array(
                        'controller' => 'maintenance',
                        'action' => 'home'
                    )
                ),
                $this->Html->link(
                    __t('Communication.Communications') . ' ' . __t('Communication.Communications_sections'),
                    array(
                        'controller' => 'communications',
                        'action' => 'maintenance_communications_sections'
                    )
                ),
                __t('General.Edit'),
            ));
        }
        ?>
    </div>
    <div>
        <?php echo $this->element('Comun/form_actions', $cancel_action); ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="aag-title m-bottom-1">
        <?php echo __t('Communication.Communications_sections'); ?>
    </div>
    <div class="cnt-form-inputs">
        <?php
        echo $this->Form->input(
            'name_en',
            array(
                'required' => true,
                'type' => 'text',
                'label' => __t('Communication.English_name'),
            )
        );
        echo $this->Form->input(
            'name_fr',
            array(
                'required' => true,
                'type' => 'text',
                'label' => __t('Communication.French_name'),
            )
        );
        echo $this->Form->input(
            'name_de',
            array(
                'required' => true,
                'type' => 'text',
                'label' => __t('Communication.German_name'),
            )
        );
        ?>
        <label class="center-check">
            <?php echo __t('Section.Scrolling'); ?>
            <div class="aag-switch round small">
                <?php echo $this->Form->input('scrolling', array('id' => 'scrolling', 'required' => true, 'type' => 'checkbox', 'label' => false, 'div' => false)); ?>
                <label for="scrolling"></label>
            </div>
        </label>
        <label class="center-check">
            <?php echo __t('Section.Visual'); ?>
            <div class="aag-switch round small">
                <?php echo $this->Form->input('visual', array('id' => 'visual', 'required' => true, 'type' => 'checkbox', 'label' => false, 'div' => false)); ?>
                <label for="visual"></label>
            </div>
        </label>
    </div>
    <div class="aag-subtitle m-top-1">
        <?php echo __t('General.Image'); ?>
    </div>
    <div id="cnt-img" class="m-0-i">
        <?php
        echo $this->Form->input(
            'image',
            array(
                'id' => 'image-input',
                'class' => 'dragdrop-js',
                'label' => false,
                'type' => 'file',
                'multiple' => false,
                'before' => '<div class="text-drop">' . __t('General.Drop_files') . '</div>',
                'div' => array(
                    'class' => 'field_file cont-fileWrapper'
                )
            )
        );
        echo $this->Form->hidden('new_image', array('id' => 'new-image-input'));
        ?>
    </div>
    <?php
    if (isset($communication_section) && !empty($communication_section['CommunicationSection']['image'])) {
    ?>
        <div class="aag-subtitle m-top-1">
            <?php echo __t('Section.Actually_image'); ?>
        </div>
    <?php
        echo $this->Html->image(
            FileManager::get_url(FilePaths::COMMUNICATIONS_IMAGES_RELATIVE . $communication_section['CommunicationSection']['image']),
            array(
                'alt' => $communication_section['CommunicationSection']['name' . __s()],
                'title' => $communication_section['CommunicationSection']['name' . __s()],
                'class' => 'logotipo',
                'style' => 'height: 50px; width: max-content;'
            )
        );
    }
    ?>
    <div class="aag-title m-top-1">
        <?php echo __t('Section.Filters'); ?>
    </div>
    <div class="aag-subtitle m-top-1 clear">
        <?php echo __t('Network.Garage_networks'); ?>
    </div>
    <div class="cnt-form-inputs cont-services w-100p">
        <?php
        if (!empty($networks)) {
            foreach ($networks as $key_network => $network) {
        ?>
                <label class="jc-center m-0-i" style="display: flex !important;">
                    <?php
                    $valor = false;
                    if ($action == 'edit_section') {
                        if (isset($communications_sections_networks)) {
                            foreach ($communications_sections_networks as $key => $communications_sections_network) {
                                if (intval($network['Network']['id']) == $key) {
                                    $valor = true;
                                }
                            }
                        }
                    }
                    echo $this->Form->input(
                        'Network.' . $network['Network']['id'],
                        array(
                            'type' => 'checkbox',
                            'label' => false,
                            'div' => false,
                            'value' => $network['Network']['id'],
                            'checked' => $valor
                        )
                    );
                    ?>
                    <span class="unselectable ta-center" title="<?php echo h($network['Network']['name']); ?>">
                        <?php
                        echo $this->Html->image(
                            FileManager::get_url(FilePaths::NETWORKS_IMAGES_RELATIVE . $network['Network']['image']),
                            array('alt' => $network['Network']['name'])
                        );
                        ?>
                    </span>
                </label>
        <?php
            }
        }
        ?>
    </div>
    <div class="cnt-form-inputs-max-width clear">
        <?php
        echo $this->Form->input(
            'without_networks',
            array(
                'label' => __t('Communication.Without_garage_network'),
                'required' => true,
                'type' => 'checkbox',
                'id' => 'without_garage_network',
                'checked' => ($action == 'add_section') ? true : $communication_section['CommunicationSection']['without_networks']
            )
        ); ?>
    </div>
    <div class="aag-subtitle m-top-1 clear">
        <?php echo __t('Network.Distributor_networks'); ?>
    </div>
    <div class="cnt-form-inputs cont-services w-100p">
        <?php
        if (!empty($distributors_networks)) {
            foreach ($distributors_networks as $key_distributor_network => $distributor_networks) {
        ?>
                <label class="jc-center m-0-i" style="display: flex !important;">
                    <?php
                    $valor = false;
                    if ($action == 'edit_section') {
                        if (isset($communications_sections_distributors_networks)) {
                            foreach ($communications_sections_distributors_networks as $key => $communications_network) {
                                if (intval($distributor_networks['DistributorNetwork']['id']) == $key) {
                                    $valor = true;
                                }
                            }
                        }
                    }
                    echo $this->Form->input(
                        'DistributorNetwork.' . $distributor_networks['DistributorNetwork']['id'],
                        array(
                            'type' => 'checkbox',
                            'label' => false,
                            'div' => false,
                            'value' => $distributor_networks['DistributorNetwork']['id'],
                            'checked' => $valor
                        )
                    );
                    ?>
                    <span class="unselectable ta-center" title="<?php echo h($distributor_networks['DistributorNetwork']['name']); ?>">
                        <?php
                        echo $this->Html->image(
                            FileManager::get_url(FilePaths::NETWORKS_IMAGES_RELATIVE . $distributor_networks['DistributorNetwork']['image']),
                            array('alt' => $distributor_networks['DistributorNetwork']['name'])
                        );
                        ?>
                    </span>
                </label>
        <?php
            }
        }
        ?>
    </div>
    <div class="cnt-form-inputs-max-width clear">
        <?php
        echo $this->Form->input(
            'without_distributor_networks',
            array(
                'label' => __t('Communication.Without_distributor_network'),
                'required' => true,
                'type' => 'checkbox',
                'id' => 'without_distributor_network',
                'checked' => ($action == 'add_section') ? true : $communication_section['CommunicationSection']['without_distributor_networks']
            )
        );
        ?>
    </div>

    <div class="aag-subtitle m-top-1 clear">
        <?php echo __t('Network.Trading_groups'); ?>
    </div>
    <div class="cnt-form-inputs cont-services w-100p">
        <?php
        if (!empty($trading_groups)) {
            foreach ($trading_groups as $key_trading_group => $trading_group) {
        ?>
                <label style="display: flex !important; justify-content: center; align-items: center;">
                    <?php
                    $valor = false;
                    if ($action == 'edit_section') {
                        if (isset($communications_sections_trading_groups)) {
                            foreach ($communications_sections_trading_groups as $key => $communications_trading_group) {
                                if (intval($trading_group['TradingGroup']['id']) == $key) {
                                    $valor = true;
                                }
                            }
                        }
                    }
                    echo $this->Form->input(
                        'TradingGroup.' . $trading_group['TradingGroup']['id'],
                        array(
                            'type' => 'checkbox',
                            'label' => false,
                            'div' => false,
                            'value' => $trading_group['TradingGroup']['id'],
                            'checked' => $valor
                        )
                    );
                    ?>
                    <span class="unselectable ta-center" title="<?php echo h($trading_group['TradingGroup']['name']); ?>">
                        <?php
                        echo $this->Html->image(
                            FileManager::get_url(FilePaths::TRADING_GROUP_IMAGES_RELATIVE . $trading_group['TradingGroup']['image']),
                            array('alt' => $trading_group['TradingGroup']['name'])
                        );
                        ?>
                    </span>
                </label>
        <?php
            }
        }
        ?>
    </div>
    <div class="aag-subtitle m-top-1 clear">
        <?php echo __t('Distributor.Distributors'); ?>
    </div>
    <div class="cnt-form-inputs-max-width">
        <?php
        echo $this->Form->input(
            'aag_member_yes',
            array(
                'label' => __t('Communication.Aag_member_yes'),
                'required' => true,
                'type' => 'checkbox',
                'id' => 'is_aag_member_yes',
                'checked' => ($action == 'add_section') ? true : $communication_section['CommunicationSection']['aag_member_yes']
            )
        );
        ?>
        <div>
            <?php
            echo $this->Form->input(
                'aag_member_no',
                array(
                    'label' => __t('Communication.Aag_member_no'),
                    'required' => true,
                    'type' => 'checkbox',
                    'id' => 'is_aag_member_no',
                    'checked' => ($action == 'add_section') ? true : $communication_section['CommunicationSection']['aag_member_no']
                )
            );
            ?>
        </div>
    </div>
    <div class="aag-subtitle m-top-1 clear">
        <?php echo __t('Distributor.Profile'); ?>
    </div>
    <div class="cnt-two-columns">
        <div class="cnt-form-inputs-max-width">
            <div>
                <div class="aag-subtitle" style="font-weight: normal;">
                    <?php echo __t('Garage.Garage'); ?>
                </div>
                <?php
                if (!empty($garage_positions)) {
                    foreach ($garage_positions as $position) {
                        $valor = true;
                        if ($action == 'edit_section') {
                            $valor = false;
                            if (isset($communications_sections_positions)) {
                                foreach ($communications_sections_positions as $key => $communication_position) {
                                    if (intval($position['Position']['id']) == $communication_position['CommunicationSectionPosition']['position_id']) {
                                        $valor = true;
                                    }
                                }
                            }
                        }
                ?>
                        <div>
                            <?php
                            echo $this->Form->input(
                                'GaragePosition.' . $position['Position']['id'],
                                array(
                                    'type' => 'checkbox',
                                    'label' => __t($position['Position']['name' . __s()]),
                                    'div' => false,
                                    'checked' => $valor
                                )
                            ); ?>
                        </div>
                <?php
                    }
                }
                ?>
            </div>
        </div>
        <div>
            <div class="aag-subtitle" style="font-weight: normal;">
                <?php echo __t('Distributor.Distributor') ?>
            </div>
            <div class="cnt-form-inputs-max-width">
                <?php
                if (!empty($distributor_positions)) {
                    foreach ($distributor_positions as $position) {
                        $valor = true;
                        if ($action == 'edit_section') {
                            $valor = false;
                            if (isset($communications_sections_positions)) {
                                foreach ($communications_sections_positions as $key => $communication_position) {
                                    if (intval($position['Position']['id']) == $communication_position['CommunicationSectionPosition']['position_id']) {
                                        $valor = true;
                                    }
                                }
                            }
                        }
                ?>
                        <div>
                            <?php
                            echo $this->Form->input(
                                'DistributorPosition.' . $position['Position']['id'],
                                array(
                                    'type' => 'checkbox',
                                    'label' => __t($position['Position']['name' . __s()]),
                                    'div' => false,
                                    'checked' => $valor
                                )
                            );
                            ?>
                        </div>
                <?php
                    }
                }
                ?>
            </div>
        </div>
    </div>
    <div class="aag-subtitle m-top-1 clear">
        <?php echo __t('Activity.Activity'); ?>
    </div>
    <div class="cnt-form-inputs-max-width m-bottom-1">
        <?php
        if (!empty($customers_activities)) {
            foreach ($customers_activities as $activity_id => $activity_name) {
                $valor = true;
                if ($action == 'edit_section') {
                    $valor = false;
                    if (isset($communications_sections_activities)) {
                        foreach ($communications_sections_activities as $key => $communication_activity) {
                            if ($activity_id == $communication_activity['CommunicationSectionCustomerActivity']['customer_activity_id']) {
                                $valor = true;
                            }
                        }
                    }
                }
        ?>
                <div>
                    <?php
                    echo $this->Form->input(
                        'Activity.' . $activity_id,
                        array(
                            'type' => 'checkbox',
                            'label' => __t($activity_name),
                            'div' => false,
                            'checked' => $valor
                        )
                    );
                    ?>
                </div>
            <?php
            }
            ?>
            <div class="medium-2 columns end">
                <?php
                echo $this->Form->input(
                    'without_activity',
                    array(
                        'type' => 'checkbox',
                        'label' => __t('Communication.Without_activity'),
                        'div' => false,
                    )
                ); ?>
            </div>
        <?php
        }
        ?>
    </div>
</div>
<?php echo $this->Form->end(); ?>
<div style="display: none;" id="cropImageModal" class="reveal-modal background-color-primary" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
    <h4 id="modalTitle"><?php echo __t('Crop.Crop_the_photo'); ?></h4>
    <img id="image-to-crop" alt="crop" src="" />
    <p class="text-center m-0-i p-top-1">
        <button class="aag-button medium green m-0-i tres" id="crop-image"><?php echo __t('General.Save'); ?></button>
    </p>
    <a class="close-modal" data-close aria-label="Close">&#215;</a>
</div>
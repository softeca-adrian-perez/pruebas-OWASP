<?php
echo $this->Html->script('lib/jscolor.min.js', array('block' => 'script'));
echo $this->Html->css('../js/lib/cropper/cropper.css');
echo $this->Html->script('trading-groups.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('lib/cropper/cropper.js?v=' . Configure::read('VERSION_CACHE'));
$action = $this->request->action;
echo $this->Html->script('distributors_networks_types.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Form->create(
    'DistributorNetwork',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data',
        'id' => 'distributor-network-form-id'
    )
);
echo $this->Form->hidden('DistributorNetwork.id');
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        if ($action == ConstantsActionsNames::ADD) {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Menu.Distributors') . ' ' . __t('Menu.Networks'),
                    array(
                        'controller' => 'distributors_networks',
                        'action' => 'home'
                    )
                ),
                __t('General.Add'),
            ));
        } else {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Menu.Distributors') . ' ' . __t('Menu.Networks'),
                    array(
                        'controller' => 'distributors_networks',
                        'action' => 'home'
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
    <div class="aag-title">
        <?php
        if ($action == ConstantsActionsNames::ADD) {
            echo __t('Network.Add_network');
        } else {
            echo __t('Network.Edit_network');
        }
        ?>
    </div>
    <div class="cnt-form-inputs p-top-1">
        <?php
        echo $this->Form->input(
            'DistributorNetwork.name',
            array(
                'type' => 'text',
                'required' => true,
                'label' => __t('Network.Name'),
                'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
            )
        );
        echo $this->Form->input(
            'DistributorNetwork.web',
            array(
                'type' => 'text',
                'required' => true,
                'label' => __t('Network.Web'),
                'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
            )
        ); ?>
    </div>
    <div class="row p-top-1">
        <?php
        if (isset($distributor_network['DistributorNetwork']['image'])) {
        ?>
            <div class=" p-1">
                <img class="trading_image img_table" src="<?php echo FileManager::get_url(FilePaths::NETWORKS_IMAGES_RELATIVE . $distributor_network['DistributorNetwork']['image']); ?>" />
            </div>
        <?php
        }
        ?>
        <div>
            <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo ConstantsFiles::MAX_FILE_SIZE ?>" />
            <?php
            echo $this->Form->input(
                'DistributorNetwork.image',
                array(
                    'id' => 'service-file-add',
                    'class' => 'dragdrop-js',
                    'label' => false,
                    'type' => 'file',
                    'multiple' => false,
                    'before' => '<div class="text-drop">' . __t('Network.Drop_files') . '</div>',
                    'after' => '<div class="ta-center">' . __t('Network.Img_extension') . '</div>',
                    'div' => array(
                        'class' => 'field_file cont-fileWrapper',
                    ),
                    'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                )
            );
            echo $this->Form->hidden(
                'new_image',
                array(
                    'id' => 'new-image-input',
                )
            )
            ?>
        </div>
    </div>
    <div class="cnt-form-inputs p-top-1">
        <?php
        echo $this->Form->input(
            'DistributorNetwork.network_type',
            array(
                'label' => __t('Network.Network_type'),
                'class' => 'select2-multiple',
                'type' => 'select',
                'multiple' => false,
                'empty' => true,
                'options' => $networks_types,
                'id' => 'network-type-select',
                'data-url' => Router::url(array(
                    'controller' => 'distributors_networks',
                    'action' => 'ajax_get_tradings_groups',
                )),
                'data-distributor_network_id' => isset($distributor_network) ? $distributor_network['DistributorNetwork']['id'] : null,
                'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
            )
        ); ?>
    </div>
    <div class="aag-title m-top-1">
        <?php echo __t('Network.Related_trading_group'); ?>
    </div>
    <div class="background-color-primary d-inline-block w-100p p-vertical-1" id="trading-groups-networks">
        <?php echo $this->element('../DistributorsNetworks/Elements/trading_groups'); ?>
    </div>
    <div class="aag-title m-top-1">
        <?php echo __t('Network.Pins'); ?>
    </div>
    <div class="background-color-primary d-inline-block w-100p p-vertical-1">
        <p class="aag-subtitle"><?php echo __t("Network.Preset_or_custom"); ?></p>
        <div class="row ">
            <div class="aag-title" style="width: max-content; gap: 5px; align-items: flex-start;">
                <?php echo __t('Network.Preset') ?>
            </div>
            <div class="pins">
                <?php
                foreach ($presets as $key => $preset) {
                ?>
                    <div class="columns medium-2 large-1 small-6 ta-center end">
                        <div class="clear ta-center" style="padding-top: 10px; padding-bottom: 5px;">
                            <img src="<?php echo FilePaths::PINS_IMAGES_REALTIVE . $preset[0]; ?>">
                            <img src="<?php echo FilePaths::PINS_IMAGES_REALTIVE . $preset[1]; ?>">
                        </div>
                        <div>
                            <span class="d-inline-block">
                                <div class="aag-switch round small">
                                    <input id="Radio_<?php echo $key ?>" type="radio" class="presets" name="RadioGroup" value="<?php echo $key ?>"
                                        <?php
                                        if (isset($selected_preset) && $selected_preset == $key) {
                                            echo "checked";
                                        }
                                        echo (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? '' : ' disabled ');
                                        ?> />
                                    <label for="Radio_<?php echo $key ?>"></label>
                                </div>
                            </span>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
        <div class="row p-top-1">
            <div class="aag-title">
                <?php echo __t('Network.Custom') ?>
                <span class="d-inline-block m-right-auto">
                    <div class="aag-switch round small">
                        <input id="Radio_Custom" type="radio" name="RadioGroup" value="custom"
                            <?php
                            if (isset($selected_preset) && $selected_preset == false) {
                                echo "checked";
                            }
                            echo (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? '' : ' disabled ');
                            ?> />
                        <label for="Radio_Custom"></label>
                    </div>
                </span>
            </div>
            <div class="d-none" id="custom-container">
                <div class="columns medium-6">
                    <div class="columns medium-12 aag-subtitle">
                        <?php echo __t('Network.Cluster') ?>
                    </div>
                    <?php if (isset($distributor_network['DistributorNetwork']['image_cluster'])) { ?>
                        <div class="medium-12 columns ta-center p-1">
                            <img class="image_cluster p-1"
                                src="<?php echo FilePaths::PINS_IMAGES_REALTIVE . $distributor_network['DistributorNetwork']['image_cluster']; ?>"
                                style="height: 75px">
                        </div>
                    <?php } ?>
                    <div class="medium-12 columns end">
                        <?php
                        echo $this->Form->input(
                            'DistributorNetwork.image_cluster',
                            array(
                                'id' => 'service-file-add',
                                'class' => 'dragdrop-js',
                                'label' => false,
                                'type' => 'file',
                                'multiple' => false,
                                'before' => '<div class="text-drop">' . __t('Network.Drop_files') . '</div>',
                                'after' => '<div class="ta-center">' . __t('Network.Cluster_dimensions') . '</div>',
                                'div' => array(
                                    'class' => 'field_file cont-fileWrapper',
                                ),
                                'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                            )
                        );
                        ?>
                    </div>
                </div>
                <div class="columns medium-6">
                    <div class="columns medium-12 aag-subtitle">
                        <?php echo __t('Network.Pins') ?>
                    </div>
                    <?php if (isset($distributor_network['DistributorNetwork']['image_pin'])) { ?>
                        <div class="medium-12 columns ta-center p-1">
                            <img class="image_pin p-1"
                                src="<?php echo FilePaths::PINS_IMAGES_REALTIVE . $distributor_network['DistributorNetwork']['image_pin']; ?>"
                                style="height: 75px">
                        </div>
                    <?php } ?>
                    <div class="medium-12 columns end">
                        <?php
                        echo $this->Form->input(
                            'DistributorNetwork.image_pin',
                            array(
                                'id' => 'service-file-add',
                                'class' => 'dragdrop-js',
                                'label' => false,
                                'type' => 'file',
                                'multiple' => false,
                                'before' => '<div class="text-drop">' . __t('Network.Drop_files') . '</div>',
                                'after' => '<div class="ta-center">' . __t('Network.Pins_dimensions') . '</div>',
                                'div' => array(
                                    'class' => 'field_file cont-fileWrapper',
                                ),
                                'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                            )
                        );
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo $this->Form->end(); ?>

<div style="display: none;" id="cropImageModal" class="reveal-modal background-color-primary" data-reveal aria-labelledby="modalTitle" aria-hidden="true"
    role="dialog">
    <h4 id="modalTitle"><?php echo __t('Crop.Crop_the_photo'); ?></h4>
    <img id="image-to-crop" alt="crop" src="" />
    <p class="text-center m-0-i p-top-1">
        <button class="aag-button medium green m-0-i tres" id="crop-image"><?php echo __t('General.Save'); ?></button>
    </p>
    <a class="close-modal" data-close aria-label="Close">&#215;</a>
</div>
<script>
    $(window).load(function() {
        $('div.live .MenuColor').css({
            color: '#' + $('#DistributorNetworkMenuColor').val()
        });
        $('div.live .MenuColorActive').css({
            color: '#' + $('#DistributorNetworkMenuColorActive').val()
        });
        $('div.live .MenuBackground').css({
            backgroundColor: '#' + $('#DistributorNetworkMenuBackground').val()
        });
        $('div.live .BackgroundPrimaryColor').css({
            backgroundColor: '#' + $('#DistributorNetworkBackgroundPrimaryColor').val()
        });
        $('div.live .bcfp').css({
            borderColor: '#' + $('#DistributorNetworkBackgroundPrimaryColor').val()
        });
        $('div.live .BackgroundSecondaryColor').css({
            backgroundColor: '#' + $('#DistributorNetworkBackgroundSecondaryColor').val()
        });
        $('div.live .TertiaryColor').css({
            backgroundColor: '#' + $('#DistributorNetworkTertiaryColor').val()
        });
        $('div.live .DistributorNetworkPrimaryColorFont').css({
            color: '#' + $('#DistributorNetworkPrimaryColor').val()
        });
        $('div.live .DistributorNetworkPrimaryColorBack').css({
            backgroundColor: '#' + $('#DistributorNetworkPrimaryColor').val()
        });
        $('div.live .DistributorNetworkSecondaryColorFont').css({
            color: '#' + $('#DistributorNetworkSecondaryColor').val()
        });
        $('div.live .DistributorNetworkFontColorPrimary').css({
            color: '#' + $('#DistributorNetworkFontColorPrimary').val()
        });
        $('div.live .DistributorNetworkFontColorSecondary').css({
            color: '#' + $('#DistributorNetworkFontColorSecondary').val()
        });
        $('div.live .DistributorNetworkFontColorTertiary').css({
            color: '#' + $('#DistributorNetworkFontColorTertiary').val()
        });

        $('input#DistributorNetworkMenuColor').change(function() {
            $('div.live .MenuColor').css({
                color: '#' + $(this).val()
            });
        });
        $('input#DistributorNetworkMenuColorActive').change(function() {
            $('div.live .MenuColorActive').css({
                color: '#' + $(this).val()
            });
        });
        $('input#DistributorNetworkMenuBackground').change(function() {
            $('div.live .MenuBackground').css({
                backgroundColor: '#' + $(this).val()
            });
        });
        $('input#DistributorNetworkBackgroundPrimaryColor').change(function() {
            $('div.live .BackgroundPrimaryColor').css({
                backgroundColor: '#' + $(this).val()
            });
        });
        $('input#DistributorNetworkBackgroundSecondaryColor').change(function() {
            $('div.live .BackgroundSecondaryColor').css({
                backgroundColor: '#' + $(this).val()
            });
        });
        $('input#DistributorNetworkTertiaryColor').change(function() {
            $('div.live .TertiaryColor').css({
                backgroundColor: '#' + $(this).val()
            });
        });
        $('input#DistributorNetworkPrimaryColor').change(function() {
            $('div.live .DistributorNetworkPrimaryColorFont').css({
                color: '#' + $(this).val()
            });
            $('div.live .DistributorNetworkPrimaryColorBack').css({
                backgroundColor: '#' + $(this).val()
            });
        });
        $('input#DistributorNetworkSecondaryColor').change(function() {
            $('div.live .DistributorNetworkSecondaryColorFont').css({
                color: '#' + $(this).val()
            });
        });
        $('input#DistributorNetworkFontColorPrimary').change(function() {
            $('div.live .DistributorNetworkFontColorPrimary').css({
                color: '#' + $(this).val()
            });
        });
        $('input#DistributorNetworkFontColorSecondary').change(function() {
            $('div.live .DistributorNetworkFontColorSecondary').css({
                color: '#' + $(this).val()
            });
        });
        $('input#DistributorNetworkFontColorTertiary').change(function() {
            $('div.live .DistributorNetworkFontColorTertiary').css({
                color: '#' + $(this).val()
            });
        });

    });
</script>
<?php
echo $this->Html->script('lib/jscolor.min.js', array('block' => 'script'));
echo $this->Html->css('../js/lib/cropper/cropper.css');
echo $this->Html->script('lib/cropper/cropper.js?v=' . Configure::read('VERSION_CACHE'));
echo $this->Html->script('networks_types.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
$action = $this->request->action;
if ($action == ConstantsActionsNames::ADD) {
    $training_boolean = false;
}
echo $this->Form->create('Network', array('class' => 'form-horizontal', 'enctype' => 'multipart/form-data', 'id' => 'form_network'));
echo $this->Form->hidden('Network.id');
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        if ($action == ConstantsActionsNames::ADD) {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Network.Networks'),
                    array(
                        'controller' => 'networks',
                        'action' => 'home'
                    )
                ),
                __t('General.Add'),
            ));
        } else {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Network.Networks'),
                    array(
                        'controller' => 'networks',
                        'action' => 'home'
                    )
                ),
                __t('General.Edit')
            ));
        }
        ?>
    </div>
    <div>
        <?php echo $this->element('Comun/form_actions', $cancel_action); ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="aag-title p-bottom-1">
        <?php echo $action == ConstantsActionsNames::ADD ? __t('Network.Add_network') : __t('Network.Edit_network'); ?>
    </div>
    <div class="cnt-form-inputs">
        <div class="two-columns">
            <?php
            echo $this->Form->input(
                'Network.name',
                array(
                    'type' => 'text',
                    'required' => true,
                    'label' => __t('Network.Name'),
                    'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                )
            );
            ?>
        </div>
        <div class="three-columns">
            <?php
            echo $this->Form->input(
                'Network.web',
                array(
                    'type' => 'text',
                    'required' => true,
                    'label' => __t('Network.Web'),
                    'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                )
            ); ?>
        </div>
    </div>
    <div class="row p-top-1">
        <?php if (isset($network['Network']['image'])) { ?>
            <div class="clear-column">
                <img class="trading_image img_table" src="<?php echo FileManager::get_url(FilePaths::NETWORKS_IMAGES_RELATIVE . $network['Network']['image']); ?>" />
            </div>
        <?php } ?>
        <div class="two-columns">
            <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo ConstantsFiles::MAX_FILE_SIZE ?>" />
            <?php
            echo $this->Form->input(
                'Network.image',
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
    <div class="row">
        <?php
        echo $this->Form->input(
            'Network.network_type',
            array(
                'label' => __t('Network.Network_type'),
                'class' => 'select2-multiple',
                'type' => 'select',
                'multiple' => false,
                'empty' => true,
                'options' => $networks_types,
                'id' => 'network-type-select',
                'data-url' => Router::url(array(
                    'controller' => 'networks',
                    'action' => 'ajax_get_tradings_groups',
                )),
                'data-network_id' => isset($network) ? $network['Network']['id'] : null,
                'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
            )
        );
        echo $this->Form->input(
            'aag_region_id',
            array(
                'id' => 'aag-region-select',
                'label' => __t('Garage.Region'),
                'class' => 'select2-multiple input-disabled-region',
                'type' => 'select',
                'options' => $aag_regions,
                'required' => true,
                'disabled' => true,
                'data-role-id' => $user_role_id,
                'data-super-admin' => ConstantsRoles::SUPER_ADMIN
            )
        );
        ?>
        <div class="cnt-select-notify p-left-0 p-right-0" style="padding-bottom: .5rem;">
            <?php
            echo $this->Form->input(
                'NetworkContactList.contact_list_id',
                array(
                    'label' => __t('Task.Contact_lists'),
                    'class' => 'select2-multiple',
                    'type' => 'select',
                    'multiple' => true,
                    'empty' => true,
                    'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                    'selected' => isset($contact_list_selected) ? $contact_list_selected : '',
                    'options' => $contacts_lists,
                )
            );
            ?>
        </div>
        <?php
        if ($network_id != NETWORK_ID_AGN) {
            echo $this->Form->input(
                'ref_code',
                array(
                    'type' => 'text',
                    'required' => false,
                    'label' => __t('Garage.Ref_code'),
                    'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                )
            );
        }
        ?>
    </div>
    <div class="cnt-form-inputs-max-width">
        <label class="center-check p-top-1">
            <?php echo __t('Network.Internal'); ?>
            <div class="aag-switch round small">
                <?php echo $this->Form->input('Network.internal', array(
                    'id' => 'internal',
                    'label' => false,
                    'div' => false,
                    'type' => 'checkbox',
                    'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                )); ?>
                <label for="internal"></label>
            </div>
        </label>
        <?php if (CakeSession::read('Auth.User.aag_region_id') == ConstantsAAGRegionId::BENELUX) { ?>
            <label class="center-check p-top-1">
                <?php echo __t('Network.Loop'); ?>
                <div class="aag-switch round small">
                    <?php echo $this->Form->input('Network.loop', array(
                        'id' => 'loop',
                        'label' => false,
                        'div' => false,
                        'type' => 'checkbox',
                        'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                    )); ?>
                    <label for="loop"></label>
                </div>
            </label>
        <?php } ?>
        <div>
            <?php if (isset($rating) && $rating !== null) { ?>
                <div>
                    <strong><?php echo __t('Review.Reviews') . ': '; ?></strong>
                    <p>
                        <?php
                        for ($i = 1; $i <= 5; $i++) {
                            if ($i <= $rating) {
                        ?>
                                <img src="/img/star_yellow.svg" />
                            <?php } elseif ($i == ceil($rating) && $rating != floor($rating)) { ?>
                                <img src="/img/star_half_yellow.svg" />
                            <?php } else { ?>
                                <img src="/img/star_light.svg" />
                        <?php
                            }
                        }
                        echo '&nbsp;' . $rating . '/5';
                        ?>
                        <br />
                        <strong><?php echo $total_reviews . ' ' ?></strong>
                        <?php echo __t('Review.Reviews'); ?>
                    </p>
                </div>
            <?php } ?>
        </div>
        <?php if (isset($network_id) && in_array($network_id, array(NETWORK_ID_AUTOCARE, NETWORK_ID_UNITED_GARAGE_SERVICE, NETWORK_ID_TOPTRUCK, NETWORK_ID_GEXPERT))) { ?>
            <label class="center-check p-top-1">
                <?php echo __t('Training.Training'); ?>
                <div class="aag-switch round small">
                    <?php echo $this->Form->input('Network.training', array('id' => 'training-boolean', 'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true, 'label' => false, 'div' => false, 'type' => 'checkbox', 'checked' => isset($training_boolean) ? $training_boolean : false)); ?>
                    <label for="training-boolean"></label>
                </div>
            </label>
        <?php } ?>
    </div>
    <div class="row training-div" <?php if ($training_boolean == false) { ?> hidden <?php } ?>>
        <div class="large-4 medium-6 columns end">
            <?php echo $this->Form->input('credit', array('type' => 'number', 'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true, 'required' => true, 'label' => __t('Credits.Credits'), 'id' => 'credit')); ?>
        </div>
        <?php
        echo $this->Form->input(
            'date_restarting_credit',
            array(
                'class' => 'fecha-js from-js clear_field',
                'type' => 'text',
                'required' => true,
                'div' => array(
                    'class' => 'datepicker datepicker-label-block'
                ),
                'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                'label' => __t('Training.Date_of_restarting_credit'),
                'id' => 'date_restarting_credit',
            )
        );
        ?>
    </div>
    <br />
    <div class="aag-title clear">
        <?php echo __t('Network.Related_trading_group'); ?>
    </div>
    <div id="trading-groups-networks">
        <?php echo $this->element('../Networks/Elements/trading_groups'); ?>
    </div>
    <div class="aag-title clear m-top-1">
        <?php echo __t('Network.Pins'); ?>
    </div>
    <div>
        <?php echo __t("Network.Preset_or_custom"); ?>
    </div>
    <div class="aag-subtitle">
        <?php echo __t('Network.Preset'); ?>
    </div>
    <div class="cnt-pins">
        <?php foreach ($presets as $key => $preset) { ?>
            <label for="Radio_<?php echo $key ?>">
                <div>
                    <img src="<?php echo FilePaths::PINS_IMAGES_REALTIVE . $preset[0]; ?>">
                    <img src="<?php echo FilePaths::PINS_IMAGES_REALTIVE . $preset[1]; ?>">
                </div>
                <div class="aag-switch round small">
                    <input id="Radio_<?php echo $key ?>" type="radio" class="presets" name="RadioGroup" value="<?php echo $key ?>" <?php if (isset($selected_preset) && $selected_preset == $key) {
                                                                                                                                        echo "checked";
                                                                                                                                    }
                                                                                                                                    if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN) {
                                                                                                                                        echo "disabled";
                                                                                                                                    } ?> />
                    <label for="Radio_<?php echo $key ?>"></label>
                </div>
            </label>
        <?php } ?>
        <label class="center-check m-top-1" style="height: 100%;">
            <?php echo __t('Network.Custom'); ?>
            <div class="aag-switch round small">
                <input id="Radio_Custom" type="radio" name="RadioGroup" value="custom" <?php if (isset($selected_preset) && $selected_preset == false) {
                                                                                            echo "checked";
                                                                                        }
                                                                                        if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN) {
                                                                                            echo "disabled";
                                                                                        } ?> />
                <label for="Radio_Custom"></label>
            </div>
        </label>
    </div>
    <div class="d-none" id="custom-container">
        <div class="cnt-two-columns">
            <div>
                <div class="aag-subtitle">
                    <?php echo __t('Network.Cluster'); ?>
                </div>
                <?php if (isset($network['Network']['image_cluster'])) { ?>
                    <div class="ta-center p-bottom-1">
                        <img class="image_cluster" src="<?php echo FileManager::get_url(FilePaths::PINS_IMAGES_REALTIVE . $network['Network']['image_cluster']); ?>" style="height: 50px;" />
                    </div>
                <?php
                }
                echo $this->Form->input(
                    'Network.image_cluster',
                    array(
                        'id' => 'service-file-add',
                        'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                        'class' => 'dragdrop-js dragdrop-multiple-js',
                        'label' => false,
                        'type' => 'file',
                        'multiple' => false,
                        'before' => '<div class="text-drop">' . __t('Network.Drop_files') . '</div>',
                        'after' => '<div class="ta-center">' . __t('Network.Cluster_dimensions') . '</div>',
                        'div' => array(
                            'class' => 'field_file cont-fileWrapper',
                        ),
                    )
                );
                ?>
            </div>
            <div>
                <div class="aag-subtitle">
                    <?php echo __t('Network.Pins'); ?>
                </div>
                <?php if (isset($network['Network']['image_pin'])) { ?>
                    <div class="ta-center p-bottom-1">
                        <img class="image_pin" src="<?php echo FileManager::get_url(FilePaths::PINS_IMAGES_REALTIVE . $network['Network']['image_pin']); ?>" style="height: 50px;" />
                    </div>
                <?php
                }
                echo $this->Form->input(
                    'Network.image_pin',
                    array(
                        'id' => 'service-file-add',
                        'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                        'class' => 'dragdrop-js dragdrop-multiple-js',
                        'label' => false,
                        'type' => 'file',
                        'multiple' => false,
                        'before' => '<div class="text-drop">' . __t('Network.Drop_files') . '</div>',
                        'after' => '<div class="ta-center">' . __t('Network.Pins_dimensions') . '</div>',
                        'div' => array(
                            'class' => 'field_file cont-fileWrapper',
                        ),
                    )
                );
                ?>
            </div>
        </div>
    </div>
    <div class="aag-title m-top-1 clear">
        <?php echo __t('Network.Styles'); ?>
    </div>
    <?php if ($action == ConstantsActionsNames::ADD) { ?>
        <div class="cnt-color-choose">
            <?php
            echo $this->Form->input(
                'Network.menu_background_color',
                array(
                    'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                    'label' => __t('Network.Style_body_background'),
                    'type' => 'text',
                    'class' => 'd-inline-block jscolor',
                    'id' => 'MenuBackground',
                    'data-default' => 'E4E4E4',
                    'value' => 'E4E4E4',
                )
            );
            echo $this->Form->input(
                'Network.primary_background_color',
                array(
                    'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                    'label' => __t('Network.Style_primary_color_background'),
                    'type' => 'text',
                    'class' => 'd-inline-block jscolor',
                    'id' => 'PrimaryBackgroundColor',
                    'data-default' => 'FFFFFF',
                    'value' => 'FFFFFF',
                )
            );
            echo $this->Form->input(
                'Network.secondary_background_color',
                array(
                    'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                    'label' => __t('Network.Style_secondary_color_background'),
                    'type' => 'text',
                    'class' => 'd-inline-block jscolor',
                    'data-default' => 'F5F4F7',
                    'id' => 'SecondaryBackgroundColor',
                    'value' => 'F5F4F7',
                )
            );
            echo $this->Form->input(
                'Network.font_default_color',
                array(
                    'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                    'label' => __t('Network.Style_font_color'),
                    'type' => 'text',
                    'class' => 'd-inline-block jscolor',
                    'data-default' => '252525',
                    'id' => 'FontColor',
                    'value' => '252525',
                )
            );
            ?>
        </div>
        <div class="cnt-color-choose">
            <div class="double">
                <?php
                echo $this->Form->input(
                    'Network.primary_color',
                    array(
                        'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                        'label' => __t('Network.Style_primary_color'),
                        'type' => 'text',
                        'class' => 'd-inline-block jscolor',
                        'data-default' => '085D9C',
                        'id' => 'PrimaryColor',
                        'value' => '085D9C',
                    )
                );
                echo $this->Form->input(
                    'Network.primary_font_color',
                    array(
                        'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                        'label' => __t('Network.Style_primary_font_color'),
                        'type' => 'text',
                        'class' => 'd-inline-block jscolor',
                        'id' => 'PrimaryFontColor',
                        'data-default' => 'FFFFFF',
                        'value' => 'FFFFFF',
                    )
                );
                ?>
            </div>
            <div class="double">
                <?php
                echo $this->Form->input(
                    'Network.secondary_color',
                    array(
                        'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                        'label' => __t('Network.Style_secondary_color'),
                        'type' => 'text',
                        'class' => 'd-inline-block jscolor',
                        'id' => 'SecondaryColor',
                        'data-default' => '2197E6',
                        'value' => '2197E6',
                    )
                );
                echo $this->Form->input(
                    'Network.secondary_font_color',
                    array(
                        'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                        'label' => __t('Network.Style_secondary_font_color'),
                        'type' => 'text',
                        'class' => 'd-inline-block jscolor',
                        'id' => 'SecondaryFontColor',
                        'data-default' => 'FFFFFF',
                        'value' => 'FFFFFF',
                    )
                );
                ?>
            </div>
            <div class="double">
                <?php
                echo $this->Form->input(
                    'Network.tertiary_color',
                    array(
                        'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                        'label' => __t('Network.Style_tertiary_color'),
                        'type' => 'text',
                        'class' => 'd-inline-block jscolor',
                        'id' => 'TertiaryColor',
                        'data-default' => '90A8BE',
                        'value' => '90A8BE',
                    )
                );
                echo $this->Form->input(
                    'Network.tertiary_font_color',
                    array(
                        'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                        'label' => __t('Network.Style_tertiary_font_color'),
                        'type' => 'text',
                        'class' => 'd-inline-block jscolor',
                        'id' => 'TertiaryFontColor',
                        'data-default' => 'FFFFFF',
                        'value' => 'FFFFFF',
                    )
                );
                ?>
            </div>
            <div class="double">
                <?php
                echo $this->Form->input(
                    'Network.quaternary_color',
                    array(
                        'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                        'label' => __t('Network.Style_quaternary_color'),
                        'type' => 'text',
                        'class' => 'd-inline-block jscolor',
                        'id' => 'QuaternaryColor',
                        'data-default' => 'E8E8E8',
                        'value' => 'E8E8E8',
                    )
                );
                echo $this->Form->input(
                    'Network.quaternary_font_color',
                    array(
                        'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                        'label' => __t('Network.Style_quaternary_font_color'),
                        'type' => 'text',
                        'class' => 'd-inline-block jscolor',
                        'id' => 'QuaternaryFontColor',
                        'data-default' => 'FFFFFF',
                        'value' => 'FFFFFF',
                    )
                );
                ?>
            </div>
        </div>
        <div class="cnt-color-choose">
            <?php
            echo $this->Form->input(
                'color_exito',
                array(
                    'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                    'label' => __t('Network.Style_color_success'),
                    'type' => 'text',
                    'class' => 'd-inline-block jscolor',
                    'id' => 'ColorExito',
                    'data-default' => '5DBC56',
                    'value' => '5DBC56',
                )
            );
            echo $this->Form->input(
                'color_fallo',
                array(
                    'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                    'label' => __t('Network.Style_color_error'),
                    'type' => 'text',
                    'class' => 'd-inline-block jscolor',
                    'data-default' => 'FE472F',
                    'id' => 'ColorFallo',
                    'value' => 'FE472F',
                )
            );
            echo $this->Form->input(
                'color_informacion',
                array(
                    'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                    'label' => __t('Network.Style_color_info'),
                    'type' => 'text',
                    'class' => 'd-inline-block jscolor',
                    'data-default' => 'F27B4D',
                    'id' => 'ColorInformacion',
                    'value' => 'F27B4D',
                )
            );
            ?>
        </div>
    <?php } else { ?>
        <div class="cnt-color-choose">
            <?php
            echo $this->Form->input(
                'Network.menu_background_color',
                array(
                    'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                    'label' => __t('Network.Style_body_background'),
                    'type' => 'text',
                    'class' => 'd-inline-block jscolor',
                    'id' => 'MenuBackground',
                    'data-default' => 'E4E4E4'
                )
            );
            echo $this->Form->input(
                'Network.primary_background_color',
                array(
                    'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                    'label' => __t('Network.Style_primary_color_background'),
                    'type' => 'text',
                    'class' => 'd-inline-block jscolor',
                    'id' => 'PrimaryBackgroundColor',
                    'data-default' => 'FFFFFF'
                )
            );
            echo $this->Form->input(
                'Network.secondary_background_color',
                array(
                    'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                    'label' => __t('Network.Style_secondary_color_background'),
                    'type' => 'text',
                    'class' => 'd-inline-block jscolor',
                    'data-default' => 'F5F4F7',
                    'id' => 'SecondaryBackgroundColor',
                )
            );
            echo $this->Form->input(
                'Network.font_default_color',
                array(
                    'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                    'label' => __t('Network.Style_font_color'),
                    'type' => 'text',
                    'class' => 'd-inline-block jscolor',
                    'data-default' => '252525',
                    'id' => 'FontColor',
                )
            );
            ?>
        </div>
        <div class="cnt-color-choose">
            <div class="double">
                <?php
                echo $this->Form->input(
                    'Network.primary_color',
                    array(
                        'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                        'label' => __t('Network.Style_primary_color'),
                        'type' => 'text',
                        'class' => 'd-inline-block jscolor',
                        'data-default' => '085D9C',
                        'id' => 'PrimaryColor',
                    )
                );
                echo $this->Form->input(
                    'Network.primary_font_color',
                    array(
                        'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                        'label' => __t('Network.Style_primary_font_color'),
                        'type' => 'text',
                        'class' => 'd-inline-block jscolor',
                        'id' => 'PrimaryFontColor',
                        'data-default' => 'FFFFFF'
                    )
                );
                ?>
            </div>
            <div class="double">
                <?php
                echo $this->Form->input(
                    'Network.secondary_color',
                    array(
                        'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                        'label' => __t('Network.Style_secondary_color'),
                        'type' => 'text',
                        'class' => 'd-inline-block jscolor',
                        'id' => 'SecondaryColor',
                        'data-default' => '2197E6',
                    )
                );
                echo $this->Form->input(
                    'Network.secondary_font_color',
                    array(
                        'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                        'label' => __t('Network.Style_secondary_font_color'),
                        'type' => 'text',
                        'class' => 'd-inline-block jscolor',
                        'id' => 'SecondaryFontColor',
                        'data-default' => 'FFFFFF'
                    )
                );
                ?>
            </div>
            <div class="double">
                <?php
                echo $this->Form->input(
                    'Network.tertiary_color',
                    array(
                        'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                        'label' => __t('Network.Style_tertiary_color'),
                        'type' => 'text',
                        'class' => 'd-inline-block jscolor',
                        'id' => 'TertiaryColor',
                        'data-default' => '90A8BE',
                    )
                );
                echo $this->Form->input(
                    'Network.tertiary_font_color',
                    array(
                        'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                        'label' => __t('Network.Style_tertiary_font_color'),
                        'type' => 'text',
                        'class' => 'd-inline-block jscolor',
                        'id' => 'TertiaryFontColor',
                        'data-default' => 'FFFFFF',
                    )
                );
                ?>
            </div>
            <div class="double">
                <?php
                echo $this->Form->input(
                    'Network.quaternary_color',
                    array(
                        'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                        'label' => __t('Network.Style_quaternary_color'),
                        'type' => 'text',
                        'class' => 'd-inline-block jscolor',
                        'id' => 'QuaternaryColor',
                        'data-default' => 'E8E8E8',
                    )
                );
                echo $this->Form->input(
                    'Network.quaternary_font_color',
                    array(
                        'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                        'label' => __t('Network.Style_quaternary_font_color'),
                        'type' => 'text',
                        'class' => 'd-inline-block jscolor',
                        'id' => 'QuaternaryFontColor',
                        'data-default' => 'FFFFFF',
                    )
                );
                ?>
            </div>
        </div>
        <div class="cnt-color-choose">
            <?php
            echo $this->Form->input(
                'color_exito',
                array(
                    'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                    'label' => __t('Network.Style_color_success'),
                    'type' => 'text',
                    'class' => 'd-inline-block jscolor',
                    'id' => 'ColorExito',
                    'data-default' => '5DBC56'
                )
            );
            echo $this->Form->input(
                'color_fallo',
                array(
                    'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                    'label' => __t('Network.Style_color_error'),
                    'type' => 'text',
                    'class' => 'd-inline-block jscolor',
                    'data-default' => 'FE472F',
                    'id' => 'ColorFallo',
                )
            );
            echo $this->Form->input(
                'color_informacion',
                array(
                    'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                    'label' => __t('Network.Style_color_info'),
                    'type' => 'text',
                    'class' => 'd-inline-block jscolor',
                    'data-default' => 'F27B4D',
                    'id' => 'ColorInformacion',
                )
            );
            ?>
        </div>
    <?php } ?>
    <div class="row">
        <br />
        <?php
        if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
            echo $this->Html->link(__t('Network.Reset_values'), array(), array('class' => 'aag-button small m-0', 'id' => 'btn-reset',));
        }
        ?>
        <div class="live">
            <div class="mini-web MenuBackground FontColor">
                <img src="/img/chrome.png" alt="Chrome">
                <div>
                    <div class="BackgroundPrimaryColor">
                        <div class="BackgroundSecondaryColor PrimaryColorForLetter">
                            <svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512">
                                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-miterlimit="10" stroke-width="32" d="M80 160h352M80 256h352M80 352h352"></path>
                            </svg>
                            <img class="BackgroundPrimaryColor" src="/img/aag.svg" alt="Logo" title="Alliance" style="width: 175px" />
                        </div>
                        <div class="BackgroundPrimaryColor">
                            <div class="BackgroundSecondaryColor"><?php echo __t('General.Header_item') . ' 1'; ?></div>
                            <div class="BackgroundSecondaryColor"><?php echo __t('General.Header_item') . ' 2'; ?></div>
                            <div class="BackgroundSecondaryColor">
                                <strong><?php echo __t('General.Line') . ' 1'; ?></strong>
                                <?php echo __t('General.Line') . ' 2'; ?>
                            </div>
                        </div>
                        <div class="BackgroundSecondaryColor">
                            <ul>
                                <li class="TertiaryColorForLetter"><span class="aag-icon-casa"></span><?php echo __t('General.Home'); ?></li>
                                <li class="active SecondaryColorForLetter BackgroundPrimaryColor"><span class="aag-icon-reunion"></span><?php echo __t('General.Link') . ' 1'; ?></li>
                                <li class="TertiaryColorForLetter"><span class="aag-icon-garage"></span><?php echo __t('General.Link') . ' 2'; ?></li>
                                <li class="TertiaryColorForLetter"><span class="aag-icon-garaje-redes"></span><?php echo __t('General.Link') . ' 3'; ?></li>
                                <li class="TertiaryColorForLetter"><span class="aag-icon-garaje-redes"></span><?php echo __t('General.Link') . ' 4'; ?></li>
                                <li class="TertiaryColorForLetter"><span class="aag-icon-distribuidores-redes"></span><?php echo __t('General.Link') . ' 5'; ?></li>
                                <li class="TertiaryColorForLetter"><span class="aag-icon-acuerdo"></span><?php echo __t('General.Link') . ' 6'; ?></li>
                                <li class="TertiaryColorForLetter"><span class="aag-icon-atril"></span><?php echo __t('General.Link') . ' 7'; ?></li>
                                <li class="TertiaryColorForLetter"><span class="aag-icon-fichero"></span><?php echo __t('General.Link') . ' 8'; ?></li>
                                <li class="TertiaryColorForLetter"><span class="aag-icon-mas-usuarios"></span><?php echo __t('General.Link') . ' 9'; ?></li>
                            </ul>
                        </div>
                        <div class="BackgroundSecondaryColor">
                            <div>
                                <div class="cnt-breadcrumb BackgroundSecondaryColor">
                                    <div>
                                        <ul class="breadcrumbs">
                                            <li>
                                                <a href="" class="PrimaryColorForLetter"><?php echo __t('General.Page'); ?></a>
                                            </li>
                                            <li class="current"><?php echo __t('General.Home'); ?></li>
                                        </ul>
                                    </div>
                                    <div>
                                        <a class="aag-button PrimaryColor PrimaryFontColor small">
                                            <?php echo __t('General.Button') . ' 1'; ?>
                                        </a>
                                        <a class="aag-button SecondaryColor SecondaryFontColor small">
                                            <?php echo __t('General.Button') . ' 2'; ?>
                                        </a>
                                        <a class="aag-button TertiaryColor TertiaryFontColor small">
                                            <?php echo __t('General.Button') . ' 3'; ?>
                                        </a>
                                        <a class="aag-button QuaternaryColor QuaternaryFontColor small">
                                            <?php echo __t('General.Button') . ' 4'; ?>
                                        </a>
                                        &nbsp;&nbsp;&nbsp;&nbsp;
                                        <a style="color: #fff;" class="aag-button ColorFallo small">
                                            <?php echo __t('Error.Error'); ?>
                                        </a>
                                        <a style="color: #fff;" class="aag-button ColorInformacion small">
                                            <?php echo __t('General.Info'); ?>
                                        </a>
                                        <a style="color: #fff;" class="aag-button ColorExito small">
                                            <?php echo __t('General.Success'); ?>
                                        </a>
                                    </div>
                                </div>
                                <div class="cnt-data BackgroundPrimaryColor">
                                    <div class="cnt-form-search BackgroundSecondaryColor" action="/app/paginator_size/distributors/home" autocomplete="off" novalidate="novalidate" method="post" accept-charset="utf-8">
                                        <div class="cnt-form-search-title PrimaryColor PrimaryFontColor">
                                            <?php echo __t('General.Items'); ?>
                                        </div>
                                        <div class="cnt-form-inputs">
                                            <div class="input text">
                                                <label><?php echo __t('General.Input') . ' 1'; ?></label>
                                                <input class="QuaternaryInputColor" type="text" />
                                            </div>
                                            <div class="input text">
                                                <label><?php echo __t('General.Input') . ' 2'; ?></label>
                                                <input class="QuaternaryInputColor" type="text" />
                                            </div>
                                        </div>
                                        <div class="cnt-form-search-buttons">
                                            <button type="submit" class=" aag-button small PrimaryColor PrimaryFontColor">
                                                <?php echo __t('General.Search'); ?>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="cnt-legend">
                                        <div>
                                            <?php echo __t('General.Table_legend'); ?>
                                        </div>
                                        <div class="d-inline-block">
                                            <span class="icon-legend c-exito ColorExito"></span><?php echo __t('General.Success'); ?>
                                        </div>
                                        <div>
                                            <span class="icon-legend c-informacion ColorInformacion"></span><?php echo __t('General.Info'); ?>
                                        </div>
                                        <div>
                                            <span class="icon-legend c-fallo ColorFallo"></span><?php echo __t('Error.Error'); ?>
                                        </div>
                                    </div>
                                    <div class="p-top-1">
                                        <div>
                                            <div class="o-auto">
                                                <table class="table-tracking">
                                                    <thead>
                                                        <tr>
                                                            <th><?php echo __t('General.Column') . ' 1'; ?></th>
                                                            <th><?php echo __t('General.Column') . ' 2'; ?></th>
                                                            <th><?php echo __t('General.Column') . ' 3'; ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>1234</td>
                                                            <td><a class="c-primary"><?php echo __t('General.Info'); ?></a></td>
                                                            <td><?php echo __t('General.More_info'); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="BackgroundSecondaryColor">5678</td>
                                                            <td class="BackgroundSecondaryColor"><a class="c-primary"><?php echo __t('General.Info'); ?></a></td>
                                                            <td class="BackgroundSecondaryColor"><?php echo __t('General.More_info'); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td>9012</td>
                                                            <td><a class="c-primary"><?php echo __t('General.Info'); ?></a></td>
                                                            <td><?php echo __t('General.More_info'); ?></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <br />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
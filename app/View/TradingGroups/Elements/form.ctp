<?php
$action = $this->request->action;
echo $this->Html->script('lib/jscolor.min.js', array('block' => 'script'));
echo $this->Html->script('trading-groups.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->css('../js/lib/cropper/cropper.css');
echo $this->Html->script('lib/cropper/cropper.js?v=' . Configure::read('VERSION_CACHE'));
echo $this->Form->create(
    'TradingGroup',
    array(
        'id' => 'form',
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data'
    )
);
echo $this->Form->hidden('TradingGroup.id');
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        if ($action == ConstantsActionsNames::ADD) {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('TradingGroup.Trading_groups'),
                    array(
                        'controller' => 'trading_groups',
                        'action' => 'home'
                    )
                ),
                __t('General.Add'),
            ));
        } else {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('TradingGroup.Trading_groups'),
                    array(
                        'controller' => 'trading_groups',
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
    <div class="aag-title m-bottom-1">
        <?php
        if ($action == ConstantsActionsNames::ADD) {
            echo __t('TradingGroup.New_trading_group');
        } else {
            echo __t('TradingGroup.Edit_trading_group');
        }
        ?>
    </div>
    <div class="cnt-form-inputs">
        <div class="two-columns">
            <?php
            echo $this->Form->input(
                'TradingGroup.name',
                array(
                    'type' => 'text',
                    'required' => true,
                    'label' => __t('TradingGroup.Name'),
                )
            );
            ?>
        </div>
        <div class="three-columns">
            <?php
            echo $this->Form->input(
                'TradingGroup.web',
                array(
                    'type' => 'text',
                    'required' => false,
                    'label' => __t('TradingGroup.Web'),
                )
            );
            ?>
        </div>
        <div class="clear-column all-columns">
            <div class="type_option" id="yes_no_option">
                <div class="cnt-form-inputs-max-width" style="flex-direction: row;">
                    <label class="center-check" style="gap: 15px">
                        <?php echo __t('General.LV'); ?>
                        <div class="aag-switch round small">
                            <input id="lv" type="radio" name="data[TradingGroup][is_cv]" value="0" <?php echo (isset($trading_group) && $trading_group['TradingGroup']['is_cv'] == ConstantsBooleans::NO) ? 'checked' : '' ?> />
                            <label for="lv"></label>
                        </div>
                    </label>
                    <label class="center-check" style="gap: 15px">
                        <?php echo __t('General.CV'); ?>
                        <div class="aag-switch round small">
                            <input id="cv" type="radio" name="data[TradingGroup][is_cv]" value="1" <?php echo (isset($trading_group) && $trading_group['TradingGroup']['is_cv'] == ConstantsBooleans::YES) ? 'checked' : '' ?> />
                            <label for="cv"></label>
                        </div>
                    </label>
                    <label class="center-check" style="gap: 15px">
                        <?php echo __t('General.Lv_cv'); ?>
                        <div class="aag-switch round small">
                            <input id="lv_cv" type="radio" name="data[TradingGroup][is_cv]" value="" <?php echo (isset($trading_group) && $trading_group['TradingGroup']['is_cv'] === null) ? 'checked' : '' ?> />
                            <label for="lv_cv"></label>
                        </div>
                    </label>
                </div>
            </div>
        </div>
        <div class="two-columns">
            <?php
            echo $this->Form->input(
                'TradingGroup.aag_region_id',
                array(
                    'label' => __t('General.Region'),
                    'class' => 'select2-multiple',
                    'type' => 'select',
                    'multiple' => false,
                    'required' => true,
                    'options' => $aag_regions,
                    'empty' => $user_role_id == ConstantsRoles::SUPER_ADMIN ? true : false,
                    'disabled' => $user_role_id == ConstantsRoles::SUPER_ADMIN ? false : true,
                    'value' => isset($trading_group) ? $trading_group['TradingGroup']['aag_region_id'] : $user_aag_region_id,
                    'id' => 'aag-region-select',
                )
            ); ?>
        </div>
    </div>
    <div class="row p-top-1">
        <?php
        if (isset($trading_group['TradingGroup']['image'])) {
        ?>
            <div class="clear-column">
                <img class="trading_image img_table" src="<?php echo FileManager::get_url(FilePaths::TRADING_GROUP_IMAGES_RELATIVE . $trading_group['TradingGroup']['image']); ?>">
            </div>
        <?php
        }
        ?>
        <div class="two-columns">
            <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo ConstantsFiles::MAX_FILE_SIZE ?>" />
            <?php
            echo $this->Form->input(
                'TradingGroup.image',
                array(
                    'id' => 'service-file-add',
                    'class' => 'dragdrop-js',
                    'label' => __t('General.Image'),
                    'type' => 'file',
                    'multiple' => false,
                    'before' => '<div class="text-drop">' . __t('TradingGroup.Drop_files') . '</div>',
                    'div' => array(
                        'class' => 'field_file cont-fileWrapper',
                    ),
                )
            );
            echo $this->Form->hidden('new_image', array('id' => 'new-image-input'));
            ?>
        </div>
    </div>
    </br>
    <div class="aag-title m-top-1">
        <?php echo __t('Network.Styles'); ?>
    </div>
    <div class="cnt-color-choose">
        <?php
        echo $this->Form->input(
            'menu_background_color',
            array(
                'label' => __t('Network.Style_menu_background'),
                'type' => 'text',
                'class' => 'd-inline-block jscolor',
                'id' => 'MenuBackground',
                'default' => 'E4E4E4'
            )
        );
        echo $this->Form->input(
            'primary_background_color',
            array(
                'label' => __t('Network.Style_primary_color_background'),
                'type' => 'text',
                'class' => 'd-inline-block jscolor',
                'id' => 'PrimaryBackgroundColor',
                'default' => 'FFFFFF'
            )
        );
        echo $this->Form->input(
            'secondary_background_color',
            array(
                'label' => __t('Network.Style_secondary_color_background'),
                'type' => 'text',
                'class' => 'd-inline-block jscolor',
                'default' => 'F5F4F7',
                'id' => 'SecondaryBackgroundColor',
            )
        );
        echo $this->Form->input(
            'font_default_color',
            array(
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
                'primary_color',
                array(
                    'label' => __t('Network.Style_primary_color'),
                    'type' => 'text',
                    'class' => 'd-inline-block jscolor',
                    'default' => '0064AE',
                    'id' => 'PrimaryColor',
                )
            );
            echo $this->Form->input(
                'primary_font_color',
                array(
                    'label' => __t('Network.Style_primary_font_color'),
                    'type' => 'text',
                    'class' => 'd-inline-block jscolor',
                    'id' => 'PrimaryFontColor',
                    'default' => '303030'
                )
            );
            ?>
        </div>
        <div class="double">
            <?php
            echo $this->Form->input(
                'secondary_color',
                array(
                    'label' => __t('Network.Style_secondary_color'),
                    'type' => 'text',
                    'class' => 'd-inline-block jscolor',
                    'id' => 'SecondaryColor',
                    'default' => '7591B0',
                )
            );
            echo $this->Form->input(
                'secondary_font_color',
                array(
                    'label' => __t('Network.Style_secondary_font_color'),
                    'type' => 'text',
                    'class' => 'd-inline-block jscolor',
                    'id' => 'SecondaryFontColor',
                    'default' => '606060'
                )
            );
            ?>
        </div>
        <div class="double">
            <?php
            echo $this->Form->input(
                'tertiary_color',
                array(
                    'label' => __t('Network.Style_tertiary_color'),
                    'type' => 'text',
                    'class' => 'd-inline-block jscolor',
                    'id' => 'TertiaryColor',
                    'default' => 'D3DBE2',
                )
            );
            echo $this->Form->input(
                'color_active',
                array(
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
                'color_disabled',
                array(
                    'label' => __t('Network.Style_quaternary_color'),
                    'type' => 'text',
                    'class' => 'd-inline-block jscolor',
                    'id' => 'QuaternaryColor',
                    'data-default' => 'E8E8E8',
                )
            );
            echo $this->Form->input(
                'menu_color',
                array(
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
                'label' => __t('Network.Style_color_success'),
                'type' => 'text',
                'class' => 'd-inline-block jscolor',
                'id' => 'ColorExito',
                'default' => '5DBC56'
            )
        );
        echo $this->Form->input(
            'color_fallo',
            array(
                'label' => __t('Network.Style_color_error'),
                'type' => 'text',
                'class' => 'd-inline-block jscolor',
                'default' => 'FE472F',
                'id' => 'ColorFallo',
            )
        );
        echo $this->Form->input(
            'color_informacion',
            array(
                'label' => __t('Network.Style_color_info'),
                'type' => 'text',
                'class' => 'd-inline-block jscolor',
                'default' => 'F27B4D',
                'id' => 'ColorInformacion',
            )
        );
        ?>
    </div>
    <div class="m-top-1">
        <?php echo $this->Html->link(__t('Network.Reset_values'), array(), array('class' => 'aag-button small', 'id' => 'btn-reset')); ?>
    </div>
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
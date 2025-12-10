<?php
echo $this->Html->script('recommended_networks.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->css('../js/lib/cropper/cropper.css');
echo $this->Html->script('lib/cropper/cropper.js?v=' . Configure::read('VERSION_CACHE'));
echo $this->Html->script('genarts_families.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('toggle.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('dynamic_lists.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
?>

<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Network.Networks'),
                array(
                    'controller' => 'networks',
                    'action' => 'recommended_networks',
                    $network_id
                )
            ),
            __t('Configuration.Configuration')
        ));
        ?>
    </div>
    <div>
        <?php
        echo $this->Html->link(
            __t('General.Back'),
            CakeSession::read('url_referer'),
            array('class' => 'aag-button medium two')
        );
        ?>
    </div>
</div>

<?php echo $this->element('../Networks/configuration_tabs', array('selected' => 'recommended_networks')); ?>

<div class="cnt-data aag-padding">
    <div class="p-top-1">
        <div>
            <div class="toggle-js cnt-form-toggle-title-secondary opacity1 m-0-i cursor-pointer" data-toggle-id-js="1">
                <?php echo __t('Network.Recommended_networks'); ?>
                <i class="ion-ios-arrow-down arrow-icon-js c-blanco"></i>
            </div>
            <div class="toggle-content-js list-container cnt-dropdown-data" data-toggle-id-js="1">
                <?php echo $this->Form->create(
                    'Network',
                    [
                        'url' => [
                            'controller' => 'networks',
                            'action' => 'recommended_networks',
                            $network_id
                        ],
                        'enctype' => 'multipart/form-data'
                    ]
                );
                ?>
                <table class="table-tracking tabla-responsive">
                    <tbody>
                        <?php foreach ($networks as $network) { ?>
                            <tr>
                                <td class="ta-center">
                                    <img title="<?php echo h($network['Network']['name']); ?>" src="<?php echo FileManager::get_url(FilePaths::NETWORKS_IMAGES_RELATIVE . $network['Network']['image']); ?>" class="img_table" style="max-height:42px">
                                </td>
                                <td>
                                    <?php if ($this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::CREATE_TRADING_GROUP)) {
                                        echo $this->Html->link(
                                            $network['Network']['name'],
                                            array(
                                                'controller' => 'networks',
                                                'action' => 'edit',
                                                $network['Network']['id']
                                            ),
                                            array(
                                                'class' => 'c-primary'
                                            )
                                        );
                                    } else {
                                        echo h($network['Network']['name']);
                                    } ?>
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; justify-content: flex-start; gap: 15px; min-width: 100px;">
                                        <label class="center-check">
                                            <div class="aag-switch round small">
                                                <?php echo $this->Form->input('Network.recommended', array(
                                                    'id' => 'recommended-' . $network['Network']['id'],
                                                    'name' => 'data[Network][' . $network['Network']['id'] . '][recommended]',
                                                    'label' => false,
                                                    'div' => false,
                                                    'type' => 'checkbox',
                                                    'checked' => $recommended_status[$network['Network']['id']] == 1
                                                )); ?>
                                                <label for="recommended-<?php echo $network['Network']['id']; ?>"></label>
                                            </div>
                                        </label>
                                        <?php echo $this->HTML->image(
                                            'edit.png',
                                            array(
                                                'class' => 'edit_button-js',
                                                'data-network_id' => $network['Network']['id'],
                                                'data-url' => Router::url(array(
                                                    'controller' => 'networks',
                                                    'action' => 'modal_recommended_label',
                                                    $network_id,
                                                    $network['Network']['id']
                                                )),
                                                'style' => 'cursor: pointer'
                                            )
                                        ); ?>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <div class="gnm-modal" id="edit-modal-js" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div>
                        <div id="modalRecommended-js"></div>
                    </div>
                </div>
                <?php if (!empty($networks)) { ?>
                <div class="ta-right p-top-1">
                    <button class="aag-button medium green">
                        <?php echo __t('General.Save'); ?>
                    </button>
                </div>
                <?php } ?>
            </div>
            <?php echo $this->Form->end(); ?>
            <div class="d-none" id="config_recommended_networks-js" data-confirmmsg="<?php echo __t('GarageQuoting.Mark_unmark_all_garages_msg'); ?>" data-type="<?php echo 'warning'; ?>" data-yes="<?php echo __t('General.Yes'); ?>" data-no="<?php echo __t('General.No'); ?>" data-url="<?php echo Router::url(array('controller' => 'networks', 'action' => 'ajax_save_garage_recommended')); ?>">
            </div>
        </div>
        <div class="p-top-1 p-bottom-1">
            <div class="toggle-js cnt-form-toggle-title-secondary opacity1 m-0-i cursor-pointer" data-toggle-id-js="2">
                <?php echo __t('General.Recommended') . ' ' . __t('Garage.Garages'); ?>
                <i class="ion-ios-arrow-down arrow-icon-js c-blanco"></i>
            </div>
            <div class="toggle-content-js list-container cnt-dropdown-data" data-toggle-id-js="2">
                <?php
                echo $this->Html->script('garages.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
                echo $this->Form->create(
                    'Search',
                    array(
                        'class' => 'cnt-form-search buscador-js m-top-0',
                        'type' => 'get',
                        'url' => array(
                            'controller' => 'networks',
                            'action' => 'recommended_networks',
                            $network_id
                        ),
                        'id' => 'search-quoting'
                    )
                );
                ?>
                <div class="cnt-form-inputs">
                    <?php
                    echo $this->Form->input(
                        'name',
                        array(
                            'type' => 'text',
                            'class' => 'search_ajax clear_field',
                            'data-url' => Router::url(
                                array(
                                    'controller' => 'garages',
                                    'action' => 'ajax_search_home',
                                    ConstantsTypeSearch::GARAGE
                                )
                            ),
                            'label' => __t('Appointment.Customer'),
                        )
                    );
                    echo $this->Form->input(
                        'trading_group_id',
                        array(
                            'label' => __t('Garage.Trading_group'),
                            'type' => 'select',
                            'class' => 'select2-multiple clear_field',
                            'options' => $trading_groups,
                            'empty' => true,
                            'id' => 'trading-group-id',
                            'multiple' => true
                        )
                    );
                    if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN) {
                        echo $this->Form->input(
                            'aag_region_id',
                            array(
                                'label' => __t('General.Region'),
                                'type' => 'select',
                                'class' => 'select2-multiple clear_field',
                                'options' => $regions,
                                'empty' => true,
                                'default' => CakeSession::read('Auth.User.aag_region_id'),
                                'id' => 'region-id',
                            )
                        );
                    }
                    echo $this->Form->input(
                        'country_id',
                        array(
                            'label' => __t('Garage.Country'),
                            'type' => 'select',
                            'class' => 'select2-multiple clear_field',
                            'options' => $countries,
                            'empty' => true,
                            'multiple' => true
                        )
                    );
                    echo $this->Form->input(
                        'city_id',
                        array(
                            'label' => __t('Garage.City'),
                            'type' => 'select',
                            'class' => 'dynamicSelect2_cities clear_field',
                            'multiple' => true,
                            'empty' => true,
                            'id' => 'city-id',
                            'data-selected-cities' => isset($array_city_name) ? $array_city_name : array(),
                        )
                    );
                    echo $this->Form->input(
                        'network_id',
                        array(
                            'label' => __t('Garage.Network'),
                            'type' => 'select',
                            'class' => 'select2-multiple clear_field',
                            'options' => $listNetworks,
                            'empty' => true,
                            'id' => 'network-id',
                            'multiple' => true
                        )
                    );
                    echo $this->Form->input(
                        'annex_detail_id',
                        array(
                            'label' => __t('Network.Annex_detail'),
                            'type' => 'select',
                            'class' => 'select2-multiple clear_field',
                            'options' => $annex_detail,
                            'empty' => true,
                            'id' => 'annex_detail-id',
                            'multiple' => true
                        )
                    );
                    if ((isset($user['aag_region_id']) && ($user['aag_region_id'] != ConstantsAAGRegionId::BENELUX)) || ($user['role_id'] == ConstantsRoles::SUPER_ADMIN)) {
                        echo $this->Form->input(
                            'g_number_id',
                            array(
                                'type' => 'text',
                                'class' => 'clear_field',
                                'id' => 'g-number-id',
                                'required' => true,
                                'label' => __t('Garage.G_number'),
                            )
                        );
                    }
                    if (
                        $user['role_id'] != ConstantsRoles::BDM_AAG && $user['role_id'] != ConstantsRoles::BDM_TG
                    ) {
                        echo $this->Form->input(
                            'bdm_id',
                            array(
                                'label' => __t('Distributor.BDM'),
                                'type' => 'select',
                                'class' => 'dynamicSelect2_contacts_garages_bdm clear_field',
                                'options' => isset($bdm) ? $bdm : array(),
                                'empty' => true,
                                'id' => 'bdm-id'
                            )
                        );
                    }
                    echo $this->Form->input(
                        'postcode',
                        array(
                            'type' => 'text',
                            'class' => 'clear_field',
                            'empty' => true,
                            'label' => __t('Garage.Postcode'),
                        )
                    );
                    echo $this->Form->input(
                        'service_id',
                        array(
                            'label' => __t('Garage.Services'),
                            'type' => 'select',
                            'class' => 'select2-multiple clear_field',
                            'options' => $services,
                            'empty' => true,
                            'id' => 'service-id',
                            'multiple' => true
                        )
                    );
                    echo $this->Form->input(
                        'distributor_id',
                        array(
                            'label' => __t('AppointmentObjective.Distributor'),
                            'type' => 'select',
                            'class' => 'dynamicSelect2_distributors clear_field',
                            'multiple' => true,
                            'empty' => true,
                            'id' => 'distributor-id-js',
                            'data-selected_distributors' => isset($distributors) ? $distributors : array(),
                        )
                    );
                    echo $this->Form->input(
                        'erp_id',
                        array(
                            'label' => __t('Garage.ERP'),
                            'type' => 'select',
                            'class' => 'select2-multiple clear_field',
                            'options' => $erp_providers,
                            'empty' => true,
                            'id' => 'erp_id-id',
                            'multiple' => true
                        )
                    );
                    ?>
                </div>
                <div class="ampliar-js">
                    <div class="cnt-form-inputs ampliacion-js">
                        <?php
                        echo $this->Form->input(
                            'town',
                            array(
                                'type' => 'text',
                                'class' => 'clear_field',
                                'id' => 'town',
                                'label' => __t('Garage.Town'),
                            )
                        );
                        echo $this->Form->input(
                            'ramps',
                            array(
                                'type' => 'text',
                                'class' => 'clear_field',
                                'id' => 'ramps-id',
                                'required' => true,
                                'label' => __t('Garage.Ramps'),
                            )
                        );
                        echo $this->Form->input(
                            'MOT_bays',
                            array(
                                'type' => 'text',
                                'class' => 'clear_field',
                                'id' => 'MOT-bays-id',
                                'required' => true,
                                'label' => __t('Garage.MOT_bays'),
                            )
                        );
                        echo $this->Form->input(
                            'phone',
                            array(
                                'type' => 'text',
                                'class' => 'clear_field',
                                'id' => 'phone-id',
                                'required' => true,
                                'label' => __t('Garage.Phone'),
                            )
                        );
                        echo $this->Form->input(
                            'vehicle_type_id',
                            array(
                                'label' => __t('Garage.Vehicle_types'),
                                'type' => 'select',
                                'class' => 'select2-multiple clear_field',
                                'options' => $vehicles_types,
                                'empty' => true,
                                'id' => 'vehicle-type-id',
                                'multiple' => true
                            )
                        );
                        echo $this->Form->input(
                            'foundation_year',
                            array(
                                'type' => 'text',
                                'class' => 'clear_field',
                                'id' => 'foundation-year-id',
                                'required' => true,
                                'label' => __t('Garage.Foundation'),
                            )
                        );
                        echo $this->Form->input(
                            'lead_source',
                            array(
                                'label' => __t('Garage.Lead_source'),
                                'type' => 'select',
                                'class' => 'select2-multiple clear_field',
                                'multiple' => false,
                                'empty' => true,
                                'id' => 'lead_source_id',
                                'options' => $lead_sources,
                                'multiple' => true
                            )
                        );
                        echo $this->Form->input(
                            'ref_code',
                            array(
                                'type' => 'text',
                                'class' => 'clear_field',
                                'id' => 'ref-code-id',
                                'required' => true,
                                'label' => __t('Garage.Ref_code'),
                            )
                        );
                        echo $this->Form->input(
                            'external_agreements',
                            array(
                                'label' => __t('Garage.External_agreements'),
                                'type' => 'select',
                                'class' => 'select2-multiple clear_field',
                                'options' => $external_agreements,
                                'empty' => true,
                                'id' => 'external-agreements-id',
                                'multiple' => true
                            )
                        );
                        echo $this->Form->input(
                            'internal_agreements',
                            array(
                                'label' => __t('Garage.Internal_agreements'),
                                'type' => 'select',
                                'class' => 'select2-multiple clear_field',
                                'options' => $internal_agreements,
                                'empty' => true,
                                'id' => 'internal-agreements-id',
                                'multiple' => true
                            )
                        );
                        ?>
                    </div>
                    <div class="cnt-form-search-buttons">
                        <?php
                        echo $this->Form->button(
                            __t('General.Search'),
                            array(
                                'type' => 'submit',
                                'class' => 'aag-button medium'
                            )
                        );
                        ?>
                        <div class="mostrar-ampliado aag-button medium one border-four outlined">
                            <?php echo __t('General.Advanced_search'); ?>
                        </div>
                        <?php
                        echo $this->Form->button(
                            "<span class='aag-icon-escoba'></span>",
                            array(
                                'id' => 'clear_field',
                                'class' => 'aag-button medium four outlined',
                                'escape' => false,
                                'title' => __t('General.Clean_search')
                            )
                        );
                        ?>
                    </div>
                </div>
                <div class="o-auto">
                    <table class="table-tracking" id="families_genarts">
                        <thead>
                            <tr>
                                <th><?php echo $this->Paginator->sort('Garage.name', __t('Appointment.Customer')); ?></th>
                                <th><?php echo __t('Garage.City'); ?></th>
                                <th><?php echo $this->Paginator->sort('Country.name', __t('Garage.Country')); ?></th>
                                <th><?php echo $this->Paginator->sort('Garage.address1', __t('Garage.Address')); ?></th>
                                <th><?php echo $this->Paginator->sort('Garage.phone', __t('Garage.Phone')); ?></th>
                                <th><?php echo $this->Paginator->sort('Garage.ref_code', __t('Garage.Ref_code')); ?></th>
                                <th class="ta-center">
                                    <?php echo __t('General.Recommended'); ?>
                                    <div>
                                        <?php if ($globalRecommendGaragesActives) { ?>
                                            <span class="all-options-js garage-network-recommend-js ion-toggle-filled c-exito icono-grande"></span>
                                        <?php } else { ?>
                                            <span class="all-options-js garage-network-recommend-js ion-toggle c-fallo icono-grande"></span>
                                        <?php } ?>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($garages as $key => $garage) { ?>
                                <tr>
                                    <td>
                                        <?php
                                        echo $this->Html->link(
                                            $garage['Garage']['name'],
                                            array(
                                                'controller' => 'garages_networks',
                                                'action' => 'network_dashboard',
                                                $garage['GarageNetwork']['id']
                                            ),
                                            array(
                                                'class' => 'c-primary'
                                            )
                                        );
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($garage['Garage']['city_id'] && isset($cities[$garage['Garage']['city_id']])) {
                                            echo h($cities[$garage['Garage']['city_id']]);
                                        }  ?>
                                    </td>
                                    <td><?php echo h($garage['Country']['name']); ?></td>
                                    <td><?php echo h($garage['Garage']['address1']); ?></td>
                                    <td><?php echo h($garage['Garage']['phone']); ?></td>
                                    <td><?php echo h($garage['Garage']['ref_code']); ?></td>
                                    <?php
                                    $td_disabled = '';
                                    $toggle_recommended = $garage['GarageNetwork']['recommended'] || $garage['Garage']['recommended_network'] ? 'ion-toggle-filled ' : 'ion-toggle ';
                                    if ($garage['Garage']['recommended_network']) {
                                        $td_disabled = 'toggle-disabled';
                                    } else {
                                        $toggle_recommended .= $garage['GarageNetwork']['recommended'] ? 'c-exito' : 'c-fallo';
                                    }
                                    ?>
                                    <td class="ta-center <?php echo $td_disabled; ?>">
                                        <?php
                                        if ($garage['Garage']['recommended_network']) {
                                        ?>
                                            <span data-tooltip aria-haspopup="true" class="has-tip" title="<?php echo __t('General.Recommended') . ' ' . __t('Config.Network') ?>">
                                                <span class="ico-toggle-garage-network-recommend-js <?php echo $toggle_recommended ?> icono-grande" id="<?php echo 'checkbox_garage-network-recommend_' . $key ?>" data-key="<?php echo $key ?>" data-garage_id="<?php echo $garage['GarageNetwork']['id'] ?>"></span>
                                            </span>
                                        <?php
                                        } else {
                                        ?>
                                            <span class="ico-toggle-garage-network-recommend-js <?php echo $toggle_recommended ?> icono-grande" id="<?php echo 'checkbox_garage-network-recommend_' . $key ?>" data-key="<?php echo $key ?>" data-garage_id="<?php echo $garage['GarageNetwork']['id'] ?>"></span>
                                        <?php
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <?php
                if (count($garages) > 0) {
                    echo $this->element('Comun/paginacion');
                }
                ?>
            </div>
            <?php echo $this->Form->end(); ?>
        </div>
    </div>
</div>

<div style="display: none;" id="cropImageModal" class="reveal-modal background-color-primary" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog" data-options="close_on_background_click:false">
    <h4 id="modalTitle"><?php echo __t('Crop.Crop_the_photo'); ?></h4>
    <img id="image-to-crop" alt="crop" src="" />
    <p class="text-center m-0-i p-top-1">
        <button class="aag-button medium green m-0-i tres" id="crop-image"><?php echo __t('General.Save'); ?></button>
    </p>
    <a class="close-modal" data-close aria-label="Close" id="close_modal">&#215;</a>
</div>

<div style="display: none;" id="cropImageListModal" class="reveal-modal background-color-primary" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog" data-options="close_on_background_click:false">
    <h4 id="modalTitle"><?php echo __t('Crop.Crop_the_photo'); ?></h4>
    <img id="image-list-to-crop" alt="crop" src="" />
    <p class="text-center m-0-i p-top-1">
        <button class="aag-button medium green m-0-i tres" id="crop-image-list"><?php echo __t('General.Save'); ?></button>
    </p>
    <a class="close-modal" data-close aria-label="Close" id="close_modal">&#215;</a>
</div>
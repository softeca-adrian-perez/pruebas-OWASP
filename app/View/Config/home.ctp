<?php
echo $this->Html->script('config.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script(CakeSession::read('GOOGLE_MAPS_API'), array('block' => 'script'));
echo $this->Html->script('provinces_list.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('cities_list.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('cities_form.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Form->create(
    'Config',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data',
        'id' => 'config_form',
        'data-url' => Router::url(array(
            'controller' => 'config',
            'action' => 'ajax_save_config'
        )),
        'data-url-module' => Router::url(array(
            'controller' => 'config',
            'action' => 'ajax_save_module_config'
        )),
        'data-url-all-modules' => Router::url(array(
            'controller' => 'config',
            'action' => 'ajax_save_all_modules_config'
        )),
        'data-active-config-modules' => Router::url(array(
            'controller' => 'config',
            'action' => 'ajax_check_active_modules'
        )),
    )
);
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
            $this->Html->link(
                __t('Configuration.Configuration'),
                array(
                    'controller' => 'config',
                    'action' => 'home'
                )
            ),
            __t('General.Home'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back') ?></a>
        <?php
        echo $this->Html->link(
            __t('General.Restore_defaults'),
            array(
                'controller' => 'config',
                'action' => 'restore_default_values',
            ),
            array('class' => 'aag-button medium restore_default_values-js')
        );
        ?>
    </div>
</div>
<div class="aag-tabs">
    <ul>
        <li id="button_data" class="btn-link active-button cursor-pointer">
            <a><?php echo __t('Configuration.Data'); ?></a>
        </li>
        <?php if ($user_role_id == ConstantsRoles::SUPER_ADMIN) { ?>
            <li id="button_module_access" class="btn-link cursor-pointer">
                <a><?php echo __t('Configuration.Module_access'); ?></a>
            </li>
        <?php } ?>
        <li id="button_config" class="btn-link cursor-pointer">
            <a><?php echo __t('Configuration.Configuration'); ?></a>
        </li>
        <li id="button_tabs" class="btn-link cursor-pointer">
            <a><?php echo __t('Configuration.Tabs'); ?></a>
        </li>
        <li id="button_lists" class="btn-link cursor-pointer">
            <a><?php echo __t('Configuration.Lists'); ?></a>
        </li>
    </ul>
</div>
<div class="cnt-data aag-padding p-vertical-1">
    <div class="aag-title">
        <?php echo __t('Maintenance.Configuration'); ?>
    </div>
    <div id="cuerpo-data" class="cnt-two-columns">
        <?php
        foreach ($aag_config as $key => $section) {
            if ($key == ConstantsSections::GARAGES || $key == ConstantsSections::DISTRIBUTORS) {
        ?>
                <div class="section_table">
                    <div class="aag-subtitle"><?php echo h($section['Name']); ?> </div>
                    <div class="o-auto">
                        <table class="table-tracking table-config">
                            <thead>
                                <tr>
                                    <th style="padding: .5rem .625rem .625rem .625rem;">
                                        <?php echo h($section['Name']); ?>
                                    </th>
                                    <th style="padding: .5rem .625rem .625rem .625rem;" class="ta-center" width="150">
                                        <?php echo __t('General.Active'); ?>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($section['Sections'] as $key_section => $field) { ?>
                                    <tr>
                                        <td style="padding: .5rem .625rem .625rem .625rem;" title="<?php echo h($field['tooltip' . __s()]); ?>">
                                            <span class="section_name unselectable cursor-pointer" data-id='<?php echo 'checkbox_' . $key . '_' . $key_section ?>'>
                                                <?php echo h($field['name' . __s()]); ?>
                                            </span>
                                        </td>
                                        <td style="padding: .5rem .625rem .625rem .625rem;" class="ta-center" title="<?php echo $field['tooltip' . __s()]; ?>">
                                            <?php if ($field['active']) { ?>
                                                <span class="ico-toggle ion-toggle-filled c-exito icono-grande" id="<?php echo 'checkbox_' .  $key . '_' . $key_section ?>" data-config-id="<?php echo $field['id'] ?>"></span>
                                            <?php } else { ?>
                                                <span class="ico-toggle ion-toggle c-fallo icono-grande" id="<?php echo 'checkbox_' .  $key . '_' . $key_section ?>" data-config-id="<?php echo $field['id'] ?>"></span>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <br />
                </div>
        <?php
            }
        }
        ?>
    </div>
    <?php if ($user_role_id == ConstantsRoles::SUPER_ADMIN) { ?>
        <div id="cuerpo-module-access" class="columns medium-12" style="display:none;">
            <div class="columns medium-12 background-color-blanco p-top-1 p-left-0 p-right-0">
                <div>
                    <?php
                    echo $this->Html->link(
                        "</span> " . __t('Default Configuration'),
                        array(
                            'controller' => 'config',
                            'action' => 'default',
                        ),
                        array(
                            'id' => 'generic-default-js',
                            'class' => 'aag-button medium one outlined',
                            'escape' => false,
                            'data-url-config' => Router::url(array(
                                'controller' => 'config',
                                'action' => 'ajax_get_config'
                            )),
                        )
                    );
                    ?>
                </div>
                <div class="columns medium-6 required" style="display:inline;" id="region-js">
                    <?php
                    echo $this->Form->input(
                        'Region',
                        array(
                            'id' => 'config-module-region',
                            'label' => __t('General.Region'),
                            'class' => 'select2-multiple modules',
                            'type' => 'select',
                            'multiple' => false,
                            'empty' => true,
                            'options' => $aag_regions,
                            'data-url' => Router::url(array(
                                'controller' => 'config',
                                'action' => 'ajax_get_modules_config'
                            )),
                            'data-url-status' => Router::url(array(
                                'controller' => 'config',
                                'action' => 'ajax_get_modules_status'
                            )),
                            'data-url-all-modules-status' => Router::url(array(
                                'controller' => 'config',
                                'action' => 'ajax_check_active_modules'
                            ))
                        )
                    );
                    ?>
                </div>
                <div class="columns medium-6 required" style="display:inline;" id="role-js">
                    <?php
                    echo $this->Form->input(
                        'Role',
                        array(
                            'id' => 'config-module-role',
                            'label' => __t('General.Role'),
                            'class' => 'select2-multiple modules',
                            'type' => 'select',
                            'multiple' => true,
                            'empty' => true,
                            'options' => $roles,
                        )
                    );
                    ?>
                </div>
                <div>
                    <?php
                    foreach ($aag_config as $key => $section) {
                        if ($key == ConstantsSections::MODULES) {
                    ?>
                            <div class="medium-6 columns section_table">
                                <h1 class="title-config-responsive"><?php echo h($section['Name']); ?> </h1>
                                <div class="o-auto" id="modules-config-js" style="display:none;">
                                    <table class="table-tracking table-config">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <?php echo h($section['Name']); ?>
                                                </th>
                                                <th width="85" class="ta-center">
                                                    <?php echo __t('General.Status'); ?>
                                                </th>
                                                <th width="1" class="ta-center" style="text-align: center;">
                                                    <?php echo __t('General.Actions'); ?>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <?php echo __t('Configuration.All_modules'); ?>
                                                </td>
                                                <td class="ta-center">
                                                    <span class="allModulesStatus" id="">
                                                    </span>
                                                </td>
                                                <td class="ta-center">
                                                    <a class="aag-button medium green outlined activateAllAction" data-config-id="">
                                                        <?php echo __t('General.Activate') ?>
                                                    </a>
                                                    <a class="aag-button medium red outlined deactivateAllAction" data-config-id="">
                                                        <?php echo __t('General.Deactivate') ?>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php foreach ($section['Sections'] as $key_section => $field) { ?>
                                                <tr>
                                                    <td title="<?php echo h($field['tooltip' . __s()]); ?>">
                                                        <span class="section_name unselectable cursor-pointer" data-id='<?php echo 'checkbox_' . $key . '_' . $key_section ?>'>
                                                            <?php echo h($field['name' . __s()]); ?>
                                                        </span>
                                                    </td>
                                                    <td class="ta-center">
                                                        <span class="moduleStatus" id="<?php echo $field['id'] ?>">
                                                        </span>
                                                    </td>
                                                    <td class="ta-center" title="<?php echo $field['tooltip' . __s()]; ?>" style="display:none;">
                                                        <?php
                                                        $id = 'checkbox_' .  $key . '_' . $key_section;
                                                        if ($field['active']) {
                                                        ?>
                                                            <span class="ico-toggle icono-grande ion-toggle-filled c-exito" id="<?php echo $id ?>" data-config-id="<?php echo $field['id'] ?>"></span>
                                                        <?php } else { ?>
                                                            <span class="ico-toggle ion-toggle c-fallo icono-grande" id="<?php echo $id ?>" data-config-id="<?php echo $field['id'] ?>"></span>
                                                        <?php } ?>
                                                    </td>
                                                    <td class="ta-center">
                                                        <a class="aag-button medium green outlined activateAction" data-config-id="<?php echo $field['id'] ?>">
                                                            <?php echo __t('General.Activate') ?>
                                                        </a>
                                                        <a class="aag-button medium red outlined deactivateAction" data-config-id="<?php echo $field['id'] ?>">
                                                            <?php echo __t('General.Deactivate') ?>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <br />
                            </div>
                    <?php
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    <?php } ?>
    <div id="cuerpo-config" style="display:none;">
        <?php
        foreach ($aag_config as $key => $section) {
            if ($key != ConstantsSections::GARAGES && $key != ConstantsSections::DISTRIBUTORS && $key != ConstantsSections::TABS && $key != ConstantsSections::MODULES) {
        ?>
                <div class="medium-6 columns section_table">
                    <h1 class="title-config-responsive"><?php echo h($section['Name']); ?> </h1>
                    <div class="o-auto">
                        <table class="table-tracking table-config">
                            <thead>
                                <tr>
                                    <th>
                                        <?php echo h($section['Name']); ?>
                                    </th>
                                    <th class="ta-center" width="150">
                                        <?php echo __t('General.Active'); ?>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($section['Sections'] as $key_section => $field) { ?>
                                    <tr>
                                        <td title="<?php echo h($field['tooltip' . __s()]); ?>">
                                            <span class="section_name unselectable cursor-pointer" data-id='<?php echo 'checkbox_' . $key . '_' . $key_section ?>'>
                                                <?php echo h($field['name' . __s()]); ?>
                                            </span>
                                        </td>
                                        <td class="ta-center" title="<?php echo $field['tooltip' . __s()]; ?>">
                                            <?php if ($field['active']) { ?>
                                                <span class="ico-toggle ion-toggle-filled c-exito icono-grande" id="<?php echo 'checkbox_' .  $key . '_' . $key_section ?>" data-config-id="<?php echo $field['id'] ?>"></span>
                                            <?php } else { ?>
                                                <span class="ico-toggle ion-toggle c-fallo icono-grande" id="<?php echo 'checkbox_' .  $key . '_' . $key_section ?>" data-config-id="<?php echo $field['id'] ?>"></span>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <br />
                </div>
        <?php
            }
        }
        ?>
    </div>
    <div id="cuerpo-tabs" style="display:none;">
        <?php
        foreach ($aag_config as $key => $section) {
            if ($key == ConstantsSections::TABS) {
        ?>
                <div class="medium-6 columns section_table">
                    <h1 class="title-config-responsive"><?php echo h($section['Name']); ?> </h1>
                    <div class="o-auto">
                        <table class="table-tracking table-config">
                            <thead>
                                <tr>
                                    <th>
                                        <?php echo h($section['Name']); ?>
                                    </th>
                                    <th class="ta-center" width="150">
                                        <?php echo __t('General.Active'); ?>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($section['Sections'] as $key_section => $field) { ?>
                                    <tr>
                                        <td title="<?php echo h($field['tooltip' . __s()]); ?>">
                                            <span class="section_name unselectable cursor-pointer" data-id='<?php echo 'checkbox_' . $key . '_' . $key_section ?>'>
                                                <?php echo h($field['name' . __s()]); ?>
                                            </span>
                                        </td>
                                        <td class="ta-center" title="<?php echo $field['tooltip' . __s()]; ?>">
                                            <?php if ($field['active']) { ?>
                                                <span class="ico-toggle ion-toggle-filled c-exito icono-grande" id="<?php echo 'checkbox_' .  $key . '_' . $key_section ?>" data-config-id="<?php echo $field['id'] ?>"></span>
                                            <?php } else { ?>
                                                <span class="ico-toggle ion-toggle c-fallo icono-grande" id="<?php echo 'checkbox_' .  $key . '_' . $key_section ?>" data-config-id="<?php echo $field['id'] ?>"></span>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <br />
                </div>
        <?php
            }
        }
        ?>
    </div>
    <div id="cuerpo-listas" style="display:none;" data-role-id="<?php echo CakeSession::read('Auth.User.role_id'); ?>">
        <div>
            <?php
            echo $this->Form->input(
                'Configuration.Lists',
                array(
                    'label' => __t('Configuration.Lists'),
                    'class' => 'select2-multiple',
                    'type' => 'select',
                    'multiple' => false,
                    'empty' => true,
                    'options' => $lists,
                    'id' => 'config-list',
                    'data-url' => Router::url(array(
                        'controller' => 'config',
                        'action' => 'ajax_get_values_from_list',
                    )),
                    'data-cities' => ConstantsLists::CITIES,
                    'data-services' => ConstantsLists::SERVICES_DRIVERS,
                    'data-orders' => ConstantsLists::ORDER_TYPES_PRODUCTS,
                    'data-cities-from-network-agn' => ConstantsLists::APPROVED_GARAGE_CITY,
                    'data-cities-from-network-gv' => ConstantsLists::GARAGEVERGELIJKER_CITY,
                    'data-cities-from-network-gc' => ConstantsLists::GARAGE_CHECKER_CITY,
                    'data-services-from-network-agn' => ConstantsLists::APPROVED_GARAGE_SERVICE_TO_DRIVER,
                    'data-services-from-network-gv' => ConstantsLists::GARAGEVERGELIJKER_SERVICE_TO_DRIVER,
                    'data-services-from-network-gc' => ConstantsLists::GARAGE_CHECKER_SERVICE_TO_DRIVER,
                )
            );
            ?>
        </div>
        <div id="trading-groups-networks">
            <div class="section_table">
                <div class="o-auto">
                    <table class="table-tracking table-config" id="tabla-listas">
                        <thead>
                            <tr>
                                <th>
                                    <?php echo __t("Configuration.Values"); ?>
                                </th>
                                <th class="ta-center" width="150">
                                    <?php echo __t("General.Actions"); ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <table class="table-tracking table-config" id="tabla-listas2">
                        <thead>
                            <tr>
                                <th>
                                    <?php echo __t("General.Name"); ?>
                                </th>
                                <th>
                                    <?php echo __t("Garage.Latitude"); ?>
                                </th>
                                <th>
                                    <?php __t("Garage.Longitude"); ?>
                                </th>
                                <th>
                                    <?php echo __t("Garage.Province"); ?>
                                </th>
                                <?php if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) { ?>
                                    <th class="ta-center" width="150">
                                        <?php echo __t("General.Actions"); ?>
                                    </th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <table class="table-tracking table-config" id="tabla-listas3">
                        <thead>
                            <tr>
                                <th>
                                    <?php echo __t("General.Name"); ?>
                                </th>
                                <th>
                                    <?php echo __t("Config.Network"); ?>
                                </th>
                                <th class="ta-center" width="150">
                                    <?php echo __t("General.Actions"); ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <table class="table-tracking table-config" id="tabla-listas4">
                        <thead>
                            <tr>
                                <th>
                                    <?php echo __t('Config.Order_type_name'); ?>
                                </th>
                                <th>
                                    <?php echo __t('Config.Order_product_name'); ?>
                                </th>
                                <th class="ta-center" width="150">
                                    <?php echo __t("General.Actions"); ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <table class="table-tracking table-config" id="tabla-listas5">
                        <thead>
                            <tr>
                                <th>
                                    <?php echo __t("Garage.City"); ?>
                                </th>
                                <th>
                                    <?php echo __t("Garage.Province"); ?>
                                </th>
                                <th>
                                    <?php echo __t("Garage.Country"); ?>
                                </th>
                                <?php if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) { ?>
                                    <th class="ta-center" width="150">
                                        <?php echo __t("General.Actions"); ?>
                                    </th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <table class="table-tracking table-config" id="tabla-listas6">
                        <thead>
                            <tr>
                                <th>
                                    <?php echo __t("GarageNetwork.Services_drivers"); ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <br />
            </div>
        </div>
        <div>
            <?php
            if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
                echo $this->Html->link(
                    __t('Configuration.Add_value'),
                    'javascript:void(0)',
                    array(
                        'escape' => false,
                        'class' => 'aag-button medium green',
                        'id' => 'addValueButton',
                        'data-modal' => "addValue",
                        'disabled' => true,
                    )
                );
            }
            ?>
        </div>
        <div style="display: none;" id="addValue" class="reveal-modal background-color-primary" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
            <div>
                <div class="aag-title"><?php echo __t('Configuration.Add_value'); ?></div>
                <div id="datos1" class="d-none modal-body medium-12 columns p-0 m-top-1 m-bottom-1">
                    <?php
                    echo $this->Form->input(
                        'Configuration.Value_name',
                        array(
                            'label' => __t('Configuration.Value_name'),
                            'type' => 'text',
                            'id' => 'val-name'
                        )
                    );
                    ?>
                </div>
                <div id="datos2" class="d-none modal-body medium-12 columns p-0 m-top-1 m-bottom-1">
                    <?php
                    echo $this->Form->input(
                        'Name',
                        array(
                            'label' => __t('Name'),
                            'type' => 'text',
                            'id' => 'val-name2'
                        )
                    );
                    ?>
                </div>
                <div id="datos3" class="d-none modal-body medium-12 columns p-0 m-top-1 m-bottom-1">
                    <?php
                    echo $this->Form->input(
                        'Latitude',
                        array(
                            'label' => __t('Latitude'),
                            'type' => 'number',
                            'id' => 'val-name3'
                        )
                    );
                    ?>
                </div>
                <div id="datos4" class="d-none modal-body medium-12 columns p-0 m-top-1 m-bottom-1">
                    <?php
                    echo $this->Form->input(
                        'Longitude',
                        array(
                            'label' => __t('Longitude'),
                            'type' => 'number',
                            'id' => 'val-name4'
                        )
                    );
                    ?>
                </div>
                <div id="datos5" class="d-none modal-body medium-12 columns p-0 m-top-1 m-bottom-1">
                    <?php
                    echo $this->Form->input(
                        'Province',
                        array(
                            'label' => __t('Province'),
                            'class' => 'select2-multiple',
                            'type' => 'select',
                            'multiple' => false,
                            'empty' => true,
                            'options' => $provinces,
                            'id' => 'val-name5'
                        )
                    );
                    ?>
                </div>
                <div id="datos6" class="d-none modal-body medium-12 columns p-0 m-top-1 m-bottom-1">
                    <?php
                    echo $this->Form->input(
                        'Config.Network',
                        array(
                            'label' => __t('Config.Network'),
                            'class' => 'select2-multiple',
                            'type' => 'select',
                            'multiple' => false,
                            'empty' => true,
                            'options' => $networks,
                            'id' => 'val-name6'
                        )
                    );
                    ?>
                </div>
                <div id="datos7" class="d-none modal-body medium-12 columns p-0 m-top-1 m-bottom-1">
                    <?php
                    echo $this->Form->input(
                        'General.Order_type',
                        array(
                            'label' => __t('General.Order_type'),
                            'class' => 'select2-multiple',
                            'type' => 'select',
                            'multiple' => false,
                            'empty' => true,
                            'id' => 'val-name7'
                        )
                    );
                    ?>
                </div>
                <div id="datos8" class="d-none modal-body medium-12 columns p-0 m-top-1 m-bottom-1">
                    <?php
                    echo $this->Form->input(
                        'Config.Order_product',
                        array(
                            'label' => __t('Config.Order_product'),
                            'class' => 'select2-multiple',
                            'type' => 'select',
                            'multiple' => false,
                            'empty' => true,
                            'id' => 'val-name8'
                        )
                    );
                    ?>
                </div>
                <div id="datos9" class="d-none modal-body medium-12 columns p-0 m-top-1 m-bottom-1">
                    <?php
                    echo $this->Form->input(
                        'Country',
                        array(
                            'label' => __t('Garage.Country'),
                            'class' => 'select2-multiple select-country-js',
                            'type' => 'select',
                            'multiple' => true,
                            'empty' => false,
                            'options' => $countries,
                            'id' => 'val-name9',
                            'data-url' => Router::url(array(
                                'controller' => 'provinces',
                                'action' => 'ajax_load_provinces',
                            )),
                            'data-div_provinces' => '.div_provinces',
                            'data-province_field_name' => 'province_id',
                            'data-is_config' => 1
                        )
                    );
                    ?>
                </div>
                <div id="datos10" class="d-none modal-body medium-12 columns p-0 m-top-1 m-bottom-1 div_provinces">
                    <?php
                    echo $this->Form->input(
                        'Province',
                        array(
                            'label' => __t('Garage.Province'),
                            'class' => 'select2-multiple select-province-js',
                            'type' => 'select',
                            'disabled' => true,
                            'multiple' => true,
                            'empty' => true,
                            'options' => $provinces_from_country,
                            'id' => 'val-name10',
                            'province_field_name' => 'province_id',
                            'data-url' => Router::url(array(
                                'controller' => 'cities',
                                'action' => 'ajax_load_cities',
                            )),
                            'data-div_cities' => '.div_cities',
                            'data-city_field_name' => 'city_id',
                            'data-is_config' => 1
                        )
                    );
                    ?>
                </div>
                <div id="datos11" class="d-none modal-body medium-12 columns p-0 m-top-1 m-bottom-1 div_cities" data-change_disabled="false">
                    <?php
                    echo $this->Form->input(
                        'City',
                        array(
                            'label' => __t('Garage.City'),
                            'class' => 'select2-multiple',
                            'type' => 'select',
                            'multiple' => true,
                            'empty' => true,
                            'options' => $cities,
                            'id' => 'val-name11',
                            'city_field_name' => 'city_id',
                        )
                    );
                    ?>
                </div>
                <div class="medium-12 ta-right">
                    <?php
                    echo $this->Html->link(
                        __t('General.Save'),
                        'javascript:void(0)',
                        array(
                            'escape' => false,
                            'required' => 'required',
                            'class' => 'aag-button medium green',
                            'id' => 'saveValueData',
                            'data-url-save' => Router::url(
                                array(
                                    'controller' => 'config',
                                    'action' => 'ajax_save_new_value'
                                )
                            ),
                            'data-url-del' => Router::url(
                                array(
                                    'controller' => 'config',
                                    'action' => 'delete_value'
                                )
                            )
                        )
                    );
                    ?>
                </div>
            </div>
            <a class="close-modal" data-close aria-label="Close">&#215;</a>
        </div>
        <div style="display: none;" id="editValue" class="reveal-modal background-color-primary" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
            <div class="columns medium-12">
                <h1><?php echo __t('Configuration.Edit_value'); ?></h1>
                <input type="hidden" name="value_id" id="value_id">
                <div id="datos1" class="d-none modal-body medium-12 columns p-0 m-top-1 m-bottom-1">
                    <?php
                    echo $this->Form->input(
                        'Configuration.Value_name',
                        array(
                            'label' => __t('Configuration.Value_name'),
                            'type' => 'text',
                            'id' => 'val-name'
                        )
                    );
                    ?>
                </div>
                <input type="hidden" name="value_id2" id="value_id2">
                <div id="datos2" class="d-none modal-body medium-12 columns p-0 m-top-1 m-bottom-1">
                    <?php
                    echo $this->Form->input(
                        'Name',
                        array(
                            'label' => 'Name',
                            'type' => 'text',
                            'id' => 'val-name2'
                        )
                    );
                    ?>
                </div>
                <input type="hidden" name="value_id3" id="value_id3">
                <div id="datos3" class="d-none modal-body medium-12 columns p-0 m-top-1 m-bottom-1">
                    <?php
                    echo $this->Form->input(
                        'Latitude',
                        array(
                            'label' => 'Latitude',
                            'type' => 'number',
                            'id' => 'val-name3',
                        )
                    );
                    ?>
                </div>
                <input type="hidden" name="value_id4" id="value_id4">
                <div id="datos4" class="d-none modal-body medium-12 columns p-0 m-top-1 m-bottom-1">
                    <?php
                    echo $this->Form->input(
                        'Longitude',
                        array(
                            'label' => __t('Longitude'),
                            'type' => 'number',
                            'id' => 'val-name4',
                        )
                    );
                    ?>
                </div>
                <input type="hidden" name="value_id5" id="value_id5">
                <div id="datos5" class="d-none modal-body medium-12 columns p-0 m-top-1 m-bottom-1">
                    <?php
                    echo $this->Form->input(
                        'Province',
                        array(
                            'label' => __t('Province'),
                            'class' => 'select2-multiple',
                            'type' => 'select',
                            'multiple' => false,
                            'empty' => true,
                            'options' => $provinces,
                            'id' => 'val-name5'
                        )
                    );
                    ?>
                </div>
                <input type="hidden" name="value_id6" id="value_id6">
                <div id="datos6" class="d-none modal-body medium-12 columns p-0 m-top-1 m-bottom-1">
                    <?php
                    echo $this->Form->input(
                        'Config.Network',
                        array(
                            'label' => __t('Config.Network'),
                            'class' => 'select2-multiple',
                            'type' => 'select',
                            'multiple' => false,
                            'empty' => true,
                            'options' => $networks,
                            'id' => 'val-name6'
                        )
                    );
                    ?>
                </div>
                <input type="hidden" name="value_id7" id="value_id7">
                <div id="datos7" class="d-none modal-body medium-12 columns p-0 m-top-1 m-bottom-1">
                    <?php
                    echo $this->Form->input(
                        'General.Order_type',
                        array(
                            'label' => __t('General.Order_type'),
                            'class' => 'select2-multiple',
                            'type' => 'select',
                            'multiple' => false,
                            'empty' => true,
                            'id' => 'val-name7'
                        )
                    );
                    ?>
                </div>
                <input type="hidden" name="value_id8" id="value_id8">
                <div id="datos8" class="d-none modal-body medium-12 columns p-0 m-top-1 m-bottom-1">
                    <?php
                    echo $this->Form->input(
                        'Config.Order_product',
                        array(
                            'label' => __t('Config.Order_product'),
                            'class' => 'select2-multiple',
                            'type' => 'select',
                            'multiple' => false,
                            'empty' => true,
                            'id' => 'val-name8'
                        )
                    );
                    ?>
                </div>
                <input type="hidden" name="value_id9" id="value_id9">
                <div id="datos9" class="d-none modal-body medium-12 columns p-0 m-top-1 m-bottom-1">
                    <?php
                    echo $this->Form->input(
                        'Country',
                        array(
                            'label' => __t('Garage.Country'),
                            'class' => 'select2-multiple select-country-js',
                            'type' => 'select',
                            'multiple' => true,
                            'empty' => true,
                            'options' => $countries,
                            'id' => 'val-name9',
                            'data-url' => Router::url(array(
                                'controller' => 'provinces',
                                'action' => 'ajax_load_provinces',
                            )),
                            'data-div_provinces' => '#div_provinces',
                            'data-province_field_name' => 'province_id',
                            'data-is_config' => 1
                        )
                    );
                    ?>
                </div>
                <input type="hidden" name="value_id10" id="value_id10">
                <div id="datos10 div_provinces" class="d-none modal-body medium-12 columns p-0 m-top-1 m-bottom-1">
                    <?php
                    echo $this->Form->input(
                        'Province',
                        array(
                            'label' => __t('Garage.Province'),
                            'class' => 'select2-multiple',
                            'type' => 'select',
                            'multiple' => true,
                            'empty' => true,
                            'options' => $provinces_from_country,
                            'id' => 'val-name10',
                            'province_field_name' => 'province_id',
                            'data-is_config' => 1
                        )
                    );
                    ?>
                </div>
                <input type="hidden" name="value_id11" id="value_id11">
                <div id="datos11" class="d-none modal-body medium-12 columns p-0 m-top-1 m-bottom-1">
                    <?php
                    echo $this->Form->input(
                        'City',
                        array(
                            'label' => __t('Garage.City'),
                            'class' => 'select2-multiple',
                            'type' => 'select',
                            'multiple' => true,
                            'empty' => true,
                            'options' => $cities_from_provinces,
                            'id' => 'val-name11'
                        )
                    );
                    ?>
                </div>
                <div class="medium-12 ta-right">
                    <?php
                    echo $this->Html->link(
                        __t('General.Save'),
                        'javascript:void(0)',
                        array(
                            'escape' => false,
                            'required' => 'required',
                            'class' => 'aag-button medium green',
                            'id' => 'editValueData',
                            'data-url-edit' => Router::url(
                                array(
                                    'controller' => 'config',
                                    'action' => 'ajax_edit_value'
                                )
                            ),
                            'data-url-get-data' => Router::url(
                                array(
                                    'controller' => 'config',
                                    'action' => 'ajax_get_data_from_value'
                                )
                            ),
                            'data-url-del' => Router::url(
                                array(
                                    'controller' => 'config',
                                    'action' => 'ajax_delete_value'
                                )
                            )
                        )
                    );
                    ?>
                </div>
            </div>
            <a class="close-modal" data-close aria-label="Close">&#215;</a>
        </div>
    </div>
</div>
<?php
echo $this->Form->end();
if ($_GET && $_GET['tab'] && $_GET['tab'] > 0) {
?>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.aag-tabs li:nth-child(<?php echo $_GET['tab']; ?>)').trigger('click');
        });
    </script>
<?php } ?>
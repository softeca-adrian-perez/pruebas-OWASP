<?php
echo $this->Html->script(CakeSession::read('GOOGLE_MAPS_API'), array('block' => 'script'));
echo $this->Html->script('gmaps.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('provinces_list.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('dynamic_lists.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('distributors.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
$action = $this->request->action;
echo $this->Form->create(
    'Distributor',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data',
        'id' => 'form-distributors'
    )
);
echo $this->Form->hidden('Distributor.id');
$config = CakeSession::read('Auth.User.Config');
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        if ($action == ConstantsActionsNames::ADD) {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Distributor.Distributors'),
                    array(
                        'controller' => 'distributors',
                        'action' => 'home'
                    )
                ),
                __t('General.Add'),
            ));
        } else {
            $action = in_array($this->Acceso->rol(), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR)) ? 'my_data' : 'view';
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Distributor.Distributors'),
                    array(
                        'controller' => 'distributors',
                        'action' => 'home'
                    )
                ),
                $this->Html->link(
                    $distributor['Distributor']['name'],
                    array(
                        'controller' => 'distributors',
                        'action' => $action,
                        $distributor['Distributor']['id']
                    )
                ),
                __t('General.Edit'),
            ));
        }
        ?>
    </div>
    <div>
        <?php
        if ($this->request->action == 'add') {
            echo $this->element(
                'Comun/form_actions',
                $cancel_action
            );
        } elseif ($this->Acceso->haveTradingGroupPermission(ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR, $distributor['Distributor']['trading_group_id'])) {
            echo $this->element(
                'Comun/form_actions',
                $cancel_action
            );
        } elseif ($this->Acceso->haveTradingGroupPermission(ConstantsPermissionsGrouping::REQUEST_CHANGE_DISTRIBUTOR, $distributor['Distributor']['trading_group_id'])) {
            echo $this->Form->button(
                __t('RequestedChanges.Request_changes'),
                array(
                    'type' => 'submit',
                    'name' => 'request_changes',
                    'style' => 'margin-top:0 !important;',
                    'class' => 'btn-edit edit',
                )
            );
        }
        ?>
    </div>
</div>
<?php echo $this->element('../Distributors/tabs', array('selected' => 'datas_distributor', 'distributor_id' => $distributor_id ?? null,)); ?>
<div class="cnt-data aag-padding">
    <div class="aag-title ">
        <?php
        if ($action == ConstantsActionsNames::ADD) {
            echo __t('Distributor.Add_distributor');
        } else {
            echo $distributor['Distributor']['name'];
        }
        ?>
    </div>
    <br />
    <div class="cnt-form-inputs">
        <?php
        echo $this->Form->input(
            'name',
            array(
                'type' => 'text',
                'required' => true,
                'label' => __t('Distributor.Name'),
            )
        );
        echo $this->Form->input(
            'abbreviation',
            array(
                'type' => 'text',
                'required' => true,
                'label' => __t('Distributor.Abbreviation'),
            )
        );
        echo $this->Form->input(
            'account_number',
            array(
                'type' => 'text',
                'required' => true,
                'label' => __t('Distributor.Account_number'),
            )
        );
        echo $this->Form->input(
            'Distributor.status',
            array(
                'label' => __t('Garage.Status'),
                'class' => 'select2-multiple',
                'type' => 'select',
                'empty' => false,
                'options' => $distributor_statuses,
            )
        );
        echo $this->Form->input(
            'Distributor.distributor_type_id',
            array(
                'label' => __t('Distributor.Distributor_type'),
                'class' => 'select2-multiple',
                'type' => 'select',
                'empty' => true,
                'options' => $distributor_types,
            )
        );
        echo $this->Form->input(
            'Distributor.distributor_id',
            array(
                'label' => __t('Distributor.Parent_distributor'),
                'class' => 'select2Dinamico_distributor cargar_distributors',
                'type' => 'select',
                'empty' => true,
                'options' => isset($selected_distributor_parent_account) ? $selected_distributor_parent_account : array(),
            )
        );
        echo $this->Form->input(
            'trading_group_id',
            array(
                'label' => __t('TradingGroup.Trading_groups'),
                'class' => 'select2-multiple',
                'type' => 'select',
                'empty' => false,
                'options' => $trading_groups,
            )
        );
        echo $this->Form->input(
            'trading_as',
            array(
                'type' => 'text',
                'required' => true,
                'label' => __t('Distributor.Trading_as'),
            )
        );
        echo $this->Form->input(
            'start_date',
            array(
                'class' => 'fecha-js from-js',
                'id' => 'start_date',
                'data-to' => '#end_date',
                'type' => 'text',
                'required' => true,
                'div' => array(
                    'class' => 'datepicker datepicker-label-block',
                ),
                'label' => __t('Distributor.Start_date'),
            )
        );
        echo $this->Form->input(
            'end_date',
            array(
                'class' => 'fecha-js to-js',
                'id' => 'end_date',
                'data-to' => '#start_date',
                'type' => 'text',
                'required' => true,
                'div' => array(
                    'class' => 'datepicker datepicker-label-block',
                ),
                'label' => __t('Distributor.End_date'),
            )
        );
        echo $this->Form->input(
            'association_id',
            array(
                'label' => __t('Distributor.Association_'),
                'class' => 'select2-multiple',
                'type' => 'select',
                'options' => $associations
            )
        );
        echo $this->Form->input(
            'association_type_id',
            array(
                'label' => __t('Distributor.Association'),
                'class' => 'select2-multiple',
                'type' => 'select',
                'options' => $associations_types
            )
        ); ?>
        <label class="center-check">
            <?php echo __t('Distributor.Aag_member'); ?>
            <div class="aag-switch round small">
                <?php echo $this->Form->input('aag_member', array('id' => 'memberInput', 'label' => false, 'div' => false, 'type' => 'checkbox')); ?>
                <label for="memberInput"></label>
            </div>
        </label>
        <?php
        echo $this->Form->input(
            'rebate_name',
            array(
                'type' => 'text',
                'required' => true,
                'label' => __t('Distributor.Rebate_name'),
            )
        );
        echo $this->Form->input(
            'currency',
            array(
                'type' => 'text',
                'required' => true,
                'label' => __t('Distributor.Currency'),
            )
        );
        echo $this->Form->input(
            'MAMID',
            array(
                'type' => 'text',
                'required' => true,
                'label' => __t('Distributor.MAMID'),
            )
        );
        echo $this->Form->input(
            'reg_number',
            array(
                'type' => 'text',
                'required' => true,
                'label' => __t('Distributor.Reg_number'),
            )
        );

        echo $this->Form->input(
            'VAT_number',
            array(
                'type' => 'text',
                'required' => true,
                'label' => __t('Distributor.VAT_number'),
            )
        );
        if ($config[ConstantsConfig::DETAX_DISTRIBUTOR]) {
            echo $this->Form->input(
                'detax_code',
                array(
                    'type' => 'text',
                    'required' => true,
                    'label' => __t('Distributor.Detax_code'),
                )
            );
        }
        if ($config[ConstantsConfig::SIRET_DISTRIBUTOR]) {
            echo $this->Form->input(
                'siret',
                array(
                    'type' => 'text',
                    'required' => true,
                    'label' => __t('Distributor.Siret'),
                )
            );
        }
        if ($config[ConstantsConfig::CREDIT_WATCH]) {
        ?>
            <label class="center-check">
                <?php echo __t('Distributor.Credit_watch'); ?>
                <div class="aag-switch round small">
                    <?php echo $this->Form->input('credit_watch', array('id' => 'activeInput-2', 'label' => false, 'div' => false, 'type' => 'checkbox')); ?>
                    <label for="activeInput-2"></label>
                </div>
            </label>
        <?php } ?>
    </div>

    <div class="aag-subtitle m-top-1 m-bottom-1">
        <?php echo __t('Distributor.Contact'); ?>
    </div>
    <div class="cnt-form-inputs">
        <?php
        echo $this->Form->input(
            'phone',
            array(
                'type' => 'text',
                'required' => true,
                'label' => __t('Distributor.Phone'),
            )
        );
        echo $this->Form->input(
            'fax',
            array(
                'type' => 'text',
                'required' => true,
                'label' => __t('Distributor.Fax'),
            )
        );
        echo $this->Form->input(
            'email',
            array(
                'type' => 'text',
                'required' => true,
                'label' => __t('Distributor.Email'),
                'id' => 'distributor_email'
            )
        );
        echo $this->Form->input(
            'language_id',
            array(
                'type' => 'select',
                'label' => __t('User.Language'),
                'class' => 'select2-multiple',
                'multiple' => false,
                'options' => $languages,
                'empty' => true,
                'id' => 'distributor_language_select',
                'value' => $distributor['Distributor']['language_id'] ?? null,
                'default' => CakeSession::read('Auth.User.aag_region_id') == Configure::read('AAG_REGION_ID_UK_IRELAND') ? ConstantsLanguages::ENGLISH : null,
            )
        );
        echo $this->Form->input(
            'web',
            array(
                'type' => 'text',
                'required' => true,
                'label' => __t('Distributor.Web'),
            )
        ); ?>
    </div>

    <hr>
    <div class="aag-subtitle m-top-1 m-bottom-1">
        <?php echo __t('Distributor.Address'); ?>
    </div>
    <div class="cnt-form-inputs">
        <?php
        echo $this->Form->input(
            'address1',
            array(
                'type' => 'text',
                'required' => true,
                'id' => 'autocomplete-address',
                'placeholder' => '',
                'data-map' => 'map-event-location',
                'label' => __t('Distributor.Address_1'),
                'class' => 'input-disabled-address1',
                'data-key' => Texto::encryptDecryptText(GOOGLE_API_KEY, false),
                'data-user_aag_region_id' => $user_aag_region_id,
            )
        );
        echo $this->Form->input(
            'address2',
            array(
                'required' => true,
                'type' => 'text',
                'label' => __t('Distributor.Address_2'),
                'class' => 'input-disabled',
                'id' => 'autocomplete-address2',
            )
        );
        echo $this->Form->input(
            'address3',
            array(
                'required' => true,
                'type' => 'text',
                'label' => __t('Distributor.Address_3'),
                'class' => 'input-disabled',
                'id' => 'autocomplete-address3',
            )
        );
        echo $this->Form->input(
            'address4',
            array(
                'required' => true,
                'type' => 'text',
                'label' => __t('Distributor.Address_4'),
                'class' => 'input-disabled',
                'id' => 'autocomplete-address4',
            )
        );
        echo $this->Form->input(
            'postcode',
            array(
                'type' => 'text',
                'required' => true,
                'id' => 'autocomplete-postcode',
                'label' => __t('Distributor.Postcode'),
            )
        );
        echo $this->Form->input(
            'town',
            array(
                'type' => 'text',
                'required' => true,
                'id' => 'autocomplete-town',
                'label' => __t('Distributor.Town'),
            )
        );
        echo $this->Form->input(
            'sales_area_id',
            array(
                'value' => $distributor['Distributor']['sales_area_id'] ?? null,
                'required' => false,
                'id' => 'sales_area_id-select',
                'label' => __t('Distributor.Sales_area'),
                'class' => 'select2-multiple',
                'type' => 'select',
                'multiple' => false,
                'empty' => true,
                'options' => $sales_area,
            )
        );
        if ($count_countries != 1) {
            if ($config[ConstantsConfig::COUNTY_COUNTRY_DISTRIBUTOR]) {
                echo $this->Form->input(
                    'country',
                    array(
                        'label' => __t('Garage.Country'),
                        'class' => 'select2-multiple select-country-js input-disabled',
                        'type' => 'select',
                        'options' => $countries,
                        'empty' => true,
                        'disabled' => true,
                        'data-url' => Router::url(array(
                            'controller' => 'provinces',
                            'action' => 'ajax_load_provinces',
                        )),
                        'data-div_provinces' => '#div_provinces',
                        'data-province_field_name' => 'province_id',
                        'id' => 'autocomplete-country',
                        'data-is_config' => 0,
                        'data-select_multiple' => 0
                    )
                );
            }
        } ?>
    </div>
    <?php
    if ($config[ConstantsConfig::COUNTY_COUNTRY_DISTRIBUTOR]) { ?>
        <div class="m-top-1" id="div_provinces">
            <?php
            echo $this->element('../Provinces/Elements/ajax_load_provinces', array(
                'province_field_name' => 'province_id',
                'provinces_list' => $provinces,
                'div_cities' => '#div-cities',
                'data-is_config' => 0,
                'select_multiple' => false
            )); ?>
        </div>
    <?php }
    echo $this->Form->hidden(
        'latitude',
        array(
            'label' => false,
            'id' => 'latitude-localization',
        )
    );
    echo $this->Form->hidden(
        'longitude',
        array(
            'label' => false,
            'id' => 'longitude-localization',
        )
    );
    ?>
    <div class="map-button p-bottom-1 p-top-1">
        <?php
        echo $this->Form->button(
            __t('Garage.My_location'),
            array(
                'type' => 'button',
                'class' => 'aag-button medium one',
                'id' => 'localizame'
            )
        );
        echo $this->Html->link(
            __t('Garage.Remove_location'),
            '#',
            array(
                'class' => 'remove-marker d-none aag-button medium one',
            )
        );
        ?>
    </div>
    <div id="map-event-location" class="contenedor-mapa m-bottom-1" style="height: 400px;" data-editable="<?php echo ConstantsBooleans::YES; ?>"></div>
    <div class="alliance_bar clear"></div>
</div>
<?php echo $this->Form->end(); ?>
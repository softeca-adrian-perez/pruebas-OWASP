<?php
$config = CakeSession::read('Auth.User.Config');
echo $this->Html->script('garages-contacts.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script(CakeSession::read('GOOGLE_MAPS_API'), array('block' => 'script'));
echo $this->Html->script('gmaps.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('provinces_list.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('cities_list.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('enable_edit_garage.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('g_number_id.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('inputsValidations.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
$action = in_array($this->Acceso->rol(), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR)) ? 'my_data' : 'view';

echo $this->Form->create(
    'Garage',
    array(
        'id' => 'form',
        'enctype' => 'multipart/form-data'
    )
);
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        if ($this->request->action == 'add') {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Garage.Garages'),
                    array(
                        'controller' => 'garages',
                        'action' => 'home'
                    )
                ),
                __t('Garage.New_garage'),
            ));
        } else {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Garage.Garages'),
                    array(
                        'controller' => 'garages',
                        'action' => 'home'
                    )
                ),
                __t('General.Edit'),
            ));
        }
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back') ?></a>
        <?php
        if ($this->request->action != 'add') {
            echo $this->Html->link(
                __t('Appointment.Export_pdf'),
                array(
                    'controller' => 'garages',
                    'action' => 'export_garages_pdf',
                    $garage_id
                ),
                array(
                    'class' => 'aag-button medium',
                    'title' => __t('Appointment.Export_pdf'),
                    'target' => '_blank',
                )
            );
        }

        if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN || (isset($garage['Garage']['status']) && $garage['Garage']['status'] == ConstantsGarageStatus::INACTIVE)) {
        ?>
            <button type="button" id="edit-btn-disable" value="1" <?php echo $this->request->action == 'add' ? 'style="display:none;"' : ''; ?> class="aag-button medium" data-url="<?php echo Router::url(array('controller' => 'garages', 'action' => 'ajax_update_edit')); ?> " data-edit_enabled="<?php echo $this->request->action == 'add' ? 1 : CakeSession::read('Auth.User.edit_enabled'); ?>"><?php echo __t('General.Edit'); ?></button>
        <?php
        }
        if ($this->request->action != 'add' && (CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN || $configModuleRegionRole['ConfigModuleRegionRole']['active'] == true)) {
            echo $this->element('Comun/form_actions_garage_history');
        }
        ?>
        <div class="f-right btn-hide" hidden>
            <?php
            if ($this->request->action == 'add') {
                echo $this->element('Comun/form_actions_garage', $cancel_action);
            } elseif ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)) {
                echo $this->element('Comun/form_actions_garage', $cancel_action);
            } elseif ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::REQUEST_CHANGE_GARAGE)) {
                echo $this->Form->button(
                    __t('RequestedChanges.Request_changes'),
                    array(
                        'type' => 'submit',
                        'name' => 'request_changes',
                        'class' => 'aag-button medium'
                    )
                );
            ?>
            <?php } ?>
        </div>
    </div>
</div>
<?php
if (!in_array($this->Acceso->rol(), array(ConstantsRoles::GARAGE))) {
    echo $this->element('../Garages/tabs', array('selected' => 'datas_garage'));
}
?>
<div class="cnt-data aag-padding">
    <?php
    if ($this->request->action != 'add') {
        echo $this->Form->hidden('Garage.id', array('id' => 'garage_id', 'value' => $garage_id));
    }
    if ($this->request->action == 'add') {
    ?>
        <div class="aag-title-background">
            <?php echo __t('Garage.New_garage'); ?>
        </div>
    <?php
    } elseif ($garage['Garage']['name']) {
    ?>
        <div class="aag-title-background">
            <?php echo h($garage['Garage']['name']); ?>
        </div>
    <?php } ?>
    <div class="aag-subtitle m-top-1">
        <?php echo __t('Garage.Garage'); ?>
    </div>
    <?php if ($this->request->action != 'add' && isset($networks_garage_last)) { ?>
        <div class="flex gap-1 fw-wrap m-bottom-1 m-top-1">
            <?php foreach ($networks_garage_last as $network_garage_last) { ?>
                <div class="flex fd-column ai-center">
                    <img class="logotipo" title="<?php echo h($network_garage_last['name']); ?>" src="<?php echo FileManager::get_url(FilePaths::NETWORKS_IMAGES_RELATIVE . $network_garage_last['image']); ?>" />
                    <?php
                    if ($network_garage_last['status'] == ConstantsNetworksStatus::UNSUBSCRIBE || $network_garage_last['status'] == ConstantsNetworksStatus::NOT_CONVERTED) {
                        $class = 'c-fallo';
                    } elseif ($network_garage_last['status'] == ConstantsNetworksStatus::LIVE) {
                        $class = 'c-exito';
                    } else {
                        $class = 'c-informacion';
                    }
                    ?>
                    <strong class="<?php echo $class; ?>">
                        <?php echo h($network_garage_last['statusText']); ?>
                    </strong>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
    <div class="cnt-form-inputs">
        <?php
        $class = 'input-disabled';
        if (isset($garage['Garage']['status']) && $garage['Garage']['status'] == ConstantsGarageStatus::INACTIVE) {
            $class = '';
        }
        echo $this->Form->input(
            'business_name',
            array(
                'required' => false,
                'type' => 'text',
                'label' => __t('Garage.Business_name'),
                'disabled' => true,
                'class' => $class
            )
        );
        echo $this->Form->input(
            'name',
            array(
                'type' => 'text',
                'required' => true,
                'label' => __t('Garage.Name'),
                'disabled' => true,
                'class' => $class
            )
        );
        if (isset($garage['Garage']['status']) && $garage['Garage']['status'] == ConstantsGarageStatus::INACTIVE) {
            echo $this->Form->input(
                'name',
                array(
                    'type' => 'hidden',
                    'required' => true,
                    'value' => $garage['Garage']['name']
                )
            );
        }
        echo $this->Form->input(
            'status',
            array(
                'required' => false,
                'id' => 'status-select',
                'label' => __t('Garage.Status'),
                'class' => 'select2-multiple input_disabled_status-js',
                'type' => 'select',
                'multiple' => false,
                'disabled' => true,
                'empty' => false,
                'options' => $garage_statuses,
                'data-manually-created' => isset($garage['Garage']['manually_created']) ? $garage['Garage']['manually_created'] : $manually_created,
            )
        );
        if ($user_aag_region_id != ConstantsAAGRegionId::BENELUX) {
            if ($this->request->action == 'add') {
                echo $this->Form->input(
                    'g_number_id',
                    array(
                        'required' => false,
                        'id' => 'g-number-input',
                        'class' => 'input-disabled-g-number',
                        'type' => 'text',
                        'label' => __t('Garage.G_number'),
                        'disabled' => true,
                        'data-url' => Router::url(array(
                            'controller' => 'garages',
                            'action' => 'ajax_autocomplete_g_number',
                        )),
                    )
                );
            } else {
                echo $this->Form->input(
                    'g_number_id',
                    array(
                        'required' => false,
                        'id' => 'g-number-input',
                        'class' => 'input-disabled-g-number',
                        'type' => 'text',
                        'label' => __t('Garage.G_number'),
                        'disabled' => true,
                    )
                );
            }
        }
        echo $this->Form->input(
            'foundation_year',
            array(
                'required' => false,
                'label' => __t('Garage.Foundation_year'),
                'disabled' => true,
                'class' => $class,
                'id' => 'test'
            )
        );
        echo $this->Form->input(
            'erp_id',
            array(
                'value' => $garage['Garage']['erp_id'] ?? null,
                'required' => false,
                'id' => 'erp_id-select',
                'label' => __t('Garage.ERP'),
                'class' => 'select2-multiple ' . $class,
                'type' => 'select',
                'multiple' => false,
                'empty' => true,
                'disabled' => true,
                'options' => $erp_providers,
            )
        );
        echo $this->Form->input(
            'ref_code',
            array(
                'required' => false,
                'type' => 'text',
                'label' => __t('Garage.Ref_code'),
                'disabled' => true,
                'class' => $class
            )
        );
        echo $this->Form->input(
            'creditor_number',
            array(
                'required' => false,
                'type' => 'text',
                'label' => __t('Garage.Creditor_number'),
                'disabled' => true,
                'class' => $class
            )
        );
        echo $this->Form->input(
            'payment_terms',
            array(
                'required' => false,
                'type' => 'text',
                'label' => __t('Garage.Payment_terms'),
                'disabled' => true,
                'class' => $class,
            )
        );
        echo $this->Form->input(
            'company_code',
            array(
                'required' => false,
                'type' => 'text',
                'label' => __t('Garage.Company_code'),
                'disabled' => true,
                'class' => $class
            )
        );
        if ($config[ConstantsConfig::SIRET]) {
            echo $this->Form->input(
                'siret',
                array(
                    'required' => false,
                    'type' => 'text',
                    'label' => __t('Garage.Siret'),
                    'disabled' => true,
                    'class' => $class
                )
            );
        }
        if ($config[ConstantsConfig::INSURANCE_AGREEMENT]) {
            echo $this->Form->input(
                'insurance_agreement_id',
                array(
                    'required' => false,
                    'label' => __t('Garage.Insurance_agreement'),
                    'class' => 'select2-multiple ' . $class,
                    'type' => 'select',
                    'empty' => true,
                    'multiple' => false,
                    'options' => $insurance_agreements,
                    'disabled' => true
                )
            );
        }
        echo $this->Form->input(
            'GarageWorkshop.workshop_activities',
            array(
                'required' => false,
                'label' => __t('Distributor.Workshop_activities'),
                'class' => 'select2-multiple ' . $class,
                'type' => 'select',
                'empty' => true,
                'disabled' => true,
                'multiple' => true,
                'options' => $workshop_activities,
            )
        );
        if ($config[ConstantsConfig::AFFILIATION_ASSEMBLY]) {
            $value = false;
            if (isset($garage['Garage']['affiliation_assembly']) && $garage['Garage']['affiliation_assembly']) {
                $value = true;
            }
            echo $this->Form->input(
                'affiliation_assembly',
                array(
                    'label' => __t('Garage.Affiliation_assembly'),
                    'type' => 'checkbox',
                    'checked' => $value,
                    'disabled' => true,
                    'class' => $class
                )
            );
        }
        if ($config[ConstantsConfig::DOCUMENTS_LEGAL]) {
            $value = false;
            if (isset($garage['Garage']['documents_legal']) && $garage['Garage']['documents_legal']) {
                $value = true;
            }
            echo $this->Form->input(
                'documents_legal',
                array(
                    'label' => __t('Garage.Documents_legal'),
                    'type' => 'checkbox',
                    'checked' => $value,
                    'disabled' => true,
                    'class' => $class
                )
            );
        }
        if ($config[ConstantsConfig::DIESEL_LIABILITY]) {
            $value = false;
            if (isset($garage['Garage']['diesel_liability']) && $garage['Garage']['diesel_liability']) {
                $value = true;
            }
            echo $this->Form->input(
                'diesel_liability',
                array(
                    'label' => __t('Garage.Diesel_liability'),
                    'type' => 'checkbox',
                    'checked' => $value,
                    'disabled' => true,
                    'class' => $class
                )
            );
        }
        echo $this->Form->input(
            'slug',
            array(
                'required' => false,
                'type' => 'text',
                'label' => __t('Garage.Slug'),
                'disabled' => true,
                'class' => $class
            )
        );
        ?>
    </div>
    <hr />
    <div class="aag-subtitle">
        <?php echo __t('Garage.Visits'); ?>
    </div>
    <div class="cnt-form-inputs">
        <?php
        echo $this->Form->input(
            'visit_frequency',
            array(
                'label' => __t('Garage.Visit_frequency'),
                'class' => 'select2-multiple ' . $class,
                'type' => 'select',
                'multiple' => false,
                'empty' => true,
                'options' => $visit_frequency,
                'disabled' => true
            )
        );
        ?>
        <div class="cnt-check-visit cnt-form-inputs-max-width">
            <?php
            echo $this->Form->input(
                'visit_monday',
                array(
                    'label' => __t('Garage.Monday'),
                    'type' => 'checkbox',
                    'disabled' => true,
                    'class' => $class,
                )
            );
            echo $this->Form->input(
                'visit_tuesday',
                array(
                    'label' => __t('Garage.Tuesday'),
                    'type' => 'checkbox',
                    'disabled' => true,
                    'class' => $class,
                )
            );
            echo $this->Form->input(
                'visit_wednesday',
                array(
                    'label' => __t('Garage.Wednesday'),
                    'type' => 'checkbox',
                    'disabled' => true,
                    'class' => $class,
                )
            );
            echo $this->Form->input(
                'visit_thursday',
                array(
                    'label' => __t('Garage.Thursday'),
                    'type' => 'checkbox',
                    'disabled' => true,
                    'class' => $class,
                )
            );
            echo $this->Form->input(
                'visit_friday',
                array(
                    'label' => __t('Garage.Friday'),
                    'type' => 'checkbox',
                    'disabled' => true,
                    'class' => $class,
                )
            );
            ?>
        </div>
    </div>
    <?php
    if ($get_list_config_tabs[ConstantsTabs::BDM] == ConstantsBooleans::ACTIVE) {
    ?>
        <hr />
        <div class="aag-subtitle">
            <?php echo __t('Contact.BDM_List'); ?>
        </div>
        <?php
        if ($this->request->action == 'add' || $this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)) {
        ?>
            <div class="row btn-hide" hidden>
                <div class="cnt-buttons-v2 d-inline f-right">
                    <?php
                    if (!isset($garage['Garage']['status']) || $garage['Garage']['status'] != ConstantsGarageStatus::INACTIVE) {
                        echo $this->Html->link(
                            __t('Contact.BDM_New'),
                            array(),
                            array(
                                'escape' => false,
                                'class' => 'aag-button small green',
                                'title' => __t('Distributor.New_distributor'),
                                'id' => 'btn_add_bdm',
                                'data-url_info' => Router::url(array(
                                    'controller' => 'garages',
                                    'action' => 'ajax_get_info_contact',
                                )),
                            )
                        );
                    ?>
                </div>
                <div class="d-inline-block f-right m-right-1" style="width:225px;">
                <?php echo $this->Form->input(
                            'DistributorName',
                            array(
                                'label' => false,
                                'type' => 'select',
                                'class' => 'select2-multiple',
                                'id' => 'distributor_name',
                                'options' => $all_contacts_bdm
                            )
                        );
                    } ?>
                </div>
            </div>
        <?php
        }
        ?>
        <div class="o-auto">
            <table class="table-tracking">
                <thead>
                    <tr>
                        <th width="40"><?php echo __t('General.Order'); ?></th>
                        <th><?php echo __t('Contact.First_name'); ?></th>
                        <th><?php echo __t('Contact.Last_name'); ?></th>
                        <th><?php echo __t('Contact.Position'); ?></th>
                        <th width="120"><?php echo __t('Contact.Phone'); ?></th>
                        <th width="120"><?php echo __t('Contact.Mobile_phone'); ?></th>
                        <th><?php echo __t('Contact.Email'); ?></th>
                        <?php if (!isset($garage['Garage']['status']) || $garage['Garage']['status'] != ConstantsGarageStatus::INACTIVE) { ?>
                            <th class="ta-center btn-hide" hidden><?php echo __t('General.Actions'); ?></th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody id="sort-contacts" data-page="<?php echo ConstantsPagination::FIRST_PAGE; ?>">
                    <?php
                    foreach ($contacts as $contact) { ?>
                        <tr>
                            <td class="order-row">
                                <?php echo $contact['GarageContactBdm']['order']; ?>
                            </td>
                            <td class="item-distributor" data-id="<?php echo $contact['GarageContactBdm']['id']; ?>" data-contact_id="<?php echo $contact['GarageContactBdm']['contact_id']; ?>">
                                <?php echo h($contact['Contact']['first_name']); ?>
                            </td>
                            <td>
                                <?php echo h($contact['Contact']['last_name']); ?>
                            </td>
                            <td>
                                <?php echo ($contact['Contact']['position_id'] != null) ? h($positions[$contact['Contact']['position_id']]) : '' ?>
                            </td>
                            <td>
                                <?php echo h($contact['Contact']['phone']); ?>
                            </td>
                            <td>
                                <?php echo h($contact['Contact']['mobile_phone']); ?>
                            </td>
                            <td>
                                <?php echo h($contact['Contact']['email']); ?>
                            </td>
                            <td class="ta-center btn-hide" hidden>
                                <?php if ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)) {
                                    if (!isset($garage['Garage']['status']) || $garage['Garage']['status'] != ConstantsGarageStatus::INACTIVE) { ?>

                                        <span class="delete-employee aag-icon-papelera c-fallo cursor-pointer delete-contact-bdm" hidden></span>
                                <?php }
                                } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <div hidden>
                <?php echo $this->Form->input(
                    'GarageContact.GaragesBDM',
                    array(
                        'type' => 'select',
                        'class' => 'select2-multiple',
                        'multiple' => true,
                        'empty' => false,
                        'id' => 'garages-bdm',
                    )
                ); ?>
            </div>
            <div hidden>
                <?php echo $this->Form->input(
                    'GarageContact.DeleteGaragesBDM',
                    array(
                        'type' => 'select',
                        'class' => 'select2-multiple',
                        'multiple' => true,
                        'empty' => false,
                        'id' => 'garages-contact-delete',
                    )
                ); ?>
            </div>
        </div>
    <?php
    }
    ?>
    <hr />
    <div class="aag-subtitle">
        <?php echo __t('Garage.Contact'); ?>
    </div>
    <div class="cnt-form-inputs">
        <?php
        echo $this->Form->input(
            'phone',
            array(
                'type' => 'text',
                'label' => __t('Garage.Phone'),
                'disabled' => true,
                'class' => 'phoneValidation ' . $class
            )
        );
        echo $this->Form->input(
            'mobile',
            array(
                'type' => 'text',
                'label' => __t('Garage.Mobile'),
                'disabled' => true,
                'class' => 'phoneValidation ' . $class

            )
        );
        echo $this->Form->input(
            'service_24h_phone',
            array(
                'type' => 'text',
                'label' => __t('Garage.24h_phone'),
                'disabled' => true,
                'class' => 'phoneValidation ' . $class
            )
        );
        echo $this->Form->input(
            'fax',
            array(
                'type' => 'text',
                'label' => __t('Garage.Fax'),
                'disabled' => true,
                'class' => 'phoneValidation ' . $class
            )
        );
        echo $this->Form->input(
            'email',
            array(
                'type' => 'text',
                'label' => __t('Garage.Email'),
                'disabled' => true,
                'class' => $class,
                'id' => 'email_garage'
            )
        );
        echo $this->Form->input(
            'language_id',
            array(
                'type' => 'select',
                'label' => __t('User.Language'),
                'disabled' => true,
                'class' => 'select2-multiple ' . $class,
                'multiple' => false,
                'options' => $languages,
                'empty' => true,
                'id' => 'garage_language_select',
                'default' => CakeSession::read('Auth.User.aag_region_id') == Configure::read('AAG_REGION_ID_UK_IRELAND') ? ConstantsLanguages::ENGLISH : null,
            )
        );
        echo $this->Form->input(
            'web',
            array(
                'required' => false,
                'type' => 'text',
                'label' => __t('Garage.Web'),
                'disabled' => true,
                'class' => $class
            )
        );
        ?>
    </div>
    <hr />
    <div class="aag-subtitle">
        <?php echo __t('Garage.Primary_contact'); ?>
    </div>
    <?php if (isset($primaryContact) && !empty($primaryContact)) { ?>

    <?php echo $this->Html->link(
            '<span>' . $primaryContact['Contact']['full_name'] . '</span>',
            array(
                'controller' => 'garages',
                'action' => 'add_contacts_staff',
                $garage_id,
                '?' => array(
                    'first_name' => $primaryContact['Contact']['first_name'],
                    'last_name' => $primaryContact['Contact']['last_name']
                )
            ),
            array(
                'escape' => false,
            )
        );
    } else {
        echo __t('General.None');
    }
    ?>
    <hr />
    <div class="aag-subtitle">
        <?php echo __t('Garage.Address'); ?>
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
                'label' => __t('Garage.Address_1'),
                'class' => $class,
                'data-key' => Texto::encryptDecryptText(GOOGLE_API_KEY, false),
                'data-user_aag_region_id' => $user_aag_region_id,
                'data-url' => Router::url(array(
                    'controller' => 'garages',
                    'action' => 'ajax_get_province_and_country_by_postcode',
                )),
                'disabled' => true
            )
        );
        if (isset($garage['Garage']['status']) && $garage['Garage']['status'] == ConstantsGarageStatus::INACTIVE) {
            echo $this->Form->input(
                'address1',
                array(
                    'type' => 'hidden',
                    'required' => true,
                    'value' => $garage['Garage']['address1']
                )
            );
        }
        echo $this->Form->input(
            'address2',
            array(
                'required' => false,
                'type' => 'text',
                'label' => __t('Garage.Address_2'),
                'disabled' => true,
                'class' => $class,
                'id' => 'autocomplete-address2'
            )
        );
        echo $this->Form->input(
            'address3',
            array(
                'required' => false,
                'type' => 'text',
                'label' => __t('Garage.Address_3'),
                'disabled' => true,
                'class' => $class,
                'id' => 'autocomplete-address3'
            )
        );
        echo $this->Form->input(
            'address4',
            array(
                'required' => false,
                'type' => 'text',
                'label' => __t('Garage.Address_4'),
                'disabled' => true,
                'class' => $class,
                'id' => 'autocomplete-address4'
            )
        );
        echo $this->Form->input(
            'latitude',
            array(
                'required' => false,
                'label' => __t('Garage.Latitude'),
                'id' => 'latitude-localization',
                'disabled' => true,
                'class' => $class
            )
        );
        echo $this->Form->input(
            'longitude',
            array(
                'required' => false,
                'label' => __t('Garage.Longitude'),
                'id' => 'longitude-localization',
                'disabled' => true,
                'class' => $class
            )
        );
        if ($count_countries != 1) {
            if ($config[ConstantsConfig::COUNTY_COUNTRY_GARAGE]) {
                $country_selected = null;
                if (isset($garage['Garage']['country'])){
                    $country_selected = $garage['Garage']['country'];
                } elseif (!isset($garage['Garage']['country']) && !empty($new_garage_country_selected)){
                    $country_selected = $this->request->data['Garage']['country'];
                }
                echo $this->Form->input(
                    'country',
                    array(
                        'label' => __t('Garage.Country'),
                        'class' => 'select2-multiple select-country-js ' . $class,
                        'type' => 'select',
                        'options' => $countries,
                        'selected' => $country_selected,
                        'empty' => false,
                        'disabled' => true,
                        'data-url' => Router::url(array(
                            'controller' => 'provinces',
                            'action' => 'ajax_load_provinces',
                        )),
                        'data-div_provinces' => '#div_provinces',
                        'data-province_field_name' => 'province_id',
                        'id' => 'autocomplete-country',
                        'data-is_config' => 0,
                        'select_multiple' => false,
                        'id' => 'autocomplete-country',
                        'value' => $garage['Garage']['country'] ?? null
                    )
                );
            }
        }
        if ($config[ConstantsConfig::COUNTY_COUNTRY_GARAGE]) {
        ?>
            <div id="div_provinces">
                <?php echo $this->element(
                    '../Provinces/Elements/ajax_load_provinces',
                    array(
                        'province_field_name' => 'province_id',
                        'provinces_list' => $provinces,
                        'inactive_province' => $inactive_province ?? null,
                        'div_cities' => '#div-cities',
                        'data-is_config' => 0,
                        'select_multiple' => false,
                    )
                ); ?>
            </div>
        <?php
        }
        if ($config[ConstantsConfig::COUNTY_COUNTRY_GARAGE]) {
        ?>
            <div id="div_cities">
                <?php echo $this->element(
                    '../Cities/Elements/ajax_load_cities',
                    array(
                        'city_field_name' => 'city_id',
                        'cities_list' => $cities,
                        'select_multiple' => false,
                    )
                ); ?>
            </div>
        <?php
        }
        echo $this->Form->input(
            'town',
            array(
                'required' => false,
                'id' => 'autocomplete-town',
                'type' => 'text',
                'label' => __t('Garage.Town') . '/' . __t('Garage.City'),
                'disabled' => true,
                'class' => $class
            )
        );
        echo $this->Form->input(
            'postcode',
            array(
                'required' => false,
                'id' => 'autocomplete-postcode',
                'type' => 'text',
                'label' => __t('Garage.Postcode'),
                'disabled' => true,
                'class' => $class
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
        echo $this->Form->input(
            'sales_area_id',
            array(
                'value' => $garage['Garage']['sales_area_id'] ?? null,
                'required' => false,
                'id' => 'sales_area_id-select',
                'label' => __t('Garage.Sales_area'),
                'class' => 'select2-multiple ' . $class,
                'type' => 'select',
                'multiple' => false,
                'empty' => true,
                'options' => $sales_area,
                'disabled' => true,
            )
        );
        ?>
    </div>
    <div class="map-button m-top-1 btn-hide" hidden>
        <?php
        echo $this->Form->button(
            __t('Garage.My_location'),
            array(
                'type' => 'button',
                'class' => 'aag-button small',
                'id' => 'localizame'
            )
        );
        echo $this->Html->link(
            __t('Garage.Remove_location'),
            '#',
            array(
                'class' => 'remove-marker d-none',
            )
        );
        ?>
    </div>
    <div id="map-event-location" class="contenedor-mapa m-top-1" style="height: 400px;" data-editable="<?php echo ConstantsBooleans::YES; ?>"></div>
    <div class="alliance_bar"></div>
</div>
<?php echo $this->Form->end(); ?>
<?php
echo $this->Html->script('/js/users.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Form->create('Contact', array('id' => 'form'));
echo $this->Form->hidden('Contact.id');
echo $this->Html->script('dynamic_lists.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
$action = $this->request->action;

if (isset($this->request->params['action'])) {
    $param = $this->request->params['action'];
} else {
    $param = '';
}
if (isset($this->request->params['named']['garage_id'])) {
    $param2 = $this->request->params['named']['garage_id'];
} else {
    $param2 = '';
}

echo $this->Form->hidden(
    '',
    array(
        'id' => 'action_form',
        'data-param1' => $param,
        'data-param2' => $param2
    )
);

echo $this->Form->hidden(
    'positions_bdm',
    array(
        'id' => 'positions_bdm_id',
        'value' => $positions_list_bdm
    )
);
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Contact.Contacts'),
                array(
                    'controller' => 'contacts',
                    'action' => 'home'
                )
            ),
            __t('Contact.Add_contact_and_user'),
        ));
        ?>
    </div>
    <div>
        <?php echo $this->element('Comun/form_actions', $cancel_action); ?>
    </div>
</div>
<div class="cnt-data">
    <div class="cnt-data-element">
        <div class="aag-title m-top-1">
            <?php echo __t('Contact.New_contact_and_user'); ?>
        </div>
        <div class="aag-subtitle m-top-1 m-bottom-1">
            <?php echo __t('Contact.Contact') ?>
        </div>
        <div class="cnt-form-inputs">
            <?php
            echo $this->Form->input(
                'Contact.title_id',
                array(
                    'label' => __t('Contact.Title'),
                    'class' => 'select2-multiple',
                    'type' => 'select',
                    'multiple' => false,
                    'required' => true,
                    'empty' => true,
                    'options' => $titles,
                )
            );
            echo $this->Form->input(
                'Contact.first_name',
                array(
                    'type' => 'text',
                    'required' => true,
                    'id' => 'texto1',
                    'label' => __t('Contact.First_name'),
                )
            );
            echo $this->Form->input(
                'Contact.last_name',
                array(
                    'type' => 'text',
                    'id' => 'texto3',
                    'required' => true,
                    'label' => __t('Contact.Last_name'),
                )
            );
            echo $this->Form->input(
                'Contact.phone',
                array(
                    'type' => 'text',
                    'required' => true,
                    'label' => __t('Contact.Phone'),
                )
            );
            echo $this->Form->input(
                'Contact.email',
                array(
                    'type' => 'text',
                    'required' => true,
                    'label' => __t('Contact.Email'),
                )
            );
            echo $this->Form->input(
                'Contact.logistic_center_id',
                array(
                    'label' => __t('Contact.Logistic_center'),
                    'class' => 'select2-multiple',
                    'type' => 'select',
                    'multiple' => false,
                    'empty' => true,
                    'options' => $logistic_centers
                )
            );
            echo $this->Form->input(
                'Contact.mobile_phone',
                array(
                    'type' => 'text',
                    'required' => true,
                    'label' => __t('Contact.Mobile_phone'),
                )
            );
            echo $this->Form->input(
                'Contact.position_id',
                array(
                    'label' => __t('Contact.Position'),
                    'class' => 'select2-multiple',
                    'type' => 'select',
                    'multiple' => false,
                    'empty' => true,
                    'options' => $positions,
                    'id' => 'position'
                )
            );
            echo $this->Form->input(
                'Contact.aag_region_id',
                array(
                    'id' => 'region-select',
                    'required' => true,
                    'label' => __t('User.Region'),
                    'type' => 'select',
                    'class' => 'select2-multiple',
                    'multiple' => false,
                    'options' => $aag_regions,
                    'empty' => $user_role_id == ConstantsRoles::SUPER_ADMIN ? true : false,
                    'disabled' => true,
                    'value' => $user_aag_region_id,
                    'data-url' => Router::url(array(
                        'controller' => 'contacts',
                        'action' => 'ajax_load_countries'
                    )),
                )
            );
            echo $this->Form->input(
                'Contact.aag_region_id',
                array(
                    'required' => true,
                    'type' => 'hidden',
                    'value' => $user_aag_region_id,
                )
            );
            ?>
            <div id="parent">
                <?php
                echo $this->Form->input(
                    'Contact.contact_id',
                    array(
                        'label' => __t('Contact.Parent'),
                        'class' => 'select2-multiple',
                        'type' => 'select',
                        'multiple' => false,
                        'empty' => true,
                        'options' => $contacts_bdm,
                        'id' => 'parent_select'
                    )
                );
                ?>
            </div>
            <?php
            if (is_null($customer_id)) {
                if (isset($garages_list) && !empty($garages_list)) {
                    $option_garage = $garages_list;
                }
            ?>
                <div id="garage">
                    <?php
                    echo $this->Form->input(
                        'Contact.garage_id',
                        array(
                            'label' => __t('Garage.Garage')  . '<span style="color:red"> *</span>',
                            'class' => 'select2-multiple',
                            'type' => 'select',
                            'multiple' => false,
                            'empty' => true,
                            'options' => $option_garage,
                            'default' => $option_garage,
                            'id' => 'garage_select',
                            'data-url' => Router::url(
                                array(
                                    'controller' => 'garages',
                                    'action' => 'ajax_get_garages',
                                    $user_aag_region_id
                                )
                            ),
                            'data-url-country' => Router::url(
                                array(
                                    'controller' => 'contacts',
                                    'action' => 'ajax_get_country_garages',
                                    $customer_id
                                )
                            ),
                        )
                    );
                    if ($back == ConstantsBackContactUser::BACK_GARAGES_ID) {
                        echo $this->Form->hidden(
                            'Contact.garage_id',
                            array(
                                'value' => $this->request->params['named']['garage_id'],
                            )
                        );
                    }
                    ?>
                </div>
            <?php
            } elseif ($back == ConstantsBackContactUser::BACK_GARAGES) {
                echo $this->Form->hidden(
                    'Contact.garage_id',
                    array(
                        'value' => $customer_id
                    )
                );
            } ?>
            <?php if (is_null($customer_id)) {
                if (isset($distributors_list) && !empty($distributors_list)) {
                    $option_distributor = $distributors_list;
                }
            ?>
                <div id="distributor">
                    <?php
                    echo $this->Form->input(
                        'Contact.distributor_id',
                        array(
                            'label' => __t('Distributor.Distributor'),
                            'class' => 'select2Dinamico_distributor_dynamic',
                            'type' => 'select',
                            'multiple' => false,
                            'empty' => true,
                            'options' => $option_distributor,
                            'default' => $option_distributor,
                            'id' => 'distributor_select',
                            'data-url-country' => Router::url(
                                array(
                                    'controller' => 'contacts',
                                    'action' => 'ajax_get_country_distributors',
                                    $customer_id
                                )
                            ),
                            'data-url' => Router::url(
                                array(
                                    'controller' => 'distributors',
                                    'action' => 'ajax_get_distributors'
                                )
                            ),
                        )
                    ); ?>
                </div>
            <?php
            } elseif ($back == ConstantsBackContactUser::BACK_DISTRIBUTORS) {
                echo $this->Form->hidden(
                    'Contact.distributor_id',
                    array(
                        'value' => $customer_id
                    )
                );
            } ?>
        </div>
        <div class="m-top-1" id="contact_networks_form">
            <?php echo $this->Form->input(
                'NetworkContactBdm.network_id',
                array(
                    'label' => __t('Garage.Garage') . ' ' . __t('Network.Networks'),
                    'class' => 'select2-multiple',
                    'type' => 'select',
                    'empty' => true,
                    'multiple' => 'true',
                    'options' => $networks,
                    'id' => 'selected_network'
                )
            ); ?>
            <?php echo $this->Form->input(
                'DistributorNetworkContactBdm.distributor_network_id',
                array(
                    'label' => __t('Distributor.Distributor') . ' ' . __t('Network.Networks'),
                    'class' => 'select2-multiple',
                    'type' => 'select',
                    'empty' => true,
                    'multiple' => 'true',
                    'options' => $distributors_networks,
                    'id' => 'selected_distributor_network'
                )
            ); ?>
        </div>
        <div class="m-top-1" id="multiple-region-cv">
            <?php echo $this->Form->input(
                'Contact.regions_cv',
                array(
                    'class' => 'select2-multiple multiple-region',
                    'type' => 'select',
                    'multiple' => 'multiple',
                    'required' => true,
                    'empty' => true,
                    'options' => $regions_cv,
                    'id' => 'multiple-region-select-cv',
                    'label' => __t('General.Regions'),
                )
            ); ?>
        </div>
        <div class="m-top-1" id="multiple-region-lv">
            <?php echo $this->Form->input(
                'Contact.regions_lv',
                array(
                    'class' => 'select2-multiple multiple-region',
                    'type' => 'select',
                    'multiple' => 'multiple',
                    'required' => true,
                    'empty' => true,
                    'options' => $regions_lv,
                    'id' => 'multiple-region-select-lv',
                    'label' => __t('General.Regions'),
                )
            ); ?>
        </div>
        <br />
        <div class="aag-subtitle m-bottom-1">
            <?php echo __t('User.User') ?>
        </div>
        <div class="cnt-form-inputs m-bottom-1">
            <?php
            echo $this->Form->input(
                'User.username',
                array(
                    'type' => 'text',
                    'required' => true,
                    'label' => __t('User.User'),
                )
            );
            echo $this->Form->input(
                'User.name',
                array(
                    'type' => 'text',
                    'required' => true,
                    'id' => 'texto2',
                    'readonly' => true,
                    'label' => __t('User.Name'),
                )
            );
            echo $this->Form->input(
                'User.surname',
                array(
                    'type' => 'text',
                    'required' => true,
                    'id' => 'texto4',
                    'readonly' => true,
                    'label' => __t('User.Surname'),
                )
            );
            echo $this->Form->input(
                'User.role',
                array(
                    'type' => 'text',
                    'required' => true,
                    'id' => 'texto6',
                    'readonly' => true,
                    'label' => __t('User.Role'),
                )
            );
            echo $this->Form->input(
                'User.language_id',
                array(
                    'label' => __t('User.Language'),
                    'type' => 'select',
                    'class' => 'select2-multiple',
                    'multiple' => false,
                    'options' => $languages,
                    'default' => CakeSession::read('Auth.User.aag_region_id') == Configure::read('AAG_REGION_ID_UK_IRELAND') ? ConstantsLanguages::ENGLISH : null,
                )
            );
            echo $this->Form->input(
                'User.aag_region_id',
                array(
                    'type' => 'text',
                    'readonly' => true,
                    'id' => 'region-select-users',
                    'label' => __t('User.Region'),
                )
            );
            echo $this->Form->input(
                'User.aag_region_id',
                array(
                    'required' => true,
                    'type' => 'hidden',
                    'value' => $user_aag_region_id,
                )
            );
            echo $this->Form->input(
                'User.country_id',
                array(
                    'id' => 'country-select',
                    'label' => __t('Garage.Country'),
                    'type' => 'select',
                    'class' => 'select2-multiple',
                    'multiple' => false,
                    'options' => $countries,
                    'empty' => false,
                    'disabled' => true,
                    'default' => $user_role_id == ConstantsRoles::SUPER_ADMIN ? ConstantsConfigSelect::ALL : $country_id,
                )
            );
            echo $this->Form->input(
                'User.user_country_id',
                array(
                    'id' => 'country-select-hidden',
                    'required' => true,
                    'type' => 'hidden',
                    'value' => $country_id,
                )
            );
            ?>
            <div id="garage_distributor_type">
                <?php
                $garage_checked = true;
                $distributor_checked = false;

                echo $this->Form->input(
                    'User.garage_type',
                    array(
                        'label' => __t('Garage.Garage'),
                        'type' => 'checkbox',
                        'id' => 'garage_type',
                        'disabled' => true,
                        'checked' => $garage_checked
                    )
                );
                echo $this->Form->input(
                    'User.distributor_type',
                    array(
                        'label' => __t('Distributor.Distributor'),
                        'type' => 'checkbox',
                        'id' => 'distributor_type',
                        'disabled' => true,
                        'checked' => $distributor_checked
                    )
                ); ?>
            </div>
            <label class="center-check">
                <?php echo __t('User.Active'); ?>
                <div class="aag-switch round small">
                    <?php echo $this->Form->input('User.active', array('id' => 'activeInput-3', 'label' => false, 'div' => false, 'type' => 'checkbox', 'default' => true)); ?>
                    <label for="activeInput-3"></label>
                </div>
            </label>
        </div>
    </div>
</div>
<?php echo $this->Form->end(); ?>

<script>
    $(document).ready(function() {
        $("#texto1").keyup(function() {
            var value = $(this).val();
            $("#texto2").val(value);
        });
        $("#texto3").keyup(function() {
            var value = $(this).val();
            $("#texto4").val(value);
        });
        var arrayJS = <?php echo json_encode($positions_roles); ?>;

        $("#position").on('change', function() {
            var value = arrayJS[parseInt($(this).val())];
            $("#texto6").val(value);
        });

        var arrayRegions = <?php echo json_encode($aag_regions); ?>;

        var value = arrayRegions[parseInt($('#region-select').val())];
        $('#region-select-users').val(value);
    });
</script>
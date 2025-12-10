<?php echo $this->Html->script('dynamic_lists.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script')); ?>
<div class="cnt-form-inputs m-bottom-1">
    <div style="display:inline; ">
        <?php
        echo $this->Form->button(
            __t('Visit.Delete_search'),
            array(
                'id' => 'delete_search',
                'class' => 'aag-button medium red f-right d-none',
                'data-url' =>
                Router::url(
                    array(
                        'controller' => 'visits',
                        'action' => 'ajax_delete_search',
                    )
                )
            )
        );
        ?>
        <?php
        echo $this->Form->button(
            __t('Visit.Search_route'),
            array(
                'id' => 'search_route_map',
                'class' => 'aag-button medium f-right',
            )
        ); ?>
        <div class="p-right-1" style="width:20%; float:right !important;">
            <?php
            echo $this->Form->input(
                __t('Visit.Routes'),
                array(
                    'id' => 'search_route_map_select',
                    'type' => 'select',
                    'class' => 'select2-multiple',
                    'empty' => true,
                    'options' => $routes,
                )
            ); ?>
        </div>
    </div>
</div>

<?php
echo $this->Form->create(
    '',
    array(
        'id' => 'search',
        'class' => 'cnt-form-search',
        'style' => 'margin: 0px',
        'data-url' =>
        Router::url(
            array(
                'controller' => 'visits',
                'action' => 'ajax_search_garages',
            )
        )
    )
);
echo $this->Form->hidden(
    '',
    array(
        'id' => 'ajax_search_route',
        'class' => 'cnt-form-search',
        'data-url' =>
        Router::url(
            array(
                'controller' => 'visits',
                'action' => 'ajax_get_route_data',
            )
        )
    )
);
?>

<div class="cnt-form-search-title">
    <?php echo __t('Visit.Route'); ?>
</div>
<div class="cnt-form-inputs">
    <?php
    echo $this->Form->input(
        'route',
        array(
            'label' => __t('Visit.Route'),
            'type' => 'select',
            'class' => 'select2-multiple',
            'options' => $routes,
            'empty' => true,
            'required' => true,
            'id' => 'search_route',
        )
    );
    echo $this->Form->input(
        'city',
        array(
            'type' => 'text',
            'class' => 'clear_field',
            'required' => true,
            'id' => 'search_city',
            'label' => __t('Visit.City'),
        )
    );
    echo $this->Form->input(
        'location',
        array(
            'type' => 'text',
            'class' => 'clear_field',
            'required' => true,
            'id' => 'autocomplete-address',
            'data-map' => 'map-visits',
            'placeholder' => '',
            'label' => __t('Visit.Location'),
        )
    );
    echo $this->Form->input(
        'distance',
        array(
            'label' => __t('Visit.Distance'),
            'type' => 'select',
            'options' => $distance_options,
            'class' => 'select2-multiple clear_field',
            'empty' => true,
            'required' => true,
            'id' => 'search_distance',
        )
    );
    echo $this->Form->input(
        'last_visit',
        array(
            'label' => __t('Visit.Last_visit'),
            'type' => 'select',
            'class' => 'select2-multiple clear_field',
            'options' => $last_visit_options,
            'empty' => true,
            'required' => true,
            'id' => 'search_last_visit'
        )
    );
    echo $this->Form->input(
        'client_type',
        array(
            'label' => __t('Visit.Client_type'),
            'type' => 'select',
            'options' => $client_type_options,
            'class' => 'select2-multiple clear_field',
            'empty' => false,
            'required' => true,
            'id' => 'search_client_type',
        )
    );
    echo $this->Form->input(
        'network',
        array(
            'type' => 'select',
            'options' => $network_options,
            'class' => 'select2-multiple clear_field',
            'required' => true,
            'multiple' => true,
            'empty' => true,
            'id' => 'search_network',
            'label' => __t('Visit.Network'),
        )
    );
    echo $this->Form->input(
        'distributor',
        array(
            'class' => 'clear_field select2Dinamico cargar_distributors',
            'type' => 'select',
            'multiple' => true,
            'options' => array(),
            'required' => true,
            'empty' => true,
            'id' => 'search_distributor',
            'label' => __t('Visit.Distributor'),
            'data-texto1' => __t('General.Min_3_characters'),
        )
    ); ?>
    <div>
        <?php echo $this->Form->input(
            'status_id',
            array(
                'label' => __t('Garage.Network_status'),
                'type' => 'select',
                'class' => 'select2-multiple clear_field',
                'options' => $networks_statuses,
                'empty' => true,
                'id' => 'search_network_status'
            )
        ); ?>
    </div>
    <?php if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::BDM_AAG && CakeSession::read('Auth.User.role_id') != ConstantsRoles::BDM_TG) {
        echo $this->Form->input(
            'contact',
            array(
                'label' => __t('Contact.BDM'),
                'class' => 'dynamicSelect2_contacts_garages_bdm update_contacts_garages_bdm clear_field',
                'type' => 'select',
                'multiple' => false,
                'options' => isset($contacts_bdm) ? $contacts_bdm : array(),
                'required' => true,
                'empty' => true,
                'id' => 'search_contact',
            )
        );
    }
    if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN) {
        echo $this->Form->input(
            'aag_region_id',
            array(
                'label' => __t('General.Region'),
                'type' => 'select',
                'class' => 'select2-multiple clear_field',
                'options' => $regions,
                'id' => 'region-id',
                'default' => CakeSession::read('Auth.User.aag_region_id'),
                'empty' => true,
            )
        );
    } else {
        echo $this->Form->input(
            'aag_region_id',
            array(
                'id' => 'region-id',
                'required' => true,
                'type' => 'hidden',
                'value' => CakeSession::read('Auth.User.aag_region_id')
            )
        );
    }
    // if( CakeSession::read('Auth.User.role_id') == ConstantsRoles::BDM_AAG ||  CakeSession::read('Auth.User.role_id') == ConstantsRoles::BDM_TG){
    //     echo $this->Form->input(
    //         'search_my_customers',
    //         array(
    //             'label' => false,
    //             'type' => 'checkbox',
    //             'required' => true,
    //             'checked' => true,
    //             'id' => 'search_my_customers',
    //             'class' => 'd-none'
    //         )
    //     );
    // } else {
    ?>
    <label class="center-check p-top-1">
        <?php echo __t('Visit.My_customers'); ?>
        <div class="aag-switch round small">
            <?php echo $this->Form->input('search_my_customers', array('label' => false, 'div' => false, 'type' => 'checkbox', 'required' => true, 'id' => 'my_customers_visits', 'class' => 'clear_field')); ?>
            <label for="my_customers_visits"></label>
        </div>
    </label>
    <?php
    // }

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
    ); ?>
    <div class="d-none" id="latitude-center" data-lat="<?php echo 54.5; ?>">
    </div>
    <div class="d-none" id="longitude-center" data-lng="<?php echo -4; ?>">
    </div>
</div>
<div class="cnt-form-search-buttons">
    <?php
    echo $this->Form->button(
        __t('General.Search'),
        array(
            'type' => 'submit',
            'class' => 'aag-button medium',
            'id' => 'show_result',
        )
    );
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
<?php
$controller = $this->request->controller;
echo $this->Html->script(CakeSession::read('GOOGLE_MAPS_API'), array('block' => 'script'));
echo $this->Html->script('appointments_calendar.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->css('../js/lib/fullcalendar-3.10.5/fullcalendar.min.css', array('block' => 'script'));
echo $this->Html->script('lib/fullcalendar-3.10.5/lib/moment.min.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('lib/fullcalendar-3.10.5/fullcalendar.min.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('lib/fullcalendar-3.10.5/locale/' . __l() . '.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('lib/easy-ticker/jquery.easing.min.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('lib/easy-ticker/jquery.easy-ticker.min.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->css('../js/lib/dataTables/css/dataTables.foundation.min.css', array('block' => 'script'));
echo $this->Html->script('lib/dataTables/js/jquery.dataTables.min.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('lib/moment.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('lib/datetime-moment.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('lib/datetime.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('gd_export_excel.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('jquery.fileDownload.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('gmaps_visits.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('visits.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('lib/purify.min.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
$userAagRegionId = CakeSession::read('Auth.User.aag_region_id');
$userRole = CakeSession::read('Auth.User.role_id');
?>
<script>
    var isIE11 = !!navigator.userAgent.match(/Trident.*rv\:11\./);
    if (!isIE11) {
        PeticionAjax.mostrarCargando();
    }
</script>
<?php
echo $this->Form->hidden(
    'Appointment.user_assigned_id',
    array(
        'id' => 'assigned-to',
        'type' => 'select',
        'default' => CakeSession::read('Auth.User.id'),
        'empty' => false,
        'options' => array(''),
    )
);
echo $this->Form->hidden(
    '',
    array(
        'id' => 'ajax_get_data_garage',
        'data-url' => Router::url(
            array(
                'controller' => 'visits',
                'action' => 'ajax_get_data_garage'
            )
        )
    )
);
echo $this->Form->hidden(
    'route_id',
    array(
        'id' => 'route_id',
        'value' => $route_id
    )
);
echo $this->Form->hidden(
    'contact_id',
    array(
        'id' => 'contact_id',
        'value' => CakeSession::read('Auth.User.contact_id')
    )
);

echo $this->Form->hidden(
    'Aag_region_id.id',
    array(
        'id' => 'aag_region_id',
        'value' => CakeSession::read('Auth.User.aag_region_id')
    )
);

echo $this->Form->hidden(
    'Aag_region_id.UK',
    array(
        'id' => 'aag_region_id_uk',
        'value' => Configure::read('AAG_REGION_ID_UK_IRELAND')
    )
);

?>
<div style="display: none;" id="modal-calendar" class="reveal-modal background-color-primary" data-reveal aria-hidden="true" role="dialog">
    <div id="calendar-visits" data-user_assigned_id="<?php echo CakeSession::read('Auth.User.id'); ?>" data-url_events="
        <?php echo Router::url(
            array(
                'controller' => 'appointments',
                'action' => 'ajax_events_list'
            )
        ); ?>"></div>
    <a class="close-modal" data-close aria-label="Close">&#215;</a>
</div>
<div class="d-none">
    <?php echo $this->Form->input(
        'users',
        array(
            'label' => false,
            'type' => 'select',
            'class' => 'select2-multiple',
            'options' => $users,
            'empty' => true,
            'required' => true,
            'id' => 'users_list',
        )
    ); ?>
</div>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('CRM.Crm'),
                array(
                    'controller' => 'dashboard',
                    'action' => 'home'
                )
            ),
            __t('CRM.Planning_visits'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back') ?></a>
        <?php
        echo $this->Html->link(
            '<span class="icon-garages"></span>' . __t('Garage.Garages'),
            array(
                'controller' => 'visits',
                'action' => 'home'
            ),
            array(
                'escape' => false,
                'class' => 'aag-button medium one'
            )
        );
        echo $this->Html->link(
            '<span class="icon-distributors"></span>' . __t('Distributor.Distributors'),
            array(
                'controller' => 'visits',
                'action' => 'home_distributor'
            ),
            array(
                'escape' => false,
                'class' => 'aag-button medium one'
            )
        );

        echo $this->Html->link(
            "<span class='ion-map'></span>" . __t('Visit.Routes_lists'),
            array(
                'controller' => 'visits',
                'action' => 'routes_list'
            ),
            array(
                'escape' => false,
                'class' => 'aag-button medium one'
            )
        );
        ?>
    </div>
</div>
<div class="cnt-data aag-padding position-relative">
    <div class="aag-title">
        <?php echo __t('Menu.Planning_visits'); ?>: <?php echo __t('Garage.Garages'); ?>
    </div>
    <div class="row">
        <div class="aag-subtitle p-top-1">
            <?php echo __t('General.Search_planning_visit_1'); ?>
        </div>
        <?php echo $this->element('../Visits/Elements/search'); ?>
    </div>
    <div class="f-right cnt-legend custom-padding">
        <div class="title-legend"><?php echo __t('Appointment.Period_of_time'); ?></div>
        <div class="d-inline-block m-right-1">
            <span class="icon-legend cursor-default" style="background-color: #979797"></span>
            <label class="cursor-default m-0-i" for="check-cancelled"><?php echo __t('Visit.No_visit'); ?></label>
        </div>
        <div class="d-inline-block m-right-1">
            <span class="icon-legend c-fallo"></span>
            <label for="check-cancelled" class="unselectable m-0-i"><?php echo __t('Visit.Plus_6_months') ?></label>
        </div>
        <div class="d-inline-block m-right-1">
            <span class="icon-legend c-informacion"></span>
            <label for="check-rescheduled" class="unselectable m-0-i"><?php echo __t('Visit.3_months_6_months') ?></label>
        </div>
        <div class="d-inline-block m-right-1">
            <span class="icon-legend c-exito"></span>
            <label for="check-accomplished" class="unselectable m-0-i"><?php echo __t('Visit.3_months') ?></label>
        </div>
    </div>
    <div class="columns medium-12" id="search_results" style="margin-bottom:80px" >
        <div class="medium-12 columns background-color-primary p-1 fieldset_visit">
            <?php /* <div class="title-before-table "><?php echo __t('General.Search'); ?></div> */ ?>
            <div id="results_table_container">
                <?php echo $this->element('../Visits/Elements/results_table'); ?>
            </div>
            <div class="ta-right d-inline-block w-100p clear p-top-1">
                <?php
                echo $this->Html->link(
                    "<span class='ion-ios-plus-outline'></span>" .  __t('Visit.Add_selected_garages'),
                    'javascript:void(0)',
                    array(
                        'escape' => false,
                        'id' => 'add_garage',
                        'class' => 'aag-button medium m-top-1 clear'
                    )
                );
                ?>
            </div>
        </div>
    </div>

    <div id="alert-div"></div>
    <div class="aag-subtitle ">
        <?php echo __t('General.Search_planning_visit_2'); ?>
    </div>
    <div class="o-auto">
        <table class="table-tracking">
            <thead>
                <tr>
                    <?php if ((isset($userAagRegionId) && ($userAagRegionId != ConstantsAAGRegionId::BENELUX)) || ($userRole == ConstantsRoles::SUPER_ADMIN)) { ?>
                        <th><?php echo __t('Garage.G_number'); ?></th>
                    <?php } ?>
                    <th><?php echo __t('Appointment.Customer'); ?></th>
                    <th><?php echo __t('Distributor.Postcode'); ?></th>
                    <th><?php echo __t('Visit.City'); ?></th>
                    <!-- <th><?php echo __t('Visit.Location'); ?></th> -->
                    <th class="ta-center"><?php echo __t('Visit.Last_visit'); ?></th>
                    <th class="ta-center"><?php echo __t('Visit.Time'); ?></th>
                    <th class="ta-center"><?php echo __t('General.Actions'); ?></th>
                </tr>
            </thead>
            <tbody id="table-added-garages"></tbody>
        </table>
    </div>

    <div class="d-inline-block w-100p ta-right cnt-blue-red p-top-1 clear">
        <?php
        echo $this->Html->link(
            "<span class='ion-map'></span>" . __t('Visit.Add_route'),
            'javascript:void(0)',
            array(
                'escape' => false,
                'id' => 'add_route',
                'class' => 'aag-button medium',
                'data-type' => ConstantsVisitType::GARAGE,
                'data-url' => Router::url(
                    array(
                        'controller' => 'routes',
                        'action' => 'ajax_create_route'
                    )
                )
            )
        );
        echo $this->Html->link(
            "<span class='ion-map'></span>" . __t('Visit.Edit_route'),
            'javascript:void(0)',
            array(
                'escape' => false,
                'id' => 'edit_route',
                'class' => 'aag-button medium d-none',
                'data-type' => ConstantsVisitType::GARAGE,
                'data-url' => Router::url(
                    array(
                        'controller' => 'routes',
                        'action' => 'ajax_edit_route'
                    )
                )
            )
        );
        echo $this->Form->hidden(
            'hidden_date',
            array(
                'id' => 'date-visit'
            )
        );
        echo $this->Html->link(
            "<span class='ion-ios-plus-outline'></span>" . __t('Visit.Generate_visit'),
            'javascript:void(0)',
            array(
                'escape' => false,
                'id' => 'show_calendar',
                'class' => 'aag-button medium',
                'data-url' => Router::url(
                    array(
                        'controller' => 'visits',
                        'action' => 'generate_visits'
                    )
                )
            )
        );
        echo $this->Html->link(
            "<span class='ion-ios-plus-outline'></span>" . __t('Visit.Generate_visit'),
            'javascript:void(0)',
            array(
                'escape' => false,
                'id' => 'generate_visit',
                'class' => 'aag-button medium d-none',
                'data-user-id' => $user['id'],
                'data-user-rol' => $user['Role']['id'],
                'data-bdm-rol' => ConstantsRoles::BDM_AAG,
                'data-url' => Router::url(
                    array(
                        'controller' => 'visits',
                        'action' => 'generate_visits'
                    )
                )
            )
        );
        echo $this->Form->button(
            "<span style='float:left'><img src='/img/iconos/excel.svg' width='20px'></span>" .
                __t('General.Export_garages'),
            array(
                'class' => 'aag-button medium conSVG gd-export-excel-planing-visit-js',
                'value' => 'submit',
                'escape' => false,
                'name' => 'export',
                'data-url' => Router::url(
                    array(
                        'controller' => 'garages',
                        'action' => 'garages_planning_visit_excel',
                        $controller
                    )
                ),
            )
        );
        echo $this->Html->link(
            __t('Visit.Delete_all_garages_added'),
            'javascript:void(0)',
            array(
                'escape' => false,
                'id' => 'delete_all_garages_added',
                'class' => 'aag-button medium red',
                'type' => 'button',
            )
        );
        ?>
    </div>
    <div class="columns medium-12 p-top-1 p-right-0">
        <div class="f-right cnt-legend custom-padding">
            <div class="title-legend"><?php echo __t('Appointment.Period_of_time'); ?></div>
            <div class="d-inline-block m-right-1">
                <span class="icon-legend cursor-default" style="background-color: #979797"></span>
                <label class="cursor-default m-0-i" for="check-cancelled"><?php echo __t('Visit.No_visit'); ?></label>
            </div>
            <div class="d-inline-block m-right-1">
                <span class="icon-legend c-fallo"></span>
                <label for="check-cancelled" class="unselectable m-0-i"><?php echo __t('Visit.Plus_6_months') ?></label>
            </div>
            <div class="d-inline-block m-right-1">
                <span class="icon-legend c-informacion"></span>
                <label for="check-rescheduled" class="unselectable m-0-i"><?php echo __t('Visit.3_months_6_months') ?></label>
            </div>
            <div class="d-inline-block m-right-1">
                <span class="icon-legend c-exito"></span>
                <label for="check-accomplished" class="unselectable m-0-i"><?php echo __t('Visit.3_months') ?></label>
            </div>
        </div>
    </div>
    <!--
        <div class="ta-center" style="height: 0px">
            <input id="last_position" type="checkbox"/>
            <label for="last_position" class="label-check"><?php echo __t('Network.Remember_position') ?></label>
        </div>
    -->
    <div id="map-visits" class="contenedor-mapa" style="height: 550px;" data-editable="<?php echo ConstantsBooleans::YES; ?>" data-url="
                    <?php echo Router::url(
                        array(
                            'controller' => 'visits',
                            'action' => 'location_garages'
                        )
                    ); ?>"></div>
    <div id="info"></div>
    <div class="alliance_bar"></div>
</div>
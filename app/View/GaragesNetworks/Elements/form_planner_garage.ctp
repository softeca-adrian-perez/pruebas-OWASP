<?php
echo $this->Html->css('../js/lib/fullcalendar-3.10.5/fullcalendar.min.css', array('block' => 'script'));
echo $this->Html->script('lib/fullcalendar-3.10.5/lib/moment.min.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('lib/fullcalendar-3.10.5/fullcalendar.min.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('lib/fullcalendar-3.10.5/locale/' . __l() . '.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('opening-times.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
$days = array('monday','tuesday','wednesday','thursday','friday','saturday','sunday');
echo $this->Form->create(
    'GarageNetwork',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data',
        'id' => 'opening-times-form'
    )
);
    echo $this->Form->hidden('GarageNetwork.id');
    foreach($days as $day)
    {
        if (!empty($garage['Garage'][strtolower($day) . '_open_1']) ||
            !empty($garage['Garage'][strtolower($day) . '_open_2'])) {
            echo $this->Form->hidden(
                "GarageNetwork.".$day . '_planner_open_1',
                array(
                    'id' => $day . '-open-1',
                )
            );
            echo $this->Form->hidden(
                "GarageNetwork.".$day . '_planner_closed_1',
                array(
                    'id' => $day . '-closed-1',
                )
            );
            echo $this->Form->hidden(
                "GarageNetwork.".$day . '_planner_open_2',
                array(
                    'id' => $day . '-open-2',
                )
            );
            echo $this->Form->hidden(
                "GarageNetwork.".$day . '_planner_closed_2',
                array(
                    'id' => $day . '-closed-2',
                )
            );
        }
    }
    ?>
    <?php if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN) { ?>
        <div class="is_superAdmin-js"></div>
    <?php } ?>
    <div class="buttons-fixed-double-tabs">
        <div>
            <?php echo $this->element('Comun/form_actions', $cancel_action); ?>
        </div>
    </div>
    <div class="aag-padding">
        <div class="aag-subtitle">
            <?php echo __t('GarageNetwork.Planner_configuration'); ?>
        </div>
        <div class="cnt-max-date-per-day" style="max-width: 470px;">
            <?php
            echo $this->Form->input(
                "GarageNetwork.booking_days_min_from",
                array(
                    'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                    'type' => 'number',
                    'min' => 1,
                    'max' => 1000,
                    'label' => __t('GarageNetwork.Booking_days_from'),
                )
            );
            echo $this->Form->input(
                "GarageNetwork.booking_days_max_to",
                array(
                    'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                    'type' => 'number',
                    'min' => 1,
                    'max' => 1000,
                    'label' => __t('GarageNetwork.Booking_days_to'),
                )
            );
            ?>
        </div>
        <br />
        <div class="cnt-max-date-per-day" id="slots_inputs-js">
            <?php
            $disabled_value_1 = true;
            $disabled_value_2 = true;

            foreach($days as $day)
            {
                if(!empty($garage['Garage'][strtolower($day) . '_open_1']) || !empty($garage['Garage'][strtolower($day) . '_open_2']))
                {
                    $disabled_value_1 = ((!empty($garage_network['GarageNetwork'][$day . '_planner_open_1']) && !empty($garage_network['GarageNetwork'][$day . '_planner_closed_1'])) && CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) ? false : true;
                    $disabled_value_2 = (!empty($garage_network['GarageNetwork'][$day . '_planner_open_2']) && !empty($garage_network['GarageNetwork'][$day . '_planner_closed_2'])) && CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true;

                    ?>
                    <div>
                        <span><?php echo ucfirst($day);?></span>
                        <?php
                            if (!empty($garage['Garage'][strtolower($day) . '_open_1'])) {
                                echo $this->Form->input(
                                    "GarageNetwork.".strtolower($day) . '_planner_max_1',
                                    array(
                                        'id' => 'day-' . $day . '-slot-1',
                                        'type' => 'number',
                                        'min' => 1,
                                        'max' => 100,
                                        'label' => __t('General.Slot') . ' 1',
                                        'title' => __t('GarageNetwork.Number_bookings_allowed_slot'),
                                        'disabled' => $disabled_value_1
                                    )
                                );
                            }
                            if (!empty($garage['Garage'][strtolower($day) . '_open_2']) || !empty($garage['Garage'][strtolower($day) . '_open_1'])) {
                                echo $this->Form->input(
                                    "GarageNetwork.".$day . '_planner_max_2',
                                    array(
                                        'id' => 'day-' . $day . '-slot-2',
                                        'type' => 'number',
                                        'min' => 1,
                                        'max' => 100,
                                        'label' => __t('General.Slot') . ' 2',
                                        'title' => __t('GarageNetwork.Number_bookings_allowed_slot'),
                                        'disabled' => $disabled_value_2
                                    )
                                );
                            }
                        ?>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
        <div>
            <div class="cnt-legend p-vertical-1">
                <div><?php echo __t('Garage.Calendar_legend');?></div>
                <div>
                    <span class="icon-legend bg-blue"></span><?php echo __t('General.Planner_times') ?>
                </div>
                <div>
                    <span class="icon-legend c-exito"></span><?php echo __t('Garage.Opening_hours') ?>
                </div>
            </div>
        </div>
        <div id="calendar"
            data-url_events="<?php echo Router::url(
                array(
                    'controller' => 'garages_networks',
                    'action' => 'ajax_planner_hours_list',
                    $garage_network['GarageNetwork']['id'],
                    $garage['Garage']['id']
                )
            ); ?>">
        </div>
    </div>
<?php echo $this->Form->end(); ?>
<style>
    .cnt-max-date-per-day
    {
        display: grid;
        gap: 15px;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    }
    .cnt-max-date-per-day > div
    {
        background-color: var(--container-elements-color);
        border-radius: 10px;
        padding: 10px
    }
    .cnt-max-date-per-day > div span
    {
        font-weight: bold;
        font-size: 14px;
    }
    .cnt-max-date-per-day > div label
    {
        font-weight: normal;
        font-size: 12px !important;
    }
    .cnt-max-date-per-day > div > div + div
    {
        margin-top: 10px;
    }
    .cnt-max-date-per-day > div input
    {
        margin: 0;
        width: 100%;
        min-width: 100%;
        max-width: 100%;
    }
    .cnt-max-date-per-day > div input[disabled]
    {
        color: transparent;
    }
</style>
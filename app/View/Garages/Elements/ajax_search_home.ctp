<?php
echo $this->Html->script('/js/garages_users_permissions.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
$config = CakeSession::read('Auth.User.Config'); ?>
<div class="o-auto">
    <table class="table-tracking">
        <thead>
            <tr>
                <th><?php echo $this->Paginator->sort('Garage.name', __t('Appointment.Customer')); ?></th>
                <th><?php echo  __t('Garage.BDM'); ?></th>
                <?php if ($config[ConstantsConfig::COUNTY_COUNTRY_GARAGE]) { ?>
                    <th><?php echo $this->Paginator->sort('Country.name', __t('Garage.Country')); ?></th>
                <?php } ?>
                <th><?php echo __t('General.Seo_city'); ?></th>
                <th>
                    <?php
                    echo $this->Paginator->sort('Garage.address1', __t('Garage.Address'));
                    ?>
                </th>
                <th><?php echo $this->Paginator->sort('Garage.phone', __t('Garage.Phone')); ?></th>
                <?php
                //If it's Benelux there is not garage g_number, but if the role is Super Admin, garages from both regions can be present at the same time
                if ((isset($user_aag_region_id) && ($user_aag_region_id != ConstantsAAGRegionId::BENELUX)) || ($user_role == ConstantsRoles::SUPER_ADMIN)) { ?>
                    <th><?php echo $this->Paginator->sort('Garage.g_number_id', __t('Garage.G_number')); ?></th>
                <?php } ?>
                <th>
                    <?php echo $this->Paginator->sort('Garage.ref_code', __t('Garage.Ref_code')); ?>
                </th>
                <th class="ta-center">
                    <?php
                    echo $this->Paginator->sort('Garage.last_visit', __t('CRM.Last_visit'));
                    ?>
                </th>
                <th class="ta-center">
                    <?php echo __t('CRM.Remaining_days'); ?>
                </th>
                <?php
                $rolesActions = array(ConstantsRoles::SUPER_ADMIN, ConstantsRoles::ADMIN, ConstantsRoles::GARAGE_NETWORK_MANAGER);
                if (in_array(CakeSession::read('Auth.User.role_id'), $rolesActions)) {
                ?>
                    <th class="ta-center">
                        <?php echo __t('General.Actions'); ?>
                    </th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($garages as $garage) { ?>
                <tr>
                    <td>
                        <?php
                        if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::DISTRIBUTOR) {
                            echo $this->Html->link(
                                $garage['Garage']['name'],
                                array(
                                    'controller' => 'garages',
                                    'action' => 'my_data',
                                    $garage['Garage']['id']
                                ),
                                array(
                                    'class' => 'c-primary'
                                )
                            );
                        } elseif (!in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GARAGE, ConstantsRoles::GENERIC_STAFF))) {
                            echo $this->Html->link(
                                $garage['Garage']['name'],
                                array(
                                    'controller' => 'garages',
                                    'action' => 'edit',
                                    $garage['Garage']['id']
                                ),
                                array(
                                    'class' => 'c-primary'
                                )
                            );
                        }
                        ?>
                    </td>
                    <td>
                        <?php if ($garage['BDMS']) { ?>
                            <?php foreach ($garage['BDMS'] as $bdm) { ?>
                                <div>
                                    <?php echo h($bdm[0]['full_name']) ?>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </td>
                    <?php if ($config[ConstantsConfig::COUNTY_COUNTRY_GARAGE]) { ?>
                        <td>
                            <?php
                            if (isset($garage['Country']['name'])) {
                                echo h($garage['Country']['name']);
                            }
                            ?>
                        </td>
                    <?php } ?>
                    <td>
                        <?php if ($garage['Garage']['city_id'] && isset($cities[$garage['Garage']['city_id']])) {
                            echo h($cities[$garage['Garage']['city_id']]);
                        }  ?>
                    </td>
                    <td>
                        <?php echo h($garage['Garage']['address1']); ?>
                    </td>
                    <td>
                        <?php echo h($garage['Garage']['phone']); ?>
                    </td>
                    <?php
                    if ((isset($user_aag_region_id) && ($user_aag_region_id != ConstantsAAGRegionId::BENELUX)) || ($user_role == ConstantsRoles::SUPER_ADMIN)) { ?>
                        <td> <?php echo h($garage['Garage']['g_number_id']); ?> </td>
                    <?php } ?>
                    <td>
                        <?php echo h($garage['Garage']['ref_code']); ?>
                    </td>
                    <?php
                    $class = '';
                    if (isset($garage['Garage']['last_visit'])) {
                        if (strtotime('+6 month', strtotime($garage['Garage']['last_visit'])) < strtotime(date('Y-m-d'))) {
                            $class = 'c-fallo';
                        } elseif (strtotime('+3 month', strtotime($garage['Garage']['last_visit']))  > strtotime(date('Y-m-d'))) {
                            $class = 'c-exito';
                        } else {
                            $class = 'c-informacion';
                        }
                    }
                    ?>
                    <td class="ta-center <?php echo $class ?>">
                        <?php echo (!is_null($garage['Garage']['last_visit'])) ? Fecha::toFormatoVistaFecha($garage['Garage']['last_visit']) : '--'; ?>
                    </td>
                    <td class="ta-center <?php echo $class ?>">
                        <?php echo h($garage['LatestVisit']); ?>
                        <?php echo __t('General.Days'); ?>
                    </td>
                    <?php if (in_array(CakeSession::read('Auth.User.role_id'), $rolesActions)) { ?>
                        <td class="display: inline-flex; justify-content: center;">
                            <div style="display: flex; align-items: center; gap: 5px;">
                                <?php if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::ADMIN) {
                                    echo $this->Html->link(
                                        '<span class="cursor-pointer icon-see"></span>',
                                        '#',
                                        array(
                                            'escape' => false,
                                            'onclick' => "$('.modal-permissions').html('');
                                                          $('#permission_list').prop('selectedIndex',0);
                                                          $('#permission_list').select2();
                                                          $('#garage_id_click').val(" . $garage['Garage']['id'] . ")",
                                            'data-open' => 'ModalPermissions',
                                            'title' => __t('Maintenance.Permissions'),
                                            'data-garage-id' => $garage['Garage']['id'],
                                            'class' => 'view_garage_permission'
                                        )
                                    );
                                } ?>
                                <?php
                                if (isset($garage['ShowGarageAccess'])) {
                                    foreach ($garage['ShowGarageAccess'] as $key => $showAccess) {
                                        if ($showAccess) {
                                            $image = $garage['NetworkId'][$key] == NETWORK_ID_AGN ?
                                                'agn_icon.png' : 'gv_icon.png';
                                            echo $this->HTML->image(
                                                $image,
                                                array(
                                                    'url' => '/garages_networks/network_dashboard/' . $garage['GarageNetworkId'][$key],
                                                    'title' => $garage['Network'][$key]
                                                )
                                            );
                                        }
                                    }
                                } ?>
                            </div>
                        </td>
                    <?php } ?>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<br />
<?php
echo $this->element('Comun/paginacion_ajax');
echo $this->Form->hidden(
    'GarageId',
    array(
        'id' => 'garage_id_click',
        'value' => null
    )
); ?>
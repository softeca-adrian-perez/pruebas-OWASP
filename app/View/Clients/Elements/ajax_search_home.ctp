<?php $config = CakeSession::read('Auth.User.Config'); ?>
<div class="o-auto">
    <table class="table-tracking">
        <thead>
            <tr>
                <th><?php echo $this->Paginator->sort('Garage.name', __t('Appointment.Customer')); ?></th>
                <th><?php echo __t('Garage.BDM'); ?></th>
                <?php if ($config[ConstantsConfig::COUNTY_COUNTRY_GARAGE]) { ?>
                    <th><?php echo __t('Garage.County'); ?></th>
                <?php } ?>
                <th><?php echo $this->Paginator->sort('Garage.town', __t('General.Seo_city')); ?></th>
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
                <?php if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) { ?>
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
                        <?php echo $this->Html->link(
                            $garage['Garage']['name'],
                            array(
                                'controller' => 'clients',
                                'action' => 'report',
                                $garage['Garage']['id']
                            ),
                            array(
                                'class' => 'c-primary'
                            )
                        ); ?>
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
                            if ($garage['Garage']['province_id']) {
                                echo h($province_list[$garage['Garage']['province_id']]);
                            }
                            ?>
                        </td>
                    <?php } ?>
                    <td>
                        <?php echo h($garage['Garage']['town']); ?>
                    </td>
                    <td>
                        <?php echo h($garage['Garage']['address1']); ?>
                    </td>
                    <td>
                        <?php echo h($garage['Garage']['phone']); ?>
                    </td>
                    <?php
                    if ((isset($user_aag_region_id) && ($user_aag_region_id != ConstantsAAGRegionId::BENELUX)) || ($user_role == ConstantsRoles::SUPER_ADMIN)) { ?>
                        <td>
                            <?php echo h($garage['Garage']['g_number_id']); ?>
                        </td>
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
                    <td class="ta-center">
                        <?php if ($garage['Garage']['status'] != ConstantsGarageStatusDe::POTENTIAL) { ?>
                        <?php if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
                                echo $this->Html->link(
                                    '<span class="icon-Visit_create" style="font-size: 22px;"></span>',
                                    array(
                                        'controller' => 'appointments',
                                        'action' => 'add',
                                        $garage['Garage']['id'],
                                        '?' => $this->request->query
                                    ),
                                    array(
                                        'escape' => false,
                                        'title' => __t('CRM.Garage_appointment'),
                                    )
                                );
                            }
                        } ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<?php echo $this->element('Comun/paginacion_ajax'); ?>
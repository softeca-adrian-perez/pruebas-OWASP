<div class="o-auto">
    <table class="table-tracking">
        <thead>
            <tr>
                <th class="ta-center">
                    <?php echo $this->Paginator->sort('Distributor.account_number', __t('Distributor.Account_number')); ?>
                </th>
                <th>
                    <?php echo $this->Paginator->sort('Distributor.name', __t('Appointment.Customer')); ?>
                </th>
                <th class="ta-center">
                    <?php echo $this->Paginator->sort('Distributor.head_office', __t('Distributor.Head_office')); ?>
                </th>
                <th><?php echo __t('Garage.BDM'); ?></th>
                <th>
                    <?php echo __t('Distributor.Trading_group'); ?>
                </th>
                <th><?php echo $this->Paginator->sort('Distributor.postcode', __t('Distributor.Postcode')); ?></th>
                <th><?php echo $this->Paginator->sort('Distributor.town', __t('Distributor.Town')); ?></th>
                <th><?php echo $this->Paginator->sort('Distributor.phone', __t('Distributor.Phone')); ?></th>
                <th class="ta-center">
                    <?php echo $this->Paginator->sort('Distributor.last_visit', __t('CRM.Last_visit')); ?>
                </th>
                <?php if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) { ?>
                    <th class="ta-center">
                        <?php echo __t('General.Actions'); ?>
                    </th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($distributors as $distributor) { ?>
                <tr>
                    <td class="ta-center">
                        <?php echo h($distributor['Distributor']['account_number']); ?>
                    </td>
                    <td>
                        <?php echo $this->Html->link(
                            $distributor['Distributor']['name'],
                            array(
                                'controller' => 'clients',
                                'action' => 'report_distributor',
                                $distributor['Distributor']['id']
                            ),
                            array(
                                'class' => 'c-primary'
                            )
                        ); ?>
                    </td>
                    <td class="ta-center">
                        <?php echo Booleano::toString($distributor['Distributor']['head_office']); ?>
                    </td>
                    <td>
                        <?php if ($distributor['BDMS']) { ?>
                            <?php foreach ($distributor['BDMS'] as $bdm) { ?>
                                <div>
                                    <?php echo h($bdm[0]['full_name']) ?>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </td>
                    <td>
                        <?php echo h($trading_groups[$distributor['Distributor']['trading_group_id']]); ?>
                    </td>
                    <td>
                        <?php echo h($distributor['Distributor']['postcode']); ?>
                    </td>
                    <td>
                        <?php echo h($distributor['Distributor']['town']); ?>
                    </td>
                    <td>
                        <?php echo h($distributor['Distributor']['phone']); ?>
                    </td>
                    <?php
                    $class = '';
                    if (isset($distributor['Distributor']['last_visit'])) {
                        if (strtotime('+6 month', strtotime($distributor['Distributor']['last_visit'])) < strtotime(date('Y-m-d'))) {
                            $class = 'c-fallo';
                        } elseif (strtotime('+3 month', strtotime($distributor['Distributor']['last_visit']))  > strtotime(date('Y-m-d'))) {
                            $class = 'c-exito';
                        } else {
                            $class = 'c-informacion';
                        }
                    }
                    ?>
                    <td class="ta-center <?php echo $class ?>">
                        <?php echo (!is_null($distributor['Distributor']['last_visit'])) ? Fecha::toFormatoVistaFecha($distributor['Distributor']['last_visit']) : '--'; ?>
                    </td>


                    <td class="ta-center">
                        <?php if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
                            echo $this->Html->link(
                                '<span class="icon-Visit_create"></span>',
                                array(
                                    'controller' => 'appointments',
                                    'action' => 'add',
                                    'null',
                                    $distributor['Distributor']['id'],
                                    '?' => $this->request->query
                                ),
                                array(
                                    'escape' => false,
                                    'title' => __t('CRM.Garage_appointment'),
                                )
                            );
                        }
                        ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<br />
<?php echo $this->element('Comun/paginacion_ajax_distributor'); ?>
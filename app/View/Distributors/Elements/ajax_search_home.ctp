<?php echo $this->Html->script('/js/distributors_users_permissions.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script')); ?>
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
                <th>
                    <?php echo __t('Distributor.Trading_group'); ?>
                </th>
                <th>
                    <?php echo $this->Paginator->sort('Distributor.postcode', __t('Distributor.Postcode')); ?>
                </th>
                <th>
                    <?php echo $this->Paginator->sort('Distributor.town', __t('Distributor.Town')); ?>
                </th>
                <th>
                    <?php echo $this->Paginator->sort('Distributor.phone', __t('Distributor.Phone')); ?>
                </th>
                <!--
                <th>
                    <?php echo $this->Paginator->sort('Distributor.reg_number', __t('Distributor.Reg_number')); ?>
                </th> -->
                <th class="ta-center">
                    <?php echo $this->Paginator->sort('Distributor.last_visit', __t('CRM.Last_visit')); ?>
                </th>
                <?php if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::ADMIN) { ?>
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
                                'controller' => 'distributors',
                                'action' => 'view',
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
                    <!--
                    <td>
                        <?php echo h($distributor['Distributor']['reg_number']); ?>
                    </td> -->
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
                    <?php if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::ADMIN) { ?>
                        <td class="ta-center">
                            <?php
                            echo $this->Html->link(
                                '<span class="c-primary cursor-pointer aag-icon-ojo"></span>',
                                '#',
                                array(
                                    'escape' => false,
                                    'onclick' => "$('.modal-permissions').html('');
                                              $('#permission_list').prop('selectedIndex',0);
                                              $('#permission_list').select2();
                                              $('#distributor_id_click').val(" . $distributor['Distributor']['id'] . ")",
                                    'data-open' => 'ModalPermissions',
                                    'title' => __t('Maintenance.Permissions'),
                                    'data-distributor-id' => $distributor['Distributor']['id'],
                                    'class' => 'view_distributor_permission'
                                )
                            );
                            ?>
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
echo $this->Form->hidden('DistributorId', array('id' => 'distributor_id_click', 'value' => null));
?>
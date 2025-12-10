<?php $action = in_array($this->Acceso->rol(), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR)) ? 'my_data' : 'view'; ?>
<div class="cnt-breadcrumb">
    <div>
        <?php
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
            __t('Logs.Logs_changes')
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back') ?></a>
    </div>
</div>
<?php echo $this->element('../Distributors/tabs', array('selected' => 'logs_changes',)); ?>
<div class="cnt-data p-top-1">
    <div class="cnt-data-element">
        <div class="aag-title m-bottom-1">
            <?php echo h($distributor['Distributor']['name']); ?>
        </div>
        <?php echo $this->element('../Distributors/Elements/search_logs_changes'); ?>
        <div class="aag-subtitle">
            <?php echo __t('Logs.Logs_list') ?>
        </div>
        <div class="o-auto">
            <table class="table-tracking tabla-responsive">
                <thead>
                    <tr>
                        <th><?php echo $this->Paginator->sort('LogTable.name_' . __l(), __t('Logs.Table')); ?></th>
                        <th><?php echo $this->Paginator->sort('LogField.name_' . __l(), __t('Logs.Field')); ?></th>
                        <th><?php echo $this->Paginator->sort('User.name', __t('Logs.User')); ?></th>
                        <th><?php echo $this->Paginator->sort('LogChange.date', __t('Logs.Date')); ?></th>
                        <th><?php echo $this->Paginator->sort('LogChange.old_value', __t('Logs.Old_value')); ?></th>
                        <th><?php echo $this->Paginator->sort('LogChange.new_value', __t('Logs.New_value')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs_changes as $log_change) { ?>
                        <tr>
                            <td>
                                <?php echo h($log_change['LogTable']['name_' . __l()]); ?>
                            </td>
                            <td>
                                <?php echo h($log_change['LogField']['name_' . __l()]); ?>
                            </td>
                            <td>
                                <?php echo h($log_change['User']['name']); ?>
                            </td>
                            <td>
                                <?php echo h(date("d-m-Y / H:i", strtotime($log_change['LogChange']['date']))); ?>
                            </td>
                            <td>
                                <?php if (Fecha::isDate(__t($log_change['LogChange']['old_value']))) {
                                    echo Fecha::toFormatoVistaFecha(__t($log_change['LogChange']['old_value']));
                                } else {
                                    echo h(__t($log_change['LogChange']['old_value']));
                                } ?>
                            </td>
                            <td>
                                <?php if (Fecha::isDate(__t($log_change['LogChange']['new_value']))) {
                                    echo Fecha::toFormatoVistaFecha(__t($log_change['LogChange']['new_value']));
                                } else {
                                    echo h(__t($log_change['LogChange']['new_value']));
                                } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <br />
        <?php echo $this->element('Comun/paginacion'); ?>
    </div>
</div>
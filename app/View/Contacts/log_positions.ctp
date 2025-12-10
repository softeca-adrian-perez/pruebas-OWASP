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
            __t('Contact.Position_log'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back')?></a>
    </div>
</div>
<?php
if(CakeSession::read('Auth.User.role_id') == ConstantsRoles::ADMIN)
{
    echo $this->element('../Contacts/Elements/log_tabs', array('active' => ConstantsLogType::PERMISSION));
}
?>
<div class="cnt-data" id="cnt_permission_group_permission">
    <?php echo $this->element('../Contacts/Elements/search_logs_positions'); ?>
    <div class="o-auto">
        <table class="table-tracking">
            <thead>
            <tr>
                <th><?php echo $this->Paginator->sort('User.name',__t('Logs.User')); ?></th>
                <th><?php echo $this->Paginator->sort('Position.name_' . __l() , __t('General.Position')); ?></th>
                <th><?php echo $this->Paginator->sort('LogField.name_' . __l() , __t('Logs.Field')); ?></th>
                <th><?php echo __t('Logs.Old_value'); ?></th>
                <th><?php echo __t('Logs.New_value'); ?></th>
                <th><?php echo $this->Paginator->sort('LogChange.date',__t('Logs.Date')); ?></th>
            </tr>
            </thead>
            <tbody>
                <?php foreach($logs_changes_permission_group_permission as $log_change) { ?>
                    <tr>
                        <td>
                            <?php echo h($log_change['User']['name']); ?>
                        </td>
                        <td>
                            <?php echo h($log_change['Position']['name_' . __l()]); ?>
                        </td>
                        <td>
                            <?php echo h($log_change['LogField']['name_' . __l()]); ?>
                        </td>
                        <td>
                            <?php echo h(__t($log_change['LogChange']['old_value'])); ?>
                        </td>
                        <td>
                            <?php if(Fecha::isDate(__t($log_change['LogChange']['new_value']))){
                                echo Fecha::toFormatoVistaFecha(__t($log_change['LogChange']['new_value'])) ;
                            } else {
                                echo h(__t($log_change['LogChange']['new_value']));
                            }?>
                        </td>
                        <td>
                            <?php echo h(date("d-m-Y / H:i", strtotime($log_change['LogChange']['date']))); ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php echo $this->element('Comun/paginacion'); ?>
</div>
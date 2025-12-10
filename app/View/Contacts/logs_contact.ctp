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
            __t('Contact.Logs_contacts'),
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
    echo $this->element('../Contacts/Elements/log_tabs', array('active' => ConstantsLogType::CONTACT));
}
?>
<div class="cnt-data" id="cnt_position_contact">
    <?php echo $this->element('../Contacts/Elements/search_logs_contacts'); ?>
    <div class="o-auto">
        <table class="table-tracking">
            <thead>
            <tr>
                <th><?php echo $this->Paginator->sort('User.name',__t('Logs.User')); ?></th>
                <th><?php echo $this->Paginator->sort('LogChange.contact',__t('Contact.Contact')); ?></th>
                <th><?php echo __t('Logs.Old_value'); ?></th>
                <th><?php echo $this->Paginator->sort('LogChange.new_value',__t('Logs.New_value')); ?></th>
                <th><?php echo $this->Paginator->sort('LogChange.date',__t('Logs.Date')); ?></th>
            </tr>
            </thead>
            <tbody>
                <?php foreach($logs_changes_position_contact as $log_change) { ?>
                    <tr>
                        <td>
                            <?php echo h($log_change['User']['name']); ?>
                        </td>
                        <td>
                            <?php echo h($log_change['LogChange']['contact']); ?>
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
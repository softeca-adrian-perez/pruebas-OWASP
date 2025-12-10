<?php echo $this->Html->script('enable_edit_garage.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script')); ?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        $action = in_array($this->Acceso->rol(), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR)) ? 'my_data' : 'view';
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Garage.Garages'),
                array(
                    'controller' => 'garages',
                    'action' => 'home'
                )
            ),
            __t('RequestedChanges.Requested_changes')
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back') ?></a>
        <?php if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN && $garage['Garage']['status'] != ConstantsGarageStatus::INACTIVE) { ?>
            <button type="button" id="edit-btn-disable" value="1" class="aag-button medium" data-url="<?php echo Router::url(array('controller' => 'garages', 'action' => 'ajax_update_edit',)); ?>" data-edit_enabled="<?php echo CakeSession::read('Auth.User.edit_enabled'); ?>"><?php echo __t('General.Edit'); ?></button>
        <?php } ?>
    </div>
</div>
<?php echo $this->element('../Garages/tabs', array('selected' => 'requested_changes')); ?>
<div class="cnt-data aag-padding">
    <div class="aag-title-background">
        <?php echo h($garage['Garage']['name']); ?>
    </div>
    <div class="aag-subtitle p-top-1">
        <?php echo __t('RequestedChanges.Changes_list') ?>
    </div>
    <div class="o-auto">
        <table class="table-tracking">
            <thead>
                <tr>
                    <th><?php echo $this->Paginator->sort('LogTable.name_' . __l(), __t('RequestedChanges.Table')); ?></th>
                    <th><?php echo __t('RequestedChanges.Field'); ?></th>
                    <th><?php echo $this->Paginator->sort('User.name', __t('RequestedChanges.User')); ?></th>
                    <th><?php echo $this->Paginator->sort('RequestedChange.date', __t('RequestedChanges.Date')); ?></th>
                    <th><?php echo $this->Paginator->sort('RequestedChange.old_value', __t('RequestedChanges.Old_value')); ?></th>
                    <th><?php echo $this->Paginator->sort('RequestedChange.new_value', __t('RequestedChanges.New_value')); ?></th>
                    <th><?php echo $this->Paginator->sort('RequestedChange.sent', __t('RequestedChanges.Sent')); ?></th>
                    <th class="ta-center btn-hide" hidden><?php echo __t('General.Actions'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requested_changes as $requested_change) { ?>
                    <tr>
                        <td>
                            <?php echo h($requested_change['LogTable']['name_' . __l()]); ?>
                        </td>
                        <td>
                            <?php
                            if (is_null($requested_change['LogField']['name'])) {
                                echo h(__t($requested_change['RequestedChange']['section']));
                            } else {
                                echo h($requested_change['LogField']['name_' . __l()]);
                            }
                            ?>
                        </td>
                        <td>
                            <?php echo h($requested_change['User']['name']); ?>
                        </td>
                        <td>
                            <?php echo h(date("d-m-Y / H:i", strtotime($requested_change['RequestedChange']['date']))); ?>
                        </td>
                        <?php if (is_null($requested_change['LogField']['name'])): ?>
                            <td colspan="2">
                                <?php
                                $crop_chars = 120;
                                if (strlen($requested_change['RequestedChange']['description']) > $crop_chars) {
                                    echo h(substr($requested_change['RequestedChange']['description'], 0, $crop_chars)) . '...';
                                } else {
                                    echo h($requested_change['RequestedChange']['description']);
                                }
                                ?>
                            </td>
                        <?php else: ?>
                            <td>
                                <?php
                                if (Fecha::isDate(__t($requested_change['RequestedChange']['old_value']))) {
                                    echo Fecha::toFormatoVistaFecha(__t($requested_change['RequestedChange']['old_value']));
                                } else {
                                    echo h(__t($requested_change['RequestedChange']['old_value']));
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if (Fecha::isDate(__t($requested_change['RequestedChange']['new_value']))) {
                                    echo Fecha::toFormatoVistaFecha(__t($requested_change['RequestedChange']['new_value']));
                                } else {
                                    echo h(__t($requested_change['RequestedChange']['new_value']));
                                }
                                ?>
                            </td>
                        <?php endif ?>
                        <td>
                            <?php echo h(__t(Booleano::toString($requested_change['RequestedChange']['sent']))); ?>
                        </td>
                        <td class="ta-center btn-hide" hidden>
                            <?php
                            if ($user_id == $requested_change['RequestedChange']['user_id'] && !$requested_change['RequestedChange']['sent']) {
                                echo $this->Html->Link(
                                    '<span class="aag-icon-papelera c-fallo"></span>',
                                    array(),
                                    array(
                                        'escape' => false,
                                        'title' => __t('General.Delete'),
                                        'class' => 'delete-request-js',
                                        'data-url' => Router::url(
                                            array(
                                                'controller' => 'garages',
                                                'action' => 'ajax_delete_requested_change',
                                                $requested_change['RequestedChange']['id']
                                            )
                                        ),
                                        'data-url_redirect' => Router::url(array(
                                            'controller' => 'garages',
                                            'action' => 'requested_changes',
                                            $requested_change['RequestedChange']['garage_id']
                                        )),
                                        'data-confirmmsg' => __t('RequestedChanges.Delete_change?'),
                                        'data-msg_correct' => __t('RequestedChanges.Well_deleted'),
                                        'data-msg_bad' => __t('Constants.Message_bad_deleted'),
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
    <?php echo $this->element('Comun/paginacion'); ?>
</div>
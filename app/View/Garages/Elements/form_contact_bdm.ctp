<?php
echo $this->Html->script('garages-contacts.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
$action = in_array($this->Acceso->rol(), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR)) ? 'my_data' : 'view';
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Garage.Garages'),
                array(
                    'controller' => 'garages',
                    'action' => 'home'
                )
            ),
            $this->Html->link(
                $garage['Garage']['name'],
                array(
                    'controller' => 'garages',
                    'action' => $action,
                    $garage['Garage']['id']
                )
            ),
            __t('Contact.Add_contact'),
        ));
        ?>
    </div>
    <div>
        <?php
        echo $this->Html->link(
            __t('General.Back'),
            array(
                'controller' => 'garages',
                'action' => 'edit',
                $garage_id
            ),
            array(
                'class' => 'aag-button medium four',
            )
        );
        ?>
    </div>
</div>
<span id="is_checked_my_contacts" class="d-none"><?php echo $is_checked_my_contacts ?></span>
<div class="cnt-data aag-padding">
    <?php echo $this->element('../Garages/Elements/search_contacts_bdm_garage'); ?>
    <div class="row">
        <div class="aag-subtitle">
            <?php echo __t('Contact.Contact_list') ?>
        </div>
    </div>
    <?php if ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)) { ?>
        <div class="cnt-buttons-v2 d-inline f-right">
            <?php echo $this->Html->link(
                __t('Contact.New_contact'),
                array(
                    'controller' => 'contacts',
                    'action' => 'add',
                    'garages',
                    'add_contacts_bdm',
                    $garage_id
                ),
                array(
                    'escape' => false,
                    'class' => 'btn-add btn-guardar',
                    'style' => 'z-index: 1; position: relative;',
                )
            ); ?>
        </div>
    <?php } ?>
</div>
<div class="row">
    <div id="alerta_msg"></div>
    <div class="o-auto">
        <table id="contact_bdm_table" class="table-tracking">
            <thead>
                <tr>
                    <th><?php echo $this->Paginator->sort('Contact.first_name', __t('Contact.First_name')); ?></th>
                    <th><?php echo $this->Paginator->sort('Contact.last_name', __t('Contact.Last_name')); ?></th>
                    <th><?php echo $this->Paginator->sort('Contact.position', __t('Contact.Position')); ?></th>
                    <th width="120"><?php echo $this->Paginator->sort('Contact.phone', __t('Contact.Phone')); ?></th>
                    <th width="120"><?php echo $this->Paginator->sort('Contact.mobile_phone', __t('Contact.Mobile_phone')); ?></th>
                    <th><?php echo $this->Paginator->sort('Contact.email', __t('Contact.Email')); ?></th>
                    <th class="ta-center" width="100"><?php echo __t('Contact.BDM'); ?></th>
                    <th class="ta-center" width="60"><?php echo __t('General.Actions'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($contacts as $contact) { ?>
                    <tr>
                        <td>
                            <?php echo h($contact['Contact']['first_name']); ?>
                        </td>
                        <td>
                            <?php echo h($contact['Contact']['last_name']); ?>
                        </td>
                        <td>
                            <?php echo ($contact['Contact']['position_id'] != null) ? h($positions[$contact['Contact']['position_id']]) : '' ?>
                        </td>
                        <td>
                            <?php echo h($contact['Contact']['phone']); ?>
                        </td>
                        <td>
                            <?php echo h($contact['Contact']['mobile_phone']); ?>
                        </td>
                        <td>
                            <?php echo h($contact['Contact']['email']); ?>
                        </td>
                        <td class="ta-center">
                            <?php
                            if ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)) {
                                if ($garage_id != null) {
                                    $exito = false;
                                    foreach ($garage_contacts as $garage_contact) {
                                        if ($garage_contact['GarageContactBdm']['contact_id'] == $contact['Contact']['id']) {
                                            $exito = true;
                                        }
                                    }
                                    if ($exito) {
                            ?>
                                        <span class="order-status-bdm d-none">0</span>
                                    <?php
                                    } else {
                                    ?>
                                        <span class="order-status-bdm d-none">1</span>
                            <?php
                                    }
                                }
                            }
                            ?>
                        </td>
                        <td class="ta-center">
                            <?php
                            if ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)) {
                                echo $this->Html->link(
                                    '<span class="aag-icon-editar c-primary"></span>',
                                    array(
                                        'controller' => 'contacts',
                                        'action' => 'edit',
                                        'garages',
                                        'add_contacts_bdm',
                                        $contact['Contact']['id'],
                                        $garage_id,
                                    ),
                                    array(
                                        'escape' => false,
                                        'title' => __t('General.Edit'),
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
    <br>
    <?php echo $this->element('Comun/paginacion'); ?>
</div>
<?php
if (
    !$this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE) &&
    $this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::REQUEST_CHANGE_GARAGE)
) { ?>
    <div class="row p-top-1">
        <?php echo $this->Form->create(); ?>
        <div class="aag-subtitle">
            <?php echo __t('RequestedChanges.Describe_change'); ?>
        </div>
        <div class="ta-right cnt-buttons-v2">
            <?php echo $this->Form->button(
                __t('RequestedChanges.Request_changes'),
                array(
                    'type' => 'submit',
                    'name' => 'request_changes',
                    'style' => 'margin-top:0 !important;',
                    'class' => 'btn-edit edit',
                )
            );
            ?>
        </div>
        <?php
        echo $this->Form->input(
            'ChangeDescription',
            array(
                'type' => 'text',
                'name' => 'change_description',
                'rows' => 7,
                'label' => false
            )
        );
        ?>
        <?php echo $this->Form->end(); ?>
    </div>
<?php } ?>
</div>
</div>
</div>
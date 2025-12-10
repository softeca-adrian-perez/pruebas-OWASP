<?php
echo $this->Html->script('garages-contacts.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('enable_edit_garage.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));

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
            __t('Contact.Add_contact'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back') ?></a>
        <button type="button" id="edit-btn-disable" value="1" class="aag-button medium" data-url="<?php echo Router::url(array('controller' => 'garages', 'action' => 'ajax_update_edit')); ?>" data-edit_enabled="<?php echo CakeSession::read('Auth.User.edit_enabled'); ?>"><?php echo __t('General.Edit'); ?></button>
    </div>
</div>
<?php echo $this->element('../Garages/tabs', array('selected' => 'contacts_general_branch_manager')); ?>
<span id="is_checked_my_contacts" class="d-none"><?php echo $is_checked_my_contacts ?></span>
<div class="cnt-data aag-padding">
    <div class="aag-title p-bottom-1">
        <?php echo h($garage['Garage']['name']); ?>
    </div>
    <?php echo $this->element('../Garages/Elements/search_contacts_garage'); ?>
    <div class="row">
        <div class="aag-subtitle">
            <?php echo __t('Contacts list') ?>
        </div>
        <?php if ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)) { ?>
            <div class="f-right btn-hide" hidden>
                <div class="cnt-buttons-v2 d-inline f-right">
                    <?php
                    echo $this->Html->link(
                        __t('Contact.New_contact'),
                        array(
                            'controller' => 'contacts',
                            'action' => 'add',
                            'garages',
                            'add_contacts_general_branch_manager',
                            $garage_id
                        ),
                        array(
                            'escape' => false,
                            'class' => 'aag-button small green',
                            'style' => 'z-index: 1; position: relative;',
                        )
                    );
                    ?>
                </div>
            </div>
        <?php } ?>
    </div>
    <div class="row">
        <div id="alerta_msg"></div>
        <div class="medium-12 columns m-top-1">
            <div class="o-auto">
                <table id="contact_general_branch_manager_table" class="table-tracking">
                    <thead>
                        <tr>
                            <th><?php echo $this->Paginator->sort('Contact.first_name', __t('Contact.First_name')); ?></th>
                            <th><?php echo $this->Paginator->sort('Contact.last_name', __t('Contact.Last_name')); ?></th>
                            <th><?php echo $this->Paginator->sort('Contact.position', __t('Contact.Position')); ?></th>
                            <th width="120"><?php echo $this->Paginator->sort('Contact.phone', __t('Contact.Phone')); ?></th>
                            <th><?php echo $this->Paginator->sort('Contact.email', __t('Contact.Email')); ?></th>
                            <th class="ta-center btn-hide" hidden width="200"><?php echo __t('Contact.General_branch_manager'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contacts as $contact) { ?>
                            <tr>
                                <td class="link-text c-primary">
                                    <?php
                                    if ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)) {
                                        echo $this->Html->link(
                                            '<span class="c-primary">' . h($contact["Contact"]["first_name"]) . '</span>',
                                            array(
                                                'controller' => 'contacts',
                                                'action' => 'edit',
                                                'garages',
                                                'add_contacts_general_branch_manager',
                                                $contact['Contact']['id'],
                                                $garage_id,
                                            ),
                                            array(
                                                'escape' => false,
                                            )
                                        );
                                    } else {
                                        echo h($contact["Contact"]["first_name"]);
                                    }
                                    ?>
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
                                    <?php echo h($contact['Contact']['email']); ?>
                                </td>
                                <td class="ta-center btn-hide" hidden>
                                    <?php
                                    if ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE) && $garage_id != null) {
                                        $exito = false;
                                        foreach ($garage_contacts as $garage_contact) {
                                            if ($garage_contact['GarageContactGeneralBranchManager']['contact_id'] == $contact['Contact']['id']) {
                                                $exito = true;
                                            }
                                        }
                                        if ($exito) {
                                    ?>
                                            <span data-url='<?php echo Router::url(array('controller' => 'garages_contacts_general_branch_manager', 'action' => 'ajax_remove_garage_contact_general_branch_manager', $garage_id, $contact['Contact']['id'])); ?>' data-garage-id='<?php echo $garage_id ?>' data-contact-id='<?php echo $contact['Contact']['id'] ?>' class='aag-icon-agregar-usuario c-exito status-active-general-branch-manager'></span>
                                            <span class="order-status-bdm d-none">0</span>
                                        <?php } else { ?>
                                            <span data-url='<?php echo Router::url(array('controller' => 'garages_contacts_general_branch_manager', 'action' => 'ajax_add_garage_contact_general_branch_manager', $garage_id, $contact['Contact']['id'])); ?>' data-garage-id='<?php echo $garage_id ?>' data-contact-id='<?php echo $contact['Contact']['id'] ?>' class='aag-icon-agregar-usuario c-defecto status-inactive-general-branch-manager'></span>
                                            <span class="order-status-general-branch-manager d-none">1</span>
                                    <?php
                                        }
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
    </div>
    <?php
    if (
        !$this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE) &&
        $this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::REQUEST_CHANGE_GARAGE)
    ) {
    ?>
        <div class="row p-top-1">
            <?php echo $this->Form->create(); ?>
            <div class="columns medium-7">
                <div class="titulo2">
                    <?php echo __t('RequestedChanges.Describe_change'); ?>
                </div>
            </div>
            <div class="medium-5 columns ta-right cnt-buttons-v2">
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
            <div class="medium-12 columns end">
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
            </div>
            <?php echo $this->Form->end(); ?>
        </div>
    <?php } ?>
</div>
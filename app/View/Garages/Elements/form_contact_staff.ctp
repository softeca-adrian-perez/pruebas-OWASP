<?php
echo $this->Html->script('garages-contacts.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('enable_edit_garage.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('employee_table.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        $action = in_array($this->Acceso->rol(), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR, ConstantsRoles::SUPER_ADMIN)) ? 'my_data' : 'view';
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
        <?php if ($garage['Garage']['status'] != ConstantsGarageStatus::INACTIVE) { ?>
            <span id="is_checked_my_contacts" class="d-none"><?php echo $is_checked_my_contacts ?></span>
            <button id="edit-btn-disable" value="1" class="aag-button medium" data-url="<?php echo Router::url(array('controller' => 'garages', 'action' => 'ajax_update_edit')); ?>" data-edit_enabled="<?php echo CakeSession::read('Auth.User.edit_enabled'); ?>"><?php echo __t('General.Edit'); ?></button>
        <?php } ?>
    </div>
</div>
<?php echo $this->element('../Garages/tabs', array('selected' => 'contact_garage_staff',)); ?>
<div class="cnt-data aag-padding">
    <div class="aag-title-background">
        <?php echo h($garage['Garage']['name']); ?>
    </div>
    <?php if ((CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN)) { ?>
        </br>
    <?php } else { ?>
        <div class="aag-subtitle m-top-1">
            <?php echo __t('Garage.Employees'); ?>
        </div>
        <div class="btn-hide" hidden>
            <div class="cnt-form-inputs">
                <?php echo $this->Form->input(
                    'employee_types',
                    array(
                        'label' => __t('Garage.Employee_type'),
                        'type' => 'select',
                        'class' => 'select2-multiple input-disabled',
                        'id' => 'select-employee',
                        'multiple' => false,
                        'empty' => true,
                        'options' => $employee_types,
                    )
                );
                echo $this->Form->input(
                    'number',
                    array(
                        'label' => __t('Garage.Quantity'),
                        'id' => 'input-number',
                        'type' => 'number',
                        'min' => 1
                    )
                );
                ?>
                <div style="display:inline !important">
                    <?php
                    echo $this->Html->link(
                        __t('General.Add'),
                        'javascript:void(0)',
                        array(
                            'escape' => false,
                            'class' => 'aag-button small green f-right',
                            'id' => 'add-employee',
                            'data-add' => Router::url(array(
                                'controller' => 'garages_employees',
                                'action' => 'ajax_save_new_value',
                                $garage['Garage']['id']
                            )),
                            'data-reload' => Router::url(array(
                                'controller' => 'garages_employees',
                                'action' => 'ajax_get_values_list',
                                $garage['Garage']['id']
                            )),
                            'data-delete' => Router::url(array(
                                'controller' => 'garages_employees',
                                'action' => 'ajax_delete_value'
                            )),
                        )
                    );
                    ?>
                </div>
            </div>
        </div>
        <div class="o-auto">
            <table class="table-tracking tabla-responsive" id="tabla-employee">
                <thead>
                    <tr>
                        <th><?php echo __t('Garage.Employee_type') ?></th>
                        <th><?php echo __t('Garage.Quantity') ?></th>
                        <th class="ta-center btn-hide" hidden><?php echo __t('General.Actions') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($employees as $key => $value) { ?>
                        <tr>
                            <td>
                                <?php echo $value['EmployeeType']['name_en'] ?>
                            </td>
                            <td>
                                <?php echo $value['GarageEmployee']['number'] ? $value['GarageEmployee']['number'] : 0; ?>
                            </td>
                            <td class="ta-center btn-hide" hidden>
                                <span class="delete-employee aag-icon-papelera c-fallo cursor-pointer" data-key="<?php echo $value['GarageEmployee']['id'] ?>"></span>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <br>
        </div>
    <?php } ?>
    <div>
        <?php echo $this->element('../Garages/Elements/search_contacts_garage'); ?>
    </div>
    <div class="aag-subtitle m-top-1">
        <?php echo __t('Contacts list') ?>
    </div>
    <?php
    if (
        $this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE) ||
        CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN
    ) {
    ?>
        <div class="ta-right cnt-buttons-v2 btn-hide" hidden>
            <div class="cnt-buttons-v2 d-inline f-right">
                <?php
                echo $this->Html->link(
                    __t('Contact.New_contact'),
                    array(
                        'controller' => 'contacts',
                        'action' => 'add',
                        'garages',
                        'add_contacts_staff',
                        $garage_id
                    ),
                    array(
                        'escape' => false,
                        'class' => 'aag-button small green',
                    )
                );
                if (
                    $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CREATE_USER) && ($this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CREATE_CONTACT)) ||
                    CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN
                ) {
                    echo $this->Html->link(
                        __t('Contact.New_contact_and_user'),
                        array(
                            'controller' => 'contacts',
                            'action' => 'add_contact_and_user',
                            'garage_id' => $garage_id,
                            ConstantsBackContactUser::BACK_GARAGES_ID,
                        ),
                        array(
                            'escape' => false,
                            'class' => 'aag-button small green',
                        )
                    );
                }
                ?>
            </div>
        </div>
    <?php } ?>
    <div id="alerta_msg"></div>
    <div class="o-auto">
        <table id="contact_staff_table" class="table-tracking">
            <thead>
                <tr>
                    <th><?php echo $this->Paginator->sort('Contact.first_name', __t('Contact.First_name')); ?></th>
                    <th><?php echo $this->Paginator->sort('Contact.last_name', __t('Contact.Last_name')); ?></th>
                    <th><?php echo __t('Contact.Position'); ?></th>
                    <th width="120"><?php echo $this->Paginator->sort('Contact.phone', __t('Contact.Phone')); ?></th>
                    <th width="120"><?php echo $this->Paginator->sort('Contact.mobile_phone', __t('Contact.Mobile_phone')); ?></th>
                    <th><?php echo $this->Paginator->sort('Contact.email', __t('Contact.Email')); ?></th>
                    <?php if ($show_contacts_check) { ?>
                        <th><?php echo __t('Contact.Interests'); ?></th>
                        <th class="ta-center"><?php echo __t('Garage.Primary_contact'); ?></th>
                    <?php } ?>
                    <th class="ta-center btn-hide" hidden width="100"><?php echo __t('Contact.Staff'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($contacts as $contact) { ?>
                    <tr>
                        <td class="link-text">
                            <?php
                            if (isset($garage['Garage']['status']) && $garage['Garage']['status'] != ConstantsGarageStatus::INACTIVE) {
                                echo $this->Html->link(
                                    '<span class="c-primary">' . h($contact['Contact']['first_name']) . '</span>',
                                    array(
                                        'controller' => 'contacts',
                                        'action' => 'edit',
                                        'garages',
                                        'add_contacts_staff',
                                        $contact['Contact']['id'],
                                        $garage_id,
                                    ),
                                    array(
                                        'class' => 'c-primary',
                                        'escape' => false,
                                    )
                                );
                            } else {
                                echo $contact['Contact']['first_name'];
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
                            <?php echo h($contact['Contact']['mobile_phone']); ?>
                        </td>
                        <td>
                            <?php echo h($contact['Contact']['email']); ?>
                        </td>
                        <?php if ($show_contacts_check) { ?>
                            <td>
                                <?php
                                echo isset($contact['GarageContactStaff']['interest']) ? h($contact['GarageContactStaff']['interest']) : ''  ?>
                            </td>
                            <td class="ta-center">
                                <?php if (isset($contact['GarageContactStaff']['priority'])) { ?>
                                    <div class="cont-vehicles cnt-vehicles-brands">
                                        <label class="m-0-i ta-center disabled">
                                            <?php
                                            echo $this->Form->input(
                                                'GarageContactStaff.Priority',
                                                array(
                                                    'type' => 'checkbox',
                                                    'label' => false,
                                                    'div' => false,
                                                    'value' => $contact['GarageContactStaff']['priority'] ? true : false,
                                                    'checked' => $contact['GarageContactStaff']['priority'] ? true : false,
                                                    'class' => 'garage_vehicle priority_contact_checkbox-js',
                                                    'disabled' => true,
                                                    'data-url' => Router::url(
                                                        array(
                                                            'controller' => 'Garages',
                                                            'action' => 'ajax_edit_garage_contact_staff_priority',
                                                            $contact['GarageContactStaff']['id'],
                                                        )
                                                    )
                                                )
                                            );
                                            ?>
                                            <span class="ion-ios-star unselectable" style="display: block;"></span>
                                        </label>
                                    </div>
                                <?php } ?>
                            </td>
                        <?php } ?>
                        <td class="ta-center btn-hide" hidden>
                            <?php
                            if ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE) && $garage_id != null) {
                                $exito = false;
                                foreach ($garage_contacts as $garage_contact) {
                                    if ($garage_contact['GarageContactStaff']['contact_id'] == $contact['Contact']['id']) {
                                        $exito = true;
                                    }
                                }
                                if ($exito) {
                            ?>
                                    <span data-url='<?php echo Router::url(array('controller' => 'garages_contacts_staff', 'action' => 'ajax_remove_garage_contact_staff', $garage_id, $contact['Contact']['id'])); ?>' data-garage-id='<?php echo $garage_id ?>' data-contact-id='<?php echo $contact['Contact']['id'] ?>' class='aag-icon-agregar-usuario c-exito status-active-staff'></span>
                                    <span class="order-status-bdm d-none">0</span>
                                <?php } else { ?>
                                    <span data-url='<?php echo Router::url(array('controller' => 'garages_contacts_staff', 'action' => 'ajax_add_garage_contact_staff', $garage_id, $contact['Contact']['id'])); ?>' data-garage-id='<?php echo $garage_id ?>' data-contact-id='<?php echo $contact['Contact']['id'] ?>' class='aag-icon-agregar-usuario c-defecto status-inactive-staff'></span>
                                    <span class="order-status-staff d-none">1</span>
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
    <?php
    echo $this->element('Comun/paginacion');
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
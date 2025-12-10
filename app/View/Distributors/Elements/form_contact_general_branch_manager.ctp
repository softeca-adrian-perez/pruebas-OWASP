<?php
echo $this->Html->script('distributors-contacts.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
$action = in_array($this->Acceso->rol(), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR)) ? 'my_data' : 'view';
?>
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
            __t('Contact.Add_contact'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium four go-back-js"><?php echo __t('General.Back') ?></a>
        <?php echo $this->Html->link(
            __t('General.Next'),
            array(
                'controller' => 'distributors',
                'action' => 'add_contacts_bdm',
                $distributor_id
            ),
            array(
                'class' => 'aag-button medium two'
            )
        ); ?>
    </div>
</div>
<?php echo $this->element('../Distributors/tabs', array('selected' => 'contacts_general_branch_manager',)); ?>
<span id="is_checked_my_contacts" class="d-none"><?php echo $is_checked_my_contacts ?></span>
<div class="cnt-data p-top-1">
    <div class="cnt-data-element p-bottom-1">
        <div class="aag-title p-bottom-1">
            <?php echo h($distributor['Distributor']['name']); ?>
        </div>
        <?php echo $this->element('../Distributors/Elements/search_contacts_distributor'); ?>
    </div>
    <div class="flex fw-wrap ai-center cnt-data-element gap-1">
        <div class="aag-subtitle m-right-auto">
            <?php echo __t('Contacts list') ?>
        </div>
        <?php
        if ($this->Acceso->haveTradingGroupPermission(ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR, $distributor['Distributor']['trading_group_id'])) { ?>
        <?php
            echo $this->Html->link(
                __t('Contact.New_contact'),
                array(
                    'controller' => 'contacts',
                    'action' => 'add',
                    'distributors',
                    'add_contacts_general_branch_manager',
                    $distributor_id
                ),
                array(
                    'escape' => false,
                    'title' => __t('Contact.Create_contact'),
                    'class' => 'aag-button medium green',
                )
            );
        } ?>
    </div>
    <div id="alerta_msg"></div>
    <div class="o-auto p-top-1">
        <table id="contact_general_branch_manager_table" class="table-tracking">
            <thead>
                <tr>
                    <th><?php echo $this->Paginator->sort('Contact.first_name', __t('Contact.First_name')); ?></th>
                    <th><?php echo $this->Paginator->sort('Contact.last_name', __t('Contact.Last_name')); ?></th>
                    <th><?php echo __t('Contact.Position'); ?></th>
                    <th width="120"><?php echo $this->Paginator->sort('Contact.phone', __t('Contact.Phone')); ?></th>
                    <th><?php echo $this->Paginator->sort('Contact.email', __t('Contact.Email')); ?></th>
                    <th class="ta-center" width="200"><?php echo __t('Contact.General_branch_manager'); ?></th>
                    <th class="ta-center" width="60"><?php echo __t('General.Actions'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($contacts as $contact) { ?>
                    <tr>
                        <td><?php echo h($contact['Contact']['first_name']); ?></td>
                        <td><?php echo h($contact['Contact']['last_name']); ?></td>
                        <td><?php echo ($contact['Contact']['position_id'] != null) ? h($positions[$contact['Contact']['position_id']]) : '' ?></td>
                        <td><?php echo h($contact['Contact']['phone']); ?></td>
                        <td><?php echo h($contact['Contact']['email']); ?></td>
                        <td class="ta-center">
                            <?php
                            if (
                                $this->Acceso->haveTradingGroupPermission(ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR, $distributor['Distributor']['trading_group_id']) &&
                                $distributor_id != null
                            ) {
                                $exito = false;
                                foreach ($distributor_contacts as $distributor_contact) {
                                    if ($distributor_contact['DistributorContactGeneralBranchManager']['contact_id'] == $contact['Contact']['id']) {
                                        $exito = true;
                                    }
                                }
                                if ($exito) {
                                    $data_url = Router::url(
                                        array(
                                            'controller' => 'distributors_contacts_general_branch_manager',
                                            'action' => 'ajax_remove_distributor_contact_general_branch_manager',
                                            $distributor_id,
                                            $contact['Contact']['id']
                                        )
                                    );
                            ?>
                                    <span data-url='<?php echo $data_url; ?>' data-distributor-id='<?php echo $distributor_id ?>' data-contact-id='<?php echo $contact['Contact']['id'] ?>' class='aag-icon-agregar-usuario c-exito status-active-general-branch-manager'></span>
                                    <span class="order-status-bdm d-none">0</span>
                                <?php
                                } else {
                                    $data_url = Router::url(
                                        array(
                                            'controller' => 'distributors_contacts_general_branch_manager',
                                            'action' => 'ajax_add_distributor_contact_general_branch_manager',
                                            $distributor_id,
                                            $contact['Contact']['id']
                                        )
                                    );
                                ?>
                                    <span data-url='<?php echo $data_url; ?>' data-distributor-id='<?php echo $distributor_id ?>' data-contact-id='<?php echo $contact['Contact']['id'] ?>' class='aag-icon-agregar-usuario c-defecto status-inactive-general-branch-manager'></span>
                                    <span class="order-status-general-branch-manager d-none">1</span>
                            <?php
                                }
                            }
                            ?>
                        </td>
                        <td class="ta-center">
                            <?php
                            if ($this->Acceso->haveTradingGroupPermission(ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR, $distributor['Distributor']['trading_group_id'])) {
                                echo $this->Html->link(
                                    '<span class="aag-icon-editar c-primary"></span>',
                                    array(
                                        'controller' => 'contacts',
                                        'action' => 'edit',
                                        'distributors',
                                        'add_contacts_general_branch_manager',
                                        $contact['Contact']['id'],
                                        $distributor_id,
                                    ),
                                    array(
                                        'escape' => false,
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
    <?php
    if (
        !$this->Acceso->haveTradingGroupPermission(ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR, $distributor['Distributor']['trading_group_id']) &&
        $this->Acceso->haveTradingGroupPermission(ConstantsPermissionsGrouping::REQUEST_CHANGE_DISTRIBUTOR, $distributor['Distributor']['trading_group_id'])
    ) { ?>
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
</div>
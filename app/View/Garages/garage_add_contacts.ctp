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
</div>
<span id="is_checked_my_contacts" class="d-none"><?php echo $is_checked_my_contacts ?></span>
<div class="cnt-data aag-padding">
    <div class="aag-title m-bottom-1">
        <?php echo h($garage['Garage']['name']); ?>
    </div>
    <?php echo $this->element('../Garages/Elements/search_contacts_garage'); ?>
    <div class="row">
        <div class="aag-subtitle"><?php echo __t('Contacts list') ?></div>
        <div class="ta-right cnt-buttons-v2">
            <div class="cnt-buttons-v2 d-inline f-right">
                <?php echo $this->Html->link(
                    __t('Contact.New_contact'),
                    array(
                        'controller' => 'contacts',
                        'action' => 'add_contact_and_user',
                        ConstantsBackContactUser::BACK_GARAGES,
                        $garage['Garage']['id']
                    ),
                    array(
                        'escape' => false,
                        'class' => 'aag-button small green',
                        'style' => 'z-index: 1; position: relative;',
                    )
                ); ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div id="alerta_msg"></div>
        <div class="o-auto">
            <table id="contact_general_branch_manager_table" class="table-tracking">
                <thead>
                    <tr>
                        <th><?php echo $this->Paginator->sort('Contact.first_name', __t('Contact.First_name')); ?></th>
                        <th><?php echo $this->Paginator->sort('Contact.last_name', __t('Contact.Last_name')); ?></th>
                        <th><?php echo $this->Paginator->sort('Contact.position', __t('Contact.Position')); ?></th>
                        <th width="120"><?php echo $this->Paginator->sort('Contact.phone', __t('Contact.Phone')); ?></th>
                        <th><?php echo $this->Paginator->sort('Contact.email', __t('Contact.Email')); ?></th>
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
                                <?php echo h($contact['Contact']['email']); ?>
                            </td>
                            <td class="ta-center">
                                <?php
                                echo $this->Html->link(
                                    '<span class="aag-icon-editar c-primary"></span>',
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

                                echo $this->Html->Link(
                                    '<span class="aag-icon-papelera c-fallo"></span>',
                                    array(
                                        'controller' => 'contacts',
                                        'action' => 'deleteFromGarageDistributor',
                                        $contact['Contact']['id'],
                                        'garages',
                                        'garage_add_contacts',
                                        $garage['Garage']['id']
                                    ),
                                    array(
                                        'class' => 'delete_contact',
                                        'escape' => false,
                                        'title' => __t('User.Delete_user?')
                                    )
                                );
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
</div>
</div>
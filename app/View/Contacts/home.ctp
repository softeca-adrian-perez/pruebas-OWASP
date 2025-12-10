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
            __t('General.Home'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back') ?></a>
        <?php
        if ($this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CREATE_CONTACT)) {
            echo $this->Html->link(
                __t('Contact.New_contact'),
                array(
                    'controller' => 'contacts',
                    'action' => 'add',
                    'contacts',
                    'home',
                ),
                array('class' => 'aag-button medium green')
            );
        }
        if ($this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CREATE_USER) && ($this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CREATE_CONTACT))) {
            echo $this->Html->link(
                __t('Contact.New_contact_and_user'),
                array(
                    'controller' => 'contacts',
                    'action' => 'add_contact_and_user',
                    ConstantsBackContactUser::BACK_CONTACTS
                ),
                array('class' => 'aag-button medium green')
            );
        }
        ?>
    </div>
</div>
<?php
if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::ADMIN) {
    echo $this->element('../Contacts/Elements/log_tabs', array('active' => false));
}
?>
<div class="cnt-data" id="cnt_contacts">
    <?php echo $this->element('../Contacts/Elements/search'); ?>
    <div class="o-auto">
        <table class="table-tracking">
            <thead>
                <tr>
                    <th><?php echo $this->Paginator->sort('Contact.first_name', __t('Contact.First_name')); ?></th>
                    <th><?php echo $this->Paginator->sort('Contact.last_name', __t('Contact.Last_name')); ?></th>
                    <th><?php echo __t('Contact.Position'); ?></th>
                    <th><?php echo __t('Contact.Logistic_center'); ?></th>
                    <th><?php echo $this->Paginator->sort('Contact.phone', __t('Contact.Phone')); ?></th>
                    <th><?php echo $this->Paginator->sort('Contact.mobile_phone', __t('Contact.Mobile_phone')); ?></th>
                    <th><?php echo $this->Paginator->sort('Contact.email', __t('Contact.Email')); ?></th>
                    <?php if ($this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CREATE_CONTACT) || $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CREATE_USER)) { ?>
                        <th class="ta-center"><?php echo __t('General.Actions'); ?></th>
                    <?php } ?>
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
                            <?php echo isset($contact['Contact']['logistic_center_id']) ? h($logistic_centers[$contact['Contact']['logistic_center_id']]) : '' ?>
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
                        <?php if (($this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CREATE_CONTACT) || $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CREATE_USER)) || CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN) { ?>
                            <td class="ta-center">
                                <?php
                                echo $this->Html->link(
                                    '<span class="aag-icon-ojo c-primary"></span>',
                                    array(
                                        'controller' => 'contacts',
                                        'action' => 'view',
                                        $contact['Contact']['id'],
                                    ),
                                    array(
                                        'escape' => false,
                                        'title' => __t('Contact.View_contact')
                                    )
                                );
                                if ($this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CREATE_CONTACT)) {
                                    echo $this->Html->link(
                                        '<span class="aag-icon-editar c-primary"></span>',
                                        array(
                                            'controller' => 'contacts',
                                            'action' => 'edit',
                                            'contacts',
                                            'home',
                                            $contact['Contact']['id'],
                                            null,
                                        ),
                                        array(
                                            'escape' => false,
                                            'title' => __t('General.Edit')
                                        )
                                    );
                                }
                                if ($this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CREATE_USER)) {
                                    echo $this->Html->link(
                                        '<span class="aag-icon-agregar-usuario"></span>',
                                        array(
                                            'controller' => 'users',
                                            'action' => 'add_user_to_contact',
                                            $contact['Contact']['id'],
                                        ),
                                        array(
                                            'escape' => false,
                                            'title' => __t('User.Add_user_to_contact'),
                                        )
                                    );
                                }
                                if ($this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::DELETE_CONTACT)) {
                                    echo $this->Html->Link(
                                        '<span class="aag-icon-papelera c-fallo"></span>',
                                        array(
                                            'controller' => 'contacts',
                                            'action' => 'delete',
                                            $contact['Contact']['id'],
                                        ),
                                        array(
                                            'class' => 'delete_contact',
                                            'escape' => false,
                                            'title' => __t('User.Delete_user?')
                                        )
                                    );
                                }
                                ?>
                            </td>
                        <?php } ?>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php echo $this->element('Comun/paginacion'); ?>
</div>
<script>
    $('.delete_contact').on('click', function(e) {
        e.preventDefault();
        var element = $(this);
        swal({
            title: $.i18n._('Alert.Sure?'),
            text: $.i18n._('Alert.No_revert'),
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#ff5648',
            cancelButtonColor: '#d33',
            confirmButtonText: $.i18n._('General.Yes')
        }).then(function(result) {
            if (result.value) {
                window.location = element.attr('href');
            }
        });
    });
</script>
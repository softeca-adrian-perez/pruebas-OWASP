<?php echo $this->Html->script('/js/users.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script')); ?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('User.Users'),
                array(
                    'controller' => 'users',
                    'action' => 'listing'
                )
            ),
            __t('General.List'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back') ?></a>
        <?php
        if ($this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CREATE_USER) || CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN) {
            echo $this->Html->link(
                __t('Contact.New_contact_and_user'),
                array(
                    'controller' => 'contacts',
                    'action' => 'add_contact_and_user',
                    ConstantsBackContactUser::BACK_USERS
                ),
                array('class' => 'aag-button medium green')
            );
        }
        ?>
    </div>
</div>
<div class="cnt-data">
    <?php echo $this->element('../Users/Elements/buscador'); ?>
    <div class="p-top-1 o-auto">
        <table class="table-tracking">
            <thead>
                <tr>
                    <th></th>
                    <th><?php echo $this->Paginator->sort('User.name', __t('User.Name')); ?></th>
                    <th><?php echo $this->Paginator->sort('User.surname', __t('User.Surname')); ?></th>
                    <th><?php echo $this->Paginator->sort('Contact.email', __t('Contact.Email')); ?></th>
                    <th><?php echo $this->Paginator->sort('User.username', __t('User.Login')); ?></th>
                    <th><?php echo __t('General.Position'); ?></th>
                    <th><?php echo $this->Paginator->sort('GarageStaff.business_name', __t('Training.Garage_name')); ?></th>
                    <th><?php echo $this->Paginator->sort('Role.name_' . __l(), __t('User.Role')); ?></th>
                    <th class="ta-center" width="60"><?php echo $this->Paginator->sort('User.active', __t('User.Active')); ?></th>
                    <th class="ta-center" width="125"><?php echo __t('General.Actions'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user) { ?>
                    <tr>
                        <td class="ta-center cnt-image-td">
                            <?php if (!isset($user['UserImage'][0])) { ?>
                                <img class="img_list_user" src="<?php echo ConstantsPath::ADD_DEFAULT_IMAGE ?>" />
                            <?php
                            } else {
                                $this->UserImage = ClassRegistry::init('UserImage');
                                $file = $this->UserImage->findById($user['UserImage'][0]['id']);
                            ?>
                                <img class="img_list_user" src="<?php echo FileManager::get_url(ConstantsPath::DIR_USER_IMAGES_CROP . '/' . $file['UserImage']['file']) ?>" />
                            <?php } ?>
                        </td>
                        <td>
                            <?php echo h($user['User']['name']); ?>
                        </td>
                        <td>
                            <?php echo h($user['User']['surname']); ?>
                        </td>
                        <td>
                            <?php echo h($user['Contact']['email']); ?>
                        </td>
                        <td>
                            <?php echo h($user['User']['username']); ?>
                        </td>
                        <td>
                            <?php echo (!empty($user['Contact']['position_id'])) ? h($positions_list[$user['Contact']['position_id']]) : ''; ?>
                        </td>
                        <td>
                            <?php
                            if (isset($user['Contact']['garage_id'])) {
                                echo $this->Html->link(
                                    '<span>' . $user['GarageContact']['business_name'] . '</span>',
                                    array(
                                        'controller' => 'garages',
                                        'action' => 'edit',
                                        $user['Contact']['garage_id']
                                    ),
                                    array(
                                        'escape' => false,
                                    )
                                );
                            } elseif (isset($user['GaragesContactsStaff']['id'])) {
                                echo $this->Html->link(
                                    '<span>' . $user['GarageStaff']['business_name'] . '</span>',
                                    array(
                                        'controller' => 'garages',
                                        'action' => 'edit',
                                        $user['GarageStaff']['id']
                                    ),
                                    array(
                                        'escape' => false,
                                    )
                                );
                            }
                            ?>
                        </td>
                        <td>
                            <?php echo h($user['Role']['name_' . __l()]); ?>
                        </td>
                        <td class="ta-center">
                            <?php echo h(Booleano::toString($user['User']['active'])); ?>
                        </td>
                        <td class="ta-center ws-nowrap">
                            <?php
                            echo $this->Html->link(
                                '<span class="aag-icon-ojo c-primary"></span>',
                                array(
                                    'controller' => 'users',
                                    'action' => 'view',
                                    $user['User']['guid'],
                                ),
                                array(
                                    'escape' => false,
                                    'title' => __t('User.View_user')
                                )
                            );

                            if ($this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CREATE_USER)) {
                                echo $this->Html->link(
                                    '<span class="aag-icon-editar c-primary"></span>',
                                    array(
                                        'controller' => 'users',
                                        'action' => 'edit',
                                        $user['User']['guid'],
                                    ),
                                    array(
                                        'escape' => false,
                                        'title' => __t('User.Edit_user'),
                                        'style' => 'padding: 0 2px 0 3px;'
                                    )
                                );
                            }
                            echo $this->Html->Link(
                                '<span class="aag-icon-reset-pass"></span>',
                                array(),
                                array(
                                    'escape' => false,
                                    'title' => __t('Email.Recover_password'),
                                    'class' => 'recover_password-js',
                                    'data-url' => Router::url(array(
                                        'controller' => 'users',
                                        'action' => 'recover_password_user_list',
                                        $user['User']['username']
                                    )),
                                    'data-url_redirect' => Router::url(array(
                                        'controller' => 'users',
                                        'action' => 'listing',
                                    )),
                                    'data-confirmmsg' => __t('User.Resend_email_recover_password?'),
                                    'data-msg_correct' => __t('User.Resend_email_succesfully'),
                                    'data-msg_bad' => __t('Constants.Message_bad_send'),
                                )
                            );
                            if ($this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_PERMISSION)) {
                                echo $this->Html->link(
                                    '<span class="icon-permissions c-primary"></span>',
                                    array(
                                        'controller' => 'users',
                                        'action' => 'manage_permissions_config',
                                        $user['User']['guid'],
                                    ),
                                    array(
                                        'escape' => false,
                                        'title' => __t('User.View_permissions')
                                    )
                                );
                            }
                            if ($this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::DELETE_USER)) {
                                echo $this->Html->Link(
                                    '<span class="aag-icon-papelera c-fallo"></span>',
                                    array(
                                        'controller' => 'users',
                                        'action' => 'delete',
                                        $user['User']['guid'],
                                    ),
                                    array(
                                        'class' => 'delete_user',
                                        'escape' => false,
                                        'title' => __t('User.Delete_user?')
                                    )
                                );
                            }
                            if ($user['User']['retries'] == Configure::read('max_login_retries')) {
                                echo $this->Html->Link(
                                    '<span class="icon-denied c-fallo"></span>',
                                    array(
                                        'controller' => 'users',
                                        'action' => 'unset_lock',
                                        $user['User']['guid'],
                                    ),
                                    array(
                                        'class' => 'unset_lock_user',
                                        'escape' => false,
                                        'title' => __t('User.Unset_lock?')
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
    <?php echo $this->element('Comun/paginacion'); ?>
</div>

<script>
    $('.delete_user').on('click', function(e) {
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

    $('.unset_lock_user').on('click', function(e) {
        e.preventDefault();
        var element = $(this);
        swal({
            title: $.i18n._('User.Unset_lock?'),
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
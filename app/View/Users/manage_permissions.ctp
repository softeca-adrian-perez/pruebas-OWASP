<?php
echo $this->Html->script('/js/permisos.js?v=' . Configure::read('VERSION_CACHE'));
echo $this->Form->create('Permissions', array('class' => 'form-horizontal custom',));
?>
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
            __t('User.Permission'),
        ));
        ?>
    </div>
    <div>
        <?php 
        echo $this->element(
            'Comun/form_actions',
            array(
                'url_cancel' => array(
                    'controller' => 'users',
                    'action' => 'listing',
                ),
            )
        );
        ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="aag-title">
        <?php echo __t('User.Permissions_list') .' / '. trim(h($user['User']['name']) . ' ' . h($user['User']['surname'])); ?>
    </div>
    <div class="o-auto p-top-1">
        <table class="table-tracking table-responsive">
            <thead>
            <tr>
                <th><?php echo __t('User.Permission'); ?></th>
                <th class="ta-center" width="80"><?php echo __t('User.Group'); ?></th>
                <th class="ta-center c-exito" width="80"><?php echo __t('User.Allow'); ?></th>
                <th class="ta-center c-fallo" width="80"><?php echo __t('User.Deny'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $last_grouping_id = -1;

            foreach ($matriz_permissions as $permission) {

                if ($permission['GroupingPermission']['id'] != $last_grouping_id) { ?>
                    <tr>
                        <td colspan="4">
                            <div class="c-secondary fs-x-large f-style-italic">
                                <?php echo h($permission['GroupingPermission']['name' . __s()]); ?>
                            </div>
                        </td>
                    </tr>

                    <?php
                    $last_grouping_id = $permission['GroupingPermission']['id'];
                }
                ?>
                <tr>
                    <td>
                        <div class="m-left-1">
                            <?php echo ' - ' . h($permission['Permission']['name' . __s()]); ?>
                        </div>
                    </td>
                    <td class="ta-center">
                        <?php echo (isset($permission['BelongsGroup']['value'])) ? '<span class="icon-check"></span>' : ''; ?>
                    </td>
                    <td class="ta-center">
                        <?php
                        if (isset($permission['BelongsGroup']['value'])) {
                            $options = array(
                                'id' => ConstantsPrefixNameFieldPermit::PERMITIR . '_' . $permission['Permission']['id'],
                                'class' => 'chk-permiso-js',
                                'label' => false,
                                'type' => 'checkbox',
                                'hiddenField' => false,
                                'default' => false,
                                'disabled' => true,
                                'checked' => false,
                                'data-permiso_id' => $permission['Permission']['id'],
                                'data-permitir' => ConstantsBooleans::YES,
                            );
                        } else {
                            $options = array(
                                'id' => ConstantsPrefixNameFieldPermit::PERMITIR . '_' . $permission['Permission']['id'],
                                'class' => 'chk-permiso-js',
                                'label' => false,
                                'type' => 'checkbox',
                                'hiddenField' => false,
                                'default' => false,
                                'data-permiso_id' => $permission['Permission']['id'],
                                'data-permitir' => ConstantsBooleans::YES,
                            );
                        }

                        echo $this->Form->input(
                            'CustomPermission.' . ConstantsPrefixNameFieldPermit::PERMITIR . '_' . $permission['Permission']['id'],
                            $options
                        );
                        ?>
                    </td>
                    <td class="ta-center">
                        <?php
                        if (isset($permission['BelongsGroup']['value'])) {
                            $options_deny = array(
                                'id' => ConstantsPrefixNameFieldPermit::DENEGAR . '_' . $permission['Permission']['id'],
                                'class' => 'chk-permiso-js',
                                'label' => false,
                                'type' => 'checkbox',
                                'hiddenField' => false,
                                'default' => false,
                                'data-permiso_id' => $permission['Permission']['id'],
                                'data-permitir' => ConstantsBooleans::NO,
                            );
                        } else {
                            $options_deny = array(
                                'id' => ConstantsPrefixNameFieldPermit::DENEGAR . '_' . $permission['Permission']['id'],
                                'class' => 'chk-permiso-js',
                                'label' => false,
                                'type' => 'checkbox',
                                'hiddenField' => false,
                                'default' => false,
                                'disabled' => true,
                                'checked' => false,
                                'data-permiso_id' => $permission['Permission']['id'],
                                'data-permitir' => ConstantsBooleans::NO,
                            );
                        }

                        echo $this->Form->input(
                            'CustomPermission.' . ConstantsPrefixNameFieldPermit::DENEGAR . '_' . $permission['Permission']['id'],
                            $options_deny
                        );


                        /*echo $this->Form->input(
                            'PermisoPersonalizado.'.ConstantsPrefixNameFieldPermit::DENEGAR.'_'.$permission['Permiso']['id'],
                            array(
                                'id' => ConstantsPrefixNameFieldPermit::DENEGAR.'_'.$permission['Permiso']['id'],
                                'class' => 'chk-permiso-js',
                                'label' => false,
                                'type' => 'checkbox',
                                'hiddenField' => false,
                                'default' => false,
                                'data-permiso_id' => $permission['Permiso']['id'],
                                'data-permitir' => ConstantsBooleans::NO,
                            )
                        );
                        */
                        ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <?php echo $this->Form->end(); ?>
</div>
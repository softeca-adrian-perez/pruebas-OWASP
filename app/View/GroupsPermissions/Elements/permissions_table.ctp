<div class="o-auto" id="permissions_table-js">
    <?php echo $this->Form->create('Permissions', array('class' => 'form-horizontal custom')); ?>
        <table class="table-tracking">
            <thead>
            <tr>
                <th><?php echo __t('Maintenance.Permissions'); ?></th>
                <th class="ta-center"><?php echo __t('General.Include'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $last_grouping_id = -1;

            foreach($permissions as $permission){

                if($permission['GroupingPermission']['id'] != $last_grouping_id){ ?>
                    <tr class="cursor-pointer">
                        <td colspan="2">
                            <div class="aag-subtitle">
                                <?php echo h($permission['GroupingPermission']['name'.__s()]); ?>
                            </div>
                        </td>
                    </tr>

                    <?php
                    $last_grouping_id = $permission['GroupingPermission']['id'];
                }
                ?>
                <tr class="cursor-pointer">
                    <td>
                        <div class="m-left-1">
                            <?php echo ' - '.h($permission['Permission']['name'.__s()]); ?>
                        </div>
                    </td>
                    <td class="ta-center">
                        <?php
                        $checked = 0;
                        if  (isset($checked_permissions)){
                            $checked = in_array($permission['Permission']['id'], $checked_permissions) ? 1 : 0;
                        }
                        echo $this->Form->input(
                            'GroupPermissionPermission.' . $permission['Permission']['id'],
                            array(
                                'label' => false,
                                'type' => 'checkbox',
                                'disabled' => (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) ? false : true,
                                'hiddenField' => false,
                                'default' => $checked,
                            )
                        );
                        ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    <?php echo $this->Form->end(); ?>
</div>
<div class="aag-subtitle">
    <?php echo __t('GroupPermission.Group_permission')?>
</div>
<div class="o-auto p-top-1">
    <table class="table-tracking table-responsive">
        <!-- <thead>
        <tr>
            <th><?php echo __t('User.Permission'); ?></th>
            <th class="ta-center" width="80"><?php echo __t('User.Group'); ?></th>
        </tr>
        </thead> -->
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
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
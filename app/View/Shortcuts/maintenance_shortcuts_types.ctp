<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Maintenance.Maintenance'),
                array(
                    'controller' => 'maintenance',
                    'action' => 'home'
                )
            ),
            $this->Html->link(
                __t('Shortcut.Shortcuts_types'),
                array(
                    'controller' => 'shortcuts',
                    'action' => 'maintenance_shortcuts_types'
                )
            ),
            __t('Shortcut.List'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back')?></a>
    </div>
</div>
<div class="cnt-data">
    <div class="aag-title cnt-data-element p-top-1">
        <?php echo __t('Shortcut.Shortcuts_types'); ?>
    </div>
    <div class="o-auto">
        <table class="table-tracking">
            <thead>
            <tr>
                <th><?php echo $this->Paginator->sort('ShortcutType.name_en', __t('Shortcut.English_name')); ?></th>
                <th><?php echo $this->Paginator->sort('ShortcutType.name_fr', __t('Shortcut.French_name')); ?></th>
                <th><?php echo $this->Paginator->sort('ShortcutType.name_de', __t('Shortcut.German_name')); ?></th>
                <th><?php echo $this->Paginator->sort('ShortcutType.single', __t('Shortcut.Size')); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($shortcuts_types as $shortcut_type) { ?>
                <tr>
                    <td>
                        <?php if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
                            echo $this->Html->link(
                                $shortcut_type['ShortcutType']['name_en'],
                                array(
                                    'controller' => 'shortcuts',
                                    'action' => 'edit_type',
                                    $shortcut_type['ShortcutType']['id']
                                ),
                                array(
                                    'class' => 'c-primary'
                                )
                            );
                        } else {
                            echo h($shortcut_type['ShortcutType']['name_en']);
                        }?>
                    </td>
                    <td>
                        <?php echo h($shortcut_type['ShortcutType']['name_fr']); ?>
                    </td>
                    <td>
                        <?php echo h($shortcut_type['ShortcutType']['name_de']); ?>
                    </td>
                    <td>
                        <?php if ($shortcut_type['ShortcutType']['single']) {
                            echo __t('Shortcut.Big_size');
                        } else {
                            echo __t('Shortcut.Small_size');
                        } ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <?php echo $this->element('Comun/paginacion'); ?>
</div>

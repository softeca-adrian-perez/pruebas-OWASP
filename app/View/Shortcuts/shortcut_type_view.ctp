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
            __t('Shortcut.View'),
        ));
        ?>
    </div>
    <div>
        <?php
        echo $this->Html->link(
            __t('General.Back'),
            array(
                'controller' => 'shortcuts',
                'action' => 'maintenance_shortcuts_types',
            ),
            array(
                'class' => 'aag-button medium four',
            )
        );
        echo $this->Html->link(
            __t('General.Edit'),
            array(
                'controller' => 'shortcuts',
                'action' => 'edit_type',
                $shortcut_type['ShortcutType']['id'],
            ),
            array(
                'escape' => false,
                'class' => 'aag-button medium',
            )
        );
        ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="aag-title">
        <?php echo __t('Shortcut.Shortcuts_types'); ?>
    </div>
    <div class="cnt-form-inputs">
        <div>
            <strong><?php echo __t('Shortcut.English_name') ?>: </strong>
            <br>
            <div class="b-bottom-1 height_input"><?php echo h($shortcut_type['ShortcutType']['name_en']); ?></div>
        </div>
        <div>
            <strong><?php echo __t('Shortcut.French_name') ?>: </strong>
            <br>
            <div class="b-bottom-1 height_input"><?php echo h($shortcut_type['ShortcutType']['name_fr']); ?></div>
        </div>
        <div>
            <strong><?php echo __t('Shortcut.German_name') ?>: </strong>
            <br>
            <div class="b-bottom-1 height_input"><?php echo h($shortcut_type['ShortcutType']['name_de']); ?></div>
        </div>
        <div id="size" data-size="<?php echo $shortcut_type['ShortcutType']['single']?>">
            <strong><?php echo __t('Shortcut.Size') ?>: </strong>
            <br>
            <div class="b-bottom-1 height_input">
                <?php if ($shortcut_type['ShortcutType']['single']) {
                    echo __t('Shortcut.Big_size');
                } else {
                    echo __t('Shortcut.Small_size');
                } ?>
            </div>
        </div>
        <div class="ta-center">
            <strong><?php echo __t('General.Example');?></strong>
            <br>
            <img src="/img/small_size.png" alt="" id="small_size">
            <img src="/img/big_size.png" alt="" id="big_size">
        </div>
    </div>
</div>
<script>
    if($('#size').data('size')){
        $('#small_size').hide();
    } else {
        $('#big_size').hide();
    }
</script>
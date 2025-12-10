<?php
$action = $this->request->action;
echo $this->Form->create('ShortcutType',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data',
    )
);
    echo $this->Form->hidden('ShortcutType.id'); ?>
    <div class="cnt-breadcrumb">
        <div>
            <?php
            if($action == 'edit_type')
            {
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
                    __t('Shortcut.Edit'),
                ));
            }
            ?>
        </div>
        <div>
            <?php echo $this->element('Comun/form_actions', $cancel_action); ?>
        </div>
    </div>
    <div class="cnt-data aag-padding">
        <div class="aag-title p-bottom-1">
            <?php echo __t('Shortcut.Shortcuts_types'); ?>
        </div>
        <div class="cnt-form-inputs">
            <?php
            echo $this->Form->input(
                'name_en',
                array(
                    'required' => true,
                    'type' => 'text',
                    'label' => __t('Shortcut.English_name'),
                )
            );
            echo $this->Form->input(
                'name_fr',
                array(
                    'required' => true,
                    'type' => 'text',
                    'label' => __t('Shortcut.French_name'),
                )
            );
            echo $this->Form->input(
                'name_de',
                array(
                    'required' => true,
                    'type' => 'text',
                    'label' => __t('Shortcut.German_name'),
                )
            );
            ?>
            <div class="cont-services follow_up">
                <label class="jc-center">
                    <?php
                    if (isset($shortcut_type['ShortcutType']['single']) && $shortcut_type['ShortcutType']['single'] == ConstantsBooleans::YES) {
                        $checked = true;
                        $text = __t('Shortcut.Big_size');
                    } else {
                        $checked = false;
                        $text = __t('Shortcut.Small_size');
                    }
                    echo $this->Form->input(
                        'ShortcutType.single',
                        array(
                            'type' => 'checkbox',
                            'label' => false,
                            'div' => false,
                            'value' => 1,
                            'checked' => $checked,
                            'id' => 'size_check'
                        )
                    ); ?>
                    <span class="ion-arrow-resize icono-grande unselectable"></span>
                    <span class="unselectable" id="label"><?php echo $text;?></span>
                </label>
            </div>
        </div>
        <div class="ta-center p-vertical-1">
            <strong><?php echo __t('General.Example');?></strong>
            <br>
            <img src="/img/small_size.png" alt="" id="small_size">
            <img src="/img/big_size.png" alt="" id="big_size">
        </div>
    </div>
<?php echo $this->Form->end(); ?>
<script>
    if($('#size_check').prop('checked')){
        $('#small_size').hide();
    } else {
        $('#big_size').hide();
    }
    $('.follow_up').on('click',function(){
        if($('#size_check').prop('checked')){
            $('#label').html($.i18n._('Shortcut.Big_size'));
            $('#small_size').hide();
            $('#big_size').show();
        } else {
            $('#label').html($.i18n._('Shortcut.Small_size'));
            $('#small_size').show();
            $('#big_size').hide();
        }
    });
</script>

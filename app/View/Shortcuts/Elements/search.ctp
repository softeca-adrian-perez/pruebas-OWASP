<?php
echo $this->Form->create(
    'Search',
    array(
        'class' => 'cnt-form-search',
        'type' => 'get',
        'url' => array(
            'controller' => 'shortcuts',
            'action' => 'maintenance_shortcuts',
        ),
    )
);
?>
    <div class="cnt-form-search-title">
        <?php echo __t('Shortcut.Shortcuts'); ?>
    </div>
    <div class="cnt-form-inputs">
        <?php
        echo $this->Form->input(
            'title',
            array(
                'class' => 'clear_field',
                'type' => 'text',
                'required' => true,
                'label' => __t('Shortcut.Title'),
            )
        );
        echo $this->Form->input(
            'network',
            array(
                'label' => __t('Shortcut.Networks'),
                'type' => 'select',
                'required' => true,
                'class' => 'select2-multiple clear_field',
                'empty' => true,
                'options' => $networks_list
            )
        );
        echo $this->Form->input(
            'type',
            array(
                'label' => __t('Communication.Communication_section'),
                'type' => 'select',
                'required' => true,
                'class' => 'select2-multiple clear_field',
                'empty' => true,
                'options' => $shortcuts_types_list
            )
        );
        echo $this->Form->input(
            'active',
            array(
                'label' => __t('Shortcut.Active'),
                'type' => 'select',
                'class' => 'select2-multiple clear_field',
                'options' => $active,
                'empty' => true,
                'id' => 'read',
                'required' => true,
            )
        );
        ?>
    </div>
    <div class="cnt-form-search-buttons">
        <?php
        echo $this->Form->button(
            __t('General.Search'),
            array(
                'type' => 'submit',
                'class' => 'aag-button medium'
            )
        );
        echo $this->Form->button(
            "<span class='aag-icon-escoba'></span>",
            array(
                'id' => 'clear_field',
                'class' => 'aag-button medium four outlined',
                'escape' => false,
                'title' => __t('General.Clean_search')
            )
        );
        ?>
    </div>
<?php echo $this->Form->end(); ?>
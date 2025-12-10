<?php
echo $this->Form->create(
    'Search',
    array(
        'class' => 'cnt-form-search',
        'type' => 'get',
        'url' => array(
            'controller' => 'communications',
            'action' => 'maintenance_communications_sections',
        ),
    )
);
?>
    <div class="cnt-form-search-title">
        <?php echo __t('Communication.Communications_sections'); ?>
    </div>
    <div class="cnt-form-inputs">
        <?php
        echo $this->Form->input(
            'name' . __s(),
            array(
                'type' => 'text',
                'class' => 'clear_field',
                'required' => true,
                'label' => __t('General.Name' . __s()),
            )
        );
        echo $this->Form->input(
            'scrolling',
            array(
                'label' => __t('Section.Scrolling'),
                'type' => 'select',
                'class' => 'select2-multiple clear_field',
                'options' => $scrolling,
                'empty' => true,
                'id' => 'read',
                'required' => true,
            )
        );
        echo $this->Form->input(
            'visual',
            array(
                'label' => __t('Section.Visual'),
                'type' => 'select',
                'class' => 'select2-multiple clear_field',
                'options' => $visual,
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
<?php
echo $this->Form->create(
    'Search',
    array(
        'class' => 'cnt-form-search',
        'type' => 'get',
        'url' => array(
            'controller' => 'communications',
            'action' => 'maintenance_section_subsection',
        ),
    )
);
?>
    <div class="cnt-form-search-title">
        <?php echo __t('Communication.Communications_subsections'); ?>
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
            'communication_section_id',
            array(
                'label' => __t('Communication.Section'),
                'type' => 'select',
                'class' => 'select2-multiple clear_field',
                'options' => $sections,
                'empty' => true,
                'id' => 'communication_section_id',
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
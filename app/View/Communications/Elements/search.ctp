<?php
echo $this->Form->create(
    'Search',
    array(
        'class' => 'cnt-form-search',
        'type' => 'get',
        'url' => array(
            'controller' => 'communications',
            'action' => 'maintenance_communications',
        ),
    )
);
?>
    <div class="cnt-form-search-title">
        <?php echo __t('Communication.Communications'); ?>
    </div>
    <div class="cnt-form-inputs">
        <?php
        echo $this->Form->input(
            'title',
            array(
                'type' => 'text',
                'class' => 'clear_field',
                'required' => true,
                'label' => __t('Communication.Title'),
            )
        );
        echo $this->Form->input(
            'type',
            array(
                'label' => __t('Communication.Communications_sections'),
                'type' => 'select',
                'required' => true,
                'class' => 'select2-multiple clear_field',
                'empty' => true,
                'options' => $communications_section
            )
        );
        echo $this->Form->input(
            'sub_type',
            array(
                'label' => __t('Communication.Communications_subsections'),
                'type' => 'select',
                'required' => true,
                'class' => 'select2-multiple clear_field',
                'empty' => true,
                'options' => $communications_subsection
            )
        );
        echo $this->Form->input(
            'network',
            array(
                'label' => __t('Communication.Networks'),
                'type' => 'select',
                'required' => true,
                'class' => 'select2-multiple clear_field',
                'empty' => true,
                'options' => $networks_list
            )
        );
        echo $this->Form->input(
            'distributor_network',
            array(
                'label' => __t('Communication.Distributors_networks'),
                'type' => 'select',
                'required' => true,
                'class' => 'select2-multiple clear_field',
                'empty' => true,
                'options' => $distributors_networks_list
            )
        );
        echo $this->Form->input(
            'trading_group',
            array(
                'label' => __t('Communication.Trading_group'),
                'type' => 'select',
                'required' => true,
                'class' => 'select2-multiple clear_field',
                'empty' => true,
                'options' => $trading_group_list
            )
        );
        echo $this->Form->input(
            'activity',
            array(
                'label' => __t('Communication.Activities'),
                'type' => 'select',
                'required' => true,
                'class' => 'select2-multiple clear_field',
                'empty' => true,
                'options' => $activities_list
            )
        );
        echo $this->Form->input(
            'active',
            array(
                'label' => __t('Communication.Active'),
                'type' => 'select',
                'class' => 'select2-multiple clear_field',
                'options' => $active,
                'empty' => true,
                'id' => 'read',
                'required' => true,
            )
        );
        echo $this->Form->input(
            'is_popup',
            array(
                'label' => __t('Communication.Popup'),
                'type' => 'select',
                'class' => 'select2-multiple clear_field',
                'options' => $pop_up,
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
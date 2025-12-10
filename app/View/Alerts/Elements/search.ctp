<?php
echo $this->Form->create(
    'Search',
    array(
        'class' => 'cnt-form-search',
        'type' => 'get',
        'url' => array(
            'controller' => 'alerts',
            'action' => 'home'
        )
    )
);
?>
    <div class="cnt-form-search-title">
        <?php echo __t('Alert.Alerts'); ?>
    </div>
    <div class="cnt-form-inputs">
        <?php
        echo $this->Form->input(
            'body',
            array(
                'class' => 'clear_field',
                'type' => 'text',
                'required' => true,
                'label' => __t('Task.Task_name'),
            )
        );
        echo $this->Form->input(
            'from',
            array(
                'class' => 'fecha-js from-js clear_field',
                'type' => 'text',
                'data-to' => '#to',
                'required' => true,
                'id' => 'from',
                'div' => array(
                    'class' => 'datepicker datepicker-label-block',
                ),
                'label' => __t('General.From'),
            )
        );
        echo $this->Form->input(
            'to',
            array(
                'class' => 'fecha-js to-js clear_field',
                'type' => 'text',
                'required' => true,
                'id' => 'to',
                'data-from' => '#from',
                'div' => array(
                    'class' => 'datepicker datepicker-label-block',
                ),
                'label' => __t('General.To'),
            )
        );
        echo $this->Form->input(
            'read',
            array(
                'label' => __t('Alert.Read'),
                'type' => 'select',
                'class' => 'select2-multiple clear_field',
                'options' => $read_types,
                'empty' => true,
                'id' => 'read',
                'required' => true,
            )
        );
        echo $this->Form->input(
            'type',
            array(
                'label' =>  __t('Alert.Type'),
                'type' => 'select',
                'class' => 'select2-multiple clear_field',
                'options' => $types,
                'empty' => true,
                'id' => 'type',
                'required' => true
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
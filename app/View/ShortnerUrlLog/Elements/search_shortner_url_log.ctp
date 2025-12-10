<?php
$controller = $this->request->controller;
$action = $this->request->action;
echo $this->Form->create(
    'Search',
    array(
        'class' => 'cnt-form-search buscador-js',
        'style' => 'margin-top: 0;',
        'type' => 'get',
        'url' => array(
            'controller' => 'shortner_url_log',
            'action' => 'home'
        )
    )
);
    ?>
    <div class="cnt-form-search-title">
        <?php echo __t('Sms.Url_shortner_configuration'); ?>
    </div>
    <div class="cnt-form-inputs">
        <?php
        echo $this->Form->input(
            'long_url',
            array(
                'class' => 'clear_field',
                'type' => 'text',
                'required' => true,
                'label' => __t('Sms.Long_url'),
            )
        );
        echo $this->Form->input(
            'short_url',
            array(
                'class' => 'clear_field',
                'type' => 'text',
                'required' => true,
                'label' => __t('Sms.Short_url'),
            )
        );
        echo $this->Form->input(
            'domain',
            array(
                'class' => 'clear_field',
                'type' => 'text',
                'required' => true,
                'label' => __t('Sms.Domain'),
            )
        );
        echo $this->Form->input(
            'short_id',
            array(
                'class' => 'clear_field',
                'type' => 'text',
                'required' => true,
                'label' => __t('Sms.Short_id'),
            )
        );
        echo $this->Form->input(
            'expire_days',
            array(
                'class' => 'clear_field',
                'type' => 'number',
                'required' => true,
                'label' => __t('Sms.Expire_days'),
            )
        );
        echo $this->Form->input(
            'expire_at_datetime_from',
            array(
                'class' => 'fecha-js to-js clear_field',
                'type' => 'text',
                'required' => true,
                'div' => array(
                    'class' => 'datepicker datepicker-label-block',
                ),
                'label' => __t('Sms.Expire_at_datetime_from'),
            )
        );
        echo $this->Form->input(
            'expire_at_datetime_to',
            array(
                'class' => 'fecha-js to-js clear_field',
                'type' => 'text',
                'required' => true,
                'div' => array(
                    'class' => 'datepicker datepicker-label-block',
                ),
                'label' => __t('Sms.Expire_at_datetime_to'),
            )
        );
        echo $this->Form->input(
            'expire_at_views',
            array(
                'class' => 'clear_field',
                'type' => 'number',
                'required' => true,
                'label' => __t('Sms.Expire_at_views'),
            )
        );
        echo $this->Form->input(
            'status',
            array(
                'label' => __t('Sms.Status'),
                'type' => 'select',
                'class' => 'select2-multiple clear_field',
                'options' => $status,
                'empty' => true,
                'required' => true,
            )
        );
        echo $this->Form->input(
            'status_code',
            array(
                'label' => __t('Sms.Status_code'),
                'type' => 'select',
                'class' => 'select2-multiple clear_field',
                'options' => $status_codes,
                'empty' => true,
                'required' => true,
            )
        );
        // echo $this->Form->input(
        //     'test_config',
        //     array(
        //         'label' => __t('Sms.Test_config'),
        //         'type' => 'select',
        //         'class' => 'select2-multiple clear_field',
        //         'options' => $test_config,
        //         'empty' => true,
        //         'required' => true,
        //     )
        // );
        echo $this->Form->input(
            'creation_date_from',
            array(
                'class' => 'fecha-js to-js clear_field',
                'type' => 'text',
                'required' => true,
                'div' => array(
                    'class' => 'datepicker datepicker-label-block',
                ),
                'label' => __t('Sms.Creation_date_from'),
            )
        );
        echo $this->Form->input(
            'creation_date_to',
            array(
                'class' => 'fecha-js to-js clear_field',
                'type' => 'text',
                'required' => true,
                'div' => array(
                    'class' => 'datepicker datepicker-label-block',
                ),
                'label' => __t('Sms.Creation_date_to'),
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
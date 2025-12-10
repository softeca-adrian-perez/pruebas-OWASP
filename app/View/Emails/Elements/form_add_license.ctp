<?php
echo $this->Form->create('SendgridLicenseConfig',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data',
        'id' => 'form-sendgrid-js'
    )
);
echo $this->Form->hidden('SendgridLicenseConfig.id');
?>
<div>
    <div class="required">
        <?php
        echo $this->Form->input(
            'country_id',
            array(
                'label' => __t('Garage.Country'),
                'type' => 'select',
                'class' => 'select2-multiple clear_field',
                'options' => $available_countries,
                'empty' => false,
            )
        );
        echo $this->Form->input(
            'name',
            array(
                'label' => __t('General.Name'),
                'type' => 'text',
            )
        );
        echo $this->Form->input(
            'from_email',
            array(
                'label' => __t('Email.From_email'),
                'type' => 'text',
            )
        );
        echo $this->Form->input(
            'api_key',
            array(
                'label' => __t('Sms.Api_key'),
                'type' => 'text',
                'class' => 'api-key-js',
            )
        );
        echo $this->Form->input(
            'platform_id',
            array(
                'label' => __t('General.Platform'),
                'type' => 'select',
                'class' => 'select2-multiple clear_field platform-disable-js',
                'options' => $platforms_list,
                'empty' => false,
                'selected' => $platform_id,
                'disabled' => true
            )
        );
        echo $this->Form->hidden(
            'platform_id',
            array(
                'id' => 'platform_id',
                'value' => $platform_id
            )
        );
        ?>
    </div>
    <div class="ta-right p-top-1">
        <?php
        echo $this->Form->button(
            __t('General.Save'),
            array(
                'class' => 'aag-button medium green',
                'id' => 'add_sendgrid_form',
                'type' => 'button',
                'data-url' => Router::url(
                    array(
                        'controller' => 'emails',
                        'action' => 'ajax_add_sendgrid_license_config'
                    )
                )
            )
        );
        ?>
    </div>
</div>
<?php echo $this->Form->end(); ?>

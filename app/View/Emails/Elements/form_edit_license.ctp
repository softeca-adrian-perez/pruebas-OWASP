<?php
echo $this->Form->create(
    'SendgridLicenseConfig',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data',
        'id' => 'sendgrid-form' . ($sendgrid['SendgridLicenseConfig']['id'] ?? 'edit')
    )
);
?>
<div>
    <?php
    echo $this->Form->hidden(
        'id',
        array(
            'value' => $sendgrid['SendgridLicenseConfig']['id'] ?? null
        )
    ); ?>
    <div class="required">
        <?php
        echo $this->Form->hidden(
            'country_id',
            array(
                'id' => 'country_id',
                'value' => $sendgrid['SendgridLicenseConfig']['country_id']
            )
        );
        echo $this->Form->input(
            'country_id',
            array(
                'label' => __t('Garage.Country'),
                'type' => 'select',
                'class' => 'select2-multiple clear_field',
                'options' => $all_countries,
                'empty' => false,
                'disabled' => true,
                'selected' => $sendgrid['SendgridLicenseConfig']['country_id']
            )
        );
        echo $this->Form->input(
            'name',
            array(
                'label' => __t('General.Name'),
                'value' => $sendgrid['SendgridLicenseConfig']['name'] ?? '',
            )
        );
        echo $this->Form->input(
            'from_email',
            array(
                'label' => __t('Email.From_email'),
                'value' => $sendgrid['SendgridLicenseConfig']['from_email'] ?? '',
            )
        );
        echo $this->Form->input(
            'api_key',
            array(
                'label' => __t('Sms.Api_key'),
                'class' => 'api-key-js',
                'value' => Texto::encryptDecryptText($sendgrid['SendgridLicenseConfig']['api_key']) ?? '',
            )
        );
        echo $this->Form->input(
            'platform_id',
            array(
                'label' => __t('General.Platform'),
                'type' => 'select',
                'class' => 'select2-multiple clear_field',
                'options' => $platforms_list,
                'empty' => false,
                'selected' => $sendgrid['SendgridLicenseConfig']['platform_id'],
                'disabled' => true
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
                'id' => $sendgrid['SendgridLicenseConfig']['id'] ?? 'edit',
                'type' => 'button',
                'data-url' => Router::url(
                    array(
                        'controller' => 'emails',
                        'action' => 'ajax_edit_sendgrid_license_config'
                    )
                ),
                'onclick' => "edit_sendgrid_conf(this)"
            )
        );
        ?>
    </div>
</div>
<?php echo $this->Form->end(); ?>
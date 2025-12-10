<?php
echo $this->Html->script('sms.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Form->create('ShortnerUrl',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data',
        'id' => 'sms-form-url'.($tokenLogin['Sms']['id'] ?? 'edit')
    )
);
?>
<div class="container-url-js">
    <?php
    echo $this->Form->hidden(
        'id',
        array(
            'value' => $tokenLogin['ShortnerUrl']['id'] ?? null
        )
    );
    echo $this->Form->hidden(
        'sms_license_config_id',
        array(
            'value' => $tokenLogin['Sms']['id'] ?? null
        )
    );?>
    <div class="aag-subtitle">
        <?php echo __t('Garage.Country') ?>
    </div>
    <?php
        echo $this->Form->input(
            'country_id',
            array(
                'label' => __t('Garage.Country'),
                'type' => 'select',
                'class' => 'select2-multiple clear_field',
                'options' => $countries,
                'empty' => false,
                'disabled' => true,
                'selected' => $tokenLogin['Sms']['country_id']
            )
        );
    ?>
    <div class="aag-subtitle">
        <?php echo __t('Sms.Api_config'); ?>
    </div>
    <div>
        <?php
        echo $this->Form->input(
            'api_key',
            array(
                'label' => __t('Sms.Api_key'),
                'value' => $tokenLogin['ShortnerUrl']['api_key'] != Texto::encryptDecryptText(SHORTNER_API_KEY, false) ? $tokenLogin['ShortnerUrl']['api_key'] : '',
                'class' => 'api-key-js',
            )
        );
        echo $this->Form->input(
            'expire_days',
            array(
                'label' => __t('Sms.Expire_datetime'),
                'value' => $tokenLogin['ShortnerUrl']['expire_days'] ?? null,
                'type' => 'number',
                'class' => 'expire-at-datetime-js',
            )
        );
        echo $this->Form->input(
            'expire_at_views',
            array(
                'label' => __t('Sms.Expire_views'),
                'value' => $tokenLogin['ShortnerUrl']['expire_at_views'] ?? null,
                'type' => 'number',
                'class' => 'expire-at-views-js',
            )
        );
        echo $this->Form->input(
            'domain',
            array(
                'label' => __t('Sms.Domain') . ' <span data-tooltip aria-haspopup="true" class="has-tip ion-information-circled" title="' . __t('Sms.Tool_tip') . '"></span>',
                'value' => $tokenLogin['ShortnerUrl']['domain'] ?? null,
                'class' => 'domain-js index-domain-js' . $tokenLogin['ShortnerUrl']['id'],
            )
        );
        ?>
    </div>
    <div class="ta-right p-top-1">
        <?php
        echo $this->Form->button(
            __t('General.Save'),
            array(
                'class' => 'aag-button medium green save-config-js',
                'id' => $tokenLogin['Sms']['id'] ?? 'edit-url',
                'type' => 'button',
                'data-url' => Router::url(
                    array(
                        'controller' => 'sms',
                        'action' => 'ajax_edit_license_config_url'
                    )
                ),
                'data-shortner-url' => Router::url(array(
                    'controller' => 'sms',
                    'action' => 'shortner_url_api_convert',
                )),
                'data-sms-id' => $tokenLogin['Sms']['id'],
                'data-country-id' => $tokenLogin['Sms']['country_id'],
            )
        );
        ?>
    </div>
</div>
<?php echo $this->Form->end(); ?>

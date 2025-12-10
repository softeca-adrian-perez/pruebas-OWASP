<?php
echo $this->Html->script('sms.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Form->create('Sms',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data',
        'id' => 'sms-form'.($tokenLogin['Sms']['id'] ?? 'edit')
    )
);
?>
<div>
    <?php
    echo $this->Form->hidden(
        'id',
        array(
            'value' => $tokenLogin['Sms']['id'] ?? null
        )
    );
    echo $this->Form->hidden(
        'role_id',
        array(
            'value' => CakeSession::read('Auth.User.role_id')
        )
    );
    if(CakeSession::read('Auth.User.role_id') == ConstantsRoles::ADMIN) { ?>
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
        }
        ?>
    <div class="aag-subtitle">
        <?php echo __t('Sms.Iframe_config'); ?>
    </div>
    <div>
        <?php
        echo $this->Form->input(
            'username_iframe',
            array(
                'label' => __t('User.Username'),
                'value' => $tokenLogin['Sms']['username_iframe'] ?? ''
            )
        );
        echo $this->Form->input(
            'password_iframe',
            array(
                'label' => __t('User.Password'),
                'value' => $tokenLogin['Sms']['password_iframe'] ?? ''
            )
        );
        echo $this->Form->input(
            'license_iframe',
            array(
                'label' => __t('Sms.License'),
                'value' => $tokenLogin['Sms']['license_iframe'] ?? ''
            )
        );
        ?>
    </div>
    <div class="aag-subtitle">
        <?php echo __t('Sms.Api_config'); ?>
    </div>
    <div>
        <?php
        echo $this->Form->input(
            'username_api',
            array(
                'label' => __t('User.Username'),
                'value' => $tokenLogin['Sms']['username_api'] ?? null
            )
        );
        echo $this->Form->input(
            'password_api',
            array(
                'label' => __t('User.Password'),
                'value' => $tokenLogin['Sms']['password_api'] ?? null
            )
        );
        echo $this->Form->input(
            'license_api',
            array(
                'label' => __t('Sms.License'),
                'value' => $tokenLogin['Sms']['license_api'] ?? null
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
                'id' => $tokenLogin['Sms']['id'] ?? 'edit',
                'type' => 'button',
                'data-url' => Router::url(
                    array(
                        'controller' => 'sms',
                        'action' => 'ajax_edit_license_config'
                    )
                ),
                'onclick' => "Edit_sms_conf(this)"
            )
        );
        ?>
    </div>
</div>
<?php echo $this->Form->end(); ?>

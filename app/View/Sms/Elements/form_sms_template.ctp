<?php
echo $this->Html->script('sms.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Form->create('SmsTemplate',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data',
        'id' => 'sms-template-form'.$sms_templates['SmsTemplate']['id']
    )
);
?>
<div>
    <div class="aag-subtitle">
        <?php echo __t('Sms.Template_config'); ?>
    </div>
    <div>
        <?php
        echo $this->Form->hidden(
            'id',
            array(
                'id' => 'sms-template-js',
                'value' => $sms_templates['SmsTemplate']['id']
            )
        );
        echo $this->Form->hidden(
            'country_id',
            array(
                'value' => $sms_templates['SmsTemplate']['country_id'],
                'class' => 'country-js-' . $sms_templates['SmsTemplate']['id'],
            )
        );
        echo $this->Form->input(
            'template_type_id',
            array(
                'label' => __t('Sms.Template_type'),
                'type' => 'select',
                'class' => 'select2-multiple clear_field',
                'options' => $template_types,
				'selected' => $sms_templates['SmsTemplate']['template_type_id'],
				'disabled' => true
            )
        );
        echo $this->Form->input(
            'template_type_id',
            array(
                'required' => true,
                'type' => 'hidden',
                'value' => $sms_templates['SmsTemplate']['template_type_id'],
            )
        );
        echo $this->Form->input(
            'description',
            array(
                'label' => __t('General.Description').' ('.__t('Sms.Max_characters').' '.'<span id="char_count_container-edit'.$sms_templates['SmsTemplate']['id'].'"></span>'.') ('.'<span id="sms_count_container-edit'.$sms_templates['SmsTemplate']['id'].'"></span>'.' '.__t('Sms.Will_be_sent').'.)',
                'value' => $sms_templates['SmsTemplate']['description'] ?? '',
                'class' => 'chart_count-edit'.$sms_templates['SmsTemplate']['id']
            )
        );
        ?>
        <?php
			echo $this->element(
				'../Sms/Elements/options_add_template',
				array(
					'class' => 'button-sms-edit-js'.$sms_templates['SmsTemplate']['id'],
					'template_type_id' => $sms_templates['SmsTemplate']['template_type_id']
				)
			);
		?>
        <?php
        echo $this->Form->input(
            'country_id',
            array(
                'label' => __t('Garage.Country'),
                'type' => 'select',
                'class' => 'clear_field',
                'options' => $countries,
                'selected' => $sms_templates['SmsTemplate']['country_id'],
                'disabled' => true
            )
        );
        echo $this->Form->input(
            'country_id',
            array(
                'required' => true,
                'type' => 'hidden',
                'value' => $sms_templates['SmsTemplate']['country_id'],
            )
        );
        echo $this->Form->input(
            'sender',
            array(
                'label' => __t('Sms.Sender').' (&#9888; '.__t('Sms.Sender_must_be_created').')',
                'value' => $sms_templates['SmsTemplate']['sender'] ?? ''
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
                'id' => $sms_templates['SmsTemplate']['id'],
                'data-url' => Router::url(
                    array(
                        'controller' => 'sms',
                        'action' => 'ajax_create_template'
                    )
                ),
                'onclick' => "Edit_template_conf(this)"
            )
        );
        ?>
    </div>
</div>
<?php echo $this->Form->end(); ?>
<?php echo $this->Html->script('sms.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script')); ?>
<?php
echo $this->Form->create('SmsTemplate',
    array(
        'class' => 'form-horizontal',
        'id' => 'sms-template-form-js'
    )
);
?>
<div>
    <div class="aag-subtitle">
        <?php echo __t('Sms.Template_config'); ?>
    </div>
    <div>
        <?php
        echo $this->Form->input(
            'template_type_id',
            array(
                'label' => __t('Sms.Template_type'),
                'type' => 'select',
                'class' => 'select2-multiple type_template_sms-js',
				'data-url' => Router::url(
					array(
						'controller' => 'sms',
						'action' => 'ajax_get_countries_template'
					)
				),
				'data-url_options_add' => Router::url(
					array(
						'controller' => 'sms',
						'action' => 'ajax_search_options_add_template_list'
					)
				),
				'value' => false,
				'multiple' => false,
                'options' => $template_types,
				'empty' => true
            )
        );
        echo $this->Form->input(
            'description',
            array(
                'label' => __t('General.Description').' ('.__t('Sms.Max_characters').' '.'<span id="char_count_container-add"></span>'.') ('.'<span id="sms_count_container-add"></span>'.' '.__t('Sms.Will_be_sent').')',
                'class' => 'chart_count-add',
				'value' => false,
            )
        );
        ?>
		<div class="options_add_template-js"></div>
        <?php
        echo $this->Form->input(
            'country_id',
            array(
                'label' => __t('Garage.Country'),
                'type' => 'select',
                'class' => 'country_sms-js',
                'options' => array(),
				'empty' => true,
				'value' => false,
            )
        );
        echo $this->Form->input(
            'sender',
            array(
                'label' => __t('Sms.Sender').' (&#9888; '.__t('Sms.Sender_must_be_created').')',
				'value' => false,
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
                'id' => 'add_sms_template',
                'data-url' => Router::url(
                    array(
                        'controller' => 'sms',
                        'action' => 'add_template'
                    )
                )
            )
        );
        ?>
    </div>
</div>
<?php echo $this->Form->end(); ?>

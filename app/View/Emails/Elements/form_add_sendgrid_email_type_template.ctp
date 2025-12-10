<?php
echo $this->Form->create(
    'SendGridEmailTypeTemplate',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data',
        'id' => 'form-add-sendgrid-email-type-template-js-' . $email_type_id . '-' . $country_id
    )
);
?>
<div>
    <div class="aag-title cnt-data-element">
        <?php echo $email_type_name; ?>
    </div>
    <div class="required">
        <?php
        if ($is_web_language) {
            echo $this->Form->input(
                'language_web_id',
                array(
                    'label' => __t('Menu.Language'),
                    'type' => 'select',
                    'class' => 'select2-multiple clear_field',
                    'options' => $languages_list_email_type,
                    'empty' => false,
                )
            );
        } else {
            echo $this->Form->input(
                'language_id',
                array(
                    'label' => __t('Menu.Language'),
                    'type' => 'select',
                    'class' => 'select2-multiple clear_field',
                    'options' => $languages_list_email_type,
                    'empty' => false,
                )
            );
        }
        echo $this->Form->input(
            'sendgrid_template_id',
            array(
                'label' => __t('Sendgrid.Template_id'),
                'type' => 'text',
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
                'id' => $email_type_id . '-' . $country_id,
                'type' => 'button',
                'data-url' => Router::url(
                    array(
                        'controller' => 'emails',
                        'action' => 'ajax_add_sendgrid_email_type_template'
                    )
                ),
                'data-email_type_id' => $email_type_id,
                'data-platform_id' => $platform_id,
                'data-country_id' => $country_id,
                'data-aag_region_id' => $aag_region_id,
                'onclick' => "add_sendgrid_email_type_template(this)"
            )
        );
        ?>
    </div>
</div>
<?php echo $this->Form->end(); ?>
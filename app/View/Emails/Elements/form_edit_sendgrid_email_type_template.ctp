<?php
$sendgrid_email_type_template_id = $sendgrid_email_type_template['SendGridEmailTypeTemplate']['id'];

echo $this->Form->create(
    'SendGridEmailTypeTemplate',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data',
        'id' => 'form-edit-sendgrid-email-type-template-js-' . $sendgrid_email_type_template_id
    )
);
?>
<div>
    <div class="aag-title cnt-data-element">
        <?php echo $email_type_name; ?>
    </div>
    <?php
    echo $this->Form->hidden(
        'sendgrid_email_type_template_id',
        array(
            'id' => 'sendgrid-email-type-template-id-js',
            'value' => $sendgrid_email_type_template['SendGridEmailTypeTemplate']['id']
        )
    );
    ?>
    <div class="required">
        <?php
        if ($is_web_language) {
            echo $this->Form->hidden(
                'language_id',
                array(
                    'value' => $sendgrid_email_type_template['SendGridEmailTypeTemplate']['language_web_id']
                )
            );
            echo $this->Form->input(
                'language_id',
                array(
                    'label' => __t('Menu.Language'),
                    'type' => 'select',
                    'class' => 'select2-multiple',
                    'options' => $languages_list,
                    'empty' => false,
                    'disabled' => true,
                    'selected' => $sendgrid_email_type_template['SendGridEmailTypeTemplate']['language_web_id']
                )
            );
        } else {
            echo $this->Form->hidden(
                'language_id',
                array(
                    'value' => $sendgrid_email_type_template['SendGridEmailTypeTemplate']['language_id']
                )
            );
            echo $this->Form->input(
                'language_id',
                array(
                    'label' => __t('Menu.Language'),
                    'type' => 'select',
                    'class' => 'select2-multiple',
                    'options' => $languages_list,
                    'empty' => false,
                    'disabled' => true,
                    'selected' => $sendgrid_email_type_template['SendGridEmailTypeTemplate']['language_id']
                )
            );
        }
        echo $this->Form->input(
            'sendgrid_template_id',
            array(
                'label' => __t('Sendgrid.Template_id'),
                'type' => 'text',
                'value' => $sendgrid_email_type_template['SendGridEmailTypeTemplate']['sendgrid_template_id']
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
                'id' => $sendgrid_email_type_template_id,
                'type' => 'button',
                'data-url' => Router::url(
                    array(
                        'controller' => 'emails',
                        'action' => 'ajax_edit_sendgrid_email_type_template'
                    )
                ),
                'onclick' => "edit_sendgrid_email_type_template(this)"
            )
        );
        echo $this->Html->link(
            __t('General.Delete'),
            array(
                'controller' => 'emails',
                'action' => 'delete_sendgrid_email_type_template',
                $sendgrid_email_type_template['SendGridEmailTypeTemplate']['id'],
                $platform_id
            ),
            array(
                'class' => 'aag-button medium red'
            )
        );
        ?>
    </div>
</div>
<?php echo $this->Form->end(); ?>
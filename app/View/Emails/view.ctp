<?php
echo $this->Html->script('/js/emails.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
$user = $this->Acceso->user();
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Email.Emails'),
                array(
                    'controller' => 'emails',
                    'action' => 'home'
                )
            ),
            __t('General.View'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium four go-back-js"><?php echo __t('General.Back') ?></a>
        <?php
        if ($email['Email']['old'] == ConstantsBooleans::NO) {
            echo $this->Html->link(
                __t('Email.Resend_email'),
                array(),
                array(
                    'escape' => false,
                    'id' => 'resend-email',
                    'title' => __t('Email.Resend_email'),
                    'class' => 'aag-button medium green ion-ios-refresh-outline',
                    'data-url-href' => Router::url(array(
                        'controller' => 'emails',
                        'action' => 'ajax_resend_email',
                        $email['Email']['id']
                    )),
                    'data-url' => Router::url(array(
                        'controller' => 'emails',
                        'action' => 'home',
                    )),
                )
            );
        }
        ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="aag-title p-bottom-1">
        <?php echo __t('Email.Email') . ': ' . $email['Email']['to'] ?>
    </div>
    <div>
        <b class="fields_views"><?php echo __t('Email.Forward_to') ?>:</b>
        <br>
        <div class="b-bottom-1 height_input c-primary">
            <?php echo h($email['Email']['to']); ?>
        </div>
    </div>
    <div class="cnt-form-inputs p-top-1">
        <div>
            <b class="fields_views"><?php echo __t('Email.Subject') ?>:</b>
            <br>

            <div class="b-bottom-1 height_input">
                <?php echo h($email['Email']['subject']); ?>
            </div>
        </div>
        <div>
            <b class="fields_views"><?php echo __t('Email.Sent') ?>: </b>
            <br>
            <div class="b-bottom-1 height_input">
                <?php echo h(Booleano::toString($email['Email']['sent'])); ?>
            </div>
        </div>
        <div>
            <b class="fields_views"><?php echo __t('Email.Type') ?>: </b>
            <br>
            <div class="b-bottom-1 height_input">
                <?php echo $email['Email']['type']; ?>
            </div>
        </div>
        <div>
            <b class="fields_views"><?php echo __t('Email.Creation_date') ?>: </b>
            <br>
            <div class="b-bottom-1 height_input">
                <?php
                $date = $email['Email']['creation_date'];
                if (isset($user['aag_region_id']) && $user['aag_region_id'] == Configure::read('AAG_REGION_ID_BENELUX')) {
                    $date = date(Fecha::_FORMATO_BD_FECHA_HORA, strtotime($date . ' +1 hours'));
                }
                echo Fecha::toFormatoVistaFechaHora($date);
                ?>
            </div>
        </div>
        <?php if ($email['Email']['sent'] == ConstantsBooleans::NO && $email['Email']['retries'] > 0) { ?>
            <div>
                <b class="fields_views"><?php echo ucfirst(strtolower(__t('Email.Error'))) ?>: </b>
                <br>
                <div class="b-bottom-1 height_input">
                    <?php
                    if(strpos($email['Email']['last_error_message'], 'Sendgrid.Template_id_not_configured') !== false){
                        echo sprintf(__t($email['Email']['last_error_message']), $country['Country']['name']);
                    } else {
                        echo __t($email['Email']['last_error_message']);
                    }
                    ?>
                </div>
            </div>
            <div>
                <div id="file-list-js" class="medium-12 columns end">
                    <?php echo $this->element('../Emails/Elements/form_attached_files'); ?>
                </div>
            </div>
        <?php } ?>
    </div>
    <div>
        <?php
        echo $this->element(
            '../Emails/Elements/sendgrid_email_viewvars',
            array(
                'email_type_id' => $email['Email']['email_type_id']
            )
        );
        ?>
    </div>
</div>
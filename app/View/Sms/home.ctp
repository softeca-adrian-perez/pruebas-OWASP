<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Menu.Sms'),
                array(
                    'controller' => 'sms',
                    'action' => 'list'
                )
            ),
            __t('Sms.Sms_configuration'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back')?></a>
        <?php
        if (!$this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::CREATE_SMS_CONFIGURATION)) {
            echo $this->Form->button(
                "<span class='aag-icon-editar'></span>" . __t('Sms.License_config'),
                array(
                    'id' => 'license-config',
                    'escape' => false,
                    'data-open' => "smsModal",
                    'type' => 'button',
                    'title' => __t('Sms.License_config'),
                    'class' => 'aag-button medium',
                    'data-is_event' => true
                )
            );
        } ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="aag-title">
        <?php echo __t('Sms.Sms_configuration') ?>
    </div>
    <div>
        <iframe id="contenedor" name="contenedor" src="about:blank" style="display:block; width:100%; border:none; height:100vh; margin-bottom:120px;"></iframe>
    </div>
    <?php
    if (!is_null($tokenLogin)) {
    ?>
    <script type="text/javascript">
        //<![CDATA[
        $(document).ready(function(){
            $('#formulario').submit();
        });
    </script>

    <?php
    echo $this->Form->create(
        null,
        array(
            'url' => Configure::read('SMS_URL'),
            'target' => 'contenedor',
            'id' => 'formulario',
        )
    );

    echo $this->Form->hidden(
        '',
        array(
            'name' => 'usuario',
            'id' => 'data',
            'value' => $tokenLogin['Sms']['username_iframe'] ?? '',
        )
    );

    echo $this->Form->hidden(
        '',
        array(
            'name' => 'contrasena',
            'id' => 'data',
            'value' => $tokenLogin['Sms']['password_iframe'] ?? '',
        )
    );

    echo $this->Form->hidden(
        '',
        array(
            'name' => 'licencia',
            'id' => 'data',
            'value' => $tokenLogin['Sms']['license_iframe'] ?? '',
        )
    );

    echo $this->Form->end();
    }

$this->end();
?>
    <div style="display: none;" id="smsModal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
        <div id="modal_form_sms" class="medium-12 columns p-right-0">
            <?php echo $this->element('../Sms/Elements/form_edit_license'); ?>
        </div>
        <a class="close-modal zi-100" data-close aria-label="Close">&#215;</a>
    </div>
</div>
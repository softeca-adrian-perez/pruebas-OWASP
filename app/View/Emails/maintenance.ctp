<?php
echo $this->Html->script('sendgrid.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
$action = $this->request->action;
$param = $this->request->pass;
$platform_id = $param[0];
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Maintenance.Maintenance'),
                array(
                    'controller' => 'maintenance',
                    'action' => 'home'
                )
            ),
            $this->Html->link(
                __t('Email.Emails'),
                array(
                    'controller' => 'emails',
                    'action' => 'maintenance',
                    1
                )
            ),
            __t('Configuration.Configuration'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium one"><?php echo __t('Email.Send_vars'); ?></a>
        <?php
        echo $this->Form->button(
            __t('Sms.New_license'),
            array(
                'id' => 'new-license-config',
                'escape' => false,
                'data-open' => "modal-new-license",
                'type' => 'button',
                'title' => __t('Sms.New_license'),
                'class' => 'aag-button medium green',
                'data-is_event' => true
            )
        );
        ?>
        <a class="aag-button medium two"><?php echo __t('General.Back'); ?></a>
    </div>
</div>
<div class="menu_garage aag-tabs">
    <ul>
        <?php
        $active = '';
        foreach ($platforms as $platform) {
            $active = $platform_id == $platform['Platform']['id'] ? 'active' : '';
        ?>
            <li class="<?php echo $active; ?>">
                <a href="/emails/maintenance/<?php echo $platform['Platform']['id']; ?>"><?php echo $platform['Platform']['name']; ?></a>
            </li>
        <?php } ?>
    </ul>
</div>
<div class="cnt-data fg-0 p-top-1">
    <div class="aag-title cnt-data-element">
        <?php echo __t('Email.Sendgrid_configuration'); ?>
    </div>
    <div class="o-auto">
        <table class="table-tracking table-with-child">
            <?php
            $classOddEven = 'odd';
            foreach ($sendgrid_config as $sendgrid) {
                $sendgrid_license_config_id = $sendgrid['SendgridLicenseConfig']['id'];
            ?>
                <thead class="<?php echo $classOddEven; ?>">
                    <tr>
                        <th><?php echo __t('Menu.Country'); ?></th>
                        <th><?php echo __t('General.Name'); ?></th>
                        <th><?php echo __t('Email.From_email'); ?></th>
                        <th><?php echo __t('Sms.Api_key'); ?></th>
                        <th width="75" class="ta-center"><?php echo __t('General.Actions'); ?></th>
                    </tr>
                </thead>
                <tbody class="<?php echo $classOddEven; ?>">
                    <tr>
                        <td>
                            <div><?php echo h($sendgrid['Country']['name'] ?? ''); ?></div>
                        </td>
                        <td>
                            <div><?php echo h($sendgrid['SendgridLicenseConfig']['name'] ?? ''); ?></div>
                        </td>
                        <td>
                            <div><?php echo h($sendgrid['SendgridLicenseConfig']['from_email']); ?></div>
                        </td>
                        <td>
                            <div><?php echo h(Texto::encryptDecryptText($sendgrid['SendgridLicenseConfig']['api_key']) ?? ''); ?></div>
                        </td>
                        <td>
                            <div style="display: flex; align-items: center; justify-content: center; gap: 15px; min-width: 100px; margin-left: auto;">
                                <span data-open='edit-license-config-<?php echo $sendgrid_license_config_id; ?>' data-is_event="true">
                                    <span class="aag-icon-editar c-primary cursor-pointer m-right-1"></span>
                                </span>
                                <div style="display: none;" id="edit-license-config-<?php echo $sendgrid_license_config_id ?>" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog" data-close-on-click="false">
                                    <div id="modal_form_sendgrid" class="medium-12 columns p-right-0">
                                        <?php echo $this->element('../Emails/Elements/form_edit_license', array('sendgrid' => $sendgrid, 'platform_id' => $platform_id)); ?>
                                    </div>
                                    <a class="close-modal zi-100" data-close aria-label="Close">&#215;</a>
                                </div>
                                <?php
                                echo $this->Html->Link(
                                    '<span class="aag-icon-papelera c-fallo"></span>',
                                    array(),
                                    array(
                                        'escape' => false,
                                        'title' => __t('General.Delete'),
                                        'class' => 'new-delete-js',
                                        'data-url' => Router::url(array(
                                            'controller' => 'emails',
                                            'action' => 'ajax_delete_sengrid_license',
                                            $sendgrid_license_config_id,
                                            $platform_id
                                        )),
                                        'data-url_redirect' => Router::url(array(
                                            'controller' => 'emails',
                                            'action' => 'maintenance',
                                            $platform_id
                                        )),
                                        'data-confirmmsg' => "<b>" . __t('Alert.Delete_license_sendgrid?') . "</b><br><br>" . " " . __t('General.Irreversible_action')
                                    )
                                ); ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <?php echo $this->element(
                            '../Emails/Elements/form_maintenance_template',
                            array(
                                'license_order_id' => $sendgrid['SendgridLicenseConfig']['id'],
                                'platform_id' => $sendgrid['SendgridLicenseConfig']['platform_id'],
                                'country_id' => $sendgrid['SendgridLicenseConfig']['country_id'],
                                'aag_region_id' => $sendgrid['SendgridLicenseConfig']['aag_region_id'],
                                'is_external' => $is_external
                            )
                        ); ?>
                    </tr>
                </tbody>
            <?php
                $classOddEven = $classOddEven == 'odd' ? 'even' : 'odd';
            }
            ?>
        </table>
    </div>
</div>
<div style="display: none;" id="modal-new-license" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog" data-close-on-click="false">
    <div id="modal_form_add_sms" class="medium-12 columns p-right-0">
        <?php echo $this->element('../Emails/Elements/form_add_license', array('platform_id' => $platform_id)); ?>
    </div>
    <a class="close-modal zi-100" data-close aria-label="Close">&#215;</a>
</div>
<script>
    $(document).ready(function() {
        $('.edit-language-js').click(function() {
            $('#modal-edit-languages').foundation('open');
        });
        $('.new-template-js').click(function() {
            $('#modal-new-template').foundation('open');
        });
    });
</script>
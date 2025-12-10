<?php echo $this->Html->script('sms.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script')); ?>
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
            __t('Sms.Sms_list'),
        ));
        ?>
    </div>
    <div>
        <?php
        echo $this->Form->button(
            __t('Sms.New_license'),
            array(
                'id' => 'new-license-config',
                'escape' => false,
                'data-open' => "smsModalAdd",
                'type' => 'button',
                'title' => __t('Sms.New_license'),
                'class' => 'aag-button medium green',
                'data-is_event' => true
            )
        );
        echo $this->Form->button(
            __t('Sms.New_template'),
            array(
                'id' => 'template-config',
                'escape' => false,
                'data-open' => "smsTemplateModal",
                'type' => 'button',
                'title' => __t('Sms.New_template'),
                'class' => 'aag-button medium green',
                'data-is_event' => true
            )
        );
        ?>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back')?></a>
    </div>
</div>
<?php echo $this->element('../Elements/Comun/sms_tabs', array('selected' => 'sms')); ?>
<div class="cnt-data fg-0 p-vertical-1">
    <div class="aag-title cnt-data-element">
        <?php echo __t('Sms.Licenses_management'); ?>
    </div>
    <div class="o-auto">
        <table class="table-tracking">
            <thead>
                <tr>
                    <th width="150"><?php echo __t('Garage.Country'); ?></th>
                    <th><?php echo __t('User.Username'); ?></th>
                    <th class="ta-center"><?php echo __t('General.Actions'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tokenLogin as $sms_license) { ?>
                    <tr>
                        <td>
                            <div><?php echo h($sms_license['Country']['name'] ?? ''); ?></div>
                        </td>
                        <td>
                            <div><?php echo h($sms_license['Sms']['username_iframe']); ?></div>
                        </td>
                        <td style="width: 1px; white-space: nowrap;">
                            <div style="display: flex; align-items: center; justify-content: flex-end; gap: 15px; min-width: 100px; margin-left: auto;">
                            <?php
                            if ($this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::CREATE_SMS_CONFIGURATION)) {
                                echo $this->Form->button(
                                    "<span class='aag-icon-editar'></span>" . __t('Sms.Url_config'),
                                    array(
                                        'id' => 'license-configUrl',
                                        'escape' => false,
                                        'data-open' => "smsModalUrl".$sms_license['Sms']['id'],
                                        'type' => 'button',
                                        'title' => __t('Sms.License_config'),
                                        'class' => 'aag-button medium',
                                        'data-is_eventUrl' => true
                                    )
                                );
                                echo $this->Form->button(
                                    "<span class='aag-icon-editar'></span>" . __t('Sms.License_config'),
                                    array(
                                        'id' => 'license-config',
                                        'escape' => false,
                                        'data-open' => "smsModal".$sms_license['Sms']['id'],
                                        'type' => 'button',
                                        'title' => __t('Sms.License_config'),
                                        'class' => 'aag-button medium',
                                        'data-is_event' => true
                                    )
                                );
                                echo $this->Html->link(
                                    __t('Sms.Sms_configuration'),
                                    array(
                                        'plugin' => false,
                                        'controller' => 'sms',
                                        'action' => 'home',
                                        $sms_license['Sms']['id']
                                    ),
                                    array(
                                        'class' => 'aag-button medium',
                                        'escape' => false
                                    )
                                );
                            }
                            ?>
                            <div style="display: none;" id="<?php echo 'smsModalUrl'.$sms_license['Sms']['id'] ?>" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog" data-close-on-click="false">
                                <div id="modal_form_smsUrl" class="medium-12 columns p-right-0">
                                    <?php
                                    $this->set(array('tokenLogin' => $sms_license));
                                    echo $this->element('../Sms/Elements/form_edit_shortner_url'); ?>
                                </div>
                                <a class="close-modal zi-100" data-close aria-label="Close">&#215;</a>
                            </div>
                            <div style="display: none;" id="<?php echo 'smsModal'.$sms_license['Sms']['id'] ?>" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
                                <div id="modal_form_sms" class="medium-12 columns p-right-0">
                                    <?php
                                    $this->set(array('tokenLogin' => $sms_license));
                                    echo $this->element('../Sms/Elements/form_edit_license'); ?>
                                </div>
                                <a class="close-modal zi-100" data-close aria-label="Close">&#215;</a>
                            </div>
                                <span
                                    class="aag-button medium red outlined delete-license-js"
                                    style="cursor: pointer;"
                                    data-delete-url="
                                    <?php echo Router::url(
                                        array(
                                            'controller' => 'sms',
                                            'action' => 'delete',
                                            $sms_license['Sms']['id'],
                                        )
                                    ); ?>"
                                    data-license-id="<?php echo $sms_license['Sms']['id']; ?>">
                                    <?php echo "<span class='aag-icon-papelera'></span>".__t('General.Delete') ?>
                                </span>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<div style="display: none;" id="smsModalAdd" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
    <div id="modal_form_add_sms" class="medium-12 columns p-right-0">
        <?php echo $this->element('../Sms/Elements/form_add_license'); ?>
    </div>
    <a class="close-modal zi-100" data-close aria-label="Close">&#215;</a>
</div>
<div class="cnt-data fg-0 p-vertical-1">
    <div class="aag-title cnt-data-element">
        <?php echo __t('Sms.Sms_template_management'); ?>
    </div>
    <div class="o-auto">
        <table class="table-tracking">
            <thead>
                <tr>
                    <th width="150"><?php echo __t('Garage.Country'); ?></th>
                    <th><?php echo __t('Sms.Template_type'); ?></th>
                    <th><?php echo __t('General.Description'); ?></th>
                    <th><?php echo __t('Sms.Sender'); ?></th>
                    <th class="ta-center"><?php echo __t('General.Actions'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sms_templates as $sms_template) { ?>
                    <tr>
                        <td>
                            <div><?php echo h($sms_template['Country']['name'] ?? ''); ?></div>
                        </td>
                        <td>
                            <div><?php echo h($sms_template['SmsTemplateType']['name'] ?? ''); ?></div>
                        </td>
                        <td>
                            <div><?php echo h($sms_template['SmsTemplate']['description'] ?? ''); ?></div>
                        </td>
                        <td>
                            <div><?php echo h($sms_template['SmsTemplate']['sender'] ?? ''); ?></div>
                        </td>
                        <td style="width: 1px; white-space: nowrap;">
                            <div style="display: flex; align-items: center; justify-content: flex-end; gap: 15px; min-width: 100px; margin-left: auto;">
                                <?php
                                echo $this->Form->button(
                                    "<span class='aag-icon-editar'></span>" . __t('Sms.Template_config'),
                                    array(
                                        'id' => 'license-config',
                                        'escape' => false,
                                        'data-open' => "smsTemplateModal".$sms_template['SmsTemplate']['id'],
                                        'type' => 'button',
                                        'title' => __t('Sms.License_config'),
                                        'class' => 'aag-button medium edit-template-js',
                                        'data-is_event' => true,
                                        'data-template-id' => $sms_template['SmsTemplate']['id'],
                                    )
                                );
                                ?>
                                <span
                                    class="aag-button medium red outlined delete-template-js"
                                    style="cursor: pointer;"
                                    data-delete-url="
                                    <?php echo Router::url(
                                        array(
                                            'controller' => 'sms',
                                            'action' => 'delete_template',
                                            $sms_template['SmsTemplate']['id'],
                                        )
                                    ); ?>"
                                    data-template-id="<?php echo $sms_template['SmsTemplate']['id']; ?>">
                                    <?php echo "<span class='aag-icon-papelera'></span>".__t('General.Delete') ?>
                                </span>
                            </div>
                            <div style="display: none;" id="<?php echo 'smsTemplateModal'.$sms_template['SmsTemplate']['id'] ?>" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
                                <div id="modal_form_sms_template" class="medium-12 columns p-right-0">
                                    <?php
                                    $this->set(array('sms_templates' => $sms_template));
                                    echo $this->element('../Sms/Elements/form_sms_template'); ?>
                                </div>
                                <a class="close-modal zi-100" data-close aria-label="Close">&#215;</a>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<div style="display: none;" id="smsTemplateModal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
    <div id="modal_form_sms_template" class="medium-12 columns p-right-0">
        <?php
        echo $this->element('../Sms/Elements/form_sms_add_template'); ?>
    </div>
    <a class="close-modal zi-100" data-close aria-label="Close">&#215;</a>
</div>

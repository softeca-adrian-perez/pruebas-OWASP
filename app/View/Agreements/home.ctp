<?php
echo $this->Html->script('markerclusterer.min.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
?>

<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Agreement.Agreements'),
                array(
                    'controller' => 'maintenance',
                    'action' => 'home'
                )
            ),
            __t('Agreement.List')
        ));
        ?>
    </div>
    <div><a class="aag-button medium two go-back-js"><?php echo __t('General.Back')?></a></div>
</div>
    <div class="cnt-data fg-0 p-vertical-1">
        <div class="aag-title cnt-data-element">
            <?php echo __t('Agreement.Internal'); ?>
        </div>
        <div class="o-auto">
            <table class="table-tracking tabla-responsive">
                <thead>
                    <tr>
                        <th><?php echo __t('Agreement.Name'); ?></th>
                        <th><?php echo __t('Agreement.AAGCode'); ?></th>
                        <th><?php echo __t('Agreement.Code'); ?></th>
                        <th><?php echo __t('Agreement.Creation_date'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($internal_agreements as $ext) { ?>
                        <tr>
                            <td><?php echo $ext['GaAcuerdo']['nombre'] ?></td>
                            <td><?php echo $ext['GaAcuerdo']['cod_alliance'] ?></td>
                            <td><?php echo $ext['GaAcuerdo']['cod'] ?></td>
                            <td><?php echo $ext['GaAcuerdo']['fecha_creacion'] ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="cnt-data fg-0 p-vertical-1">
        <div class="flex fw-wrap ai-center cnt-data-element gap-1">
            <div class="aag-title m-right-auto">
                <?php echo __t('Agreement.External'); ?>
            </div>
            <?php if (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CREATE_AGREEMENTS)
            ) {
                echo $this->Html->link(
                    __t('Agreement.New_external_agreement'),
                    array(
                        'controller' => 'agreements',
                        'action' => 'add'
                    ),
                    array(
                        'escape' => false,
                        'class' => 'aag-button green medium m-0'
                    )
                );
            } ?>
        </div>
        <div class="o-auto">
            <table class="table-tracking tabla-responsive">
                <thead>
                    <tr>
                        <th><?php echo __t('Agreement.Name'); ?></th>
                        <th><?php echo __t('Agreement.Code'); ?></th>
                        <th><?php echo __t('Agreement.Creation_date'); ?></th>
                        <?php if ($this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::EDIT_AGREEMENTS)) { ?>
                            <th class="ta-center"><?php echo __t('General.Actions'); ?></th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($external_agreements as $ext) { ?>
                        <tr>
                            <td>
                                <?php if ($this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::EDIT_AGREEMENTS)) {
                                    echo $this->Html->link(
                                        $ext['Agreement']['nombre'],
                                        array(
                                            'controller' => 'agreements',
                                            'action' => 'edit',
                                            $ext['Agreement']['id']
                                        ),
                                        array(
                                            'class' => 'c-primary'
                                        )
                                    );
                                } else {
                                    echo $ext['Agreement']['nombre'];
                                } ?>
                            </td>
                            <td><?php echo $ext['Agreement']['codigo'] ?></td>
                            <td><?php echo date('d-m-Y H:i:s', strtotime($ext['Agreement']['fecha_creacion'])) ?></td>
                            <?php if ($this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::EDIT_AGREEMENTS)) { ?>
                                <td class="ta-center">
                                    <?php echo $this->Html->Link(
                                        '<span class="aag-icon-papelera c-fallo"></span>',
                                        array(),
                                        array(
                                            'escape' => false,
                                            'title' => __t('General.Delete'),
                                            'class' => 'delete-js',
                                            'data-url' => Router::url(array(
                                                'controller' => 'agreements',
                                                'action' => 'ajax_delete',
                                                $ext['Agreement']['id']
                                            )),
                                            'data-url_redirect' => Router::url(array(
                                                'controller' => 'agreements',
                                                'action' => 'home',
                                            )),
                                            'data-confirmmsg' => __t('Agreement.Delete_agreement?'),
                                            'data-msg_correct' => __t('Agreement.Correct_deleted'),
                                            'data-msg_bad' => __t('Constants.Message_bad_deleted'),
                                        )
                                    ); ?>
                                </td>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php echo $this->Html->script('alerts.js', array('block' => 'script')); ?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Alert.Alerts'),
                array(
                    'controller' => 'alerts',
                    'action' => 'home'
                )
            ),
            __t('General.Home'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back')?></a>
    </div>
</div>
<div class="d-none" id="confirm-uncheck" data-msg="<?php echo __t('Alert.Confirm_unchecked');?>"
     data-url="<?php echo Router::url(
         array(
             'controller' => 'alerts',
             'action' => 'ajax_uncheck_alert'
         )
     );?>">
</div>
<div class="d-none" id="confirm-check" data-msg="<?php echo __t('Alert.Confirm_checked');?>"
     data-url="<?php echo Router::url(
         array(
             'controller' => 'alerts',
             'action' => 'ajax_check_alert'
         )
     );?>">
</div>
<div class="cnt-data">
    <?php echo $this->element('../Alerts/Elements/search'); ?>
    <div class="cnt-data-element ta-right">
        <?php
        echo $this->Html->link(
            __t('Alert.Mark_all_as_read'),
            array(),
            array('class' => 'read-all-messages aag-button green outlined medium')
        );
        ?>

        <?php
        echo $this->Html->link(
            __t('Alert.Mark_all_as_unread'),
            array(),
            array('class' => 'unread-all-messages aag-button two outlined medium')
        );
        ?>
    </div>
    <div class="o-auto m-top-1">
        <table class="table-tracking">
            <thead>
            <tr>
                <th><?php echo $this->Paginator->sort('Alert.creation_date', __t('Alert.Date')); ?></th>
                <th><?php echo $this->Paginator->sort('Alert.body', __t('Alert.Text')); ?></th>
                <th class="ta-center"><?php echo __t('Appointment.Feedback'); ?></th>
                <th class="ta-center"><?php echo $this->Paginator->sort('Alert.alert_type_id', __t('Alert.Type')); ?></th>
                <th class="ta-center" width="100"><?php echo $this->Paginator->sort('Alert.read', __t('Alert.Read')); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($alerts as $alert) { ?>
                <tr class="<?php echo $alert['Alert']['read'] ? 'read' : 'unread';?>"
                data-url="<?php echo $alert['Alert']['url']?>"
                data-alert-id="<?php echo $alert['Alert']['id']?>"
                >
                    <td>
                        <?php echo Fecha::toFormatoVistaFecha(h($alert['Alert']['creation_date'])); ?>
                    </td>
                    <td>
                        <?php
                        if(empty($alert['Alert']['url'])){
                            echo h($alert['Alert']['body']);
                        } else {
                            echo $this->Html->link(
                                $alert['Alert']['body'],
                                $alert['Alert']['url'],
                                array(
                                    'class' => 'c-primary link-alert-js'
                                )
                            );
                        }
                        ?>
                    </td>
                    <td class="ta-center">
                        <?php
                        if( $alert['Alert']['feedback'] ){
                            echo $this->Html->link(
                                '<span class="aag-icon-ojo c-exito"></span>',
                                'javascript:void(0)',
                                array(
                                    'escape' => false,
                                    'title' => __t('Appointment.Feedback'),
                                    'data-open' => "feedbackModal".$alert['Alert']['id'],
                                )
                            );
                        ?>
                            <div style="display: none;" id="feedbackModal<?php echo $alert['Alert']['id']; ?>" class="reveal-modal background-color-primary" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog" >
                                <div class="columns medium-12">
                                    <h1><?php echo __t('Appointment.Feedback');?></h1>
                                    <div class="medium-12 columns p-0 m-top-1 m-bottom-1" style="border: 1px solid gray;padding: 5px;border-radius: 5px;">
                                        <?php echo $alert['Alert']['creation_date'].' / '; ?>
                                        <?php echo $alert['Alert']['body']; ?>
                                    </div>
                                    <div class="medium-12 columns p-0 m-top-1 m-bottom-1">
                                        <?php echo $alert['Alert']['feedback']; ?>
                                    </div>
                                </div>
                                <a class="close-modal" data-close aria-label="Close">&#215;</a>
                            </div>
                        <?php
                        }
                        ?>
                    </td>
                    <td class="ta-center">
                        <?php echo h($types[$alert['Alert']['alert_type_id']]); ?>
                    </td>
                    <td class="ta-center">
                        <?php
                        //Resolve icons
                        if (!($alert['Alert']['read'])) {
                            echo $this->Html->link(
                                '<span class="icon-tick_off c-fallo"></span>',
                                array(
                                    'controller' => 'alerts',
                                    'action' => 'ajax_check_alert',
                                    $alert['Alert']['id']
                                ),
                                array(
                                    'escape' => false,
                                    'class' => 'check-alert-js',
                                    'title' => __t('Alert.Confirm_checked'),
                                    'data-confirmmsg' => __t('Alert.Confirm_checked'),
                                    'data-id' => $alert['Alert']['id'],
                                    'data-yes' => __t('General.Yes'),
                                    'data-no' => __t('General.No')
                                )
                            );
                        } else {
                            echo $this->Html->link(
                                '<span class="icon-tick_on c-exito"></span>',
                                array(
                                    'controller' => 'alerts',
                                    'action' => 'ajax_uncheck_alert',
                                    $alert['Alert']['id']
                                ),
                                array(
                                    'escape' => false,
                                    'class' => 'uncheck-alert-js',
                                    'title' => __t('Alert.Confirm_unchecked'),
                                    'data-id' => $alert['Alert']['id'],
                                    'data-confirmmsg' => __t('Alert.Confirm_unchecked'),
                                    'data-yes' => __t('General.Yes'),
                                    'data-no' => __t('General.No')
                                )
                            );
                            ?>
                            <?php
                        }
                        ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <?php echo $this->element('Comun/paginacion'); ?>
</div>

<?php echo $this->Html->script('mailbox.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script')); ?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Mailbox.Mailbox'),
                array(
                    'controller' => 'messages',
                    'action' => 'recieved_messages'
                )
            ),
            __t('Message.Recieved_messages'),
        ));
        ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <?php echo $this->element('../Messages/Elements/search_mine'); ?>
    <div class="o-auto m-top-1">
        <table class="table-tracking">
            <thead>
            <tr>
                <th><?php echo $this->Paginator->sort('Message.date_sent', __t('Message.Sent_date')); ?></th>
                <th><?php echo $this->Paginator->sort('Message.subject', __t('Message.Subject')); ?></th>
                <th><?php echo __t('Message.Body'); ?></th>
                <th><?php echo __t('Message.Read'); ?></th>
                <th class="ta-center"><?php echo __t('General.Actions'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($messages as $message) { ?>
                <tr>
                    <td>
                        <?php
                        if($message['Message']['date_sent']){
                            echo Fecha::toFormatoVistaFecha(h($message['Message']['date_sent']));
                        }else{
                            echo __t('Message.Not_sent');
                        }
                        ?>
                    </td>
                    <td>
                        <?php echo h($message['Message']['subject']); ?>
                    </td>
                    <td>
                        <?php echo h($message['Message']['body']); ?>
                    </td>
                    <td>
                        <?php
                            if($role == ConstantsRoles::GARAGE){
                                if($message['MessageGarage']['date_read']){
                                    echo __t('General.Yes');
                                }else{
                                    echo __t('General.No');
                                }
                            }elseif($role == ConstantsRoles::DISTRIBUTOR){
                                if($message['MessageDistributor']['date_read']){
                                    echo __t('General.Yes');
                                }else{
                                    echo __t('General.No');
                                }
                            }
                        ?>
                    </td>
                    <td class="ta-center">
                        <?php
                        echo $this->Html->link(
                            '<span class="cursor-pointer aag-icon-ojo c-primary"></span>',
                            array(
                                'controller' => 'messages',
                                'action' => 'view',
                                $message['Message']['id']
                            ),
                            array(
                                'escape' => false,
                                'title' => __t('General.View'),
                                'class' => 'm-right-1'
                            )
                        );
                        ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <br/>
    <?php echo $this->element('Comun/paginacion'); ?>
</div>

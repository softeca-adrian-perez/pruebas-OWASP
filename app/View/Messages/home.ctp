<?php echo $this->Html->script('mailbox.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script')); ?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Mailbox.Mailbox'),
                array(
                    'controller' => 'messages',
                    'action' => 'home'
                )
            ),
            __t('General.Home'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back')?></a>
        <?php
        echo $this->Html->link(
            __t('Message.To_garages'),
            array(
                'controller' => 'messages',
                'action' => 'new_garages'
            ),
            array('class' => 'aag-button medium green')
        );
        echo $this->Html->link(
            __t('Message.To_distributors'),
            array(
                'controller' => 'messages',
                'action' => 'new_distributors'
            ),
            array('class' => 'aag-button medium green')
        );
        ?>
    </div>
</div>



<div class="cnt-data">
    <?php echo $this->element('../Messages/Elements/search'); ?>
    <div class="o-auto">
        <table class="table-tracking">
            <thead>
            <tr>
                <th><?php echo $this->Paginator->sort('Message.creation_date', __t('Message.Creation_date')); ?></th>
                <th><?php echo $this->Paginator->sort('Message.subject', __t('Message.Subject')); ?></th>
                <th><?php echo __t('Message.Body'); ?></th>
                <th><?php echo __t('Message.Type'); ?></th>
                <th><?php echo $this->Paginator->sort('Message.date_sent', __t('Message.Sent_date')); ?></th>
                <th class="ta-center"><?php echo __t('General.Actions'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($messages as $message) { ?>
                <tr>
                    <td>
                        <?php echo Fecha::toFormatoVistaFecha(h($message['Message']['creation_date'])); ?>
                    </td>
                    <td>
                        <?php echo h($message['Message']['subject']); ?>
                    </td>
                    <td>
                        <?php echo h($message['Message']['body']); ?>
                    </td>
                    <td>
                        <?php echo h($types[$message['Message']['type']]); ?>
                    </td>
                    <td>
                        <?php
                        if($message['Message']['date_sent']){
                            echo Fecha::toFormatoVistaFecha(h($message['Message']['date_sent']));
                        }else{
                            echo __t('Message.Not_sent');
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
                        if(is_null($message['Message']['date_sent']) && $message['Message']['user_id'] == $this->Acceso->user('id')){
                            $action = $message['Message']['type'] == ConstantsMessagesTypes::GARAGE?'edit_garages':'edit_distributors';
                            echo $this->Html->link(
                                '<span class="cursor-pointer aag-icon-editar c-primary"></span>',
                                array(
                                    'controller' => 'messages',
                                    'action' => $action,
                                    $message['Message']['id']
                                ),
                                array(
                                    'escape' => false,
                                    'title' => __t('General.Edit'),
                                    'class' => 'm-right-1'
                                )
                            );
                            echo $this->Html->link(
                                '<span class="cursor-pointer aag-icon-papelera c-fallo"></span>',
                                array(
                                    'controller' => 'messages',
                                    'action' => 'delete_message',
                                    $message['Message']['id']
                                ),
                                array(
                                    'data-form' => 'message',
                                    'class' => 'swal-msg',
                                    'data-confirmmsg' => __t('Message.Delete?'),
                                    'data-yes' => __t('General.Yes'),
                                    'data-no' => __t('General.No'),
                                    'data-type' => 'warning',
                                    'data-url' => Router::url(array(
                                        'controller' => 'messages',
                                        'action' => 'delete_message',
                                        $message['Message']['id']
                                    )),
                                    'escape' => false,
                                    'title' => __t('General.Delete'),
                                )
                            );
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

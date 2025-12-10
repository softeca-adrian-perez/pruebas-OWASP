<div class="medium-12 columns">
    <div class="o-auto">
        <table class="table-tracking">
            <thead>
                <tr>
                    <th><?php echo $this->Paginator->sort('Email.to', __t('Email.Forward_to')); ?></th>
                    <th><?php echo $this->Paginator->sort('Email.subject', __t('Email.Subject')); ?></th>
                    <th class="ta-center"><?php echo __t('Email.Attachments'); ?></th>
                    <th class="ta-center"><?php echo __t('Email.Type'); ?></th>
                    <th class="ta-center"><?php echo __t('General.Platform'); ?></th>
                    <th class="ta-center"><?php echo $this->Paginator->sort('Email.sent', __t('Email.Sent')); ?></th>
                    <th class="ta-center"><?php echo $this->Paginator->sort('Email.creation_date', __t('Email.Creation_date')); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($emails as $email) { ?>
                    <tr>
                        <td class="c-defecto">
                            <?php echo h($email['Email']['to']); ?>
                        </td>
                        <td class="c-defecto">
                            <?php echo $this->Html->link(
                                $email['Email']['subject'],
                                array(
                                    'controller' => 'emails',
                                    'action' => 'view',
                                    $email['Email']['id']
                                ),
                                array(
                                    'class' => 'c-primary'
                                )
                            ); ?>
                        </td>
                        <td class="c-defecto fw-bold ta-center">
                            <?php
                            if (!isset($email['Attachments']['Event_action']) && !empty($email['Email']['files'])) { ?>
                                <span class="ion-paperclip c-informacion hover-c-primary"></span>
                            <?php } ?>
                        </td>
                        <td class="ta-center">
                            <?php echo $email['Email']['type']; ?>
                        </td>
                        <td class="ta-center">
                            <?php echo $email['Email']['platform']; ?>
                        </td>
                        <?php if ($email['Email']['sent'] == ConstantsBooleans::YES) { ?>
                            <td class="c-exito ta-center">
                                <?php echo __t('General.Sent'); ?>
                            </td>
                        <?php
                        } elseif ($email['Email']['sent'] == ConstantsBooleans::NO && $email['Email']['retries'] == ConstantsBooleans::NO) { ?>
                            <td class="c-informacion ta-center">
                                <?php echo __t('General.Pending'); ?>
                            </td>
                        <?php
                        } else { ?>
                            <td class="c-fallo ta-center">
                                <?php echo __t('General.Fail'); ?>
                            </td>
                        <?php } ?>
                        <td class="ta-center">
                            <?php echo ($email['Email']['creation_date'] != '0000-00-00') ? Fecha::toFormatoVistaFecha($email['Email']['creation_date']) : ''; ?>
                        </td>
                    <?php } ?>
            </tbody>
        </table>
    </div>
</div>
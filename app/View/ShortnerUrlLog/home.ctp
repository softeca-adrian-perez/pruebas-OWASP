<?php
$user = $this->Acceso->user();
?>
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
            __t('Sms.Url_shortner_configuration'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back') ?></a>
    </div>
</div>
<?php echo $this->element('../Elements/Comun/sms_tabs', array('selected' => 'shortner_url_log')); ?>
<div class="cnt-data" style="padding-top: 1rem">
    <?php echo $this->element('../ShortnerUrlLog/Elements/search_shortner_url_log'); ?>
    <div class="o-auto">
        <table id="conferences_table" class="table-tracking">
            <thead>
                <tr>
                    <th><?php echo $this->Paginator->sort('ShortnerUrlLog.long_url', __t('Sms.Long_url')); ?></th>
                    <th><?php echo $this->Paginator->sort('ShortnerUrlLog.short_url', __t('Sms.Short_url')); ?></th>
                    <th><?php echo $this->Paginator->sort('ShortnerUrlLog.domain', __t('Sms.Domain')); ?></th>
                    <th><?php echo $this->Paginator->sort('ShortnerUrlLog.short_id', __t('Sms.Short_id')); ?></th>
                    <th><?php echo $this->Paginator->sort('ShortnerUrlLog.expire_days', __t('Sms.Expire_days')); ?></th>
                    <th><?php echo $this->Paginator->sort('ShortnerUrlLog.expire_at_datetime', __t('Sms.Expire_at_datetime')); ?></th>
                    <th><?php echo $this->Paginator->sort('ShortnerUrlLog.expire_at_views', __t('Sms.Expire_at_views')); ?></th>
                    <th><?php echo $this->Paginator->sort('ShortnerUrlLog.status', __t('Sms.Status')); ?></th>
                    <th><?php echo $this->Paginator->sort('ShortnerUrlLog.status_code', __t('Sms.Status_code')); ?></th>
                    <th><?php echo $this->Paginator->sort('ShortnerUrlLog.creation_date', __t('Sms.Creation_date')); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($shortner_url_logs as $shortner_url_log) { ?>
                    <tr>
                        <td><?php echo $shortner_url_log['ShortnerUrlLog']['long_url']; ?></td>
                        <td><?php echo $shortner_url_log['ShortnerUrlLog']['short_url']; ?></td>
                        <td><?php echo $shortner_url_log['ShortnerUrlLog']['domain']; ?></td>
                        <td><?php echo $shortner_url_log['ShortnerUrlLog']['short_id']; ?></td>
                        <td><?php echo $shortner_url_log['ShortnerUrlLog']['expire_days']; ?></td>
                        <td><?php echo Fecha::toFormatoVistaFechaHora($shortner_url_log['ShortnerUrlLog']['expire_at_datetime']); ?></td>
                        <td><?php echo $shortner_url_log['ShortnerUrlLog']['expire_at_views']; ?></td>
                        <td><?php echo $shortner_url_log['ShortnerUrlLog']['status'] == ConstantsBooleans::YES ? __t('General.Ok') : __t('General.Ko'); ?></td>
                        <td><?php echo $shortner_url_log['ShortnerUrlLog']['status_code']; ?></td>
                        <td>
                            <?php
                            $date = $shortner_url_log['ShortnerUrlLog']['creation_date'];
                            if ($user['aag_region_id'] == Configure::read('AAG_REGION_ID_BENELUX')) {
                                $date = date(Fecha::_FORMATO_BD_FECHA_HORA, strtotime($date . ' +1 hours'));
                            }
                            echo Fecha::toFormatoVistaFechaHora($date);
                            ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php echo $this->element('Comun/paginacion'); ?>
</div>
<?php $config = CakeSession::read('Auth.User.Config'); ?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Distributor.Distributors'),
                array(
                    'controller' => 'distributors',
                    'action' => 'home'
                )
            ),
            $this->Html->link(
                __t('Maintenance.Software'),
                array(
                    'controller' => 'distributors',
                    'action' => 'add_software',
                    $distributor_id
                )
            ),
            __t('General.View'),
        ));
        ?>
    </div>
    <div>
        <?php
        echo $this->Html->link(
            __t('General.Back'),
            array(
                'controller' => 'distributors',
                'action' => 'add_software',
                $distributor_software['DistributorSoftware']['distributor_id']
            ),
            array(
                'class' => 'aag-button medium four',
            )
        );
        echo $this->Html->link(
            '<span class="aag-icon-editar"></span> ' .
                __t('General.Edit'),
            array(
                'controller' => 'distributors_software',
                'action' => 'edit',
                $distributor_software['DistributorSoftware']['id']
            ),
            array(
                'escape' => false,
                'title' => __t('General.Edit'),
                'class' => 'aag-button medium',
            )
        );
        echo $this->Html->link(
            '<span class="aag-icon-papelera"></span>' .
                __t('General.Delete'),
            array(),
            array(
                'escape' => false,
                'title' => __t('General.Delete'),
                'class' => 'aag-button medium red outlined swal-msg',
                'data-confirmmsg' => __t('Maintenance.Software_delete?'),
                'data-yes' => __t('General.Yes'),
                'data-no' => __t('General.No'),
                'data-type' => 'warning',
                'data-url' => Router::url(array(
                    'controller' => 'distributors_software',
                    'action' => 'delete',
                    $distributor_software['DistributorSoftware']['distributor_id'],
                    $distributor_software['DistributorSoftware']['id'],
                )),
            )
        );
        ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="aag-title">
        <?php echo __t('Maintenance.Software_view'); ?>
    </div>
    <div class="cnt-form-inputs p-top-1">
        <div>
            <strong><?php echo __t('Maintenance.Software') ?>: </strong><br /><?php echo h($software[$distributor_software['DistributorSoftware']['software_id']]); ?>
        </div>
        <div>
            <strong><?php echo __t('Software.Type') ?>: </strong><br />
            <?php echo $distributor_software['DistributorSoftware']['software_type_id'] ? h($software_types[$distributor_software['DistributorSoftware']['software_type_id']]) : ''; ?>
        </div>
        <div>
            <strong><?php echo __t('Software.Supplier') ?>: </strong><br />
            <?php echo $distributor_software['DistributorSoftware']['supplier_id'] ? h($suppliers[$distributor_software['DistributorSoftware']['supplier_id']]) : ''; ?>
        </div>
        <div>
            <strong><?php echo __t('Software.Manufacturer') ?>: </strong><br />
            <?php echo $distributor_software['DistributorSoftware']['software_manufacture_id'] ? h($software_manufactures[$distributor_software['DistributorSoftware']['software_manufacture_id']]) : ''; ?>
        </div>
        <div>
            <strong><?php echo __t('Network.Start_date') ?>: </strong><br /><?php echo h(Fecha::toFormatoVista($distributor_software['DistributorSoftware']['start_date'])); ?>
        </div>
        <div>
            <strong><?php echo __t('Network.End_date') ?>: </strong><br /><?php echo h(Fecha::toFormatoVista($distributor_software['DistributorSoftware']['end_date'])); ?>
        </div>
        <div>
            <strong><?php echo __t('Maintenance.Version') ?>: </strong><br /><?php echo h($distributor_software['DistributorSoftware']['version']); ?>
        </div>
        <?php if ($config[ConstantsConfig::SOFTWARE_USER_PASSWORD_DISTRIBUTOR]) { ?>
            <div>
                <strong><?php echo __t('Maintenance.Username') ?>: </strong><br /><?php echo h($distributor_software['DistributorSoftware']['username']); ?>
            </div>
            <div>
                <strong><?php echo __t('Maintenance.Password') ?>: </strong><br /><?php echo h($distributor_software['DistributorSoftware']['password']); ?>
            </div>
        <?php } ?>
    </div>
</div>
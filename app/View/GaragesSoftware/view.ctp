<?php $config = CakeSession::read('Auth.User.Config'); ?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Garage.Garages'),
                array(
                    'controller' => 'garages',
                    'action' => 'home'
                )
            ),
            $this->Html->link(
                __t('Software.Software'),
                array(
                    'controller' => 'garages',
                    'action' => 'add_equipment_and_software_garage',
                    $garage_id
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
                'controller' => 'garages',
                'action' => 'add_equipment_and_software_garage',
                $garage_software['GarageSoftware']['garage_id']
            ),
            array(
                'class' => 'aag-button medium four',
                'style' => 'margin-top:0 !important;'
            )
        );
        echo $this->Html->link(
            __t('General.Edit'),
            array(
                'controller' => 'garages_software',
                'action' => 'edit',
                $garage_software['GarageSoftware']['id']
            ),
            array(
                'escape' => false,
                'class' => 'aag-button medium',
                'style' => 'margin-top:0 !important;'
            )
        ); ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="aag-title">
        <?php echo __t('Maintenance.Software_view'); ?>
    </div>
    <div class="cnt-form-inputs p-top-1">
        <div>
            <strong><?php echo __t('Maintenance.Software') ?>:</strong>
            <br>

            <div class="b-bottom-1 height_input">
                <?php echo h($software[$garage_software['GarageSoftware']['software_id']]);?>
            </div>
        </div>
        <div>
            <strong><?php echo __t('Software.Type') ?>:</strong>
            <br>

            <div class="b-bottom-1 height_input">
                <?php echo $garage_software['GarageSoftware']['software_type_id'] ? h($software_types[$garage_software['GarageSoftware']['software_type_id']]) : '';?>
            </div>
        </div>
        <div>
            <strong><?php echo __t('Software.Supplier') ?>:</strong>
            <br>

            <div class="b-bottom-1 height_input">
                <?php echo $garage_software['GarageSoftware']['supplier_id'] ? h($suppliers[$garage_software['GarageSoftware']['supplier_id']]) : ''; ?>
            </div>
        </div>
        <div>
            <strong><?php echo __t('Software.Manufacturer') ?>:</strong>
            <br>

            <div class="b-bottom-1 height_input">
                <?php echo $garage_software['GarageSoftware']['software_manufacture_id'] ? h($software_manufactures[$garage_software['GarageSoftware']['software_manufacture_id']]) : ''; ?>
            </div>
        </div>
        <div>
            <strong><?php echo __t('General.Billing_schedule') ?>:</strong>
            <br>

            <div class="b-bottom-1 height_input">
                <?php echo $garage_software['GarageSoftware']['billing_schedule_id'] ? h($billing_schedule[$garage_software['GarageSoftware']['billing_schedule_id']]) : ''; ?>
            </div>
        </div>
        <div>
            <strong><?php echo __t('Maintenance.Version') ?>:</strong>
            <br>

            <div class="b-bottom-1 height_input">
                <?php echo h($garage_software['GarageSoftware']['version']); ?>
            </div>
        </div>
        <div>
            <strong>
                <?php
                    $msg = isset($country['Country']['symbol']) ? __t('General.Amount') . ' ' . $country['Country']['symbol'] : __t('General.Amount');
                    echo $msg;
                ?>:
            </strong>
            <br>

            <div class="b-bottom-1 height_input">
                <?php echo $garage_software['GarageSoftware']['amount'] ? h($garage_software['GarageSoftware']['amount']) : ''; ?>
            </div>
        </div>
        <div>
            <strong><?php echo __t('General.Member_pay') ?>:</strong>
            <br>

            <div class="b-bottom-1 height_input">
                <?php echo $garage_software['GarageSoftware']['member_pay'] ? h($garage_software['GarageSoftware']['member_pay']) : ''; ?>
            </div>
        </div>
        <div>
            <strong><?php echo __t('General.Garage_pay') ?>:</strong>
            <br>

            <div class="b-bottom-1 height_input">
                <?php echo $garage_software['GarageSoftware']['garage_pay'] ? h($garage_software['GarageSoftware']['garage_pay']) : ''; ?>
            </div>
        </div>
        <?php if($config[ConstantsConfig::SOFTWARE_USER_GARAGE]){ ?>
            <div>
                <strong><?php echo __t('Maintenance.Username') ?>:</strong>
                <br>

                <div class="b-bottom-1 height_input">
                    <?php echo h($garage_software['GarageSoftware']['username']); ?>
                </div>
            </div>
        <?php } ?>
        <?php if($config[ConstantsConfig::SOFTWARE_PASSWORD_GARAGE]){ ?>
            <div>
                <strong><?php echo __t('Maintenance.Password') ?>:</strong>
                <br>

                <div class="b-bottom-1 height_input">
                    <?php echo h($garage_software['GarageSoftware']['password']); ?>
                </div>
            </div>
        <?php } ?>
        <div>
            <strong><?php echo __t('General.Number_subscription') ?>:</strong>
            <br>

            <div class="b-bottom-1 height_input">
                <?php echo $garage_software['GarageSoftware']['number_subscription'] ? h($garage_software['GarageSoftware']['number_subscription']) : 0; ?>
            </div>
        </div>
        <div>
            <strong><?php echo __t('General.From') ?>:</strong>
            <br>

            <div class="b-bottom-1 height_input">
                <?php echo $garage_software['GarageSoftware']['start_date'] ? Fecha::toFormatoVista($garage_software['GarageSoftware']['start_date']) : ''; ?>
            </div>
        </div>
        <div>
            <strong><?php echo __t('General.To') ?>:</strong>
            <br>

            <div class="b-bottom-1 height_input">
                <?php echo $garage_software['GarageSoftware']['end_date'] ? Fecha::toFormatoVista($garage_software['GarageSoftware']['end_date']) : ''; ?>
            </div>
        </div>
        <div>
            <strong><?php echo __t('General.Billed_by_aag') ?>:</strong>
            <br>

            <div class="b-bottom-1 height_input">
                <?php echo Booleano::toString($garage_software['GarageSoftware']['billed_by_aag']); ?>
            </div>
        </div>
        <div>
            <strong><?php echo __t('General.Online_ordering') ?>:</strong>
            <br>

            <div class="b-bottom-1 height_input">
                <?php echo Booleano::toString($garage_software['GarageSoftware']['online_ordering']); ?>
            </div>
        </div>
    </div>
</div>
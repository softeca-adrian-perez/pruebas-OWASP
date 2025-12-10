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
                __t('Equipment.Equipment'),
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
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back') ?></a>
        <?php
        echo $this->Html->link(
            __t('General.Edit'),
            array(
                'controller' => 'garages_equipments',
                'action' => 'edit',
                $garage_equipment['GarageEquipment']['id']
            ),
            array(
                'escape' => false,
                'class' => 'aag-button medium',
                'style' => 'margin-top:0 !important;'
            )
        );
        ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="aag-title">
        <?php echo __t('Equipment.Equipment_view'); ?>
    </div>
    <div class="cnt-form-inputs p-top-1">
        <div>
            <strong><?php echo __t('Equipment.Equipment') ?>:</strong>
            <br>

            <div class="b-bottom-1 height_input">
                <?php echo h($equipments[$garage_equipment['GarageEquipment']['equipment_id']]); ?>
            </div>
        </div>
        <div>
            <strong><?php echo __t('Equipment.Type') ?>:</strong>
            <br>

            <div class="b-bottom-1 height_input">
                <?php echo $garage_equipment['GarageEquipment']['equipment_type_id'] ? h($equipment_types[$garage_equipment['GarageEquipment']['equipment_type_id']]) : ''; ?>
            </div>
        </div>
        <div>
            <strong><?php echo __t('Equipment.Supplier') ?>:</strong>
            <br>

            <div class="b-bottom-1 height_input">
                <?php echo $garage_equipment['GarageEquipment']['supplier_id'] ? h($suppliers[$garage_equipment['GarageEquipment']['supplier_id']]) : ''; ?>
            </div>
        </div>
        <div>
            <strong><?php echo __t('Equipment.Brand') ?>:</strong>
            <br>

            <div class="b-bottom-1 height_input">
                <?php echo $garage_equipment['GarageEquipment']['brand_id'] ? h($brands[$garage_equipment['GarageEquipment']['brand_id']]) : ''; ?>
            </div>
        </div>
        <div>
            <strong><?php echo __t('General.Billing_schedule') ?>:</strong>
            <br>

            <div class="b-bottom-1 height_input">
                <?php echo $garage_equipment['GarageEquipment']['billing_schedule_id'] ? h($billing_schedule[$garage_equipment['GarageEquipment']['billing_schedule_id']]) : ''; ?>
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
                <?php echo h($garage_equipment['GarageEquipment']['amount']); ?>
            </div>
        </div>
        <div>
            <strong><?php echo __t('General.Member_pay') ?>:</strong>
            <br>

            <div class="b-bottom-1 height_input">
                <?php echo h($garage_equipment['GarageEquipment']['member_pay']); ?>
            </div>
        </div>
        <div>
            <strong><?php echo __t('General.Garage_pay') ?>:</strong>
            <br>

            <div class="b-bottom-1 height_input">
                <?php echo h($garage_equipment['GarageEquipment']['garage_pay']); ?>
            </div>
        </div>
        <div>
            <strong><?php echo __t('General.From') ?>:</strong>
            <br>

            <div class="b-bottom-1 height_input">
                <?php echo Fecha::toFormatoVista($garage_equipment['GarageEquipment']['start_date']); ?>
            </div>
        </div>
        <div>
            <strong><?php echo __t('General.To') ?>:</strong>
            <br>

            <div class="b-bottom-1 height_input">
                <?php echo Fecha::toFormatoVista($garage_equipment['GarageEquipment']['end_date']); ?>
            </div>
        </div>
        <div>
            <strong><?php echo __t('General.Billed_by_aag') ?>:</strong>
            <br>

            <div class="b-bottom-1 height_input">
                <?php echo Booleano::toString($garage_equipment['GarageEquipment']['billed_by_aag']); ?>
            </div>
        </div>
    </div>
</div>
<?php
echo $this->Html->script('gd_export_excel.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('jquery.fileDownload.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));

$user = $this->Acceso->user();
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Training.Training'),
                array(
                    'controller' => 'trainings_providers',
                    'action' => 'home'
                )
            ),
            __t('Training.Trainings_credits'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back') ?></a>
        <?php
        echo $this->Form->button(
            __t('Training.Trainings_credits_movements_export'),
            array(
                'class' => 'aag-button medium gd-export-credits-movements-js',
                'value' => 'submit',
                'escape' => false,
                'name' => 'export',
                'data-url' => Router::url(
                    array(
                        'controller' => 'trainings_credits_movements',
                        'action' => 'training_credits_movements_excel',
                    )
                ),
            )
        );
        ?>
    </div>
</div>
<?php echo $this->element('../Elements/Comun/trainings_general_tab', array('selected' => 'credits',)); ?>
<div class="cnt-data">
    <div class="aag-padding">
        <?php echo $this->element('../Elements/Comun/trainings_credit_tab', array('selected' => 'credits_movements',)); ?>
    </div>
    <?php echo $this->element('../TrainingsCreditsMovements/Elements/search_training_credit_movement'); ?>
    <div class="cnt-form-inputs aag-padding">
        <div>
            <span><?php echo $this->Html->image(FileManager::get_url(FilePaths::ICONS_IMAGES_RELATIVE . 'cr.svg')); ?></span>
            <p class="color-blue-text"><?php echo __t('Training.Credit_equivalences') ?></p>
            <h2 class="color-blue-text"><b class="color-blue-text"><?php echo $price_credits['TrainingCredit']['pound'] . '£'; ?></b><?php echo ' = ' . $price_credits['TrainingCredit']['credit'] . ' ' . $this->Html->image(FileManager::get_url(FilePaths::ICONS_IMAGES_RELATIVE . 'cr.svg')); ?></h2>
        </div>
    </div>
    <div class="o-auto">
        <table id="training_table" class="table-tracking">
            <thead>
                <tr>
                    <th><?php echo $this->Paginator->sort('Garage.name', __t('Training.Garage_name')); ?></th>
                    <th><?php echo __t('Training.Network'); ?></th>
                    <th><?php echo $this->Paginator->sort('TrainingCreditMovement.creation_date', __t('Training.Changed_date')); ?></th>
                    <th><?php echo __t('Training.Credits'); ?></th>
                    <th><?php echo $this->Paginator->sort('TrainingCreditMovement.description', __t('Training.Description')); ?></th>
                    <th><?php echo $this->Paginator->sort('TrainingPlannedCourse.date_from', __t('Training.Course_date')); ?></th>
                    <th><?php echo __t('Delegate.Delegate_name'); ?></th>
                    <th><?php echo $this->Paginator->sort('TrainingDelegate.cancelled', __t('CRM.Cancelled')); ?></th>
                    <th><?php echo $this->Paginator->sort('TrainingDelegate.order_number', __t('Training.Purchase_order_number')); ?></th>
                    <th><?php echo $this->Paginator->sort('TrainingAllowance.is_actual', __t('Allowance.Actual_allowance')); ?></th>
                    <th class="ta-center"><?php echo __t('General.Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($garages_networks as $garage_network) { ?>
                    <tr>
                        <td>
                            <strong>
                                <?php echo $this->Html->link(
                                    '<span>' . $garage_network['Garage']['name'] . '</span>',
                                    array(
                                        'controller' => 'garages',
                                        'action' => 'edit',
                                        $garage_network['Garage']['id'],
                                    ),
                                    array(
                                        'escape' => false,
                                    )
                                );
                                ?>
                            </strong>
                        </td>
                        <td class="color-blue-text"><b><?php echo $network_list[$garage_network['GarageNetwork']['network_id']]; ?></b></td>
                        <td>
                            <b>
                                <?php
                                $date = $garage_network['TrainingCreditMovement']['creation_date'];
                                if ($user['aag_region_id'] == Configure::read('AAG_REGION_ID_BENELUX')) {
                                    $date = date(Fecha::_FORMATO_BD_FECHA_HORA, strtotime($date . ' +1 hours'));
                                }
                                echo Fecha::toFormatoVistaFechaHora($date);
                                ?>
                            </b>
                        </td>
                        <?php
                        $calculationGivenResult = $garage_network['TrainingCreditMovement']['credit_spent'] + $garage_network['TrainingCreditMovement']['credit_given'];
                        if ($garage_network['TrainingCreditMovement']['description'] == ConstantsDescriptionCredits::REFUNDED_CREDITS) {
                        ?>
                            <td class="color-yellow2-text"><strong><?php echo '+' . $calculationGivenResult; ?></strong></td>
                        <?php } elseif ($garage_network['TrainingCreditMovement']['credit_given'] != null && $calculationGivenResult >= 0) { ?>
                            <td class="color-blue-sky"><strong> <?php echo '+' . $calculationGivenResult; ?></strong></td>
                        <?php } elseif ($garage_network['TrainingCreditMovement']['credit_spent'] != null) { ?>
                            <td class="color-red-text"><strong><?php echo '-' . $calculationGivenResult; ?></strong></td>
                        <?php } elseif ($garage_network['TrainingCreditMovement']['credit_given'] == null && $garage_network['TrainingCreditMovement']['credit_spent'] == null) { ?>
                            <td class="color-blue-sky"><strong><?php echo '+' . $calculationGivenResult; ?></strong></td>
                        <?php } else { ?>
                            <td class="color-red-text"><strong><?php echo $calculationGivenResult; ?></strong></td>
                        <?php
                        }
                        if ($garage_network['TrainingCreditMovement']['description'] == ConstantsDescriptionCredits::EXTRA_GIVEN || $garage_network['TrainingCreditMovement']['description'] == ConstantsDescriptionCredits::YEARLY_RENEW || $garage_network['TrainingCreditMovement']['description'] == ConstantsDescriptionCredits::REFUNDED_CREDITS) {
                        ?>
                            <td class="color-blue-sky">
                                <?php
                                if (isset($garage_network['TrainingCreditMovement']['reason_allowance_id'])) {
                                    echo $garage_network['TrainingCreditMovement']['description'] . ' - ' . $reasons_allowance_list[$garage_network['TrainingCreditMovement']['reason_allowance_id']];
                                } else {
                                    echo $garage_network['TrainingCreditMovement']['description'];
                                }
                                ?>
                            </td>
                        <?php } else { ?>
                            <td><?php echo $garage_network['TrainingCreditMovement']['description']; ?></td>
                        <?php } ?>
                        <td><b><?php echo Fecha::toFormatoVistaFecha($garage_network['TrainingPlannedCourse']['date_from']); ?></b></td>
                        <td class="color-blue-text">
                            <b>
                                <?php
                                if (isset($garage_network['ContactDelegate'])) {
                                    echo $this->Html->link(
                                        '<span>' . $garage_network['ContactDelegate']['first_name'] . ' ' .  $garage_network['ContactDelegate']['last_name'] . '</span>',
                                        array(
                                            'controller' => 'trainings_delegates',
                                            'action' => 'home',
                                            $garage_network['TrainingCreditMovement']['training_planned_course_id'],
                                            '?' => array(
                                                'Delegate_name' => $garage_network['ContactDelegate']['id']
                                            )
                                        ),
                                        array(
                                            'escape' => false,
                                        )
                                    );
                                }
                                ?>
                            </b>
                        </td>
                        <td><b><?php echo $garage_network['TrainingDelegate']['cancelled'] == ConstantsBooleans::YES ? __t('General.Yes') : __t('General.No') ?></b></td>
                        <td><b><?php echo $garage_network['TrainingDelegate']['order_number']; ?></b></td>
                        <td><?php echo $garage_network['TrainingAllowance']['is_actual'] == ConstantsBooleans::YES ? __t('General.Yes') : ''; ?></td>
                        <td class="td-icons">
                            <?php
                            if ($garage_network['TrainingAllowance']['is_actual'] == ConstantsBooleans::YES && $garage_network['TrainingCreditMovement']['garage_network_id']) {
                                echo $this->Html->link(
                                    '<span class="cursor-pointer edit-garage_network ion-android-add-circle color-green-btn"></span>',
                                    array(
                                        'controller' => 'trainings_credits_networks',
                                        'action' => 'add_trainings_credits_networks',
                                        $garage_network['TrainingCreditMovement']['garage_network_id'],
                                        $contact_id,
                                        $garage_network['TrainingAllowance']['id']
                                    ),
                                    array(
                                        'class' => 'flex',
                                        'escape' => false
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
    <br><br>
    <?php echo $this->element('Comun/paginacion'); ?>
</div>
<?php echo $this->Form->end(); ?>
<style>
    .cnt-search-creditInfo {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
    }

    .cnt-search-creditInfo .m-1 {
        display: flex;
        gap: 5px;
    }

    .cnt-search-creditInfo .m-1>div {
        width: auto;
    }

    .cnt-search-creditInfo form .columns {
        width: auto;
    }

    .cnt-search-creditInfo .cnt-buttons-search-training {
        white-space: nowrap;
    }
</style>
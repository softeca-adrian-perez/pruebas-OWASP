<?php
echo $this->Html->script('gd_export_excel.js?v='.Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('jquery.fileDownload.js?v='.Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('/js/conferences_delegates.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
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
        ));?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back')?></a>
        <?php
        echo $this->Form->button(
            __t('Training.Trainings_credits_export'),
            array(
                'class' => 'aag-button medium gd-export-credits-garages-js',
                'value' => 'submit',
                'escape' => false,
                'name' => 'export',
                'data-url' => Router::url(
                    array(
                        'controller' => 'trainings_credits_networks',
                        'action' => 'training_credits_network_excel',
                    )
                ),
            )
        );
        ?>
    </div>
</div>
<?php echo $this->element('../Elements/Comun/trainings_general_tab',array('selected' => 'credits',)); ?>
<div class="cnt-data">
    <div class="aag-padding">
        <?php echo $this->element('../Elements/Comun/trainings_credit_tab', array('selected' => 'credits_networks',)); ?>
    </div>
    <?php echo $this->element('../TrainingsCreditsNetworks/Elements/search_training_credit_network'); ?>
    <div>
        <div class="cnt-legend p-vertical-1">
            <div><?php echo __t('Allowance.Allowance_legend');?></div>
            <div>
                <span class="legend-example past"></span><?php echo __t('Allowance.Past') ?>
            </div>
            <div>
                <span class="legend-example present"></span><?php echo __t('Allowance.Actual_allowance') ?>
            </div>
            <div>
                <span class="legend-example future"></span><?php echo __t('Allowance.Future') ?>
            </div>
        </div>
    </div>
    <div class="cnt-form-inputs aag-padding">
        <div>
            <span style="display: flex; gap: 3px; align-items: center;"><?php echo $this->Html->image(FileManager::get_url(FilePaths::ICONS_IMAGES_RELATIVE . 'cr.svg')); ?></span>
            <p class="color-blue-text"><?php echo __t('Training.Credit_equivalences') ?></p>
            <h2 class="color-blue-text"><b class="color-blue-text"><?php echo $price_credits['TrainingCredit']['pound'] . '£'; ?></b><?php echo ' = ' . $price_credits['TrainingCredit']['credit'] . ' ' . $this->Html->image(FileManager::get_url(FilePaths::ICONS_IMAGES_RELATIVE . 'cr.svg')); ?></h2>
        </div>
        <div>
            <span style="display: flex; gap: 3px; align-items: center;"><?php echo $this->Html->image(FileManager::get_url(FilePaths::ICONS_IMAGES_RELATIVE . 'p.svg')); ?></span>
            <p class="color-blue-text"><?php echo __t('Training.Network_default_credit') ?></p>
            <h2 class="color-blue-text">
                <b class="color-blue-text"><?php echo ($network_selected_data != null) ? $network_selected_data[0]['Network']['credit'] : '';?></b>
            </h2>
        </div>
    </div>
    <div class="o-auto">
        <table id="training_table" class="table-tracking">
            <thead>
                <tr>
                    <th><?php echo $this->Paginator->sort('Garage.name', __t('Training.Garage_name')); ?></th>
                    <th><?php echo $this->Paginator->sort('Network.credit', __t('Training.Default_credits')); ?></th>
                    <th><?php echo $this->Paginator->sort('TrainingAllowance.start_date', __t('Conference.Start_date')); ?></th>
                    <th><?php echo $this->Paginator->sort('TrainingAllowance.end_date', __t('Software.End_date')); ?></th>
                    <th><?php echo __t('Training.Extra_credits'); ?></th>
                    <th><?php echo __t('Training.Credits_taken'); ?></th>
                    <th><?php echo __t('Training.CreditsRemaining'); ?></th>
                    <th><?php echo $this->Paginator->sort('TrainingAllowance.is_actual', __t('Allowance.Actual_allowance')); ?></th>
                    <th class="ta-center"><?php echo __t('General.Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($garages_networks)) {
                    foreach ($garages_networks as $garage_network) {
                        if($garage_network['TrainingAllowance']['start_date'] > date("Y-m-d")) {
                            $tableAllowanceClass = 'futuro';
                        }
                        elseif($garage_network['TrainingAllowance']['end_date'] < date("Y-m-d")) {
                            $tableAllowanceClass = 'pasado';
                        }
                        else {
                            $tableAllowanceClass = 'actual';
                        }
                        ?>
                        <tr class="<?php echo $tableAllowanceClass; ?>">
                            <td>
                                <strong>
                                    <?php echo $this->Html->link(
                                        '<span style="display: flex; gap: 3px; align-items: center;">' . h($garage_network['Garage']['name']) . '</span>',
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
                            <td class="color-blue-text">
                                <b><?php echo $garage_network['Network']['credit'] ? $garage_network['Network']['credit'] . ' ' . $this->Html->image(FileManager::get_url(FilePaths::ICONS_IMAGES_RELATIVE . 'cr.svg')) : ' - '; ?></b>
                            </td>
                            <td>
                                <b><?php echo isset($garage_network['TrainingAllowance']['start_date']) ? Fecha::toFormatoVistaFecha($garage_network['TrainingAllowance']['start_date']) : ''; ?></b>
                            </td>
                            <td>
                                <b><?php echo isset($garage_network['TrainingAllowance']['end_date']) ? Fecha::toFormatoVistaFecha($garage_network['TrainingAllowance']['end_date']) : ''; ?></b>
                            </td>
                            <td class="color-blue-text">
                                <strong><?php
                                    echo $this->Html->link(
                                        '<span style="display: flex; gap: 3px; align-items: center;">' . ($sums_given_list[$garage_network['TrainingAllowance']['id']] ?? '0') . ' ' . $this->Html->image(FileManager::get_url(FilePaths::ICONS_IMAGES_RELATIVE . 'cr.svg')) . '</span>',
                                        array(
                                            'controller' => 'trainings_credits_movements',
                                            'action' => 'home',
                                            '?' => array(
                                                'Garage_name' => $garage_network['Garage']['id'],
                                                'TrainingAllowances_complete_date' => $garage_network['TrainingAllowance']['id'],
                                                'is_actual' => 0,
                                            )
                                        ),
                                        array(
                                            'escape' => false,
                                        )
                                    );
                                    ?>
                                </strong>
                            </td>
                            <td class="color-blue-sky">
                                <strong><?php
                                    echo $this->Html->link(
                                        '<span style="display: flex; gap: 3px; align-items: center;">' . ($sums_spent_list[$garage_network['TrainingAllowance']['id']] ?? '0') . ' ' . $this->Html->image(FileManager::get_url(FilePaths::ICONS_IMAGES_RELATIVE . 'cr.svg')) . '</span>',
                                        array(
                                            'controller' => 'trainings_credits_movements',
                                            'action' => 'home',
                                            '?' => array(
                                                'Garage_name' => $garage_network['Garage']['id'],
                                                'TrainingAllowances_complete_date' => $garage_network['TrainingAllowance']['id'],
                                                'is_actual' => 0,
                                            )
                                        ),
                                        array(
                                            'escape' => false,
                                        )
                                    );
                                    ?>
                                </strong>
                            </td>
                            <td class="color-blue-text">
                                <strong><?php
                                    if ($garage_network['Network']['credit']) {
                                        $total_points = ($sums_given_list[$garage_network['TrainingAllowance']['id']] - $sums_spent_list[$garage_network['TrainingAllowance']['id']]) + $garage_network['Network']['credit'];
                                    }else {
                                        $total_points = ConstantsBooleans::NO;
                                    }
                                    echo $this->Html->link(
                                        '<span style="display: flex; gap: 3px; align-items: center;">' . $total_points . $this->Html->image(FileManager::get_url(FilePaths::ICONS_IMAGES_RELATIVE . 'cr.svg')) . '</span>',
                                        array(
                                            'controller' => 'trainings_credits_movements',
                                            'action' => 'home',
                                            '?' => array(
                                                'Garage_name' => $garage_network['Garage']['id'],
                                                'TrainingAllowances_complete_date' => $garage_network['TrainingAllowance']['id'],
                                                'is_actual' => 0,
                                            )
                                        ),
                                        array('escape' => false)
                                    );
                                    ?>
                                </strong>
                            </td>
                            <td>
                                <?php echo $garage_network['TrainingAllowance']['is_actual'] == ConstantsBooleans::YES ? __t('General.Yes') : ''; ?>
                            </td>
                            <td class="td-icons">
                                <?php
                                    if ($garage_network['TrainingAllowance']['is_actual'] == ConstantsBooleans::YES) {
                                        echo $this->Html->link(
                                            '<span class="cursor-pointer edit-garage_network ion-android-add-circle color-green-btn"></span>',
                                            array(
                                                'controller' => 'trainings_credits_networks',
                                                'action' => 'add_trainings_credits_networks',
                                                $garage_network['GarageNetwork']['id'],
                                                $contact_id,
                                                $garage_network['TrainingAllowance']['id']
                                            ),
                                            array(
                                                'escape' => false,
                                                'class' => 'btn-flex-icon',
                                            )
                                        );
                                    }
                                ?>
                            </td>
                        </tr>
                    <?php }
                }?>
            </tbody>
        </table>
    </div>
    <br><br>
    <?php echo $garages_networks ? $this->element('Comun/paginacion') : ''; ?>
</div>
<?php echo $this->Form->end(); ?>
<style>
    .cnt-search-creditInfo
    {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
    }
    .cnt-search-creditInfo .m-1
    {
        display: flex;
        gap: 5px;
    }
    .cnt-search-creditInfo .m-1 > div { width: auto; }
    .cnt-search-creditInfo form .columns { width: auto; }
    .cnt-search-creditInfo .cnt-buttons-search-training { white-space: nowrap; }

    /* tr.actual td, tr.pasado td { color: #fff !important; } */
    tr>td:not(:last-child) a:hover {
        text-decoration: underline;
    }
    tr.actual td:first-child {
        border-left: 5px solid color-mix(in srgb, var(--success-color), white 25%) !important;
    }
    /* tr.actual td img, tr.pasado td img { filter: brightness(0) invert(1); } */
    tr.pasado td:first-child~td:not(:last-child) * {
        pointer-events: none !important;
    }
    tr.pasado td:first-child {
        /* background: #ccc !important; */
        border-left: 5px solid #aaa !important;
    }
    tr.futuro td:first-child {
        /* background: #ccc !important; */
        border-left: 5px solid #f7a000 !important;
    }
    tr.pasado td:not(:last-child) :where(span, div, a, strong, b) {
        color: #888 !important;
    }
    tr.pasado td:not(:last-child) img {
        filter: grayscale(1);
        opacity: .75;
    }

    /* tr.actual :where(span, div, a, strong, b),
    tr.pasado :where(span, div, a, strong, b) {
        color: inherit !important;
    } */

    .legend-example {
        color: #fff;
        display: inline-flex;
        line-height: 1;
        height: 20px;
        width: 5px;
    }
    .legend-example.past {
        background-color: #aaa;
    }
    .legend-example.present {
        background-color: color-mix(in srgb, var(--success-color), white 25%);
    }
    .legend-example.future {
        background-color: #f7a000;
    }
    .legend-example.future span {
        color: var(--font-default-color);
    }
</style>
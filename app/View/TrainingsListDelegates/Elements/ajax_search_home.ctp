<div class="o-auto p-top-1">
    <table id="training_table" class="table-tracking">
        <thead>
            <tr>
                <th><?php echo $this->Paginator->sort('Contact.first_name', __t('Training.Delegate_name')); ?></th>
                <th><?php echo $this->Paginator->sort('Position.name_' . __l() ,__t('Training.Employment_position')); ?></th>
                <th><?php echo $this->Paginator->sort('Network.name', __t('Training.Network_name')); ?></th>
                <th><?php echo $this->Paginator->sort('Garage.name', __t('Training.Garage_name')); ?></th>
                <th><?php echo $this->Paginator->sort('TrainingProvider.name', __t('Training.Training_provider')); ?></th>
                <th><?php echo $this->Paginator->sort('TrainingCourse.name', __t('Training.Planned_courses')); ?></th>
                <th><?php echo $this->Paginator->sort('TrainingPlannedCourse.date_from', __t('Training.Date_from')); ?></th>
                <th><?php echo $this->Paginator->sort('TrainingPlannedCourse.date_to', __t('Training.Date_to')); ?></th>
                <th><?php echo $this->Paginator->sort('Distributor.account_number', __t('Distributor.Account_number')); ?></th>
                <th><?php echo $this->Paginator->sort('Venue.name', __t('Training.Venue')); ?></th>
                <th><?php echo $this->Paginator->sort('TrainingCourse.price_credit', __t('Training.Credits')); ?></th>
                <th><?php echo $this->Paginator->sort('TrainingDelegate.is_refund_eligible', __t('Training.Credits_taken')); ?></th>
                <th><?php echo $this->Paginator->sort('TrainingDelegate.invoice_number', __t('Training.Invoice_number')); ?></th>
                <th><?php echo $this->Paginator->sort('TrainingDelegate.order_number', __t('Training.Purchase_order_number')); ?></th>
                <th><?php echo $this->Paginator->sort('TrainingDelegate.cancelled', __t('CRM.Cancelled')); ?></th>
                <th><?php echo __t('General.Cancelled_reason'); ?></th>
                <th class="ta-center"></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($delegates_list as $delegate) { ?>
                <tr>
                    <td class="color-blue-text">
                        <?php
                        echo $this->Html->link(
                            '<b>' . h($delegate['Contact']['first_name']) . ' ' . h($delegate['Contact']['last_name']) . '</b>',
                            array(
                                'controller' => 'contacts',
                                'action' => 'home',
                                '?' => array(
                                    'first_name' => $delegate['Contact']['first_name'],
                                    'last_name' => $delegate['Contact']['last_name'],
                                )
                            ),
                            array(
                                'escape' => false,
                            )
                        );
                        ?>
                    </td>
                    <td>
                        <?php echo h($delegate['Position']['name_' . __l()]); ?>
                    </td>
                    <td>
                        <?php echo h($delegate['Network']['name']); ?>
                    </td>
                    <td>
                        <?php
                        echo $this->Html->link(
                            '<spam>' . h($delegate['Garage']['name']) . ' - ' . h($delegate['Garage']['g_number_id']) . '</spam>',
                            array(
                                'controller' => 'garages',
                                'action' => 'home',
                                '?' => array(
                                    'name' => $delegate['Garage']['name'],
                                )
                            ),
                            array(
                                'escape' => false,
                            )
                        );
                        ?>
                    </td>
                    <td>
                        <?php echo h($delegate['TrainingProvider']['name']); ?>
                    </td>
                    <td>
                        <?php
                        echo $this->Html->link(
                            '<b>' . h($delegate['TrainingCourse']['name']) . '</b>',
                            array(
                                'controller' => 'trainings_planned_courses',
                                'action' => 'home',
                                '?' => array(
                                    'TrainingCourse_name' => $delegate['TrainingCourse']['id'],
                                    'TrainingPlannedCourse_date_from' => '',
                                )
                            ),
                            array(
                                'escape' => false,
                            )
                        );
                        ?>
                    </td>
                    <td class="ws-nowrap">
                        <b><?php echo Fecha::toFormatoVistaFecha(h($delegate['TrainingPlannedCourse']['date_from'])); ?></b>
                    </td>
                    <td class="ws-nowrap">
                        <b><?php echo Fecha::toFormatoVistaFecha(h($delegate['TrainingPlannedCourse']['date_to'])); ?></b>
                    </td>
                    <td>
                        <?php echo $delegate['Distributor']['account_number']; ?>
                    </td>
                    <td>
                        <?php
                        echo $this->Html->link(
                            '<b>' . h($delegate['Venue']['name']) . '</b>',
                            array(
                                'controller' => 'venues',
                                'action' => 'home',
                                '?' => array(
                                    'name' => $delegate['Venue']['name']
                                )
                            ),
                            array(
                                'escape' => false,
                            )
                        );
                        ?>
                    </td>
                    <td class="ta-center color-blue-text ws-nowrap">
                        <b><?php echo h($delegate['TrainingCourse']['price_credit']) . ' ' . $this->Html->image(FileManager::get_url(FilePaths::ICONS_IMAGES_RELATIVE . 'cr.svg')); ?></b>
                    </td>
                    <td>
                        <?php echo ($delegate['TrainingDelegate']['is_refund_eligible'] == 1) ? __t('General.Yes') : __t('General.No'); ?>
                    </td>
                    <td>
                        <?php echo h($delegate['TrainingDelegate']['invoice_number']); ?>
                    </td>
                    <td>
                        <?php if (isset($delegate['TrainingDelegate']['order_number']) && !empty($delegate['TrainingDelegate']['order_number']) && ($delegate['TrainingDelegate']['order_number'] != 'NULL')) {
                            echo h($delegate['TrainingDelegate']['order_number']);
                        } ?>
                    </td>
                    <td>
                        <?php echo $delegate['TrainingDelegate']['cancelled'] == ConstantsBooleans::YES ? __t('General.Yes') : __t('General.No'); ?>
                    </td>
                    <td>
                        <?php if ($delegate['TrainingDelegate']['reason_cancelled_id'] && $delegate['TrainingDelegate']['cancelled'] != ConstantsBooleans::NO) {
                            echo $reason_cancelled_list[$delegate['TrainingDelegate']['reason_cancelled_id']];
                        } ?>
                    </td>
                    <?php if ($delegate['TrainingDelegate']['cancelled'] != ConstantsBooleans::YES) { ?>
                        <td class="td-icons span-icons-gap">
                            <?php
                            if ($delegate['TrainingPlannedCourse']['status'] != ConstantsPlannnedCourseStatus::CANCELED) {
                                echo $this->Html->link(
                                    '<span class="cursor-pointer edit-delegate aag-icon-editar"></span>',
                                    array(
                                        'controller' => 'trainings_delegates',
                                        'action' => 'edit',
                                        $delegate['TrainingPlannedCourse']['id'],
                                        $delegate['TrainingDelegate']['id'],
                                    ),
                                    array(
                                        'class' => 'flex',
                                        'escape' => false,
                                    )
                                );
                            }
                            ?>
                        </td>
                    <?php } else { ?><td></td><?php }; ?>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<br><br>
<?php echo $this->element('Comun/paginacion_ajax'); ?>
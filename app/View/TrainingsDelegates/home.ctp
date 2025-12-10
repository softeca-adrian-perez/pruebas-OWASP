<?php
echo $this->Html->script('training_course.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('training_delegates_credits.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('gd_export_excel.js?v='.Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('jquery.fileDownload.js?v='.Configure::read('VERSION_CACHE'), array('block' => 'script'));

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
            $this->Html->link(
                __t('Training.Planned_courses'),
                array(
                    'controller' => 'trainings_planned_courses',
                    'action' => 'home'
                )
            ),
            __t('Training.Training_delegates'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back')?></a>
        <?php
        echo $this->Form->button(
            __t('General.Export_list_delegates'),
            array(
                'class' => 'gd-export-delegates-js aag-button medium blue',
                'value' => 'submit',
                'escape' => false,
                'name' => 'export',
                'data-url' => Router::url(
                    array(
                        'controller' => 'trainings_delegates',
                        'action' => 'training_delegates_excel',
                        $training_planned_course_id,
                    )
                )
            )
        );
        if ($availability_status && ($course['TrainingPlannedCourse']['status'] != ConstantsPlannnedCourseStatus::INACTIVE && $course['TrainingPlannedCourse']['status'] != ConstantsPlannnedCourseStatus::CANCELED)) {
            if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
                echo $this->Html->link(
                    __t('Training.New_assistant'),
                    array(
                        'controller' => 'trainings_delegates',
                        'action' => 'add',
                        $training_planned_course_id
                    ),
                    array(
                        'escape' => false,
                        'class' => 'aag-button medium green',
                        'style' => 'z-index: 1; position: relative;',
                    )
                );
            }
        }
        ?>
    </div>
</div>
<?php echo $this->element('../Elements/Comun/trainings_general_tab',array('selected' => 'training',)); ?>
<div class="cnt-data aag-padding">
    <?php echo $this->element('../TrainingsDelegates/Elements/search_training_delegate'); ?>
    <div class="o-auto p-top-1">
        <table id="training_table" class="table-tracking">
            <thead>
                <tr>
                    <th><?php echo __t('Training.Course_name') ?></th>
                    <th><?php echo __t('Training.Network_name') ?></th>
                    <th><?php echo __t('Training.Garage_name') ?></th>
                    <th><?php echo __t('Training.Delegate_name') ?></th>
                    <th><?php echo __t('Training.Employment_position') ?></th>
                    <th><?php echo __t('Training.Purchase_order_number') ?></th>
                    <th><?php echo __t('Training.Invoice_number') ?></th>
                    <th><?php echo __t('CRM.Cancelled') ?></th>
                    <th><?php echo __t('General.Cancelled_reason') ?></th>
                    <th class="ta-center"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($trainings_delegates as $delegate) { ?>
                    <tr>
                        <td>
                            <b><?php echo h($delegate['TrainingCourse']['name']); ?></b>
                        </td>
                        <td>
                            <b><?php echo h($delegate['Network']['name']); ?></b>
                        </td>
                        <td class="color-blue-text">
                            <?php echo h($delegate['Garage']['name']) . ' - ' . h($delegate['Garage']['g_number_id']); ?>
                        </td>
                        <td>
                            <b><?php echo h($delegate['Contact']['first_name']) . ' ' . h($delegate['Contact']['last_name']); ?></b>
                        </td>
                        <td class="color-blue-text">
                            <?php echo h($delegate['Position']['name_' . __l()]); ?>
                        </td>
                        <td>
                            <b><?php echo h($delegate['TrainingDelegate']['order_number']); ?></b>
                        </td>
                        <td>
                            <b><?php echo h($delegate['TrainingDelegate']['invoice_number']); ?></b>
                        </td>
                        <td class="ta-center">
                            <?php if ($delegate['TrainingDelegate']['cancelled'] == ConstantsBooleans::YES) { ?>
                                <span class="cursor-pointer ico-toggle-activate ion-toggle-filled c-fallo icono-grande <?php if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN) {
                                    echo ' no-click';
                                }
                                ?>" id="<?php echo 'checkbox_' .  $delegate['TrainingDelegate']['id'] ?>"
                                    data-url="
                                        <?php echo Router::url(
                                            array(
                                                'controller' => 'trainings_delegates',
                                                'action' => 'ajax_toggle_status',
                                                $delegate['TrainingDelegate']['id'],
                                                $delegate['Network']['id'],
                                            )
                                        ); ?>"
                                    >
                                </span>
                            <?php } else { ?>
                                <span class="cursor-pointer ico-toggle ion-toggle c-exito icono-grande btn-delete-delegate
                                <?php if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN) {
                                    echo ' no-click';
                                }
                                ?>" id="<?php echo 'checkbox_' .  $delegate['TrainingDelegate']['id'] ?>"
                                    data-url-delete="
                                        <?php echo Router::url(
                                            array(
                                                'controller' => 'trainings_delegates',
                                                'action' => 'ajax_delete',
                                                $delegate['TrainingDelegate']['id'],
                                            )
                                        ); ?>"
                                    data-array-reason='<?php echo $reasons_cancelled_delegates;?>'>
                                </span>
                            <?php } ?>
                        </td>
                        <td>
                            <?php if ($delegate['TrainingDelegate']['reason_cancelled_id'] && $delegate['TrainingDelegate']['cancelled'] != ConstantsBooleans::NO) {
                                echo $reason_cancelled_list[$delegate['TrainingDelegate']['reason_cancelled_id']];
                            } ?>
                        </td>
                        <?php if ($delegate['TrainingDelegate']['cancelled'] != ConstantsBooleans::YES) { ?>
                            <td class="td-icons span-icons-gap">
                                <?php
                                    if ($course['TrainingPlannedCourse']['status'] != ConstantsPlannnedCourseStatus::CANCELED) {
                                        echo $this->Html->link(
                                            '<span class="cursor-pointer edit-delegate aag-icon-editar"></span>',
                                            array(
                                                'controller' => 'trainings_delegates',
                                                'action' => 'edit',
                                                $training_planned_course_id,
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
                <?php }; ?>
            </tbody>
        </table>
        <input type="hidden" name="reason_cancelled_id" id="reason_cancelled_id" />
    </div>
    <br><br>
    <?php echo $this->element('Comun/paginacion'); ?>
</div>
<?php echo $this->Form->end(); ?>
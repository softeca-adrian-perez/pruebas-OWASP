<?php
echo $this->Html->script('training_planned_course.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('gd_export_excel.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('jquery.fileDownload.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
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
            __t('Training.Planned_courses'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back') ?></a>
        <?php
        echo $this->Form->button(
            __t('General.Export_training'),
            array(
                'class' => 'gd-export-js aag-button medium blue',
                'value' => 'submit',
                'escape' => false,
                'name' => 'export',
                'data-max_export' => TRAININGS_EXPORT_LIMIT,
                'data-modal_export' => 'modal_send_csv_email_training',
                'data-url_ajax_count' => '/trainings_planned_courses/ajax_count_trainings',
                'data-url' => Router::url(
                    array(
                        'controller' => 'trainings_planned_courses',
                        'action' => 'general_training_excel'
                    )
                )
            )
        );
        echo $this->Form->button(
            __t('General.Export_planned_courses'),
            array(
                'class' => 'gd-export-excel-courses-js aag-button medium',
                'value' => 'submit',
                'escape' => false,
                'name' => 'export',
                'data-url' => Router::url(
                    array(
                        'controller' => 'trainings_planned_courses',
                        'action' => 'training_planned_courses_excel'
                    )
                )
            )
        );
        if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
            echo $this->Html->link(
                __t('Training.New_planned_course'),
                array(
                    'controller' => 'trainings_planned_courses',
                    'action' => 'add'
                ),
                array('class' => 'aag-button medium green')
            );
        }
        ?>
        <div style="display: none;" id="modal_send_csv_email_training" class="reveal-modal background-color-primary" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog" data-user-id="<?php echo CakeSession::read('Auth.User.id'); ?>" data-close-on-click="false">
            <div class="aag-subtitle">
                <?php echo sprintf(__t('General.Export_limit_exceeded'), TRAININGS_EXPORT_LIMIT . ' ' . strtolower(__t('General.Rows'))); ?>
            </div>
            <?php
            $user = $this->Acceso->user();
            echo $this->Form->input(
                'email',
                array(
                    'label' => __t('Contact.Email'),
                    'value' => $user['Contact']['email'],
                    'id' => 'contact-email-js',
                )
            );
            ?>
            <div class="ta-right p-top-1">
                <?php
                echo $this->Form->button(
                    __t('General.Send'),
                    array(
                        'id' => 'send-data-export-js',
                        'class' => 'aag-button medium green send-data-export-js',
                        'type' => 'submit',
                        'escape' => false,
                        'data-url_ajax' => '/trainings_planned_courses/ajax_get_csv_data',
                        'data-url' => Router::url(
                            array(
                                'controller' => 'app',
                                'action' => 'ajax_verify_email',
                            )
                        ),
                    )
                );
                ?>
            </div>
            <a class="close-modal" data-close aria-label="Close">&#215;</a>
        </div>
    </div>
</div>
<?php echo $this->element('../Elements/Comun/trainings_general_tab', array('selected' => 'training')); ?>
<div class="cnt-data">
    <div class="aag-padding">
        <?php echo $this->element('../Elements/Comun/trainings_sub_tab', array('selected' => 'planned_courses')); ?>
    </div>
    <?php echo $this->element('../TrainingsPlannedCourses/Elements/search_training_planned_course'); ?>
    <div class="o-auto">
        <table id="training_table" class="table-tracking">
            <thead>
                <tr>
                    <th><?php echo $this->Paginator->sort('TrainingCourse.name', __t('Training.Course_name')); ?></th>
                    <th><?php echo $this->Paginator->sort('CourseType.name_' . __l(), __t('Training.Course_type')); ?></th>
                    <th><?php echo $this->Paginator->sort('TrainingProvider.name', __t('Training.Training_provider')); ?></th>
                    <th><?php echo $this->Paginator->sort('TrainingPlannedCourse.date_from', __t('Training.Date_from')); ?></th>
                    <th><?php echo $this->Paginator->sort('TrainingPlannedCourse.date_to', __t('Training.Date_to')); ?></th>
                    <th><?php echo $this->Paginator->sort('TrainingPlannedCourse.duration', __t('Training.Duration')); ?></th>
                    <th><?php echo $this->Paginator->sort('Venue.name', __t('Training.Venue')); ?></th>
                    <th><?php echo $this->Paginator->sort('TrainingPlannedCourse.invoice_number', __t('Training.Invoice_number')); ?></th>
                    <th class="ta-center"><?php echo $this->Paginator->sort('TrainingPlannedCourse.status', __t('Training.Status')); ?></th>
                    <th class="ta-center"><?php echo $this->Paginator->sort('TrainingCourse.price_credit', __t('Training.Credit')); ?></th>
                    <th class="ta-center"><?php echo $this->Paginator->sort('TrainingCourse.cost_training', __t('Training.Cost_of_course')); ?></th>
                    <th><?php echo $this->Paginator->sort('TrainingPlannedCourse.status', __t('Training.Availability')); ?></th>
                    <th><?php echo $this->Paginator->sort('TrainingCourse.is_online', __t('Training.Online')); ?></th>
                    <th class="ta-center" width="125"><?php echo __t('General.Actions'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($trainings_planned_courses as $planned_course) { ?>
                    <tr>
                        <td>
                            <b><?php echo h($planned_course['TrainingCourse']['name']); ?></b>
                        </td>
                        <td>
                            <?php echo h($planned_course['CourseType']['name_' . __l()]); ?>
                        </td>
                        <td class="color-blue-text">
                            <?php
                            echo $this->Html->link(
                                '<span>' . h($planned_course['TrainingProvider']['name']) . '</span>',
                                array(
                                    'controller' => 'trainings_providers',
                                    'action' => 'home',
                                    '?' => array(
                                        'TrainingProvider_name' => $planned_course['TrainingProvider']['id']
                                    )
                                ),
                                array(
                                    'escape' => false,
                                )
                            ); ?>
                        </td>
                        <td class="color-blue-text ws-nowrap">
                            <?php echo h(Fecha::toFormatoVista($planned_course['TrainingPlannedCourse']['date_from'])); ?>
                        </td>
                        <td class="color-blue-text ws-nowrap">
                            <?php echo h(Fecha::toFormatoVista($planned_course['TrainingPlannedCourse']['date_to'])); ?>
                        </td>
                        <td class="color-blue-text">
                            <b><?php echo h($planned_course['TrainingPlannedCourse']['duration']); ?></b>
                        </td>
                        <td>
                            <?php
                            echo $this->Html->link(
                                '<b>' . $planned_course['Venue']['name'] . '</b>',
                                array(
                                    'controller' => 'venues',
                                    'action' => 'home',
                                    '?' => array(
                                        'name' => $planned_course['Venue']['name']
                                    )
                                ),
                                array(
                                    'escape' => false,
                                )
                            );
                            ?>
                        </td>
                        <td>
                            <b><?php echo h($planned_course['TrainingPlannedCourse']['invoice_number']); ?></b>
                        </td>
                        <td class="ta-center ws-nowrap">
                            <?php
                            $inactive_status_on = 'hidden';
                            $inactive_status_off = '';
                            $active_status_on = 'hidden';
                            $active_status_off = '';
                            $cancel_status_on = 'hidden';
                            $cancel_status_off = '';
                            $block_cancel = false;
                            if ($planned_course['TrainingPlannedCourse']['status'] == ConstantsPlannnedCourseStatus::INACTIVE) {
                                $inactive_status_on = '';
                                $inactive_status_off = 'hidden';
                            } elseif ($planned_course['TrainingPlannedCourse']['status'] == ConstantsPlannnedCourseStatus::ACTIVE) {
                                $active_status_on = '';
                                $active_status_off = 'hidden';
                            } else {
                                $cancel_status_on = '';
                                $cancel_status_off = 'hidden';
                                $block_cancel = true;
                            }
                            ?>
                            <span <?php echo $cancel_status_off ?> data-planned-course="<?php echo $planned_course['TrainingPlannedCourse']['id']; ?>" class="cursor-pointer ico-toggle-cancel ico-toggle-hover<?php if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN) {
                                                                                                                                                                                                                    echo ' no-click';
                                                                                                                                                                                                                }
                                                                                                                                                                                                                ?>" id="cancel_status_off_<?php echo $planned_course['TrainingPlannedCourse']['id']; ?>" data-url="
                                        <?php echo Router::url(
                                            array(
                                                'controller' => 'trainings_planned_courses',
                                                'action' => 'ajax_toggle_status_cancel',
                                                $planned_course['TrainingPlannedCourse']['id'],
                                            )
                                        ); ?>" data-url-delete="
                                        <?php echo Router::url(
                                            array(
                                                'controller' => 'trainings_delegates',
                                                'action' => 'ajax_delete_all',
                                                $planned_course['TrainingPlannedCourse']['id'],
                                            )
                                        ); ?>
                                    ">
                                <?php echo $this->Html->image(FileManager::get_url(FilePaths::ICONS_IMAGES_RELATIVE . 'cancel_off.svg'), ['title' => ConstantsPlannnedCourseLabelName::CANCEL]) ?>
                            </span>
                            <span <?php echo $cancel_status_on ?> class="<?php if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN) {
                                                                                echo ' no-click';
                                                                            }
                                                                            ?>" id="cancel_status_on_<?php echo $planned_course['TrainingPlannedCourse']['id']; ?>">
                                <?php echo $this->Html->image(FileManager::get_url(FilePaths::ICONS_IMAGES_RELATIVE . 'cancel_on.svg'), ['title' => ConstantsPlannnedCourseLabelName::CANCEL]) ?>
                            </span>
                            <span <?php echo $inactive_status_off ?> data-planned-course="<?php echo $planned_course['TrainingPlannedCourse']['id']; ?>" class="cursor-pointer ico-toggle-inactive ico-toggle-hover<?php if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN) {
                                                                                                                                                                                                                        echo ' no-click';
                                                                                                                                                                                                                    }
                                                                                                                                                                                                                    ?>" id="inactive_status_off_<?php echo $planned_course['TrainingPlannedCourse']['id']; ?>" data-url="
                                        <?php echo Router::url(
                                            array(
                                                'controller' => 'trainings_planned_courses',
                                                'action' => 'ajax_toggle_status_inactive',
                                                $planned_course['TrainingPlannedCourse']['id'],
                                            )
                                        ); ?>">
                                <?php echo $this->Html->image(FileManager::get_url(FilePaths::ICONS_IMAGES_RELATIVE . 'off_off.svg'), ['title' => ConstantsPlannnedCourseLabelName::DEACTIVATE]) ?>
                            </span>
                            <span <?php echo $inactive_status_on ?> class="<?php if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN) {
                                                                                echo ' no-click';
                                                                            }
                                                                            ?>" id="inactive_status_on_<?php echo $planned_course['TrainingPlannedCourse']['id']; ?>">
                                <?php echo $this->Html->image(FileManager::get_url(FilePaths::ICONS_IMAGES_RELATIVE . 'off_on.svg'), ['title' => ConstantsPlannnedCourseLabelName::DEACTIVATE]) ?>
                            </span>
                            <span <?php echo $active_status_off ?> data-planned-course="<?php echo $planned_course['TrainingPlannedCourse']['id']; ?>" class="cursor-pointer ico-toggle-active ico-toggle-hover<?php if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN) {
                                                                                                                                                                                                                    echo ' no-click';
                                                                                                                                                                                                                }
                                                                                                                                                                                                                ?>" id="active_status_off_<?php echo $planned_course['TrainingPlannedCourse']['id']; ?>" data-url="
                                        <?php echo Router::url(
                                            array(
                                                'controller' => 'trainings_planned_courses',
                                                'action' => 'ajax_toggle_status_active',
                                                $planned_course['TrainingPlannedCourse']['id'],
                                            )
                                        ); ?>">
                                <?php echo $this->Html->image(FileManager::get_url(FilePaths::ICONS_IMAGES_RELATIVE . 'check_off.svg'), ['title' => ConstantsPlannnedCourseLabelName::ACTIVATE]) ?>
                            </span>
                            <span <?php echo $active_status_on ?> class="<?php if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN) {
                                                                                echo ' no-click';
                                                                            }
                                                                            ?>" id="active_status_on_<?php echo $planned_course['TrainingPlannedCourse']['id']; ?>">
                                <?php echo $this->Html->image(FileManager::get_url(FilePaths::ICONS_IMAGES_RELATIVE . 'tick_on.svg'), ['title' => ConstantsPlannnedCourseLabelName::ACTIVATE]) ?>
                            </span>
                        </td>
                        <td class="ta-center color-blue-text ws-nowrap">
                            <b><?php echo h($planned_course['TrainingCourse']['price_credit']) . ' ' . $this->Html->image(FileManager::get_url(FilePaths::ICONS_IMAGES_RELATIVE . 'cr.svg')); ?></b>
                        </td>
                        <td class="ta-center color-blue-text">
                            <b><?php echo '£ ' . h($planned_course['TrainingCourse']['cost_training']); ?></b>
                        </td>
                        <td class="ws-nowrap">
                            <?php
                            if ($planned_course['TrainingPlannedCourse']['status'] == ConstantsPlannnedCourseStatus::INACTIVE || $planned_course['TrainingPlannedCourse']['status'] == ConstantsPlannnedCourseStatus::CANCELED) {
                            ?>
                                <b>
                                    <?php echo __t('Training.Unavailable'); ?>
                                </b>
                            <?php } elseif ($planned_course['TrainingPlannedCourse']['availability'] == $count_available[$planned_course['TrainingPlannedCourse']['id']]) { ?>
                                <b class="color-yellow-text">
                                    <?php echo __t('Training.Completed'); ?>
                                </b>
                            <?php } else { ?>
                                <b class="color-blue-text">
                                    <?php echo __t('Training.Available'); ?>
                                </b>
                            <?php
                            }
                            echo $this->Html->link(
                                '<span class="cursor-pointer">' . $count_available[$planned_course['TrainingPlannedCourse']['id']] . '/' . h($planned_course['TrainingPlannedCourse']['availability']) . '</span>',
                                array(
                                    'controller' => 'trainings_delegates',
                                    'action' => 'home',
                                    $planned_course['TrainingPlannedCourse']['id'],
                                ),
                                array(
                                    'escape' => false,
                                    'class' => 'btn-flex-icon',
                                )
                            );
                            if ($planned_course['TrainingPlannedCourse']['availability'] != $count_available[$planned_course['TrainingPlannedCourse']['id']] && ($planned_course['TrainingPlannedCourse']['status'] != ConstantsPlannnedCourseStatus::INACTIVE && $planned_course['TrainingPlannedCourse']['status'] != ConstantsPlannnedCourseStatus::CANCELED) && CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
                                echo $this->Html->link(
                                    '<span class="cursor-pointer ion-android-add-circle color-green-btn"></span>',
                                    array(
                                        'controller' => 'trainings_delegates',
                                        'action' => 'add',
                                        $planned_course['TrainingPlannedCourse']['id'],
                                    ),
                                    array(
                                        'escape' => false,
                                        'class' => 'btn-flex-icon',
                                    )
                                );
                            }
                            ?>
                        </td>
                        <td>
                            <?php echo $planned_course['TrainingCourse']['is_online'] == ConstantsBooleans::YES ? __t('General.Yes') : __t('General.No'); ?>
                        </td>
                        <td class="ta-center">
                            <?php echo $this->Html->link(
                                '<span class="cursor-pointer edit-course aag-icon-editar"></span>',
                                array(
                                    'controller' => 'trainings_planned_courses',
                                    'action' => 'edit',
                                    $planned_course['TrainingPlannedCourse']['id'],
                                ),
                                array(
                                    'escape' => false,
                                    'class' => 'btn-flex-icon flex',
                                )
                            );
                            ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <br />
    <?php echo $this->element('Comun/paginacion'); ?>
</div>
<?php echo $this->Form->end(); ?>
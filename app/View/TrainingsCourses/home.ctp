<?php
echo $this->Html->script('training_course.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
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
            __t('Training.Courses_list'),
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
            __t('General.Export_courses'),
            array(
                'class' => 'gd-export-excel-training-courses-js aag-button medium',
                'value' => 'submit',
                'escape' => false,
                'name' => 'export',
                'data-url' => Router::url(
                    array(
                        'controller' => 'trainings_courses',
                        'action' => 'training_courses_excel',
                    )
                ),
            )
        );
        if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
            echo $this->Html->link(
                __t('Training.New_planned_course'),
                array(
                    'controller' => 'trainings_planned_courses',
                    'action' => 'add',
                ),
                array('class' => 'aag-button medium green')
            );
            echo $this->Html->link(
                __t('Training.New_training'),
                array(
                    'controller' => 'trainings_courses',
                    'action' => 'add',
                ),
                array('class' => 'aag-button medium green')
            );
        } else {
        ?>
            <div class="is_superAdmin-js"></div>
        <?php } ?>
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
        <?php echo $this->element('../Elements/Comun/trainings_sub_tab', array('selected' => 'list_of_courses')); ?>
    </div>
    <?php echo $this->element('../TrainingsCourses/Elements/search_training_course'); ?>
    <div class="o-auto">
        <table id="training_table" class="table-tracking">
            <thead>
                <tr>
                    <th></th>
                    <th><?php echo $this->Paginator->sort('TrainingCourse.name', __t('Training.Course_name')); ?></th>
                    <th><?php echo $this->Paginator->sort('CourseType.name_' . __l(), __t('Training.Course_type')); ?></th>
                    <th><?php echo $this->Paginator->sort('TrainingProvider.name', __t('Training.Training_provider')); ?></th>
                    <th><?php echo __t('Training.Date_from'); ?></th>
                    <th><?php echo __t('Training.Date_to'); ?></th>
                    <th><?php echo __t('Training.Duration'); ?></th>
                    <th><?php echo __t('Training.Venue'); ?></th>
                    <th><?php echo $this->Paginator->sort('TrainingCourse.active', __t('Training.Status')); ?></th>
                    <th class="ta-center"><?php echo $this->Paginator->sort('TrainingCourse.cost_training', __t('Training.Cost_of_training')); ?></th>
                    <th class="ta-center"><?php echo $this->Paginator->sort('TrainingCourse.price_credit', __t('Training.Credits')); ?></th>
                    <th><?php echo $this->Paginator->sort('TrainingCourse.invoice_number', __t('Training.Invoice_number')); ?></th>
                    <th><?php echo __t('Training.Availability'); ?></th>
                    <th><?php echo $this->Paginator->sort('TrainingCourse.part_number', __t('Training.Part_number')); ?></th>
                    <th><?php echo $this->Paginator->sort('TrainingCourse.is_online', __t('Training.Online')); ?></th>
                    <th class="ta-center" width="125"><?php echo __t('General.Actions'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($trainings_courses as $course) {
                    $url_delete = Router::url(array(
                        'controller' => 'trainings_courses',
                        'action' => 'ajax_delete_products',
                    ));
                    $url_data =  Router::url(array(
                        'controller' => 'trainings_courses',
                        'action' => 'ajax_charge_training_planned_courses',
                        $course['TrainingCourse']['id'],
                    ));
                ?>
                    <tr class="tr-show" data-identificator="<?php echo $course['TrainingCourse']['id'] ?>" id="<?php echo $course['TrainingCourse']['id'] ?>" data-delete="<?php echo $url_delete; ?>" data-url="<?php echo $url_data; ?>">
                        <td>
                            <div class="ampliar-js cursor-pointer">
                                <div class="mostrar-ampliado">
                                    <i class="ion-ios-arrow-down fs-x-large"></i>
                                </div>
                            </div>
                        </td>
                        <td>
                            <b><?php echo h($course['TrainingCourse']['name']); ?></b>
                        </td>
                        <td>
                            <?php echo h($course['CourseType']['name_' . __l()]); ?>
                        </td>
                        <td class="color-blue-text">
                            <?php
                            echo $this->Html->link(
                                '<span>' . h($course['TrainingProvider']['name']) . '</span>',
                                array(
                                    'controller' => 'trainings_providers',
                                    'action' => 'home',
                                    '?' => array(
                                        'TrainingProvider_name' => $course['TrainingProvider']['id']
                                    )
                                ),
                                array(
                                    'escape' => false,
                                )
                            );
                            ?>
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>
                            <div class="aag-switch round small f-left">
                                <?php
                                echo $this->Form->input(
                                    'active',
                                    array(
                                        'label' => false,
                                        'div' => false,
                                        'type' => 'checkbox',
                                        'checked' => $course['TrainingCourse']['active'] == ConstantsBooleans::ACTIVE ? true : false,
                                        'id' => 'active_' . $course['TrainingCourse']['id'],
                                    )
                                );
                                if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
                                    $url = Router::url(
                                        array(
                                            'controller' => 'trainings_courses',
                                            'action' => $course['TrainingCourse']['active'] == ConstantsBooleans::ACTIVE ? 'ajax_toggle_status_active' : 'ajax_toggle_status_inactive',
                                        )
                                    );
                                ?>
                                    <label for="activeInput" class="activeInput" data-url="<?php echo $url; ?>" data-id-value="<?php echo $course['TrainingCourse']['id']; ?>"></label>
                                <?php } else { ?>
                                    <label for="activeInput" class="activeInput no-click"></label>
                                <?php } ?>
                            </div>
                        </td>
                        <td class="ta-center">
                            <strong><?php echo '£ ' . $course['TrainingCourse']['cost_training']; ?></strong>
                        </td>
                        <td class="ta-center color-blue-text">
                            <b><?php echo h($course['TrainingCourse']['price_credit']) . ' ' . $this->Html->image(FileManager::get_url(FilePaths::ICONS_IMAGES_RELATIVE . 'cr.svg')); ?></b>
                        </td>
                        <td>
                            <b><?php echo h($course['TrainingCourse']['invoice_number']); ?></b>
                        </td>
                        <td></td>
                        <td>
                            <b><?php echo h($course['TrainingCourse']['part_number']); ?></b>
                        </td>
                        <td>
                            <?php echo $course['TrainingCourse']['is_online'] == ConstantsBooleans::YES ? __t('General.Yes') : __t('General.No'); ?>
                        </td>
                        <td class="ta-center">
                            <div class="flex gap-1 ta-center">
                                <?php if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
                                    echo $this->Html->link(
                                        '<span class="icon cursor-pointer ion-android-add-circle color-green-btn"></span>',
                                        array(
                                            'controller' => 'trainings_planned_courses',
                                            'action' => 'add',
                                            $course['TrainingCourse']['id'],
                                        ),
                                        array(
                                            'escape' => false,
                                            'class' => 'btn-flex-icon',
                                        )
                                    );
                                }
                                echo $this->Html->link(
                                    '<span class="icon cursor-pointer aag-icon-editar edit-course"></span>',
                                    array(
                                        'controller' => 'trainings_courses',
                                        'action' => 'edit',
                                        $course['TrainingCourse']['id'],
                                    ),
                                    array(
                                        'escape' => false,
                                        'class' => 'btn-flex-icon flex',
                                        'style' => 'margin-top: 0.5rem',
                                    )
                                );
                                ?>
                                </span>
                            </div>
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
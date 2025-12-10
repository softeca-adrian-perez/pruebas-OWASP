<?php
$action = $this->request->action;
echo $this->Html->script('training_planned_course.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Form->create(
    'TrainingPlannedCourse',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data',
        'id' => 'planned-course-form'
    )
);
echo $this->Form->hidden('TrainingPlannedCourse.id');
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        if ($action == ConstantsActionsNames::ADD) {
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
                __t('General.Add'),
            ));
        } else {
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
                __t('General.Edit'),
            ));
        }
        ?>
    </div>
    <div>
        <?php echo $this->element('Comun/form_actions', $cancel_action); ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="aag-title m-bottom-1">
        <?php
        if ($action == ConstantsActionsNames::ADD) {
            echo __t('Training.Add_course');
        } else {
            echo __t('Training.Edit_course');
        }
        ?>
    </div>
    <div class="cnt-form-inputs">
        <?php
        echo $this->Form->input(
            'training_course_id',
            array(
                'label' => __t('Training.Course'),
                'type' => 'select',
                'class' => 'select2-multiple',
                'options' => $trainings_courses,
                'empty' => true,
                'id' => 'training_course_id',
                'data-url' => Router::url(array(
                    'controller' => 'trainings_planned_courses',
                    'action' => 'ajax_select_course',
                )),
                'value' => isset($training_course_id) ? $training_course_id : null,
                'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
            )
        );
        ?>
        <div>
            <?php
            echo $this->Form->input(
                'TrainingCourse.type',
                array(
                    'type' => 'text',
                    'required' => true,
                    'label' => __t('Training.Course_type'),
                    'disabled' => true,
                    'id' => 'course_type_id',
                    'value' => isset($course_type[0]['CourseType']['name_' . __l()]) ? $course_type[0]['CourseType']['name_' . __l()] : null,
                )
            );
            ?>
        </div>
        <div>
            <?php
            echo $this->Form->input(
                'TrainingCourse.training_provider',
                array(
                    'type' => 'text',
                    'required' => true,
                    'label' => __t('Training.Training_provider'),
                    'disabled' => true,
                    'id' => 'training_provider_id',
                    'value' => isset($course_type[0]['TrainingProvider']['name']) ? $course_type[0]['TrainingProvider']['name'] : null,
                    'data-url' => Router::url(array(
                        'controller' => 'trainings_planned_courses',
                        'action' => 'ajax_select_provider'
                    )),
                    'data-trainer' => isset($array_trainer_name) ? array_keys($array_trainer_name)[0] : null
                )
            );
            ?>
        </div>
        <?php
        echo $this->Form->input(
            'training_trainer_id',
            array(
                'label' => __t('Training.Trainer'),
                'type' => 'select',
                'class' => 'select2-multiple',
                'options' => isset($array_trainer_name) ? $array_trainer_name : array(),
                'empty' => true,
                'id' => 'training_trainer_id',
                'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
            )
        );
        echo $this->Form->input(
            'venue_id',
            array(
                'label' => __t('Training.Venue'),
                'type' => 'select',
                'class' => 'select2-multiple',
                'options' => $venues,
                'empty' => true,
                'id' => 'venue_id',
                'data-url' => Router::url(array(
                    'controller' => 'trainings_planned_courses',
                    'action' => 'ajax_select_venue',
                )),
                'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
            )
        );
        ?>
        <div class="two-columns">
            <?php
            echo $this->Form->input(
                'Venue.address_1',
                array(
                    'type' => 'text',
                    'required' => true,
                    'label' => __t('Training.Venue_address_1'),
                    'disabled' => true,
                    'id' => 'venue_address',
                    'value' => isset($venue_data['Venue']['address_1']) ? $venue_data['Venue']['address_1'] : '',
                )
            );
            ?>
        </div>
        <?php
        echo $this->Form->input(
            'Venue.post_code',
            array(
                'type' => 'text',
                'required' => true,
                'label' => __t('Training.Venue_post_code'),
                'disabled' => true,
                'id' => 'venue_post_code',
                'value' => isset($venue_data['Venue']['post_code']) ? $venue_data['Venue']['post_code'] : '',
            )
        );
        echo $this->Form->input(
            'Venue.town',
            array(
                'type' => 'text',
                'required' => true,
                'label' => __t('Training.Venue_town'),
                'disabled' => true,
                'id' => 'venue_town',
                'value' => isset($venue_data['Venue']['town']) ? $venue_data['Venue']['town'] : '',
            )
        );
        echo $this->Form->input(
            'date_from',
            array(
                'class' => 'fecha-js from-js clear_field',
                'type' => 'text',
                'required' => true,
                'div' => array(
                    'class' => 'datepicker datepicker-label-block',
                ),
                'label' => __t('Training.From'),
                'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
            )
        );
        echo $this->Form->input(
            'date_to',
            array(
                'class' => 'fecha-js to-js clear_field',
                'type' => 'text',
                'required' => true,
                'div' => array(
                    'class' => 'datepicker datepicker-label-block',
                ),
                'label' => __t('Training.To'),
                'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
            )
        );
        echo $this->Form->input(
            'starting_time',
            array(
                'type' => 'text',
                'required' => true,
                'class' => 'timepicker disabled_fields',
                'id' => 'start_time',
                'label' => __t('Training.Starting_time'),
                'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
            )
        );
        echo $this->Form->input(
            'duration',
            array(
                'type' => 'number',
                'required' => true,
                'label' => __t('Training.Duration'),
                'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
            )
        );
        echo $this->Form->input(
            'availability',
            array(
                'type' => 'number',
                'required' => true,
                'label' => __t('Training.Max_availability'),
                'value' => isset($availabilityValue) ? $availabilityValue : ConstantsAvailabilityLenght::LENGHT,
                'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
            )
        );
        echo $this->Form->input(
            'invoice_number',
            array(
                'type' => 'text',
                'required' => true,
                'label' => __t('Training.Invoice_number'),
                'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
            )
        );
        ?>
        <div class="two-columns">
            <?php echo $this->Form->input(
                'note',
                array(
                    'type' => 'text',
                    'required' => true,
                    'label' => __t('Order.Notes'),
                    'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                )
            );
            ?>
        </div>
    </div>
</div>
<?php echo $this->Form->end(); ?>
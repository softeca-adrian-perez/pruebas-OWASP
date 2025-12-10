<?php
$action = $this->request->action;
echo $this->Html->script('training_course.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('tinymce/tinymce.min.js');
echo $this->Html->script('tinymce_init.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));

echo $this->Form->create(
    'TrainingCourse',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data',
        'id' => 'course-form'
    )
);
echo $this->Form->hidden('TrainingCourse.id');
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
                    __t('Training.Trainings_course_list'),
                    array(
                        'controller' => 'trainings_courses',
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
                    __t('Training.Trainings_course_list'),
                    array(
                        'controller' => 'trainings_courses',
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
    <div class="cnt-form-inputs m-bottom-1">
        <?php
        echo $this->Form->input(
            'name',
            array(
                'type' => 'text',
                'required' => true,
                'label' => __t('Training.Course_name'),
                'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
            )
        );
        echo $this->Form->input(
            'course_type_id',
            array(
                'label' => __t('Training.Course_type'),
                'type' => 'select',
                'class' => 'select2-multiple',
                'options' => $courses_types,
                'empty' => true,
                'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
            )
        );
        echo $this->Form->input(
            'training_provider_id',
            array(
                'label' => __t('Training.Training_provider'),
                'type' => 'select',
                'class' => 'select2-multiple',
                'options' => $trainings_providers,
                'empty' => true,
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
            'price',
            array(
                'type' => 'decimal',
                'required' => true,
                'label' => __t('Training.Price') . ' ' . $country['Country']['symbol'] . ' <span data-tooltip aria-haspopup="true" class="has-tip ion-information-circled" title="' . __t('Training.Tool_tip_price') . '"></span>',
                'id' => 'pricePounds',
                'style' => 'display: flex; width:100%;',
                'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
            )
        );
        echo $this->Form->input(
            'price_credit',
            array(
                'type' => 'decimal',
                'required' => true,
                'label' => __t('Training.Price_in_credits'),
                'id' => 'priceCredits',
                'style' => 'display: flex; width:100%',
                'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
            )
        );
        echo $this->Form->input(
            'cost_training',
            array(
                'type' => 'decimal',
                'required' => true,
                'label' => __t('Training.Cost_of_prov') . ' ' . $country['Country']['symbol'],
                'id' => 'priceCredits',
                'style' => 'display: flex; width:100%',
                'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
            )
        );
        echo $this->Form->hidden(
            'TrainingCredit.pound',
            array(
                'type' => 'text',
                'label' => false,
                'value' => $credits['TrainingCredit']['pound'],
                'id' => 'creditPoundInput',
                'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
            )
        );
        echo $this->Form->hidden(
            'TrainingCredit.credit',
            array(
                'type' => 'text',
                'label' => false,
                'value' => $credits['TrainingCredit']['credit'],
                'id' => 'creditCreditInput',
                'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
            )
        );
        echo $this->Form->input(
            'part_number',
            array(
                'type' => 'text',
                'required' => false,
                'label' => __t('Training.Part_number'),
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
        echo $this->Form->input(
            'is_online',
            array(
                'label' => __t('Training.Online'),
                'type' => 'select',
                'class' => 'select2-multiple',
                'options' => $is_online,
                'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
            )
        );
        ?>
    </div>
    <?php
    echo $this->Form->input(
        'description',
        array(
            'label' => __t('Training.Description'),
            'type' => 'textarea',
            'rows' => 30,
            'cols' => 25,
            'class' => 'ta-high description_tinymce-js',
            'data-type' => ConstantsTypesTinyMce::TRAINING_COURSE,
            'data-message_file_size' => __t(ConstantsMessages::MAX_FILE_SIZE),
            'data-general_error' => __t('General.Error'),
            'required' => false,
        )
    );
    ?>
</div>
<?php echo $this->Form->end(); ?>
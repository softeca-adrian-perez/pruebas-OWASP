<?php
echo $this->Html->script('garage_reviews.js?v='.Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Form->create('RequestFeefoReview', array('enctype' => 'multipart/form-data'));
    $action = $this->request->action;
    ?>
    <div class="buttons-fixed-double-tabs">
    </div>
    <div class="cnt-form-search" style="margin-left: 0; margin-right: 0;">
        <div class="cnt-form-inputs">
            <div class="aag-subtitle ai-start"><?php echo __t('Review.Manual_review_text'); ?></div>
        </div>
        <div class="cnt-form-inputs required">
                <div>
                    <?php echo $this->Form->input(
                        'name',
                        array(
                            'label' => __t('General.Customer_name'),
                            'type' => 'text',
                            'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                            'required' => true
                        )
                    ); ?>
                </div>
                <div>
                    <?php echo $this->Form->input(
                        'email',
                        array(
                            'label' => __t('Garage.Customer_email'),
                            'type' => 'text',
                            'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                            'required' => true
                        )
                    ); ?>
                </div>
                <div>
                    <?php echo $this->Form->input(
                    'date',
                    array(
                        'type' => 'text',
                        'required' => true,
                        'class' => 'fecha-js from-js',
                        'id' => 'review_date-js',
                        'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                        'data-to' => '#end_date',
                        'div' => array(
                            'class' => 'datepicker datepicker-label-block',
                        ),
                        'label' => __t('EmailGv.Booking_date'),
                    )
                ); ?>
                </div>
                <div class="jc-end">
                    <?php echo $this->Form->submit(__t('Review.Submit') , array('div' => false, 'class' => 'aag-button medium green','id' => 'btn-guardar', 'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true));
                    ?>
                </div>
            </div>
        </div>
    </div>
<?php echo $this->Form->end(); ?>
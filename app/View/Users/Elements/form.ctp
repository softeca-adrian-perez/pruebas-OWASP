<?php
echo $this->Html->css('../js/lib/cropper/cropper.css');
echo $this->Html->script('lib/cropper/cropper.js?v=' . Configure::read('VERSION_CACHE'));
echo $this->Html->script('/js/user_profile.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
$action = $this->request->action;
echo $this->Form->create('User', array('id' => 'form', 'enctype' => 'multipart/form-data'));
echo $this->Form->hidden('User.id');
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        if ($action == ConstantsActionsNames::ADD) {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('User.Users'),
                    array(
                        'controller' => 'users',
                        'action' => 'listing'
                    )
                ),
                __t('User.New'),
            ));
        } else {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('User.Users'),
                    array(
                        'controller' => 'users',
                        'action' => 'listing'
                    )
                ),
                __t('General.Edit'),
            ));
        }
        ?>
        <?php
        ?>
    </div>
    <div>
        <?php
        if ($is_my_data) {
            echo $this->Html->link(
                __t('General.Reset_password'),
                array(
                    'controller' => 'users',
                    'action' => 'reset_password',
                    $user['User']['guid']
                ),
                array('class' => 'aag-button medium two')
            );
        }
        ?>
    </div>
    <div>
        <?php echo $this->element('Comun/form_actions'); ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="aag-title">
        <?php
        if ($action == ConstantsActionsNames::ADD) {
            echo __t('User.New_user');
        } else {
            echo __t('User.Edit_user');
        }
        ?>
    </div>
    <div class="cnt-form-inputs m-top-1 m-bottom-1">
        <?php
        echo $this->Form->input(
            'User.username',
            array(
                'type' => 'text',
                'required' => true,
                'label' => __t('User.User'),
            )
        );
        echo $this->Form->input(
            'User.name',
            array(
                'type' => 'text',
                'required' => true,
                'readonly' => true,
                'label' => __t('User.Name'),
            )
        );
        echo $this->Form->input(
            'User.surname',
            array(
                'type' => 'text',
                'required' => true,
                'readonly' => true,
                'label' => __t('User.Surname'),
            )
        );
        echo $this->Form->input(
            'Contact.position_id',
            array(
                'type' => 'text',
                'required' => true,
                'readonly' => true,
                'value' => $position['Position']['name_' . __l()],
                'label' => __t('Contact.Position'),
            )
        );
        echo $this->Form->input(
            'Contact.role_id',
            array(
                'type' => 'text',
                'required' => true,
                'readonly' => true,
                'value' => $roles[$user['User']['role_id']],
                'label' => __t('General.Role'),
            )
        );
        echo $this->Form->input(
            'Contact.email',
            array(
                'type' => 'text',
                'required' => true,
                'readonly' => true,
                'label' => __t('Contact.Email'),
            )
        );
        echo $this->Form->input(
            'User.language_id',
            array(
                'label' => __t('User.Language'),
                'type' => 'select',
                'class' => 'select2-multiple',
                'multiple' => false,
                'required' => true,
                'default' => CakeSession::read('Auth.User.aag_region_id') == Configure::read('AAG_REGION_ID_UK_IRELAND') ? ConstantsLanguages::ENGLISH : null,
            )
        );
        echo $this->Form->input(
            'User.aag_region_id',
            array(
                'id' => 'region-select',
                'label' =>  __t('User.Region'),
                'type' => 'select',
                'class' => 'select2-multiple',
                'multiple' => false,
                'required' => true,
                'disabled' => $actual_user['role_id'] != ConstantsRoles::SUPER_ADMIN ? true : false,
                'empty' => true,
                'options' => $aag_regions,
                'value' => isset($user['User']) ? $user['User']['aag_region_id'] : $user['aag_region_id']
            )
        );
        if (isset($regions) && !empty($regions)) {
        ?>
            <label for="" class="c-primary"><?php echo __t('General.Regions'); ?></label>
        <?php echo $this->Form->input(
                'Contact.regions',
                array(
                    'label' => false,
                    'type' => 'select',
                    'class' => 'select2-multiple',
                    'multiple' => true,
                    'options' => $regions_list,
                    'required' => true,
                    'disabled' => true
                )
            );
        }

        $countrySelectDisabled = false;
        if ($contact['Contact']['position_id'] == ConstantsPositions::GARAGE_MANAGER_ID || $contact['Contact']['position_id'] == ConstantsPositions::DISTRIBUTOR_MANAGER_ID) {
            $countrySelectDisabled = true;
        }

        echo $this->Form->input(
            'User.country_id',
            array(
                'label' => __t('Garage.Country'),
                'type' => 'select',
                'class' => 'select2-multiple',
                'multiple' => false,
                'options' => $countries,
                'default' => !empty($user['User']['country_id']) ? $user['User']['country_id'] : ConstantsConfigSelect::ALL,
                'required' => true,
                'disabled' => $countrySelectDisabled
            )
        );
        ?>
    </div>
    <?php
    if (!$is_my_data) {
        if ($position['Position']['role_id'] == ConstantsRoles::BDM_AAG || $position['Position']['role_id'] == ConstantsRoles::BDM_TG) {
    ?>
            <div class="p-boton" id="garage_distributor_type">
            <?php
            echo $this->Form->input(
                'User.garage_type',
                array(
                    'label' => __t('Garage.Garage'),
                    'type' => 'checkbox',
                    'id' => 'garage_type'
                )
            );
            echo $this->Form->input(
                'User.distributor_type',
                array(
                    'label' => __t('Distributor.Distributor'),
                    'type' => 'checkbox',
                    'id' => 'distributor_type'
                )
            );
        }
            ?>
            <label class="center-check m-bottom-1">
                <?php echo __t('User.Active'); ?>
                <div class="aag-switch round small">
                    <?php echo $this->Form->input('User.active', array('id' => 'activeInput-5', 'label' => false, 'div' => false, 'type' => 'checkbox', 'default' => true)); ?>
                    <label for="activeInput-5"></label>
                </div>
            </label>
        <?php } else { ?>
            <div class="medium-6 columns p-0 end cnt-profile-user ta-center">
                <div>
                    <?php
                    echo $this->element('../Users/Elements/profile_image');
                    echo $this->Html->link(
                        '<span class="aag-icon-papelera c-fallo icono-grande"></span>',
                        'javascript:;',
                        array(
                            'class' => 'btn-delete-user-image-js',
                            'escape' => false,
                            'title' => __t('User.Delete_image'),
                        )
                    );
                    ?>
                </div>
            </div>
        <?php } ?>
            </div>
            <?php echo $this->Form->end(); ?>
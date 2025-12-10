<?php
echo $this->Html->script('/js/users.js?v='.Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Form->create('User');

echo $this->Form->hidden(
    'User.role_id'
);

?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('User.Users'),
                array(
                    'controller' => 'users',
                    'action' => 'listing'
                )
            ),
            __t('General.Add'),
        ));
        ?>
    </div>
    <div>
        <?php echo $this->element('Comun/form_actions', $cancel_action); ?>
    </div>
</div>
<div class="cnt-data p-top-1">
    <div class="cnt-data-element">
        <div class="aag-title">
            <?php echo __t('Contact.New_user_to_contact'); ?>
        </div>
        <div class="aag-subtitle m-top-1 m-bottom-1">
            <?php echo __t('User.User'); ?>
        </div>
        <div class="cnt-form-inputs m-bottom-1">
            <?php echo $this->Form->input(
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
                'Contact.Position',
                array(
                    'type' => 'text',
                    'value' => $positions[$contact['Contact']['position_id']],
                    'required' => true,
                    'readonly' => true,
                    'label' => __t('Contact.Position'),
                )
            );
            echo $this->Form->input(
                'Contact.Position',
                array(
                    'type'=>'hidden',
                    'value' => $contact['Contact']['position_id'],
                    'required' => false,
                    'id' => 'position'
                )
            );
            echo $this->Form->input(
                'User.language_id',
                array(
                    'label' => __t('User.Language'),
                    'type' => 'select',
                    'class' => 'select2-multiple',
                    'multiple' => false,
                    'options' => $languages,
                    'default' => CakeSession::read('Auth.User.aag_region_id') == Configure::read('AAG_REGION_ID_UK_IRELAND') ? ConstantsLanguages::ENGLISH : null,
                )
            );
            echo $this->Form->input(
                'User.country_id',
                array(
                    'id' => 'country-select',
                    'label' => __t('Garage.Country'),
                    'type' => 'select',
                    'class' => 'select2-multiple',
                    'multiple' => false,
                    'options' => $countries,
                    'empty' => false,
                    'disabled' => true,
                    'default' => $role_id == ConstantsRoles::SUPER_ADMIN ? ConstantsConfigSelect::ALL : $country_id,
                )
            );
            echo $this->Form->input(
                'User.aag_region_id',
                array(
                    'label' => __t('User.Region'),
                    'type' => 'select',
                    'class' => 'select2-multiple',
                    'multiple' => false,
                    'options' => $aag_regions,
                    'disabled' => $role_id == ConstantsRoles::SUPER_ADMIN ? false : true,
                )
            );
            ?>
            <label class="center-check">
                <?php echo __t('User.Active'); ?>
                <div class="aag-switch round small">
                    <?php echo $this->Form->input('User.active', array('id'=> 'activeInput-4', 'label' => false, 'div' => false, 'type' => 'checkbox', 'default' => true)); ?>
                    <?php // echo !empty($garage_network['GarageNetwork']['quoting_active']) ? 'checked' : '' ?>
                    <label for="activeInput-4"></label>
                </div>
            </label>
        </div>
    </div>
</div>
<?php echo $this->Form->end(); ?>
<?php
echo $this->Html->script('enable_edit_garage.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
$action = in_array($this->Acceso->rol(), array(ConstantsRoles::GARAGE,ConstantsRoles::DISTRIBUTOR))?'my_data':'view';
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Garage.Garages'),
                array(
                    'controller' => 'garages',
                    'action' => 'home'
                )
            ),
            $this->Html->link(
                __t('Network.Distributor_link'),
                array(
                    'controller' => 'garages',
                    'action' => 'add_dis_and_net_garage',
                    $garage['Garage']['id']
                )
            ),
            CakeSession::read('Auth.User.edit_enabled') ? __t('General.Edit') : __t('General.View'),
        ));
		echo $this->Form->create('GarageAgreement', array('class' => 'form-horizontal','enctype' => 'multipart/form-data'));
		echo $this->Form->hidden('GarageAgreement.id', array('value' => $agreement['GarageAgreement']['id'],));
        ?>
    </div>
    <div>
        <?php 
        echo $this->element(
            'Comun/form_actions_garage',
            $cancel_action
        ); ?>
        <button type="button" id="edit-btn-disable" value="1" class="aag-button medium"
        data-url="<?php echo Router::url(
            array(
                'controller' => 'garages',
                'action' => 'ajax_update_edit',
            )); ?>"
            data-edit="<?php echo __t('General.Edit'); ?>"
            data-view="<?php echo __t('General.View'); ?>"
            data-edit_enabled="<?php echo CakeSession::read('Auth.User.edit_enabled'); ?>">
            <?php echo __t('General.Edit'); ?>
        </button>
    </div>
</div>
<?php
$action = $this->request->action;
?>
<div class="cnt-data aag-padding">
    <div class="aag-title">
        <?php echo $agreement['GarageAgreement']['fleet_agreement'] ?>
    </div>

    <div class="cnt-form-inputs p-top-1">
        <?php 
        echo $this->Form->input(
            'GarageAgreement.agreement_code',
            array(
                'type' => 'text',
                'label' => __t('General.Cod'),
                'value' => $agreement['GarageAgreement']['agreement_code'],
                'disabled' => true,
            )
        );
        echo $this->Form->input(
            'GarageAgreement.fleet_reference',
            array(
                'type' => 'text',
                'label' => __t('General.Fleet_reference'),
                'value' => $agreement['GarageAgreement']['fleet_reference'],
                'disabled' => true,
                'class' => 'input-disabled',
            )
        );
        echo $this->Form->input(
            'GarageAgreement.parent_acct',
            array(
                'type' => 'text',
                'label' => __t('General.Parent_acct'),
                'value' => $agreement['GarageAgreement']['parent_acct'],
                'disabled' => true,
            )
        );
        echo $this->Form->input(
            'GarageAgreement.garage_ref',
            array(
                'type' => 'text',
                'label' => __t('General.Garage_ref'),
                'value' => $agreement['GarageAgreement']['garage_ref'],
                'disabled' => true,
                'class' => 'input-disabled',
            )
        ); ?>
    </div>
</div>
<?php echo $this->Form->end(); ?>
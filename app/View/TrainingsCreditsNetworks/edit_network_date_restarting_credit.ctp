<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Training.Trainings_credits'),
                array(
                    'controller' => 'trainings_credits_networks',
                    'action' => 'home'
                )
            ),
            __t('General.Edit_network_credit'),
        ));
        ?>
    </div>
    <div>
        <?php 
        echo $this->element(
            'Comun/form_actions',
            $cancel_action
        ); 
        ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="aag-title">
        <?php echo __t('Training.Edit_credits_network'); ?>
    </div>
    <?php echo $this->element('../TrainingsCreditsNetworks/Elements/form_network_date_restarting_credit'); ?>
</div>
<?php $action = in_array($this->Acceso->rol(), array(ConstantsRoles::GARAGE,ConstantsRoles::DISTRIBUTOR))?'my_data':'view'; ?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Distributor.Distributors'),
                array(
                    'controller' => 'distributors',
                    'action' => 'home'
                )
            ),
            $this->Html->link(
                $distributor['Distributor']['name'],
                array(
                    'controller' => 'distributors',
                    'action' => $action,
                    $distributor['Distributor']['id']
                )
            ),
            __t('Network.Networks'),
        ));
        ?>
    </div>
    <div>
        <?php 
        echo $this->Html->link(
            __t('General.Back'),
            CakeSession::read('url_referer'),
            array(
            'class' => 'aag-button medium four',
        )
        ); 
        echo $this->Html->link(
            __t('General.Next'),
            array(
                'controller' => 'distributors',
                'action' => 'add_opening_distributor',
                $distributor_id
            ),
            array(
                'class' => 'aag-button medium two'
            )
        ); ?>
    </div>
</div>
<?php echo $this->element('../Distributors/tabs',array('selected' => 'network_distributor',)); ?>
<div class="cnt-data aag-padding">
    <div class="flex ai-center gap-1">
        <div class="aag-title m-right-auto">
            <?php echo h($distributor['Distributor']['name']); ?>
        </div>
        <?php if($this->Acceso->haveTradingGroupPermission( ConstantsPermissionsGrouping::EDIT_DISTRIBUTOR , $distributor['Distributor']['trading_group_id'] )){ ?>
                <div>
                    <?php
                        echo $this->Html->link(
                            __t('Network.New_network'),
                            array(
                                'controller' => 'distributors_distributors_networks',
                                'action' => 'add',
                                $distributor_id
                            ),
                            array(
                                'escape' => false,
                                'class' => 'aag-button medium green m-top-1',
                            )
                        );
                    ?>
                </div>
            <?php } ?>
    </div>
    <?php echo $this->element('../Distributors/Elements/form_networks_distributor'); ?>
</div>

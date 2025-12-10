<?php
echo $this->Html->script('distributor_contracts.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
$action = $this->request->action;
echo $this->Form->create(
    'DistributorContract',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data'
    )
);
echo $this->Form->hidden('DistributorContract.id');
echo $this->Form->hidden('DistributorContract.distributor_id');
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        if ($action == ConstantsActionsNames::ADD) {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Distributor.Distributors'),
                    array(
                        'controller' => 'distributors',
                        'action' => 'home'
                    )
                ),
                $this->Html->link(
                    __t('Distributor.Contracts'),
                    array(
                        'controller' => 'distributors',
                        'action' => 'add_contract',
                        $distributor_id
                    )
                ),
                __t('Distributor.Add_contract'),
            ));
        } else {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Distributor.Distributors'),
                    array(
                        'controller' => 'distributors',
                        'action' => 'home'
                    )
                ),
                $this->Html->link(
                    __t('Distributor.Contracts'),
                    array(
                        'controller' => 'distributors',
                        'action' => 'add_contract',
                        $distributor_id
                    )
                ),
                __t('Maintenance.Contract_edit'),
            ));
        }
        ?>
    </div>
    <div>
        <?php echo $this->element('Comun/form_actions', $cancel_action); ?>
    </div>
</div>
<div class="cnt-data p-top-1 p-bottom-1">
    <div class="cnt-data-element">
        <div class="aag-title m-bottom-1">
            <?php
            if ($action == ConstantsActionsNames::ADD) {
                echo __t('Distributor.Add_contract');
            } else {
                echo __t('Maintenance.Contract_edit');
            }
            ?>
        </div>
        <div class="cnt-form-inputs">
            <?php
            echo $this->Form->input(
                'DistributorContract.trading_group_id',
                array(
                    'label' => __t('Distributor.Trading_group'),
                    'type' => 'select',
                    'class' => 'select2-multiple',
                    'id' => 'trading_group_id',
                    'options' => $trading_groups,
                    'required' => true,
                    'empty' => true,
                    'data-url' => Router::url(array(
                        'controller' => 'distributors_contracts',
                        'action' => 'ajax_distributor_networks'
                    ))
                )
            ); ?>
            <div id="distributor_network">
                <?php echo $this->Form->input(
                    'DistributorContract.network_id',
                    array(
                        'label' => __t('Network.Network'),
                        'type' => 'select',
                        'class' => 'select2-multiple',
                        'options' => $action == ConstantsActionsNames::ADD ? $network_options : $distributor_networks,
                        'default' => key($network_options),
                        'required' => true,
                        'empty' => true,
                        'disabled' => false
                    )
                ); ?>
            </div>
            <?php
            echo $this->Form->input(
                'DistributorContract.start_date',
                array(
                    'required' => true,
                    'type' => 'text',
                    'class' => 'fecha-js from-js',
                    'data-to' => '#to',
                    'id' => 'from',
                    'label' => __t('Distributor.Start_date'),
                )
            );
            echo $this->Form->input(
                'DistributorContract.end_date',
                array(
                    'required' => true,
                    'type' => 'text',
                    'class' => 'fecha-js to-js',
                    'data-from' => '#from',
                    'id' => 'to',
                    'label' => __t('Distributor.End_date'),
                )
            );
            echo $this->Form->input(
                'DistributorContract.leaving_reason_id',
                array(
                    'label' => __t('Distributor.Leaving_reason'),
                    'required' => true,
                    'type' => 'select',
                    'empty' => true,
                    'class' => 'select2-multiple',
                    'options' => $leaving_reasons
                )
            );
            ?>
        </div>
    </div>
</div>

<?php echo $this->Form->end(); ?>
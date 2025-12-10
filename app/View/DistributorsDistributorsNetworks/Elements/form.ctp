<?php
$action = $this->request->action;
echo $this->Html->script('distributors_distributors_networks.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Form->create(
    'DistributorDistributorNetwork',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data',
        'id' => 'form-distributors-distributors-networks'
    )
);
echo $this->Form->hidden('DistributorDistributorNetwork.id');
echo $this->Form->hidden('DistributorDistributorNetwork.distributor_id');
?>

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
                __t('Network.Networks'),
                array(
                    'controller' => 'distributors',
                    'action' => 'add_networks_distributor',
                    $distributor_id
                )
            ),
            $action == ConstantsActionsNames::ADD ? __t('Network.New_network') : __t('Network.Edit_network'),
        ));
        ?>
    </div>
    <div>
        <?php echo $this->element('Comun/form_actions', $cancel_action); ?>
    </div>
</div>

<div class="cnt-data aag-padding">
    <div class="aag-title">
        <?php
        echo $action == ConstantsActionsNames::ADD ? __t('Network.New_network') : __t('Network.Edit_network');
        ?>
    </div>
    <div class="cnt-form-inputs m-top-1">
        <?php
        echo $this->Form->input(
            'network_id',
            array(
                'label' => __t('Network.Network'),
                'type' => 'select',
                'class' => 'select2-multiple select-network-js',
                'disabled' => $action == ConstantsActionsNames::EDIT,
                'empty' => true,
                'options' => $networks,
                'data-url' => Router::url(array(
                    'controller' => 'distributors_distributors_networks',
                    'action' => 'ajax_load_trading_groups',
                )),
                'data-div_trading_groups' => '#div_trading_groups',
                'data-trading_group_field_name' => 'trading_group_id',
            )
        );
        if ($action == ConstantsActionsNames::EDIT) {
            echo $this->Form->hidden(
                'network_id',
                array(
                    'value' => $distributor_network['DistributorDistributorNetwork']['network_id']
                )
            );
        }
        ?>
        <div id="div_trading_groups">
            <?php echo $this->element(
                '../DistributorsDistributorsNetworks/Elements/ajax_load_trading_groups',
                array(
                    'trading_group_field_name' => 'trading_group_id',
                    'trading_group_list' => $trading_groups
                )
            ); ?>
        </div>
        <?php
        echo $this->Form->input(
            'status',
            array(
                'id' => 'select-status',
                'label' => __t('Network.Status'),
                'class' => 'select2-multiple',
                'type' => 'select',
                'multiple' => false,
                'empty' => true,
                'options' => $networks_statuses,
            )
        );
        echo $this->Form->input(
            'contract_start_date',
            array(
                'class' => 'fecha-js from-js',
                'id' => 'contract_start_date',
                'data-to' => '#contract_end_date',
                'type' => 'text',
                'required' => true,
                'div' => array(
                    'class' => 'datepicker datepicker-label-block',
                ),
                'label' => __t('Network.Start_date'),
            )
        );
        echo $this->Form->input(
            'contract_end_date',
            array(
                'class' => 'fecha-js to-js',
                'id' => 'contract_end_date',
                'data-from' => '#contract_start_date',
                'type' => 'text',
                'required' => true,
                'div' => array(
                    'class' => 'datepicker datepicker-label-block',
                ),
                'label' => __t('Network.End_date'),
            )
        );
        $style = isset($distributor_network) && in_array($distributor_network['DistributorDistributorNetwork']['status'], array(7, 8)) ? 'display:inline' : 'display:none';
        ?>
        <div id="reason-leaving" class="required" style="<?php echo $style; ?>">
            <?php
            echo $this->Form->input(
                'reason_leaving_id',
                array(
                    'id' => 'select-reason-leaving',
                    'label' => __t('Distributor.Leaving_reason'),
                    'type' => 'select',
                    'class' => 'select2-multiple',
                    'options' => $reasons_leaving,
                    'empty' => true,
                    'required' => true
                )
            );
            ?>
        </div>
    </div>
</div>
<?php echo $this->Form->end(); ?>
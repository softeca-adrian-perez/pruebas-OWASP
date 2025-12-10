<?php echo $this->Html->script('routes_list.js?v='.Configure::read('VERSION_CACHE'), array('block' => 'script')); ?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('CRM.Crm'),
                array(
                    'controller' => 'dashboard',
                    'action' => 'home'
                )
            ),
            __t('CRM.Planning_visits'),
            __t('Visit.Routes_lists'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back')?></a>
        <?php
        if( CakeSession::read('Auth.User.role_id') != ConstantsRoles::GPC_LOGISTICS_BDM ) {
            echo $this->Html->link(
                '<span class="icon-garages"></span>' . __t('Garage.Garages'),
                array(
                    'controller' => 'visits',
                    'action' => 'home'
                ),
                array(
                    'escape' => false,
                    'class' => 'aag-button medium one'
                )
            );
        }
        echo $this->Html->link(
            '<span class="icon-distributors"></span>' . __t('Distributor.Distributors'),
            array(
                'controller' => 'visits',
                'action' => 'home_distributor'
            ),
            array(
                'escape' => false,
                'class' => 'aag-button medium one'
            )
        );
        ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="aag-title p-bottom-1">
        <?php echo __t('Visit.Routes_lists'); ?>
    </div>
        <?php echo $this->element('../Visits/Elements/search_routes_list'); ?>
        <div class="row " id="cnt-routes-lists">
            <?php echo $this->element('../Visits/Elements/ajax_routes_list');?>
        </div>
    </div>
</div>
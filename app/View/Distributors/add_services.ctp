<?php $action = in_array($this->Acceso->rol(), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR)) ? 'my_data' : 'view'; ?>
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
            __t('Distributor.Aag_services'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back') ?></a>
        <?php
        echo $this->Html->link(
            __t('General.Next'),
            array(
                'controller' => 'distributors',
                'action' => 'add_label',
                $distributor_id
            ),
            array(
                'class' => 'aag-button medium'
            )
        ); ?>
    </div>
</div>
<?php echo $this->element('../Distributors/tabs', array('selected' => 'services_distributor',)); ?>
<div class="cnt-data aag-padding">
    <div class="aag-title">
        <?php echo h($distributor['Distributor']['name']); ?>
    </div>
    <?php echo $this->element('../Distributors/Elements/form_services'); ?>
</div>
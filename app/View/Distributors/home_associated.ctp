<?php
$action = in_array($this->Acceso->rol(), array(ConstantsRoles::GARAGE,ConstantsRoles::DISTRIBUTOR))?'my_data':'view';
$config = CakeSession::read('Auth.User.Config');
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
                $distributor['Distributor']['name'],
                array(
                    'controller' => 'distributors',
                    'action' => $action,
                    $distributor['Distributor']['id']
                )
            ),
            __t('Distributor.Garages_associated'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium four go-back-js"><?php echo __t('General.Back')?></a>
        <?php
        echo $this->Html->link(
            __t('General.Next'),
            array(
                'controller' => 'distributors',
                'action' => 'home_logs_changes',
                $distributor_id
            ),
            array(
                'class' => 'aag-button medium two'
            )
        ); ?>
    </div>
</div>
<?php echo $this->element('../Distributors/tabs', array('selected' => 'associated',));?>
<div class="cnt-data p-top-1 p-bottom-1">
    <div class="cnt-data-element">
        <div class="aag-title m-bottom-1">
            <?php echo $distributor['Distributor']['name']; ?>
        </div>
        <div class="abrir-buscador-movil">
            <div class="titulo2">
                <?php echo __t('General.Search'); ?>
            </div>
        </div>
        <div class="clear cnt-form-animate p-bottom-1 buscador-mo-movil">
            <?php echo $this->element('../Distributors/Elements/search_associated'); ?>
        </div>
        <div class="o-auto">
            <table class="table-tracking table-responsive">
                <thead>
                <tr>
                    <th><?php echo $this->Paginator->sort('Garage.name', __t('Garage.Name')); ?></th>
                    <?php if($config[ConstantsConfig::COUNTY_COUNTRY_GARAGE]){ ?>
                        <th><?php echo __t('Garage.County'); ?></th>
                    <?php } ?>
                    <th><?php echo $this->Paginator->sort('Garage.town', __t('Garage.Town')); ?></th>
                    <th><?php echo $this->Paginator->sort('Garage.phone', __t('Garage.Phone')); ?></th>
                    <?php
                    //If it's Benelux there is not garage g_number, but if the role is Super Admin, garages from both regions can be present at the same time
                    if ((isset($user_aag_region_id) && ($user_aag_region_id != ConstantsAAGRegionId::BENELUX)) || ($user_role == ConstantsRoles::SUPER_ADMIN)) { ?>
                        <th><?php echo __t('Garage.G_number'); ?></th>
                    <?php } ?>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($distributor_garages as $garage) { ?>
                <tr>
                    <td>
                        <?php
                        if($this->Acceso->havePermission(ConstantsPermissionsGrouping::VIEW_GARAGE)) {
                            echo $this->Html->link(
                                $garage['Garage']['name'],
                                array(
                                    'controller' => 'garages',
                                    'action' => 'view',
                                    $garage['Garage']['id']
                                ),
                                array(
                                    'class' => 'c-primary'
                                )
                            );
                        } else {
                            echo h($garage['Garage']['name']);
                        }
                        ?>
                    </td>
                    <?php if($config[ConstantsConfig::COUNTY_COUNTRY_GARAGE]){ ?>
                        <td>
                            <?php
                            if ($garage['Garage']['province_id']) {
                                echo h($province_list[$garage['Garage']['province_id']]);
                            }
                            ?>
                        </td>
                    <?php } ?>
                    <td>
                        <?php echo h($garage['Garage']['town']); ?>
                    </td>
                    <td>
                        <?php echo h($garage['Garage']['phone']); ?>
                    </td>
                    <?php
                    if ((isset($user_aag_region_id) && ($user_aag_region_id != ConstantsAAGRegionId::BENELUX)) || ($user_role == ConstantsRoles::SUPER_ADMIN)) { ?>
                        <td>
                        <?php
                        if (isset($garage['Garage']['g_number_id'])) {
                            echo h($garage['Garage']['g_number_id']);
                        }
                        ?>
                        </td>
                    <?php } ?>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
        <br>
        <?php echo $this->element('Comun/paginacion'); ?>
    </div>
</div>

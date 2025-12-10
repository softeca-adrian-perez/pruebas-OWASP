<?php
$config = CakeSession::read('Auth.User.Config');
if (!empty($distributor_garages)) {
?>
    <div class="o-auto">
        <table class="table-tracking table-responsive z-index-priority">
            <thead>
                <tr>
                    <?php
                    //If it's Benelux there is not garage g_number, but if the role is Super Admin, garages from both regions can be present at the same time
                    if ((isset($user_aag_region_id) && ($user_aag_region_id != ConstantsAAGRegionId::BENELUX)) || ($user_role == ConstantsRoles::SUPER_ADMIN)) { ?>
                        <th><?php echo $this->Paginator->sort('Garage.g_number_id', __t('Garage.G_number')); ?></th>
                    <?php } ?>
                    <th><?php echo $this->Paginator->sort('Garage.name', __t('Garage.Name')); ?></th>
                    <?php if ($config[ConstantsConfig::COUNTY_COUNTRY_DISTRIBUTOR]) { ?>
                        <th><?php $this->Paginator->sort('Garage.county', __t('Garage.County')); ?></th>
                    <?php } ?>
                    <th><?php echo $this->Paginator->sort('Garage.town', __t('Garage.Town')); ?></th>
                    <th><?php echo $this->Paginator->sort('Garage.phone', __t('Garage.Phone')); ?></th>
                    <th><?php echo $this->Paginator->sort('GarageNetwork.status', __t('GarageNetwork.Garage_network')); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($distributor_garages as $garage) { ?>
                    <tr>
                        <?php
                        if ((isset($user_aag_region_id) && ($user_aag_region_id != ConstantsAAGRegionId::BENELUX)) || ($user_role == ConstantsRoles::SUPER_ADMIN)) { ?>
                            <td>
                                <?php
                                if ($this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE)) {
                                    if (isset($garage['Garage']['g_number_id'])) {
                                        echo $this->Html->link(
                                            $garage['Garage']['g_number_id'],
                                            array(
                                                'controller' => 'garages',
                                                'action' => 'view',
                                                $garage['Garage']['id']

                                            ),
                                            array(
                                                'class' => 'c-primary'
                                            )
                                        );
                                    }
                                } else {
                                    echo h($garage['Garage']['g_number_id']);
                                }
                                ?>
                            </td>
                        <?php } ?>
                        <td>
                            <?php
                            if ($this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE)) {
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
                        <?php if ($config[ConstantsConfig::COUNTY_COUNTRY_DISTRIBUTOR]) { ?>
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
                        <td>
                            <?php
                            foreach ($garage['GarageNetwork'] as $garage_network) {
                            ?>
                                <div class="d-inline-block ta-center end p-right-1 item-logo m-top-1">
                                    <div>
                                        <img style="height: 40px !important" class="logotipo" title="<?php echo h($garage_network['Network']['name']); ?>" src="<?php echo FileManager::get_url(FilePaths::NETWORKS_IMAGES_RELATIVE . $garage_network['Network']['image']); ?>" />
                                    </div>
                                    <div>
                                        <?php
                                        if ($garage_network['GarageNetwork']['status'] == ConstantsNetworksStatus::UNSUBSCRIBE || $garage_network['GarageNetwork']['status'] == ConstantsNetworksStatus::NOT_CONVERTED) {
                                            $class = 'c-fallo';
                                        } elseif ($garage_network['GarageNetwork']['status'] == ConstantsNetworksStatus::LIVE) {
                                            $class = 'c-exito';
                                        } else {
                                            $class = 'c-informacion';
                                        }
                                        ?>
                                        <strong class="<?php echo $class; ?>">
                                            <?php
                                            $msg = isset($networks_statuses[$garage_network['GarageNetwork']['status']]) ? h($networks_statuses[$garage_network['GarageNetwork']['status']]) : '';
                                            echo $msg;
                                            ?>
                                        </strong>
                                    </div>
                                </div>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
<?php }
if ($this->request->controller == 'distributors') {
    echo $this->element('Comun/paginacion_ajax_distributors_garages_associated');
} elseif ($this->request->controller == 'clients') {
    echo $this->element('Comun/paginator_ajax_distributors_garages_associated_crm');
}
?>
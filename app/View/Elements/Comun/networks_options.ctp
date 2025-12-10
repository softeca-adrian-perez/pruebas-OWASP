<?php
$current_network_id = CakeSession::read('Auth.User.current_network');
$Network = ClassRegistry::init('Network');
$current_network = $Network->findById($current_network_id);
$aag_region_id =  CakeSession::read('Auth.User.aag_region_id');
$networksInAagRegion = $Network->findAllByAagRegionId($aag_region_id);

if (isset($current_network['Network']['aag_region_id']) && !empty($aag_region_id) && ($current_network['Network']['aag_region_id'] != $aag_region_id)) {
    $current_network = $networksInAagRegion[0];
    CakeSession::write('Auth.User.current_network', $networksInAagRegion[0]['Network']['id']); // T001 SECURITY - It is not changed
}

if (is_array($networksInAagRegion) && count($networksInAagRegion) > 1 && CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN) {
?>
    <ul id="header-networks" class="left menu-usuario">
        <li class="has-dropdown cnt-networks">
            <div>
                <?php
                echo $this->Html->image(
                    FileManager::get_url(FilePaths::NETWORKS_IMAGES_RELATIVE . $current_network['Network']['image']),
                    array(
                        'alt' => $current_network['Network']['name'],
                        'class' => 'logotipo_appactive',
                        'title' => $current_network['Network']['name']
                    )
                );
                echo $current_network['Network']['name'];
                echo '<i class="ion-ios-arrow-down"></i>';
                ?>
            </div>
            <ul>
                <?php foreach ($networksInAagRegion as $network) { ?>
                    <li <?php if ($current_network['Network']['id'] == $network['Network']['id']) {
                            echo 'class="active"';
                        } ?>>
                        <?php
                        echo $this->Html->link(
                            $network['Network']['name'],
                            array(
                                'plugin' => false,
                                'controller' => 'paginas',
                                'action' => 'home',
                                $network['Network']['id'],
                                CakeSession::read('Auth.User.aag_region_id'),
                                CakeSession::read('Auth.User.current_country'),
                            )
                        );
                        ?>
                    </li>
                <?php } ?>
            </ul>
        </li>
    </ul>
<?php } ?>
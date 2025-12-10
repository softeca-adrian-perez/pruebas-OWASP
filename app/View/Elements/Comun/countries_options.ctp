<?php
$region_id = CakeSession::read('Auth.User.aag_region_id');
$Country = ClassRegistry::init('Country');
$AagRegion = ClassRegistry::init('AagRegion');
$country_id = CakeSession::read('Auth.User.current_country');
$regions = $AagRegion->find('all');

$current_country = $Country->findById($country_id);

if (isset($current_country['Country']['aag_region_id']) && isset($region_id) && ($current_country['Country']['aag_region_id'] != $region_id)) {
    $result = $Country->findAllByAagRegionId($region_id);
    $current_country = $result[0];
    CakeSession::write('Auth.User.current_country', $result[0]['Country']['id']); // T001 SECURITY - It is not changed
}
?>
<ul id="header-countrys" class="left menu-usuario">
    <li class="has-dropdown cnt-countrys">
        <div>
            <?php
            echo $this->Html->image(
                FilePaths::COUNTRIES_IMAGES_RELATIVE . $current_country['Country']['image'],
                array(
                    'alt' => $current_country['Country']['name'],
                    'title' => $current_country['Country']['name']
                )
            );
            if ($current_country['Country']['name'] != '') {
                echo $current_country['Country']['name'];
            } else {
                echo __t('Menu.Country');
            }
            echo '<i class="ion-ios-arrow-down"></i>';
            ?>
        </div>
        <ul>
            <?php
            $countries = $Country->find(
                'all',
                array(
                    'conditions' => array(
                        'aag_region_id ' => $region_id
                    )
                )
            );
            foreach ($countries as $country) {
            ?>
                <li>
                    <?php
                    echo $this->Html->link(
                        $country['Country']['name'],
                        array(
                            'plugin' => false,
                            'controller' => 'paginas',
                            'action' => 'home',
                            CakeSession::read('Auth.User.current_network'),
                            CakeSession::read('Auth.User.aag_region_id'),
                            $country['Country']['id'],
                        )
                    );
                    ?>
                </li>
            <?php } ?>
        </ul>
    </li>
</ul>
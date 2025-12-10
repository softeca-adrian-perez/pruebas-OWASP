<ul id="header-regions" class="left menu-usuario">
    <li class="has-dropdown cnt-regions">
        <div>
            <?php
            $AagRegion = ClassRegistry::init('AagRegion');
            $regions = $AagRegion->find('all');
            $region_id = CakeSession::read('Auth.User.aag_region_id');
            if (!isset($region_id)) {
                $region_id = $regions[0]['AagRegion']['id'];
                CakeSession::write('Auth.User.aag_region_id', $region_id); // T001 SECURITY - It is not changed
            }
            $current_region = $AagRegion->findById($region_id);
            echo $this->Html->image(
                FilePaths::REGIONS_IMAGES_RELATIVE . $current_region['AagRegion']['image'],
                array(
                    'alt' => $current_region['AagRegion']['name'],
                    'class' => 'logotipo_appactive',
                    'title' => $current_region['AagRegion']['name']
                )
            );
            echo !empty($current_region['AagRegion']['name']) ? $current_region['AagRegion']['name'] : __t('Menu.Region');
            ?>
            <i class="ion-ios-arrow-down"></i>
        </div>
        <ul>
            <?php foreach ($regions as $region) { ?>
                <li>
                    <?php
                    echo $this->Html->link(
                        $region['AagRegion']['name'],
                        array(
                            'plugin' => false,
                            'controller' => 'paginas',
                            'action' => 'home',
                            CakeSession::read('Auth.User.current_network'),
                            $region['AagRegion']['id'],
                            CakeSession::read('Auth.User.current_country')
                        )
                    );
                    ?>
                </li>
            <?php
            }
            ?>
        </ul>
    </li>
</ul>
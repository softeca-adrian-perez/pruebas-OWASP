<ul id="header-user" class="left menu-usuario">
    <li class="has-dropdown">
        <div class="cnt-user">
            <?php
            if (CakeSession::read('Auth.User.img') == '') {
                // <img src="<?php echo ConstantsPath::ADD_DEFAULT_IMAGE "/>
            ?>
                <svg xmlns="http://www.w3.org/2000/svg" width="34" height="34" viewBox="0 0 34 34">
                    <g transform="translate(0.5 0.5)">
                        <path d="M26.125,0H6.875A6.883,6.883,0,0,0,0,6.875v19.25A6.883,6.883,0,0,0,6.875,33h19.25A6.883,6.883,0,0,0,33,26.125V6.875A6.883,6.883,0,0,0,26.125,0ZM9.625,30.25V28.875a6.875,6.875,0,0,1,13.75,0V30.25ZM30.25,26.125a4.125,4.125,0,0,1-4.125,4.125V28.875a9.625,9.625,0,0,0-19.25,0V30.25A4.125,4.125,0,0,1,2.75,26.125V6.875A4.125,4.125,0,0,1,6.875,2.75h19.25A4.125,4.125,0,0,1,30.25,6.875Z" fill="var(--primary-color)" stroke="var(--container-elements-color)" stroke-width="1" />
                        <path d="M13.5,4A5.5,5.5,0,1,0,19,9.5,5.5,5.5,0,0,0,13.5,4Zm0,8.25A2.75,2.75,0,1,1,16.25,9.5,2.75,2.75,0,0,1,13.5,12.25Z" transform="translate(3 1.5)" fill="var(--primary-color)" stroke="var(--container-elements-color)" stroke-width="1" />
                    </g>
                </svg>
            <?php
            } else {
                $this->UserImage = ClassRegistry::init('UserImage');
                $file = $this->UserImage->findById(CakeSession::read('Auth.User.img')); ?>
                <img id="img_cabecera_user" src="<?php echo FileManager::get_url(ConstantsPath::DIR_USER_IMAGES_CROP . '/' . $file['UserImage']['file']) ?>" />
            <?php
            }
            if (CakeSession::read('Auth.User.garage_id')) {
                $this->Garage = ClassRegistry::init('Garage');
                $garage = $this->Garage->findById(CakeSession::read('Auth.User.garage_id'));
                echo $this->Html->link('<div>' . $user['name'] . ' ' . $user['surname'] . '<div>' . $user['position'] . '</div><span>' . $garage['Garage']['name'] . '</span></div><i class="ion-ios-arrow-down"></i>', '#', array('escape' => false, 'class' => 'name_customer'));
            } elseif (CakeSession::read('Auth.User.distributor_id')) {
                $this->Distributor = ClassRegistry::init('Distributor');
                $distributor = $this->Distributor->findById(CakeSession::read('Auth.User.distributor_id'));
                $distributor['Distributor']['name'];
                echo $this->Html->link('<div>' . $user['name'] . ' ' . $user['surname'] . '<div>' . $user['position'] . '</div><span>' . $distributor['Distributor']['name'] . '</span></div><i class="ion-ios-arrow-down"></i>', '#', array('escape' => false, 'class' => 'name_customer'));
            } else {
                echo $this->Html->link('<div>' . $user['name'] . ' ' . $user['surname'] . '<div>' . $user['position'] . '</div></div><i class="ion-ios-arrow-down"></i>', '#', array('escape' => false));
            }
            ?>
        </div>
        <ul class="dropdown">
            <li>
                <?php echo $this->Html->link(
                    __t('Menu.User_guides'),
                    array(
                        'plugin' => false,
                        'controller' => 'user_guides',
                        'action' => 'home',
                    )
                ); ?>
            </li>
            <li>
                <?php echo $this->Html->link(
                    __t('Menu.My_account'),
                    array(
                        'plugin' => false,
                        'controller' => 'users',
                        'action' => 'my_data',
                    )
                ); ?>
            </li>
            <li>
                <?php echo $this->Html->link(
                    __t('Menu.My_preferences'),
                    array(
                        'plugin' => false,
                        'controller' => 'users',
                        'action' => 'my_preferences',
                    )
                ); ?>
            </li>
            <li>
                <?php echo $this->Html->link(
                    __t('Menu.Logout'),
                    array(
                        'plugin' => false,
                        'controller' => 'users',
                        'action' => 'logout',
                    )
                ); ?>
            </li>
        </ul>
    </li>
</ul>
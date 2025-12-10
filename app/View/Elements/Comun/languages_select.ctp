<?php $controller = $this->request->controller; ?>
<ul id="header-languages" class="left menu-usuario">
    <li class="has-dropdown">
        <div>
            <?php
            foreach ($this->Acceso->getLanguagesCodeName() as $language_code => $language_name) {
                if ($language_code == __l()) {
                    echo $language_name . '<i class="ion-ios-arrow-down"></i>';
                }
            }
            ?>
        </div>
        <ul>
            <?php
            foreach ($this->Acceso->getLanguagesCodeName() as $language_code => $language_name) {
                if (
                    ($language_code != 'lc' || ($language_code == 'lc' && $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::TRANSLATIONS))) ||
                    CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN
                ) {
            ?>
                    <li <?php echo ($language_code == __l()) ? 'class="active"' : '' ?>>
                        <?php
                        echo $this->Html->link(
                            $language_name,
                            array(
                                'plugin' => false,
                                'controller' => 'languages',
                                'action' => 'change',
                                $language_code
                            )
                        );
                        ?>
                    </li>
            <?php
                }
            }
            ?>
        </ul>
    </li>
</ul>
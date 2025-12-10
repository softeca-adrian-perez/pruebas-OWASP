<?php echo $this->Html->script('section.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script')); ?>
<div class="cnt-bullets-dashboard">
    <?php
    $page_1 = '';
    if ($active_page == ConstantsActiveHomePage::PAGE_1) {
        $page_1 = 'active';
    }
    $page_2 = '';
    if ($active_page == ConstantsActiveHomePage::PAGE_2) {
        $page_2 = 'active';
    }
    $page_3 = '';
    if ($active_page == ConstantsActiveHomePage::PAGE_3) {
        $page_3 = 'active';
    }
    $page_4 = '';
    if ($active_page == ConstantsActiveHomePage::PAGE_4) {
        $page_4 = 'active';
    }
    $page_5 = '';
    if ($active_page == ConstantsActiveHomePage::PAGE_5) {
        $page_5 = 'active';
    }
    $page_6 = '';
    if ($active_page == ConstantsActiveHomePage::PAGE_6) {
        $page_6 = 'active';
    }
    if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN) {
        echo $this->Html->link(
            '<span class="aag-icon-casa"></span>',
            array(
                'plugin' => false,
                'controller' => 'home',
                'action' => 'home_dashboard',
            ),
            array(
                'id' => 'home_page1',
                'title' => __t('General.Home'),
                'class' => "bullet $page_1",
                'escape' => false
            )
        );
    } else {
        echo $this->Html->link(
            '<span class="aag-icon-casa"></span>',
            array(
                'plugin' => false,
                'controller' => 'home',
                'action' => 'home',
            ),
            array(
                'id' => 'home_page1',
                'title' => __t('General.Home'),
                'class' => "bullet $page_1",
                'escape' => false
            )
        );
    }
    if ($this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_COMMUNICATIONS)) {
        echo $this->Html->link(
            '<span class="aag-icon-megafono"></span>',
            array(
                'plugin' => false,
                'controller' => 'home',
                'action' => 'home_page2',
            ),
            array(
                'id' => 'home_page2',
                'title' => __t('Maintenance.Communications'),
                'class' => "bullet $page_2",
                'escape' => false
            )
        );
    }
    //NETWORKS MAP + WIDGETS
    if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN) {
        echo $this->Html->link(
            '<span class="aag-icon-calendario-boli"></span>',
            array(
                'plugin' => 'panel',
                'controller' => 'widgets',
                'action' => 'index'
            ),
            array(
                'id' => 'home_page5',
                'class' => "bullet $page_5",
                'escape' => false,
            )
        );
        // STATISTICS
        echo $this->Html->link(
            '<span class="aag-icon-grafica"></span>',
            array(
                'plugin' => false,
                'controller' => 'home',
                'action' => 'widget_general',
            ),
            array(
                'id' => 'home_page6',
                'class' => "bullet $page_6",
                'escape' => false
            )
        );
    }
    if (
        CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
        $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_SHORTCUTS)
    ) {
        echo $this->Html->link(
            '<span class="aag-icon-codigo"></span>',
            array(
                'plugin' => false,
                'controller' => 'home',
                'action' => 'home_page3',
            ),
            array(
                'id' => 'home_page3',
                'title' => __t('Maintenance.Shortcuts'),
                'class' => "bullet $page_3",
                'escape' => false
            )
        );
    }
    if (
        $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_SUPPLIERS) &&
        $this->Acceso->haveModulePermission(ConstantsConfigModules::SUPPLIERS)
    ) {
        echo $this->Html->link(
            '<span class="aag-icon-maletin"></span>',
            array(
                'plugin' => false,
                'controller' => 'suppliers',
                'action' => 'index',
            ),
            array(
                'id' => 'home_page4',
                'title' => __t('Suppliers.Suppliers'),
                'class' => "bullet $page_4",
                'escape' => false
            )
        );
    }
    ?>
</div>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('General.Home'),
                array(
                    'controller' => 'home',
                    'action' => 'home'
                )
            )
        ));
        ?>
    </div>
</div>
<?php
echo $this->element('../Home/Elements/home_bullets');
echo $this->element('../Home/Elements/home_header');
?>
<div class="iconos-dashboard">
    <?php
    $span = __t("General.Trading_Groups");
    echo $this->Html->link(
        '<span class="aag-icon-reunion"></span>' . $span,
        array(
            'controller' => 'trading_groups',
            'action' => 'home',
        ),
        array('escape' => false)
    );
    $span = __t("General.Garages");
    echo $this->Html->link(
        '<span class="aag-icon-garage"></span>' . $span,
        array(
            'controller' => 'garages',
            'action' => 'home',
        ),
        array('escape' => false)
    );
    $span = __t('Menu.Garages') . ' ' . __t('Menu.Networks');
    echo $this->Html->link(
        '<span class="aag-icon-garaje-redes"></span>' . $span,
        array(
            'controller' => 'networks',
            'action' => 'home',
        ),
        array('escape' => false)
    );
    $span =  __t('Menu.Distributors');
    echo $this->Html->link(
        '<span class="aag-icon-distribuidores"></span>' . $span ,
        array(
            'controller' => 'distributors',
            'action' => 'home',
        ),
        array('escape' => false)
    );
    $span =   __t('Menu.Distributors') . ' ' . __t('Menu.Networks');
    echo $this->Html->link(
        '<span class="aag-icon-distribuidores-redes"></span>'. $span,
        array(
            'controller' => 'distributors_networks',
            'action' => 'home',
        ),
        array('escape' => false)
    );
    $span = __t('Menu.Agreement');
    echo $this->Html->link(
        '<span class="aag-icon-acuerdo"></span>'. $span,
        array(
            'controller' => 'agreements',
            'action' => 'home',
        ),
        array('escape' => false)
    );
    $span = 'Training';
    echo $this->Html->link(
        '<span class="aag-icon-atril"></span>'. $span,
        array(
            // 'controller' => 'distributors_networks',
            // 'action' => 'home',
        ),
        array(
            'escape' => false,
            'class' => 'disabled'
        )
    );
    $span = 'Audits';
    echo $this->Html->link(
        '<span class="aag-icon-fichero"></span>'.$span,
        array(
            // 'controller' => 'distributors_networks',
            // 'action' => 'home',
        ),
        array(
            'escape' => false,
            'class' => 'disabled'
        )
    );
    $span =  __t('Menu.Users');
    echo $this->Html->link(
        '<span class="aag-icon-mas-usuarios"></span>' . $span,
        array(
            'controller' => 'users',
            'action' => 'listing',
        ),
        array('escape' => false)
    );
    $span = __t("Alert.Alerts");
    echo $this->Html->link(
        '<span class="aag-icon-campana"></span>'. $span,
        array(
            'controller' => 'alerts',
            'action' => 'home',
        ),
        array('escape' => false)
    );
    $span = 'CRM';
    echo $this->Html->link(
        '<span class="aag-icon-configuracion"></span>' . $span,
        array(
            'controller' => 'dashboard',
            'action' => 'home',
        ),
        array('escape' => false)
    );
    $span =  __t('Menu.Maintenance');
    echo $this->Html->link(
        '<span class="aag-icon-rodillo"></span>'. $span,
        array(
            'controller' => 'maintenance',
            'action' => 'home',
        ),
        array('escape' => false)
    );
    $span = __t('Menu.User_guides');
    echo $this->Html->link(
        '<span class="aag-icon-birrete"></span>'. $span,
        array(
            'controller' => 'user_guides',
            'action' => 'home',
        ),
        array('escape' => false)
    );
    $span =  __t('Suppliers.Suppliers');
    echo $this->Html->link(
        '<span class="aag-icon-maletin"></span>'. $span,
        array(
            'controller' => 'suppliers',
            'action' => 'maintenance_suppliers',
        ),
        array('escape' => false)
    );
    $span =  __t('Maintenance.Suppliers_categories');
    echo $this->Html->link(
        '<span class="aag-icon-maletin-reloj"></span>'. $span,
        array(
            'controller' => 'suppliers',
            'action' => 'maintenance_suppliers_categories',
        ),
        array('escape' => false)
    );
    $span = __t('Suppliers.Brands');
    echo $this->Html->link(
        '<span class="aag-icon-medalla"></span>'. $span,
        array(
            'controller' => 'brands',
            'action' => 'maintenance_brands',
        ),
        array('escape' => false)
    );
    $span =  __t('Suppliers.Products');
    echo $this->Html->link(
        '<span class="aag-icon-caja"></span>'. $span,
        array(
            'controller' => 'products',
            'action' => 'maintenance_products',
        ),
        array('escape' => false)
    );
    $span =  __t('Menu.Emails');
    echo $this->Html->link(
        '<span class="aag-icon-correo-aviso"></span>'. $span,
        array(
            'controller' => 'emails',
            'action' => 'home',
        ),
        array('escape' => false)
    );
    $span =  __t('Menu.Contacts');
    echo $this->Html->link(
        '<span class="aag-icon-comentario"></span>'. $span,
        array(
            'controller' => 'contacts',
            'action' => 'home',
        ),
        array('escape' => false)
    );
    $span =  __t('Menu.Contacts_lists');
    echo $this->Html->link(
        '<span class="aag-icon-agenda"></span>'. $span,
        array(
            'controller' => 'contacts_lists',
            'action' => 'home',
        ),
        array('escape' => false)
    );
    $span = 'Statistics';
    echo $this->Html->link(
        '<span class="aag-icon-grafica-con-barras"></span>'. $span,
        array(
            'controller' => 'statistics',
            'action' => 'home',
        ),
        array('escape' => false)
    );
    ?>
</div>

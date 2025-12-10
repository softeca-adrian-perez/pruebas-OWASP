<?php
$controller = $this->request->controller;
$action = $this->request->action;
if (
    (in_array($controller, array('dashboard', 'services', 'portfolio', 'vehicles', 'vehicle_types', 'software', 'websites', 'contracts', 'associations')) && $action == 'home') ||
    $controller == 'suppliers' && $action == 'index' ||
    $controller == 'appointments' && $action == 'maintenance_home_events' ||
    $controller == 'communications' && $action == 'home_section' ||
    $controller == 'home'
) {
    $container_class = '';
} else {
    $container_class = 'container-breadcrumb';
}
echo $this->fetch('breadcrumb');

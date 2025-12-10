<?php
$controller = $this->request->controller;
$action = $this->request->action;
$role = $this->Acceso->rol();
$user = $this->Acceso->user();
$this->Alert = ClassRegistry::init('Alert');
$this->Message = ClassRegistry::init('Message');
$htmlString = $this->Alert->find('count', array(
    'conditions' => array(
        'read' => ConstantsBooleans::NO,
        'user_id' => CakeSession::read('Auth.User.id')
    )
));
if ($role == ConstantsRoles::GARAGE) {
    $messagesCount = $this->Message->getCountUnreadByGarage(CakeSession::read('Auth.User.garage_id'));
} elseif ($role == ConstantsRoles::DISTRIBUTOR) {
    $messagesCount = $this->Message->getCountUnreadByDistributor(CakeSession::read('Auth.User.distributor_id'));
} else {
    $messagesCount = '-';
}
echo $this->element('Comun/menu');
?>
<script type="text/javascript">
    $(document).ready(function() {
        var htmlString = "<?php echo $htmlString; ?>";
        $('#count-alerts').html(htmlString);
        var messageCount = "<?php echo $messagesCount; ?>";
        $('#count-mailbox').html(messageCount);
    });
</script>
<div class="logo">
    <div id="oc-menu">
        <svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512">
            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-miterlimit="10" stroke-width="32" d="M80 160h352M80 256h352M80 352h352" />
        </svg>
    </div>
    <?php
    if (CakeSession::read('Auth.User.garage_id') != null || CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN) {
        if (CakeSession::read('Auth.User.networks')) {
            if (CakeSession::read('Auth.User.current_network')) {
                echo $this->Html->link(
                    $this->Html->image('aag.svg', array('alt' => __t('General.Logo'), 'title' => 'Alliance', 'style' => 'width: 175px')),
                    array(
                        'plugin' => false,
                        'controller' => 'paginas',
                        'action' => 'home',
                        CakeSession::read('Auth.User.current_network')
                    ),
                    array(
                        'escape' => false,
                        'class' => 'logotipo-general',
                        'id' => 'logo_home'
                    )
                );
            }
        } else {
            echo $this->Html->link(
                $this->Html->image('aag.svg', array('alt' => __t('General.Logo'), 'title' => 'Alliance', 'style' => 'width: 175px')),
                array(
                    'plugin' => false,
                    'controller' => 'paginas',
                    'action' => 'home',
                ),
                array(
                    'escape' => false,
                    'class' => 'logotipo-general',
                    'id' => 'logo_home'
                )
            );
        }
    } else {
        echo $this->Html->link(
            $this->Html->image('aag.svg', array('alt' => __t('General.Logo'), 'title' => 'Alliance', 'style' => 'width: 175px')),
            array(
                'plugin' => false,
                'controller' => 'paginas',
                'action' => 'home',
            ),
            array(
                'escape' => false,
                'class' => 'logotipo-general',
                'id' => 'logo_home'
            )
        );
    }
    ?>
</div>
<header class="main-header">
    <?php
    if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN) {
        echo $this->Element('Comun' . DS . 'regions_options');
        echo $this->Element('Comun' . DS . 'countries_options');
    }
    echo $this->Element('Comun' . DS . 'networks_options');
    $Appointment = ClassRegistry::init('Appointment');
    $user = CakeSession::read('Auth.User');
    $my_appointment_assigned = $Appointment->getAppointmentWithCustomerByUserAssignedIdAndStatus($user['id'], ConstantsStatusAppointmentsDe::RUNNING);
    ?>
    <div id="header-notifications" class="notifications">
        <?php
        if (($this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_DIRECTORY) && $role != ConstantsRoles::GARAGE) || CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN) {
            echo $this->Html->link(
                '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M7.5,13A4.5,4.5,0,1,1,12,8.5,4.5,4.5,0,0,1,7.5,13Zm0-7A2.5,2.5,0,1,0,10,8.5,2.5,2.5,0,0,0,7.5,6ZM15,23v-.5a7.5,7.5,0,0,0-15,0V23a1,1,0,0,0,2,0v-.5a5.5,5.5,0,0,1,11,0V23a1,1,0,0,0,2,0Zm9-5a7,7,0,0,0-11.667-5.217,1,1,0,1,0,1.334,1.49A5,5,0,0,1,22,18a1,1,0,0,0,2,0ZM17.5,9A4.5,4.5,0,1,1,22,4.5,4.5,4.5,0,0,1,17.5,9Zm0-7A2.5,2.5,0,1,0,20,4.5,2.5,2.5,0,0,0,17.5,2Z" fill="var(--primary-color)"/>
                </svg>
                <div id="count-directory" class="ion-arrow-right-c"></div>',
                array(
                    'plugin' => false,
                    'controller' => 'contacts',
                    'action' => 'directory',
                ),
                array(
                    'class' => 'notificaciones una',
                    'title' => __t('Contact.Directory'),
                    'escape' => false
                )
            );
        }
        if (($this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_MAILBOX)) || CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN) {
            if ($messagesCount === '-') {
                $action = 'home';
            } else {
                $action = 'recieved_messages';
            }
            echo $this->Html->link(
                '<svg xmlns="http://www.w3.org/2000/svg" width="27.045" height="27.034" viewBox="0 0 27.045 27.034">
                    <path d="M25.309,9.127l-7.8-7.478a5.664,5.664,0,0,0-7.95-.017L1.735,9.127A5.666,5.666,0,0,0,0,13.192V21.41a5.641,5.641,0,0,0,5.634,5.634H21.411a5.641,5.641,0,0,0,5.634-5.634V13.192A5.663,5.663,0,0,0,25.309,9.127ZM11.133,3.242a3.4,3.4,0,0,1,4.8.018l7.6,7.284-7.621,7.622a3.464,3.464,0,0,1-4.78,0L3.51,10.544ZM24.792,21.41a3.381,3.381,0,0,1-3.381,3.381H5.634A3.381,3.381,0,0,1,2.254,21.41V13.192a3.4,3.4,0,0,1,.066-.651L9.539,19.76a5.662,5.662,0,0,0,7.967,0l7.219-7.219a3.4,3.4,0,0,1,.066.651Z" transform="translate(0 -0.011)" fill="var(--primary-color)"/>
                </svg>
                <div id="count-mailbox"></div>',
                array(
                    'plugin' => false,
                    'controller' => 'messages',
                    'action' => $action,
                ),
                array(
                    'class' => 'notificaciones dos',
                    'title' => __t('Mailbox.Mailbox'),
                    'escape' => false
                )
            );
        }
        if (
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_ALERT) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::ALERTS)
            ) || CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN
        ) {
            echo $this->Html->link(
                '<svg xmlns="http://www.w3.org/2000/svg" width="33.907" height="33.907" viewBox="0 0 33.907 33.907">
                    <path d="M2.31,17.192l5.5,5.5a4.22,4.22,0,0,1-5.5-5.5ZM7.327,1.98a1,1,0,1,0-.4-1.96A10.689,10.689,0,0,0,.213,4.145,1,1,0,0,0,1.82,5.334,8.641,8.641,0,0,1,7.327,1.98ZM23.2,16.023a1,1,0,0,0-1.191.762,8.638,8.638,0,0,1-3.317,5.407,1,1,0,0,0,1.18,1.616,10.7,10.7,0,0,0,4.09-6.593,1,1,0,0,0-.762-1.192ZM20.167,5.247l1.54-1.54a1,1,0,1,0-1.414-1.414L18.751,3.835a8.456,8.456,0,0,0-9.822-.5l-5.5,3.4a5.026,5.026,0,0,0-.912,7.829l6.959,6.959a5.026,5.026,0,0,0,7.839-.926l3.6-5.876a8.543,8.543,0,0,0-.748-9.474Z" transform="translate(-0.024 16.961) rotate(-45)"  fill="var(--primary-color)"/>
                </svg>
                <div id="count-alerts"></div>',
                array(
                    'plugin' => false,
                    'controller' => 'alerts',
                    'action' => 'home',
                ),
                array(
                    'class' => 'notificaciones tres',
                    'title' => __t('Alert.Alerts'),
                    'escape' => false
                )
            );
        }
        ?>
    </div>
    <?php
    echo $this->Element('Comun' . DS . 'languages_select');
    echo $this->Element('Comun' . DS . 'user_menu_options', array('user' => $user));
    ?>
</header>
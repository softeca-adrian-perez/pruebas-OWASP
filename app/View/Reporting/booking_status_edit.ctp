<?php
echo $this->Html->script(CakeSession::read('GOOGLE_MAPS_API'), array('block' => 'script'));
echo $this->Html->script('gmaps.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('inputsValidations.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
$action = $this->request->action;
echo $this->Html->script('agreements.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));

echo $this->Form->create(
    'Booking',
    array(
        'id' => 'form',
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data'
    )
);
echo $this->Form->hidden('Booking.id'); ?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        if ($action == ConstantsActionsNames::ADD) {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Booking.Booking'),
                    array(
                        'controller' => 'reporting',
                        'action' => 'bookings',
                        $network_id
                    )
                ),
                __t('General.Add'),
            ));
        } else {
            if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::GARAGE) {
                $url = array(
                    'controller' => 'reporting',
                    'action' => 'bookings',
                    $network_id
                );
            } else {
                $url = $url_redirect;
            }
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Booking.Booking'),
                    $url
                ),
                __t('General.Edit')
            ));
        }
        ?>
    </div>
    <div>
        <?php echo $this->element('Comun/form_actions', $cancel_action); ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="aag-title">
        <?php
        if ($action == ConstantsActionsNames::ADD) {
            echo __t('General.Add');
        } else {
            echo __t('General.Edit');
        }
        ?>
    </div>
    <br />
    <div class="cnt-form-inputs">
        <?php
        echo $this->Form->input(
            'booking_status',
            array(
                'label' => __t('Booking.Booking_status'),
                'type' => 'select',
                'class' => 'select2-multiple clear_field',
                'options' => $booking_status,
                'empty' => true,
                'required' => true,
            )
        );
        ?>
    </div>
</div>
<?php echo $this->Form->end(); ?>
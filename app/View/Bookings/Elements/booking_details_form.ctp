<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('GarageNetwork.Garage_network'),
                array(
                    'controller' => 'garages_networks',
                    'action' => 'view',
                    $garage_network_id
                )
            ),
            __t('Booking.Bookings'),
        ));
        ?>
    </div>
    <div>
        <?php
        echo $this->element(
            'Comun/form_actions',
            $cancel_action
        );
        ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="aag-title">
        <?php echo __t('Booking.Booking_details'); ?>
    </div>
    <div class="aag-subtitle p-top-1">
        <?php echo __t('General.Vehicle_info'); ?>
    </div>
    <div class="cnt-form-inputs">
        <?php
        echo $this->Form->input(
            'Booking.plate',
            array(
                'value' => Texto::encryptDecryptText($booking['Booking']['plate'], false),
                'disabled' => true,
                'type' => 'text',
                'label' => __t('General.Plate'),
            )
        );
		echo $this->Form->input(
            'Booking.vin',
            array(
                'value' => Texto::encryptDecryptText($booking['Booking']['vin'], false),
                'disabled' => true,
                'type' => 'text',
                'label' => __t('General.Vin'),
            )
        );
        echo $this->Form->input(
            'Booking.registered_on',
            array(
                'value' => Fecha::toFormatoVistaFecha($booking['Booking']['registered_on']),
                'disabled' => true,
                'type' => 'text',
                'label' => __t('General.Registered_on'),
            )
        );
        echo $this->Form->input(
            'Booking.mot_exp_date',
            array(
                'value' => Fecha::toFormatoVistaFecha($booking['Booking']['mot_exp_date']),
                'disabled' => true,
                'type' => 'text',
                'label' => __t('General.Mot_due_on'),
            )
        );
        echo $this->Form->input(
            'Booking.brand',
            array(
                'value' => $booking['Booking']['brand'],
                'disabled' => true,
                'type' => 'text',
                'label' => __t('Brands.Brand'),
            )
        );
        echo $this->Form->input(
            'Booking.model',
            array(
                'value' => $booking['Booking']['model'],
                'disabled' => true,
                'type' => 'text',
                'label' => __t('General.Model'),
            )
        );
        echo $this->Form->input(
            'Booking.version',
            array(
                'value' => $booking['Booking']['version'],
                'disabled' => true,
                'type' => 'text',
                'label' => __t('General.Version'),
            )
        );
        echo $this->Form->input(
            'Booking.mileage',
            array(
                'value' => $booking['Booking']['mileage'],
                'disabled' => true,
                'type' => 'number',
                'label' => __t('General.Mileage'),
            )
        );
        echo $this->Form->input(
            'Booking.fuel',
            array(
                'value' => $booking['Booking']['fuel'],
                'disabled' => true,
                'type' => 'text',
                'label' => __t('General.Fuel'),
            )
        );
        echo $this->Form->input(
            'Booking.work_id',
            array(
                'value' => $booking['Booking']['work_name'],
                'disabled' => true,
                'type' => 'text',
                'label' => __t('General.Work'),
            )
        ); ?>
        </div>
    </div>
</div>

<?php echo $this->Form->end(); ?>

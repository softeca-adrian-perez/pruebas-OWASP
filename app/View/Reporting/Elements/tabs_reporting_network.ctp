<div class="aag-tabs">
    <ul>
        <li <?php echo $selected == 'button_reporting_dates' ? 'class="active"' : ''; ?>>
            <?php
            if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::GARAGE) {
                echo $this->Html->link(
                    __t('Reporting.ReportingDates'),
                    array(
                        'controller' => 'reporting',
                        'action' => 'home',
                        $network_id,
                        true
                    )
                );
            }
            ?>
        </li>
        <li <?php echo $selected == 'button_bookings' ? 'class="active"' : ''; ?>>
            <?php
            echo $this->Html->link(
                __t('Booking.Bookings'),
                array(
                    'controller' => 'reporting',
                    'action' => 'bookings',
                    $network_id
                )
            );
            ?>
        </li>
        <li <?php echo $selected == 'button_enquiries' ? 'class="active"' : ''; ?>>
            <?php
            echo $this->Html->link(
                __t('Enquiry.Enquiries'),
                array(
                    'controller' => 'reporting',
                    'action' => 'enquiries',
                    $network_id
                )
            );
            ?>
        </li>
    </ul>
</div>
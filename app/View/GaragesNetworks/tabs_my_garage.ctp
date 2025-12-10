<?php $classActive = 'class="active"'; ?>
<ul class="aag-subtabs">
    <?php
    if(isset($garage_network_id))
    {
        ?>
        <li <?php if($selected == 'configuration') { echo $classActive; }?>>
            <?php
            echo $this->Html->link(
                __t('Configuration.Configuration'),
                array(
                    'controller' => 'garages_networks',
                    'action' => 'configuration',
                    $garage_network_id
                )
            );
            ?>
        </li>
        <li <?php if($selected == 'images') { echo $classActive; }?>>
            <?php
            echo $this->Html->link(
                __t('Garage.Images'),
                array(
                    'controller' => 'garages_networks',
                    'action' => 'images',
                    $garage_network_id
                )
            );
            ?>
        </li>
        <li <?php if($selected == 'add_planner_garage') { echo $classActive; }?>>
            <?php
            echo $this->Html->link(
                __t('General.Planner_times'),
                array(
                    'controller' => 'garages_networks',
                    'action' => 'add_planner_garage',
                    $garage_network_id,
                )
            );
            ?>
        </li>
        <li <?php if($selected == 'my_garage_services') { echo $classActive; }?>>
            <?php
            echo $this->Html->link(
                __t('GarageNetwork.My_garage_services'),
                array(
                    'controller' => 'garages_networks',
                    'action' => 'add_works_garage',
                    $garage_network_id,
                )
            );
            ?>
        </li>
        <li <?php if($selected == 'bookings') { echo $classActive; }?>>
            <?php
            echo $this->Html->link(
                __t('Booking.Bookings'),
                array(
                    'controller' => 'bookings',
                    'action' => 'home',
                    $garage_network_id,
                )
            );
            ?>
        </li>
        <li <?php if($selected == 'enquiries') { echo $classActive; }?>>
            <?php
            echo $this->Html->link(
                __t('Enquiry.Enquiries'),
                array(
                    'controller' => 'enquiries',
                    'action' => 'home',
                    $garage_network_id,
                )
            );
            ?>
        </li>
        <li <?php if($selected == 'reviews') { echo $classActive; }?>>
            <?php
            echo $this->Html->link(
                __t('Review.Reviews'),
                array(
                    'controller' => 'reviews',
                    'action' => 'home',
                    $garage_network_id,
                )
            );
            ?>
        </li>
        <li <?php if($selected == 'reporting') { echo $classActive; }?>>
            <?php
            echo $this->Html->link(
                __t('Reporting.Reporting'),
                array(
                    'controller' => 'reporting',
                    'action' => 'home',
                    $garage_network_id,
                )
            );
            ?>
        </li>
        <?php
    }
    ?>
</ul>
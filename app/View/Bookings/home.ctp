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
</div>
<?php echo $this->element('../GaragesNetworks/tabs_network', array('selected' => 'button_my_garage')); ?>
<div class="cnt-data">
    <div class="aag-padding">
        <?php echo $this->element('../GaragesNetworks/tabs_my_garage', array('selected' => 'bookings')); ?>
    </div>
    <?php echo $this->element('../Bookings/Elements/search'); ?>
    <div class="cnt-legend">
        <div>
            <?php echo "* " . sprintf(__t('Quotations.Pdf_availability'), __t('Quotation.Quotation')); ?>
        </div>
    </div>
    <div class="o-auto">
        <table class="table-tracking">
            <thead>
                <tr>
                    <th><?php echo __t('General.Customer_name'); ?></th>
                    <th><?php echo __t('General.Customer_phone'); ?></th>
                    <th><?php echo __t('Email.Email'); ?></th>
                    <?php if ($has_child_networks) { ?>
                        <th><?php echo $this->Paginator->sort('Booking.child_network_id', __t('ChildNetworks.secondary_networks')); ?></th>
                    <?php } ?>
                    <th><?php echo $this->Paginator->sort('Booking.date', __t('General.Date')); ?></th>
                    <th><?php echo $this->Paginator->sort('Booking.time', __t('General.Time')); ?></th>
                    <th><?php echo $this->Paginator->sort('Booking.time_to', __t('General.Time_to')); ?></th>
                    <th><?php echo $this->Paginator->sort('Booking.quotation_id', __t('General.Quotation_id')); ?></th>
                    <th><?php echo $this->Paginator->sort('Booking.additional_info', __t('General.Additional_info')); ?></th>
                    <th><?php echo __t('Booking.Booking_status'); ?></th>
                    <th><?php echo $this->Paginator->sort('Booking.creation_date', __t('General.Creation_date')); ?></th>
                    <th class="ta-center"><?php echo __t('General.Actions'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $booking) : ?>
                    <tr>
                        <td><?php echo Texto::encryptDecryptText($booking['Booking']['customer_name'], false); ?></td>
                        <td><?php echo Texto::encryptDecryptText($booking['Booking']['customer_phone'], false); ?></td>
                        <td><?php echo Texto::encryptDecryptText($booking['Booking']['customer_email'], false); ?></td>
                        <?php if ($has_child_networks) { ?>
                            <td>
                                <?php if (isset($booking['Booking']['child_network_id'])) {
                                    echo $child_networks[$booking['Booking']['child_network_id']];
                                } ?>
                            </td>
                        <?php } ?>
                        <td><?php echo Fecha::toFormatoVista($booking['Booking']['date']); ?></td>
                        <td><?php echo date("H:i", strtotime($booking['Booking']['time'])); ?></td>
                        <td><?php echo date("H:i", strtotime($booking['Booking']['time_to'])); ?></td>
                        <td><?php echo $booking['Booking']['quotation_id']; ?></td>
                        <td><?php echo $booking['Booking']['additional_info']; ?></td>
                        <td class="color-blue-text">
                            <?php
                                if(!empty($booking['Booking']['booking_status'])){
                                    if($booking['Booking']['booking_status'] != ConstantsBookingsStatus::EXPIRED){
                                        echo $this->Html->link(
                                            '<span>' . h($booking_status[$booking['Booking']['booking_status']]) . '</span>',
                                            array(
                                                'controller' => 'reporting',
                                                'action' => 'booking_status_edit',
                                                $booking['Booking']['id'],
                                                $network_id
                                            ),
                                            array(
                                                'escape' => false,
                                            )
                                        );
                                    } else {
										echo $booking_status[$booking['Booking']['booking_status']];
									}
                                }
                            ?>
                        </td>
                        <td><?php echo Fecha::toFormatoVista($booking['Booking']['creation_date']); ?></td>
                        <td>
                            <?php
                            echo $this->Html->link(
                                '<span class="aag-icon-ojo c-primary"></span>',
                                array(
                                    'controller' => 'bookings',
                                    'action' => 'booking_details',
                                    $garage_network_id,
                                    $booking['Booking']['id']
                                ),
                                array(
                                    'escape' => false,
                                    'title' => __t('General.View_booking'),
                                )
                            );
                            if (
                                ($booking['Booking']['quotation_exist'] && !$booking['Booking']['quotation_deleted']) ||
                                (!$booking['Booking']['quotation_exist'] && !empty($booking['Booking']['quotation_id']))
                            ) {
                                echo $this->Html->link(
                                    '<span class="icon-contact_list-2 c-primary"></span>',
                                    array(
                                        'controller' => 'quotations',
                                        'action' => 'download_details_pdf',
                                        $garage_network_id,
                                        $booking['Booking']['quotation_exist'] && !empty($booking['Booking']['file_guid']) ? $booking['Booking']['file_guid'] : $booking['Booking']['quotation_id'],
                                        '1',
                                        $booking['Booking']['quotation_exist']
                                    ),
                                    array(
                                        'escape' => false,
                                        'title' => __t('General.View_quotation'),
                                    )
                                );
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php echo $this->element('Comun/paginacion'); ?>
</div>
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
            __t('Quotation.Quotations'),
        ));
        ?>
    </div>
</div>
<?php echo $this->element('../GaragesNetworks/tabs_network', array('selected' => 'button_list_quotations')); ?>
<div class="cnt-data p-bottom-1">
    <?php echo $this->element('../Quotations/Elements/search'); ?>
    <div class="cnt-legend">
        <div>
            <?php
            echo "* " . __t('General.Prices') . " ";
            if ($with_vat) {
                echo __t("General.With_vat");
            } else {
                echo __t("General.Without_vat");
            }
            ?>
        </div>
        <div style="font-weight: bold; font-size: 14px;">
            <?php echo "* " . sprintf(__t('Quotations.Pdf_availability'), __t('General.Pdfs'));?>
        </div>
    </div>
    <div>
        <table class="table-tracking">
            <thead>
                <tr>
                    <th><?php echo __t('General.Date'); ?></th>
                    <th><?php echo __t('General.Price') ; ?></th>
                    <th><?php echo __t('General.Quotation_id'); ?></th>
                    <th><?php echo __t('Email.Email'); ?></th>
                    <th><?php echo __t('General.Plate'); ?></th>
					<th><?php echo __t('General.Vin'); ?></th>
                    <th><?php echo __t('Brands.Brand'); ?></th>
                    <th><?php echo __t('General.Model'); ?></th>
                    <th><?php echo __t('General.Version'); ?></th>
                    <th><?php echo __t('General.Actions'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($quotations as $quotation) : ?>
                    <tr>
                        <td><?php echo Fecha::toFormatoVista($quotation['creation_date']); ?></td>
                        <td>
                            <?php
                                if (!empty($quotation['price']) && $quotation['price'] != "0.00") {
                                    echo Numero::redondear($quotation['price'], 2);
                                } else {
                                    echo "-";
                                }
                            ?>
                        </td>
                        <td><?php echo $quotation['quotation_id']; ?></td>
                        <td><?php echo $quotation['email']; ?></td>
                        <td><?php echo $quotation['vehicle_plate']; ?></td>
						<td><?php echo $quotation['vehicle_vin']; ?></td>
                        <td><?php echo $quotation['vehicle_brand_name']; ?></td>
                        <td><?php echo $quotation['vehicle_model_name']; ?></td>
                        <td><?php echo $quotation['vehicle_version_name']; ?></td>
                        <td>
                            <?php
                            if (($quotation['quotation_exist'] && !$quotation['deleted']) ||
                                !$quotation['quotation_exist']) {
                                echo $this->Html->link(
                                    '<span class="aag-icon-ojo c-primary"></span>',
                                    array(
                                        'controller' => 'quotations',
                                        'action' => 'download_details_pdf',
                                        $garage_network_id,
                                        $quotation['quotation_exist'] ? $quotation['file_guid'] :$quotation['quotation_id'],
                                        '2',
                                        $quotation['quotation_exist']
                                    ),
                                    array(
                                        'escape' => false,
                                        'title' => __t('General.View_quotation'))
                                );
                            }
                            if ($quotation['booking_exist']) {
                                echo $this->Html->link(
                                    '<span class="icon-contact_list-2 c-primary"></span>',
                                    array(
                                        'controller' => 'bookings',
                                        'action' => 'home',
                                        $garage_network_id,
                                        '?' => array('quotation_id' => $quotation['quotation_id'])
                                    ),
                                    array(
                                        'escape' => false,
                                        'title' => __t('General.View_booking'))
                                );
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php echo $this->element('../Elements/Comun/pagination_list', array('pagination_size' => $pagination_size, 'pagination_count' => $pagination_count)); ?>
</div>

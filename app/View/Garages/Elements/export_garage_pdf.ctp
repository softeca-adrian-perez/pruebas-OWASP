<style>
    * {
        font-family: sans-serif;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        border: 1px solid #555;
        text-align: left;
        font-size: 10px;
        padding: 4px 7px;
        font-family: sans-serif;
        color: #333;
    }

    th {
        font-size: 8px;
        font-weight: bold;
        text-transform: uppercase;
        background-color: #e5e5e5;
        width: 1px;
        white-space: nowrap;
        color: #555;
    }

    .title-pdf {
        margin-top: 20px;
        margin-bottom: 10px;
    }

    .title-pdf td {
        color: #555;
        font-size: 22px;
        padding-left: 0;
        padding-right: 0;
        border: none;
        border-bottom: 2px solid #555;
    }

    .subtitle-pdf {
        margin-top: 10px;
        margin-bottom: 10px;
    }

    .subtitle-pdf td {
        color: #555;
        font-size: 17px;
        padding-left: 0;
        padding-right: 0;
        border: none;
    }

    .simple-text td {
        padding-left: 0;
        padding-right: 0;
        border: none;
    }

    .table-wrap th {
        white-space: normal;
        width: auto;
    }
</style>
<table class="title-pdf" style="margin-top: 0;">
    <tbody>
        <tr>
            <td> <?php echo __t('Garage.Garage'); ?> </td>
        </tr>
    </tbody>
</table>
<table class="subtitle-pdf" style="margin-top: 0;">
    <tbody>
        <tr>
            <td> <?php echo __t('Garage.Garage'); ?> </td>
        </tr>
    </tbody>
</table>
<table>
    <tbody>
        <tr>
            <th colspan="2"><?php echo __t('Garage.Business_name') . ':'; ?></th>
            <th colspan="2"><?php echo __t('Garage.Name') . ':'; ?></th>
        </tr>
        <tr>
            <td colspan="2"><?php echo $garage['Garage']['business_name']; ?></td>
            <td colspan="2"><?php echo $garage['Garage']['name']; ?></td>
        </tr>
        <tr>
            <th><?php echo __t('General.Status') . ':'; ?></th>
            <td><?php echo h($garage['GarageStatus'][$garage['Garage']['status']]); ?></td>
            <th><?php echo __t('Garage.G_number') . ':'; ?></th>
            <td><?php echo $garage['Garage']['g_number_id']; ?></td>
        </tr>
        <tr>
            <th><?php echo __t('Garage.Foundation_year') . ':'; ?></th>
            <td><?php echo $garage['Garage']['foundation_year']; ?></td>
            <th><?php echo __t('Garage.Ref_code') . ':'; ?></th>
            <td><?php echo $garage['Garage']['ref_code']; ?></td>
        </tr>
        <tr>
            <th><?php echo __t('Distributor.Workshop_activities') . ':'; ?></th>
            <td>
                <?php
                if (isset($garage['WorkshopActivities'])) {
                    foreach ($garage['WorkshopActivities'] as $activities) {
                        echo $activities['WorkshopActivities']['name_' . __l()] . ' ';
                    }
                };
                ?>
            </td>
            <th><?php echo __t('Garage.Slug') . ':'; ?></th>
            <td><?php echo $garage['Garage']['slug']; ?></td>
        </tr>
    </tbody>
</table>
<table class="subtitle-pdf">
    <tbody>
        <tr>
            <td> <?php echo __t('Garage.Visits'); ?> </td>
        </tr>
    </tbody>
</table>
<table>
    <tbody>
        <tr>
            <th><?php echo __t('Garage.Visit_frequency') . ':'; ?></th>
            <th><?php echo __t('Garage.Visit_days') . ':'; ?></th>
        </tr>
        <tr>
            <td><?php echo $garage['Garage']['visit_frequency']; ?></td>
            <td>
                <?php
                if (!empty($garage['Garage']['visit_monday'])) {
                    echo __t('Garage.Monday') . ' ';
                }
                if (!empty($garage['Garage']['visit_tuesday'])) {
                    echo __t('Garage.Tuesday') . ' ';
                }
                if (!empty($garage['Garage']['visit_wednesday'])) {
                    echo __t('Garage.Wednesday') . ' ';
                }
                if (!empty($garage['Garage']['visit_thursday'])) {
                    echo __t('Garage.Thursday') . ' ';
                }
                if (!empty($garage['Garage']['visit_friday'])) {
                    echo __t('Garage.Friday');
                }
                ?>
            </td>
        </tr>
    </tbody>
</table>
<table class="subtitle-pdf">
    <tbody>
        <tr>
            <td> <?php echo __t('Garage.BDM') . ' ' . __t('General.List'); ?> </td>
        </tr>
    </tbody>
</table>
<table class="table-wrap">
    <thead>
        <tr>
            <th><?php echo __t('General.Order'); ?></th>
            <th><?php echo __t('General.Name'); ?></th>
            <th><?php echo __t('User.Surname'); ?></th>
            <th><?php echo __t('General.Position'); ?></th>
            <th><?php echo __t('Garage.Phone'); ?></th>
            <th><?php echo __t('Garage.Mobile'); ?></th>
            <th><?php echo __t('Garage.Email'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        $counter = 1;
        foreach ($garage['ContactsBDM'] as $contacts) {
        ?>
            <tr>
                <td>
                    <?php
                    echo $counter;
                    $counter++;
                    ?>
                </td>
                <td>
                    <?php echo h($contacts['Contact']['first_name']); ?>
                </td>
                <td>
                    <?php echo h($contacts['Contact']['last_name']); ?>
                </td>
                <td>
                    <?php echo h(!empty($contacts['Contact']['position_id']) ? $garage['Positions'][$contacts['Contact']['position_id']] : ''); ?>
                </td>
                <td>
                    <?php echo h($contacts['Contact']['phone']); ?>
                </td>
                <td>
                    <?php echo h($contacts['Contact']['mobile_phone']); ?>
                </td>
                <td>
                    <?php echo h($contacts['Contact']['email']); ?>
                </td>
            </tr>
        <?php
        }
        ?>
    </tbody>
</table>
<table class="subtitle-pdf">
    <tbody>
        <tr>
            <td> <?php echo __t('Garage.Contact'); ?> </td>
        </tr>
    </tbody>
</table>
<table>
    <tbody>
        <tr>
            <th><?php echo __t('Garage.Phone') . ':'; ?></th>
            <td><?php echo $garage['Garage']['phone']; ?></td>
            <th><?php echo __t('Garage.Mobile') . ':'; ?></th>
            <td><?php echo $garage['Garage']['mobile']; ?></td>
        </tr>
        <tr>
            <th><?php echo __t('Garage.24h_phone') . ':'; ?></th>
            <td><?php echo $garage['Garage']['service_24h_phone']; ?></td>
            <th><?php echo __t('Garage.Fax') . ':'; ?></th>
            <td><?php echo $garage['Garage']['fax']; ?></td>
        </tr>
        <tr>
            <th><?php echo __t('Garage.Email') . ':'; ?></th>
            <td><?php echo $garage['Garage']['email']; ?></td>
            <th><?php echo __t('Garage.Web') . ':'; ?></th>
            <td><?php echo $garage['Garage']['web']; ?></td>
        </tr>
    </tbody>
</table>
<table class="subtitle-pdf">
    <tbody>
        <tr>
            <td> <?php echo __t('Garage.Primary_contact') ?> </td>
        </tr>
    </tbody>
</table>
<table class="simple-text">
    <tbody>
        <tr>
            <td> <?php echo isset($garage['PrimaryContact']['Contact']['full_name']) ? $garage['PrimaryContact']['Contact']['full_name'] : __t('General.None'); ?> </td>
        </tr>
    </tbody>
</table>
<table class="subtitle-pdf">
    <tbody>
        <tr>
            <td> <?php echo __t('Garage.Address'); ?> </td>
        </tr>
    </tbody>
</table>
<table>
    <tbody>
        <tr>
            <th colspan="2"><?php echo __t('Garage.Address') . ':'; ?></th>
            <th colspan="2"><?php echo __t('Garage.Address_2') . ':'; ?></th>
        </tr>
        <tr>
            <td colspan="2"><?php echo $garage['Garage']['address1']; ?></td>
            <td colspan="2"><?php echo $garage['Garage']['address2']; ?></td>
        </tr>
        <tr>
            <th colspan="2"><?php echo __t('Garage.Address_3') . ':'; ?></th>
            <th colspan="2"><?php echo __t('Garage.Address_4') . ':'; ?></th>
        </tr>
        <tr>
            <td colspan="2"><?php echo $garage['Garage']['address3']; ?></td>
            <td colspan="2"><?php echo $garage['Garage']['address4']; ?></td>
        </tr>
        <tr>
            <th><?php echo __t('Garage.Latitude') . ':'; ?></th>
            <td><?php echo isset($garage['Garage']['latitude']) ? $garage['Garage']['latitude'] : null ; ?></td>
            <th><?php echo __t('Garage.Longitude') . ':'; ?></th>
            <td><?php echo isset($garage['Garage']['longitude']) ? $garage['Garage']['longitude'] : null; ?></td>
        </tr>
        <tr>
            <th><?php echo __t('Garage.Country') . ':'; ?></th>
            <td><?php echo isset($garage['Country']['Country']['name']) ? $garage['Country']['Country']['name'] : null; ?></td>
            <th><?php echo __t('Garage.Province') . ':'; ?></th>
            <td><?php echo isset($garage['Province']['Province']['name']) ? $garage['Province']['Province']['name'] : null; ?></td>
        </tr>
        <tr>
            <th><?php echo __t('General.Seo_city') . ':'; ?></th>
            <td><?php echo isset($garage['City']['City']['name']) ? $garage['City']['City']['name'] : null; ?></td>
            <th><?php echo __t('Garage.Town') . '/' . __t('Garage.City') . ':'; ?></th>
            <td><?php echo isset($garage['Garage']['town']) ? $garage['Garage']['town'] : null; ?></td>
        </tr>
        <tr>
            <th><?php echo __t('Garage.Postcode') . ':'; ?></th>
            <td><?php echo isset($garage['Garage']['postcode']) ? $garage['Garage']['postcode'] : null; ?></td>
            <th><?php echo __t('Garage.Region') . ':'; ?></th>
            <td><?php echo isset($garage['Region']['AagRegion']['name']) ? $garage['Region']['AagRegion']['name'] : null; ?></td>
        </tr>
        <tr>
            <th><?php echo __t('Garage.Sales_area') . ':'; ?></th>
            <td><?php echo isset($garage['SalesArea']['SalesArea']['name_' . __l()]) ? $garage['SalesArea']['SalesArea']['name_' . __l()] : null; ?></td>
        </tr>
    </tbody>
</table>
<table class="title-pdf">
    <tbody>
        <tr>
            <td> <?php echo __t('Network.Distributors_networks'); ?> </td>
        </tr>
    </tbody>
</table>
<table class="subtitle-pdf">
    <tbody>
        <tr>
            <td> <?php echo __t('Distributor.Distributors'); ?> </td>
        </tr>
    </tbody>
</table>
<table class="table-tracking">
    <thead>
        <tr>
            <th><?php echo __t('General.Order'); ?></th>
            <th><?php echo __t('Distributor.Distributor'); ?></th>
            <th><?php echo __t('Distributor.Account_number'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        $counter = 1;
        foreach ($garage['Distributors'] as $distributor) {
        ?>
            <tr>
                <td>
                    <?php
                    echo $counter;
                    $counter++;
                    ?>
                </td>
                <td>
                    <?php echo h($distributor['Distributor']['name']); ?>
                </td>
                <td>
                    <?php echo h($distributor['Distributor']['account_number']); ?>
                </td>
            </tr>
        <?php
        }
        ?>
    </tbody>
</table>
<table class="subtitle-pdf">
    <tbody>
        <tr>
            <td> <?php echo __t('Network.Networks'); ?> </td>
        </tr>
    </tbody>
</table>
<table class="subtitle-pdf">
    <tbody>
        <tr>
            <td> <?php echo __t('Network.Internal_network'); ?> </td>
        </tr>
    </tbody>
</table>
<table class="table-wrap">
    <thead>
        <tr>
            <th><?php echo __t('Network.Internal_network'); ?></th>
            <th><?php echo __t('Distributor.Trading_group'); ?></th>
            <th><?php echo __t('Network.Received_date'); ?></th>
            <th><?php echo __t('Distributor.Start_date'); ?></th>
            <th><?php echo __t('Network.Leaving_date'); ?></th>
            <th><?php echo __t('Distributor.Leaving_reason'); ?></th>
            <th><?php echo __t('Network.Status'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($garage['InternalNetworksDis'] as $internalNetwork) { ?>
            <tr>
                <td>
                    <?php echo h($garage['InternalNetworks'][$internalNetwork['GarageNetwork']['network_id']]); ?>
                </td>
                <td>
                    <?php echo h($garage['TradingGroups'][$internalNetwork['GarageNetwork']['trading_group_id']] ?? ''); ?>
                </td>
                <td>
                    <?php echo h($internalNetwork['GarageNetwork']['contract_received_date']); ?>
                </td>
                <td>
                    <?php echo h($internalNetwork['GarageNetwork']['contract_start_date']); ?>
                </td>
                <td>
                    <?php echo h($internalNetwork['GarageNetwork']['leaving_date']); ?>
                </td>
                <td>
                    <?php echo h($internalNetwork['GarageNetwork']['reason_leaving']); ?>
                </td>
                <td>
                    <?php echo __t($garage['NetworkStatus'][$internalNetwork['GarageNetwork']['status']]); ?>
                </td>
            </tr>
        <?php
        }
        ?>
    </tbody>
</table>
<table class="subtitle-pdf">
    <tbody>
        <tr>
            <td> <?php echo __t('Network.External_network'); ?> </td>
        </tr>
    </tbody>
</table>
<table class="table-wrap">
    <thead>
        <tr>
            <th><?php echo __t('Network.External_network'); ?></th>
            <th><?php echo __t('Distributor.Trading_group'); ?></th>
            <th><?php echo __t('Network.Received_date'); ?></th>
            <th><?php echo __t('Distributor.Start_date'); ?></th>
            <th><?php echo __t('Network.Leaving_date'); ?></th>
            <th><?php echo __t('Distributor.Leaving_reason'); ?></th>
            <th><?php echo __t('Network.Status'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($garage['ExternalNetworksDis'] as $externalNetwork) { ?>
            <tr>
                <td>
                    <?php echo h($garage['ExternalNetworks'][$externalNetwork['GarageNetwork']['network_id']]); ?>
                </td>
                <td>
                    <?php echo h($garage['TradingGroups'][$externalNetwork['GarageNetwork']['trading_group_id']] ?? ''); ?>
                </td>
                <td>
                    <?php echo h($externalNetwork['GarageNetwork']['contract_received_date']); ?>
                </td>
                <td>
                    <?php echo h($externalNetwork['GarageNetwork']['contract_start_date']); ?>
                </td>
                <td>
                    <?php echo h($externalNetwork['GarageNetwork']['leaving_date']); ?>
                </td>
                <td>
                    <?php echo h($externalNetwork['GarageNetwork']['reason_leaving']); ?>
                </td>
                <td>
                    <?php echo __t($garage['NetworkStatus'][$externalNetwork['GarageNetwork']['status']]); ?>
                </td>
            </tr>
        <?php
        }
        ?>
    </tbody>
</table>
<table class="subtitle-pdf">
    <tbody>
        <tr>
            <td> <?php echo __t('General.Agreements'); ?> </td>
        </tr>
    </tbody>
</table>
<table class="subtitle-pdf">
    <tbody>
        <tr>
            <td> <?php echo __t('Agreement.Internal'); ?> </td>
        </tr>
    </tbody>
</table>
<table class="table-wrap">
    <thead>
        <tr>
            <th><?php echo __t('Agreement.Internal'); ?></th>
            <th><?php echo __t('Agreement.Code'); ?></th>
            <th><?php echo __t('Agreement.Fleet_reference'); ?></th>
            <th><?php echo __t('General.Parent_acct'); ?></th>
            <th><?php echo __t('Agreement.Garage_ref'); ?></th>
            <th><?php echo __t('Agreement.Sent_date'); ?></th>
            <th><?php echo __t('Agreement.Start_date'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($garage['InternalAgreements'] as $internalAgreement) { ?>
            <tr>
                <td>
                    <?php echo h($internalAgreement['GaAcuerdo']['fleet_agreement']); ?>
                </td>
                <td>
                    <?php echo h($internalAgreement['GaAcuerdo']['agreement_code']); ?>
                </td>
                <td>
                    <?php echo h($internalAgreement['GaAcuerdo']['fleet_reference']); ?>
                </td>
                <td>
                    <?php echo h($internalAgreement['GaAcuerdo']['parent_acct']); ?>
                </td>
                <td>
                    <?php echo h($internalAgreement['GaAcuerdo']['garage_ref']); ?>
                </td>
                <td>
                    <?php echo h($internalAgreement['GaAcuerdo']['contract_sent_date']); ?>
                </td>
                <td>
                    <?php echo h($internalAgreement['GaAcuerdo']['contract_start_date']); ?>
                </td>
            </tr>
        <?php
        }
        ?>
    </tbody>
</table>
<table class="subtitle-pdf">
    <tbody>
        <tr>
            <td> <?php echo __t('Agreement.External'); ?> </td>
        </tr>
    </tbody>
</table>
<table class="table-wrap">
    <thead>
        <tr>
            <th><?php echo __t('Agreement.External'); ?></th>
            <th><?php echo __t('Agreement.Sent_date'); ?></th>
            <th><?php echo __t('Agreement.Received_date'); ?></th>
            <th><?php echo __t('Agreement.Start_date'); ?></th>
            <th><?php echo __t('Distributor.Leaving_reason'); ?></th>
            <th><?php echo __t('General,Status'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($garage['ExternalAgreements'] as $externalAgreement) { ?>
            <tr>
                <td>
                    <?php echo h($externalAgreement['Agreement']['nombre']); ?>
                </td>
                <td>
                    <?php echo h($externalAgreement['GarageAgreement']['contract_sent_date']); ?>
                </td>
                <td>
                    <?php echo h($externalAgreement['GarageAgreement']['contract_received_date']); ?>
                </td>
                <td>
                    <?php echo h($externalAgreement['GarageAgreement']['contract_start_date']); ?>
                </td>
                <td>
                    <?php echo h($externalAgreement['GarageAgreement']['reason_leaving']); ?>
                </td>
                <td>
                    <?php echo __t($garage['NetworkStatus'][$externalAgreement['GarageAgreement']['status']]); ?>
                </td>
            </tr>
        <?php
        }
        ?>
    </tbody>
</table>
<table class="title-pdf">
    <tbody>
        <tr>
            <td> <?php echo __t('Garage.Activity') . ' / ' . __t('Garage.Services'); ?> </td>
        </tr>
    </tbody>
</table>
<table class="subtitle-pdf">
    <tbody>
        <tr>
            <td> <?php echo __t('Garage.Customer_activities'); ?> </td>
        </tr>
    </tbody>
</table>
<table class="table-tracking">
    <thead>
        <tr>
            <th><?php echo __t('General.Order'); ?></th>
            <th><?php echo __t('General.Name'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        $counter = 1;
        foreach ($garage['CustomerActivities'] as $customerActivity) { ?>
            <tr>
                <td>
                    <?php
                    echo $counter;
                    $counter++;
                    ?>
                </td>
                <td>
                    <?php echo h($garage['Activities'][$customerActivity['GarageCustomerActivity']['customer_activity_id']]); ?>
                </td>
            <?php
        }
            ?>
            <tr>
                <th colspan="2">
                    <?php echo __t('Garage.Facilities') . ':'; ?>
                </th>
            </tr>
            <tr>
                <td colspan="2">
                    <?php
                    $count = count($garage['GarageFacilities']);
                    $i = 0;
                    foreach ($garage['GarageFacilities'] as $facilities) {
                        echo $garage['Facilities'][$facilities];
                        if (++$i !== $count) {
                            echo ', ';
                        } else {
                            echo '.';
                        }
                    }
                    ?>
                </td>
            </tr>
    </tbody>
</table>
<table class="subtitle-pdf">
    <tbody>
        <tr>
            <td> <?php echo __t('Garage.Suppliers'); ?> </td>
        </tr>
    </tbody>
</table>
<table class="table-tracking">
    <thead>
        <tr>
            <th><?php echo __t('Garage.Suppliers'); ?></th>
            <th><?php echo __t('Garage.Value_and_supplier_type'); ?></th>
            <th><?php echo __t('Garage.From_date'); ?></th>
            <th><?php echo __t('Garage.To_date'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($garage['GarageValueSupplier'] as $garageSupplier) { ?>
            <tr>
                <td>
                    <?php echo h($garageSupplier['ValueAddSupplier']['name_' . __l()]); ?>
                </td>
                <td>
                    <?php echo h($garageSupplier['ValueAddSupplierType']['name_' . __l()]); ?>
                </td>
                <td>
                    <?php echo h($garageSupplier['GarageValueAddSupplier']['from_date']); ?>
                </td>
                <td>
                    <?php echo h($garageSupplier['GarageValueAddSupplier']['to_date']); ?>
                </td>
            </tr>
        <?php
        }
        ?>
    </tbody>
</table>
<table class="subtitle-pdf">
    <tbody>
        <tr>
            <td> <?php echo __t('Garage.Services'); ?> </td>
        </tr>
    </tbody>
</table>
<table class="simple-text">
    <tbody>
        <tr>
            <td>
                <?php
                $count = count($garage['GarageServices']);
                $i = 0;
                foreach ($garage['GarageServices'] as $services) {
                    echo $garage['Services'][$services];
                    if (++$i !== $count) {
                        echo ', ';
                    } else {
                        echo '.';
                    }
                }
                ?>
            </td>
        </tr>
    </tbody>
</table>
<table class="subtitle-pdf">
    <tbody>
        <tr>
            <td> <?php echo __t('Garage.Vehicle_brands_specialist'); ?> </td>
        </tr>
    </tbody>
</table>
<table class="simple-text">
    <tbody>
        <tr>
            <td>
                <?php
                $count = count($garage['VehicleSpecialist']);
                $i = 0;
                foreach ($garage['VehicleSpecialist'] as $specialist) {
                    echo $specialist;
                    if (++$i !== $count) {
                        echo ', ';
                    } else {
                        echo '.';
                    }
                }
                ?>
            </td>
        </tr>
    </tbody>
</table>
<table class="subtitle-pdf">
    <tbody>
        <tr>
            <td> <?php echo __t('Maintenance.Vehicle_types'); ?> </td>
        </tr>
    </tbody>
</table>
<table class="simple-text">
    <tbody>
        <tr>
            <td>
                <?php
                $count = count($garage['GarageVehicleType']);
                $i = 0;
                foreach ($garage['GarageVehicleType'] as $vehicleType) {
                    echo $garage['VehicleType'][$vehicleType];
                    if (++$i !== $count) {
                        echo ', ';
                    } else {
                        echo '.';
                    }
                }
                ?>
            </td>
        </tr>
    </tbody>
</table>
<table class="title-pdf">
    <tbody>
        <tr>
            <td> <?php echo __t('Equipment.Equipment') . ' / ' . __t('Garage.Software'); ?> </td>
        </tr>
    </tbody>
</table>
<table class="subtitle-pdf">
    <tbody>
        <tr>
            <td> <?php echo __t('Equipment.Equipment'); ?> </td>
        </tr>
    </tbody>
</table>
<table class="table-wrap">
    <thead>
        <tr>
            <th><?php echo __t('Equipment.Equipment'); ?></th>
            <th><?php echo __t('Equipment.Type'); ?></th>
            <th><?php echo __t('Software.Supplier'); ?></th>
            <th><?php echo __t('Equipment.Brand'); ?></th>
            <th><?php echo __t('General.Billing_schedule'); ?></th>
            <th><?php echo __t('General.Amount') . ' ' . (isset($garage['Country']['Country']['symbol']) ? $garage['Country']['Country']['symbol'] : null); ?></th>
            <th><?php echo __t('General.Member_pay'); ?></th>
            <th><?php echo __t('General.Garage_pay'); ?></th>
            <th><?php echo __t('Equipment.Start_date'); ?></th>
            <th><?php echo __t('Equipment.End_date'); ?></th>
            <th><?php echo __t('General.Billed_by_aag'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($garage['Equipment'] as $equipment) { ?>
            <tr>
                <td>
                    <?php echo h($equipment['Equipments']['equipment_name']); ?>
                </td>
                <td>
                    <?php echo h($equipment['EquipmentTypes']['equipment_type_name']); ?>
                </td>
                <td>
                    <?php echo h($garage['Suppliers'][$equipment['GarageEquipment']['supplier_id']]); ?>
                </td>
                <td>
                    <?php echo h($garage['Brands'][$equipment['GarageEquipment']['brand_id']]); ?>
                </td>
                <td>
                    <?php echo h(isset($garage['BillingSchedule'][$equipment['GarageEquipment']['billing_schedule_id']]) ?
                        $garage['BillingSchedule'][$equipment['GarageEquipment']['billing_schedule_id']] : null); ?>
                </td>
                <td>
                    <?php echo h($equipment['GarageEquipment']['amount']); ?>
                </td>
                <td>
                    <?php echo h($equipment['GarageEquipment']['member_pay']); ?>
                </td>
                <td>
                    <?php echo h($equipment['GarageEquipment']['garage_pay']); ?>
                </td>
                <td>
                    <?php echo h($equipment['GarageEquipment']['start_date']); ?>
                </td>
                <td>
                    <?php echo h($equipment['GarageEquipment']['end_date']); ?>
                </td>
                <td>
                    <?php echo Booleano::toString($equipment['GarageEquipment']['billed_by_aag']); ?>
                </td>
            </tr>
        <?php
        }
        ?>
    </tbody>
</table>
<table class="subtitle-pdf">
    <tbody>
        <tr>
            <td> <?php echo __t('Software.Software'); ?> </td>
        </tr>
    </tbody>
</table>
<table class="table-wrap">
    <thead>
        <tr>
            <th><?php echo __t('Software.Software'); ?></th>
            <th><?php echo __t('Software.Type'); ?></th>
            <th><?php echo __t('Software.Manufacturer'); ?></th>
            <th><?php echo __t('General.Billing_schedule'); ?></th>
            <th><?php echo __t('Garage.Version'); ?></th>
            <th><?php echo __t('Garage.Username'); ?></th>
            <th><?php echo __t('General.Amount') . ' ' . (isset($garage['Country']['Country']['symbol']) ? $garage['Country']['Country']['symbol'] : null); ?></th>
            <th><?php echo __t('General.Member_pay'); ?></th>
            <th><?php echo __t('General.Garage_pay'); ?></th>
            <th><?php echo __t('General.Number_subscription'); ?></th>
            <th><?php echo __t('Garage.Start_date'); ?></th>
            <th><?php echo __t('Garage.End_date'); ?></th>
            <th><?php echo __t('General.Online_ordering'); ?></th>
            <th><?php echo __t('General.Billed_by_aag'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($garage['Software'] as $software) { ?>
            <tr>
                <td>
                    <?php echo h($software['Software']['software_name']); ?>
                </td>
                <td>
                    <?php echo h($software['SoftwareType']['software_type_name']); ?>
                </td>
                <td>
                    <?php echo h($software['SoftwareManufactures']['software_manufactures_name']); ?>
                </td>
                <td>
                    <?php echo h(isset($garage['BillingSchedule'][$software['GarageSoftware']['billing_schedule_id']]) ?
                        $garage['BillingSchedule'][$software['GarageSoftware']['billing_schedule_id']] : null); ?>
                </td>
                <td>
                    <?php echo h($software['GarageSoftware']['version']); ?>
                </td>
                <td>
                    <?php echo h($software['GarageSoftware']['username']); ?>
                </td>
                <td>
                    <?php echo h($software['GarageSoftware']['amount']); ?>
                </td>
                <td>
                    <?php echo h($software['GarageSoftware']['member_pay']); ?>
                </td>
                <td>
                    <?php echo h($software['GarageSoftware']['garage_pay']); ?>
                </td>
                <td>
                    <?php echo h($software['GarageSoftware']['number_subscription']); ?>
                </td>
                <td>
                    <?php echo h($software['GarageSoftware']['start_date']); ?>
                </td>
                <td>
                    <?php echo h($software['GarageSoftware']['end_date']); ?>
                </td>
                <td>
                    <?php echo Booleano::toString($software['GarageSoftware']['online_ordering']); ?>
                </td>
                <td>
                    <?php echo Booleano::toString($software['GarageSoftware']['billed_by_aag']); ?>
                </td>
            </tr>
        <?php
        }
        ?>
    </tbody>
</table>
<table class="title-pdf">
    <tbody>
        <tr>
            <td> <?php echo __t('Garage.Orders'); ?> </td>
        </tr>
    </tbody>
</table>
<table class="subtitle-pdf">
    <tbody>
        <tr>
            <td> <?php echo __t('Garage.Orders'); ?> </td>
        </tr>
    </tbody>
</table>
<table class="table-wrap">
    <thead>
        <tr>
            <th><?php echo __t('Order.Order_number'); ?></th>
            <th><?php echo __t('Order.Product'); ?></th>
            <th><?php echo __t('Order.Quantity'); ?></th>
            <th><?php echo __t('Order.Invoice_date'); ?></th>
            <th><?php echo __t('Order.Order_date'); ?></th>
            <th><?php echo __t('Order.Completed_date'); ?></th>
            <th><?php echo __t('Order.Invoce_number'); ?></th>
            <th><?php echo __t('Order.Invoice_amount') . ' ' . (isset($garage['Country']['Country']['symbol']) ? $garage['Country']['Country']['symbol'] : null); ?></th>
            <th><?php echo __t('Order.Notes'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($garage['Orders'] as $orders) { ?>
            <tr>
                <td>
                    <?php echo h($orders['Order']['order_number']); ?>
                </td>
                <td>
                    <?php echo h($orders['OrderProduct']['name_' . __l()]); ?>
                </td>
                <td>
                    <?php echo h($orders['GarageProduct']['quantity']); ?>
                </td>
                <td>
                    <?php echo h($orders['Order']['invoice_date']); ?>
                </td>
                <td>
                    <?php echo h($orders['Order']['order_date']); ?>
                </td>
                <td>
                    <?php echo h($orders['Order']['completed_date']); ?>
                </td>
                <td>
                    <?php echo h($orders['Order']['invoce_number']); ?>
                </td>
                <td>
                    <?php echo h($orders['Order']['invoice_amount']); ?>
                </td>
                <td>
                    <?php echo h($orders['Order']['notes']); ?>
                </td>
            </tr>
        <?php
        }
        ?>
    </tbody>
</table>
<table class="title-pdf">
    <tbody>
        <tr>
            <td> <?php echo __t('Contact.Staff'); ?> </td>
        </tr>
    </tbody>
</table>
<table class="subtitle-pdf">
    <tbody>
        <tr>
            <td> <?php echo __t('Garage.Employees'); ?> </td>
        </tr>
    </tbody>
</table>
<table class="table-wrap">
    <thead>
        <tr>
            <th><?php echo __t('Garage.Employee_type'); ?></th>
            <th><?php echo __t('Garage.Quantity'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($garage['Employees'] as $employees) { ?>
            <tr>
                <td>
                    <?php echo $garage['EmployeeTypes'][$employees['GarageEmployee']['employee_type_id']]; ?>
                </td>
                <td>
                    <?php echo ($employees['GarageEmployee']['number']); ?>
                </td>
            </tr>
        <?php
        }
        ?>
    </tbody>
</table>
<table class="subtitle-pdf">
    <tbody>
        <tr>
            <td> <?php echo __t('Contact.Contacts_lists'); ?> </td>
        </tr>
    </tbody>
</table>
<table class="table-wrap">
    <thead>
        <tr>
            <th><?php echo __t('User.Name'); ?></th>
            <th><?php echo __t('User.Surname'); ?></th>
            <th><?php echo __t('Contact.Position'); ?></th>
            <th><?php echo __t('Contact.Phone'); ?></th>
            <th><?php echo __t('Garage.Mobile'); ?></th>
            <th><?php echo __t('Contact.Email'); ?></th>
            <th><?php echo __t('Contact.Interests'); ?></th>
            <th><?php echo __t('Garage.Primary_contact'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($garage['Staff'] as $staff) { ?>
            <tr>
                <td>
                    <?php echo h($staff['Contact']['first_name']); ?>
                </td>
                <td>
                    <?php echo h($staff['Contact']['last_name']); ?>
                </td>
                <td>
                    <?php echo h($garage['Position'][$staff['Contact']['position_id']]); ?>
                </td>
                <td>
                    <?php echo h($staff['Contact']['phone']); ?>
                </td>
                <td>
                    <?php echo h($staff['Contact']['mobile_phone']); ?>
                </td>
                <td>
                    <?php echo h($staff['Contact']['email']); ?>
                </td>
                <td>
                    <?php echo h($staff['GarageContactStaff']['interest']); ?>
                </td>
                <td>
                    <?php echo h($staff['GarageContactStaff']['main_contact']); ?>
                </td>
            </tr>
        <?php
        }
        ?>
    </tbody>
</table>
<table class="title-pdf">
    <tbody>
        <tr>
            <td> <?php echo __t('General.Admin'); ?> </td>
        </tr>
    </tbody>
</table>
<table class="subtitle-pdf">
    <tbody>
        <tr>
            <td> <?php echo __t('Garage.Comments'); ?> </td>
        </tr>
    </tbody>
</table>
<table class="table-wrap">
    <thead>
        <tr>
            <th><?php echo __t('Garage.Date'); ?></th>
            <th><?php echo __t('User.Username'); ?></th>
            <th><?php echo __t('Garage.Comment'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($garage['Admin'] as $comments) { ?>
            <tr>
                <td>
                    <?php echo h($comments['GarageComment']['creation_date']); ?>
                </td>
                <td>
                    <?php echo h(isset($garage['UserNames'][$comments['GarageComment']['id']]['User']) ? $garage['UserNames'][$comments['GarageComment']['id']]['User']['full_name'] : $comments['GarageComment']['created_by_name'] ); ?>
                </td>
                <td>
                    <?php echo h($comments['GarageComment']['body']); ?>
                </td>
            </tr>
        <?php
        }
        ?>
    </tbody>
</table>
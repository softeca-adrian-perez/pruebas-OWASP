<?php
$total_connections_desktop = 0;
$total_connections_mobile = 0;
$total_connections_tablet = 0;
?>
<div class="row">
    <div class="columns p-0 large-6 medium-12 m-bottom-1">
        <div class="p-1">
            <canvas id="chartDevices"></canvas>
        </div>
    </div>
    <div class="columns p-0 large-6 medium-12 m-bottom-1">
        <div class="o-auto">
            <table class="table-tracking">
                <thead>
                    <tr>
                        <th></th>
                        <th class="ta-center"><?php echo __t('Statistics.Desktop');?></th>
                        <th class="ta-center"><?php echo __t('Statistics.Mobile');?></th>
                        <th class="ta-center"><?php echo __t('Statistics.Tablet');?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($statistics_devices as $device){ ?>
                        <tr>
                            <td class="ta-center">
                                <?php echo $device['month'] ?>
                            </td>
                            <td class="ta-center"><?php echo $device[ConstantsDevices::DESKTOP];?></td>
                            <td class="ta-center"><?php echo $device[ConstantsDevices::MOBILE];?></td>
                            <td class="ta-center"><?php echo $device[ConstantsDevices::TABLET];?></td>
                        </tr>
                        <?php
                            $total_connections_desktop += $device[ConstantsDevices::DESKTOP];
                            $total_connections_mobile += $device[ConstantsDevices::MOBILE];
                            $total_connections_tablet += $device[ConstantsDevices::TABLET];
                        ?>
                    <?php } ?>
                    <tr>
                        <td class="ta-center fw-bold"><?php echo __t('General.Total'); ?></td>
                        <td class="ta-center fw-bold"><?php echo $total_connections_desktop; ?></td>
                        <td class="ta-center fw-bold"><?php echo $total_connections_mobile; ?></td>
                        <td class="ta-center fw-bold"><?php echo $total_connections_tablet; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
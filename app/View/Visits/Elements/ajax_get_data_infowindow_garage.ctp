<span class="ion-close-round f-right m-right-1" id="close_info"></span>
<?php if (!empty($garage_image)) {
    echo $this->Html->image(
        Router::url(
            array(
                'controller' => 'garages_images',
                'action' => 'download_file',
                $garage_image['GarageImage']['id'],
            )
        ),
        array(
            'alt' => __t('Garage.Picture_not_loaded'),
        )
    );
} else { ?>
    <img src="<?php echo '//maps.googleapis.com/maps/api/streetview?size=320x200&location=' . $garage['Garage']['latitude'] . ',' . $garage['Garage']['longitude'] . '&fov=90&heading=235&pitch=10&key=' . Texto::encryptDecryptText(GOOGLE_API_KEY, false); ?>" alt="Google Maps Street View Image" width="320px" height="200px">
<?php } ?>

<div class="p-1">
    <p style="color: #0064AE; margin-bottom: 5px !important;"><?php echo h($garage['Garage']['name']); ?></p>

    <p style="font-size: 12px;"><?php echo h($garage['Garage']['business_name']) . ' - ' . h($garage['Garage']['g_number_id']); ?></p>

    <div style="font-weight: lighter; color: dimgray; font-size: 22px;"><?php echo __t('Visit.Garage_data'); ?></div>

    <div style="display: block; padding: 5px 10px; background-color: #f5f5f7; font-size: 12px">
        <?php echo __t('Visit.Address'); ?>
        <strong style="display: block; font-size: 13px;">
            <?php echo h($garage['Garage']['address1']);
            if (!empty($garage['Garage']['address2'])) {
                echo ", " . h($garage['Garage']['address2']);
            }
            if (!empty($garage['Garage']['address3'])) {
                echo ", " . h($garage['Garage']['address3']);
            }
            if (!empty($garage['Garage']['address4'])) {
                echo ", " . h($garage['Garage']['address4']);
            }

            if (!empty($garage['Garage']['postcode'])) {
                echo ", " . h($garage['Garage']['postcode']);
            }
            if (!empty($garage['Garage']['town'])) {
                echo ", " . h($garage['Garage']['town']);
            }
            ?>
        </strong>
    </div>

    <div style="display: block; padding: 5px 10px; background-color: #f5f5f7; font-size: 12px; margin-top: 5px;">
        <?php echo __t('Visit.Garage_manager'); ?>
        <strong style="display: block; font-size: 13px;">
            <?php foreach ($garage_managers as $garage_manager) {
                echo (isset($garage_manager['Contact'])) ? h($garage_manager['Contact']['first_name']) . ' ' . h($garage_manager['Contact']['last_name']) : '';
                echo "<br>";
            } ?>
        </strong>
    </div>

    <div style="display: block; padding: 5px 10px; background-color: #f5f5f7; font-size: 12px; margin-top: 5px;">
        <?php echo __t('Visit.Phone'); ?>
        <strong style="display: block; font-size: 13px;"><?php echo h($garage['Garage']['phone']); ?> </strong>
    </div>

    <div id="add_to_route">
        <?php echo __t('Visit.Add_to_route'); ?>
    </div>

    <div id="remove_from_route">
        <?php echo __t('Visit.Remove_from_route'); ?>
    </div>
</div>
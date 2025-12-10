<span class="ion-close-round f-right m-right-1" id="close_info"></span>

<?php if (!empty($distributor_image)) {
    echo $this->Html->image(
        Router::url(
            array(
                'controller' => 'distributors_images',
                'action' => 'download_file',
                $distributor_image['DistributorImage']['id'],
            )
        ),
        array(
            'alt' => __t('Garage.Picture_not_loaded'),
        )
    );
} else { ?>
    <img src="<?php echo '//maps.googleapis.com/maps/api/streetview?size=320x200&location=' . $distributor['Distributor']['latitude'] . ',' . $distributor['Distributor']['longitude'] . '&fov=90&heading=235&pitch=10&key=' . Texto::encryptDecryptText(GOOGLE_API_KEY, false); ?>" alt="Google Maps Street View Image" width="320px" height="200px">
<?php } ?>


<div class="p-1">
    <p style="color: #0064AE; margin-bottom: 5px !important;"><?php echo $distributor['Distributor']['name']; ?></p>

    <p style="font-size: 12px;"><?php echo $distributor['Distributor']['account_number']; ?></p>

    <div style="font-weight: lighter; color: dimgray; font-size: 22px;"><?php echo __t('Visit.Distributor_data'); ?></div>

    <div style="display: block; padding: 5px 10px; background-color: #f5f5f7; font-size: 12px">
        <?php echo __t('Visit.Address'); ?>
        <strong style="display: block; font-size: 13px;">
            <?php echo h($distributor['Distributor']['address1']);
            if (!empty($distributor['Distributor']['address2'])) {
                echo ", " . h($distributor['Distributor']['address2']);
            }
            if (!empty($distributor['Distributor']['address3'])) {
                echo ", " . h($distributor['Distributor']['address3']);
            }
            if (!empty($distributor['Distributor']['address4'])) {
                echo ", " . h($distributor['Distributor']['address4']);
            }

            if (!empty($distributor['Distributor']['postcode'])) {
                echo ", " . h($distributor['Distributor']['postcode']);
            }
            if (!empty($distributor['Distributor']['town'])) {
                echo ", " . h($distributor['Distributor']['town']);
            }
            ?>
        </strong>
    </div>

    <div style="display: block; padding: 5px 10px; background-color: #f5f5f7; font-size: 12px; margin-top: 5px;">
        <?php echo __t('Visit.Distributor_manager'); ?>
        <strong style="display: block; font-size: 13px;">
            <?php foreach ($distributor_managers as $distributor_manager) {
                echo (isset($distributor_manager['Contact'])) ? h($distributor_manager['Contact']['first_name']) . ' ' . $distributor_manager['Contact']['last_name'] : '';
                echo "<br>";
            } ?>
        </strong>
    </div>

    <div style="display: block; padding: 5px 10px; background-color: #f5f5f7; font-size: 12px; margin-top: 5px;">
        <?php echo __t('Visit.Phone'); ?>
        <strong style="display: block; font-size: 13px;"><?php echo h($distributor['Distributor']['phone']); ?> </strong>
    </div>

    <div id="add_to_route">
        <?php echo __t('Visit.Add_to_route'); ?>
    </div>

    <div id="remove_from_route">
        <?php echo __t('Visit.Remove_from_route'); ?>
    </div>
</div>
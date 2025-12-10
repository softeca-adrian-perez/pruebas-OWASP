<div class="cnt-breadcrumb">
    <div>
        <?php echo $this->Html->breadcrumb(array(__t('General.My_garage'))); ?>
    </div>
</div>
<?php
echo $this->Html->script(CakeSession::read('GOOGLE_MAPS_API'), array('block' => 'script'));
echo $this->Html->script('gmaps.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('lib/fullcalendar-3.10.5/lib/moment.min.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('lib/fullcalendar-3.10.5/fullcalendar.min.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('lib/fullcalendar-3.10.5/locale/' . __l() . '.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('opening-times-general.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->css('../js/lib/fullcalendar-3.10.5/fullcalendar.min.css', array('block' => 'script'));

$days = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');
echo $this->Form->create(
    'Garage',
    array(
        'class' => 'aag-margin aag-padding aag-radius aag-background-container-color',
        'enctype' => 'multipart/form-data',
        'id' => 'opening-times-form'
    )
);
echo $this->Form->hidden(
    'Garage.id'
);
foreach ($days as $day) {
    echo $this->Form->hidden(
        $day . '_open_1',
        array(
            'id' => $day . '-open-1',
        )
    );
    echo $this->Form->hidden(
        $day . '_closed_1',
        array(
            'id' => $day . '-closed-1',
        )
    );
    echo $this->Form->hidden(
        $day . '_open_2',
        array(
            'id' => $day . '-open-2',
        )
    );
    echo $this->Form->hidden(
        $day . '_closed_2',
        array(
            'id' => $day . '-closed-2',
        )
    );
}
?>
<div class="buttons-fixed-double-tabs ta-right">
    <div>
        <?php echo $this->element('Comun/form_actions'); ?>
    </div>
</div>
<div class="aag-title-background">
    <?php echo h($garage['Garage']['name']); ?>
</div>
<hr />
<div class="aag-subtitle">
    <?php echo __t('Garage.Garage'); ?>
</div>

<?php
echo $this->Form->input(
    'Garage.slug',
    array(
        'type' => 'text',
        'readonly' => true,
        'disabled' => true,
        'label' => __t('General.Network_url'),
        'value' => ConstantsHTTP::HTTPS . Configure::read('URL_BASE') . "/" . $garage['Garage']['slug']
    )
);
?>
<hr />
<div class="aag-subtitle">
    <?php echo __t('Garage.Contact'); ?>
</div>
<div class="cnt-form-inputs">
    <?php
    echo $this->Form->input(
        'Garage.phone',
        array(
            'readonly' => $is_aag_region_uk,
            'disabled' => $is_aag_region_uk,
            'type' => 'text',
            'label' => __t('Garage.Phone'),
        )
    );
    echo $this->Form->input(
        'Garage.mobile',
        array(
            'readonly' => $is_aag_region_uk,
            'disabled' => $is_aag_region_uk,
            'type' => 'text',
            'label' => __t('Garage.Mobile'),
        )
    );
    echo $this->Form->input(
        'Garage.service_24h_phone',
        array(
            'readonly' => $is_aag_region_uk,
            'disabled' => $is_aag_region_uk,
            'type' => 'text',
            'label' => __t('Garage.24h_phone'),
        )
    );
    echo $this->Form->input(
        'Garage.fax',
        array(
            'readonly' => $is_aag_region_uk,
            'disabled' => $is_aag_region_uk,
            'type' => 'text',
            'label' => __t('Garage.Fax'),
        )
    );
    echo $this->Form->input(
        'Garage.email',
        array(
            'readonly' => $is_aag_region_uk,
            'disabled' => $is_aag_region_uk,
            'type' => 'text',
            'label' => __t('Garage.Email'),
        )
    );
    echo $this->Form->input(
        'Garage.web',
        array(
            'readonly' => $is_aag_region_uk,
            'disabled' => $is_aag_region_uk,
            'type' => 'text',
            'required' => false,
            'label' => __t('Garage.Web'),
        )
    );
    ?>
</div>
<hr />
<div class="aag-subtitle">
    <?php echo __t('Garage.Address'); ?>
</div>
<div class="cnt-form-inputs">
    <div class="two-columns">
        <?php
        echo $this->Form->input(
            'Garage.address1',
            array(
                'type' => 'text',
                'readonly' => true,
                'disabled' => true,
                'id' => 'autocomplete-address',
                'placeholder' => '',
                'data-map' => 'map-event-location',
                'label' => __t('Garage.Address_1'),
            )
        );
        ?>
    </div>
    <?php
    echo $this->Form->input(
        'Garage.address2',
        array(
            'type' => 'text',
            'readonly' => true,
            'disabled' => true,
            'label' => __t('Garage.Address_2'),
        )
    );
    echo $this->Form->input(
        'Garage.address3',
        array(
            'type' => 'text',
            'readonly' => true,
            'disabled' => true,
            'label' => __t('Garage.Address_3'),
        )
    );
    echo $this->Form->input(
        'Garage.address4',
        array(
            'type' => 'text',
            'readonly' => true,
            'disabled' => true,
            'label' => __t('Garage.Address_4'),
        )
    );
    echo $this->Form->input(
        'Garage.town',
        array(
            'type' => 'text',
            'readonly' => true,
            'disabled' => true,
            'label' => __t('Garage.Town'),
        )
    );
    echo $this->Form->input(
        'Garage.country',
        array(
            'type' => 'text',
            'readonly' => true,
            'disabled' => true,
            'value' => $country_name,
            'label' => __t('Garage.Country'),
        )
    );
    ?>
    <div id="div_provinces">
        <?php
        echo $this->Form->input(
            'Garage.province_id',
            array(
                'type' => 'text',
                'readonly' => true,
                'disabled' => true,
                'value' => $province_name,
                'label' => __t('Garage.Province'),
            )
        );
        ?>
    </div>
    <?php
    echo $this->Form->input(
        'Garage.postcode',
        array(
            'type' => 'text',
            'readonly' => true,
            'disabled' => true,
            'label' => __t('Garage.Postcode'),
        )
    );
    ?>
</div>
<br />
<div class="cnt-form-inputs">
    <?php
    echo $this->Form->input(
        'latitude',
        array(
            'label' => __t('Garage.Latitude'),
            'id' => 'latitude-localization',
            'value' => $garage['Garage']['latitude'],
            'readonly' => true,
            'disabled' => true,
        )
    );
    echo $this->Form->input(
        'longitude',
        array(
            'label' => __t('Garage.Longitude'),
            'id' => 'longitude-localization',
            'value' => $garage['Garage']['longitude'],
            'readonly' => true,
            'disabled' => true,
        )
    );
    ?>
</div>
<br />
<div id="map-event-location" class="contenedor-mapa" style="height: 400px;" data-editable=""></div>
<div class="alliance_bar"></div>
<hr />
<div class="aag-subtitle">
    <?php echo __t('Garage.Opening_times'); ?>
</div>
<div class="p-1" id="calendar" data-url_events="<?php echo Router::url(array('controller' => 'garages', 'action' => 'ajax_events_list', $garage_id)); ?>"></div>
<?php echo $this->Form->end(); ?>
<style>
    .cnt-max-date-per-day {
        display: grid;
        gap: 15px;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    }

    .cnt-max-date-per-day>div {
        background-color: var(--container-elements-color);
        border-radius: 10px;
        padding: 10px
    }

    .cnt-max-date-per-day>div span {
        font-weight: bold;
        font-size: 14px;
    }

    .cnt-max-date-per-day>div label {
        font-weight: normal;
        font-size: 12px !important;
    }

    .cnt-max-date-per-day>div>div+div {
        margin-top: 10px;
    }

    .cnt-max-date-per-day>div input {
        margin: 0;
        width: 100%;
        min-width: 100%;
        max-width: 100%;
    }
</style>
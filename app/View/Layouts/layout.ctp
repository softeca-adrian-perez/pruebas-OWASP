<!DOCTYPE html>
<!--[if IE 8]><html class="no-js lt-ie9" lang="es" > <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="en"> <!--<![endif]-->

<head>
    <!-- Google Tag Manager -->
    <script>
        (function(w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start': new Date().getTime(),
                event: 'gtm.js'
            });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s),
                dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src =
                'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', '<?php echo GOOGLE_TAG_MANAGER_ID ?>');
    </script>
    <!-- End Google Tag Manager -->
    <?php echo $this->Html->charset(); ?>
    <meta name="viewport" content="width=device-width, user-scalable=no" />
    <title>
        <?php echo __('Alliance Automotive Group') ?>
    </title>
    <?php
    echo $this->Html->meta('icon');
    echo $this->fetch('meta');

    ?>
    <!-- <link href='https://fonts.googleapis.com/css?family=Roboto:300,400,700' rel='stylesheet' type='text/css'> -->
    <?php
    echo $this->Html->css('ionicons.min.css');
    echo $this->Html->css('lib/foundation-6.0.0/css/foundation.min.css');
    echo $this->Html->css('../js/lib/jquery-ui-1.13.2/jquery-ui.min.css');
    echo $this->Html->css('../js/lib/tooltipster/dist/css/tooltipster.bundle.min.css');
    echo $this->Html->css('../js/lib/sweetalert2/dist/sweetalert2.css');
    echo $this->Html->css('../js/lib/timepicker/timepicker.css');
    echo $this->fetch('css');
    $networks = CakeSession::read('Auth.User.networks');
    $network = array();
    $this->Network = ClassRegistry::init('Network');
    if(!empty($networks)){
        $network = $this->Network->findById($networks[0]);
    }

    ?>
    <style>
        :root {
            <?php
            if (CakeSession::read('Auth.User.current_network') && CakeSession::read('Auth.User.garage_id')) {
            ?>--font-size: 10px;
            --body-color: <?php echo '#' . $network['Network']['menu_background_color']; ?>;
            --container-color: <?php echo '#' . $network['Network']['primary_background_color']; ?>;
            --container-elements-color: <?php echo '#' . $network['Network']['secondary_background_color']; ?>;
            --success-color: <?php echo '#' . $network['Network']['color_exito']; ?>;
            --error-color: <?php echo '#' . $network['Network']['color_fallo']; ?>;
            --information-color: <?php echo '#' . $network['Network']['color_informacion']; ?>;
            --primary-color: <?php echo '#' . $network['Network']['primary_color']; ?>;
            --primary-font-color: <?php echo '#' . $network['Network']['primary_font_color']; ?>;
            --secondary-color: <?php echo '#' . $network['Network']['secondary_color']; ?>;
            --secondary-font-color: <?php echo '#' . $network['Network']['secondary_font_color']; ?>;
            --tertiary-color: <?php echo '#' . $network['Network']['tertiary_color']; ?>;
            --tertiary-font-color: <?php echo '#' . $network['Network']['tertiary_font_color']; ?>;
            --quaternary-color: <?php echo '#' . $network['Network']['quaternary_color']; ?>;
            --quaternary-font-color: <?php echo '#' . $network['Network']['quaternary_font_color']; ?>;
            --font-default-color: <?php echo '#' . $network['Network']['font_default_color']; ?>;
            <?php
            } else {
            ?>--font-size: 10px;
            --body-color: #E4E4E4;
            --container-color: #FFFFFF;
            --container-elements-color: #F5F4F7;
            --success-color: #5DBC56;
            --error-color: #FE472F;
            --information-color: #F27B4D;
            --primary-color: #085D9C;
            --primary-font-color: #FFFFFF;
            --secondary-color: #2197E6;
            --secondary-font-color: #FFFFFF;
            --tertiary-color: #90A8BE;
            --tertiary-font-color: #FFFFFF;
            --quaternary-color: #E8E8E8;
            --quaternary-font-color: #FFFFFF;
            --font-default-color: #252525;
            <?php
                // echo $this->Html->css('estilos.css?v='.Configure::read('VERSION_CACHE'),array('id' => 'css_id'));
            }
            ?>
        }
    </style>
    <?php
    echo $this->Html->css('estilos.css?v=' . Configure::read('VERSION_CACHE'), array('id' => 'css_id'));
    echo $this->Html->css('new-style.css?v=' . Configure::read('VERSION_CACHE'));
    ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!--[if lt IE 9]><?php echo $this->Html->css('lib/ie8.css'); ?><![endif]-->
    <!--[if lt IE 9]><?php echo $this->Html->css('lib/ie8-grid-foundation-4.css'); ?><![endif]-->
    <?php
    echo $this->Html->script('../css/lib/foundation-6.0.0/js/vendor/modernizr.js?v=' . Configure::read('VERSION_CACHE'));
    echo $this->Html->script('lib/jquery-3.7.1.min.js?v=' . Configure::read('VERSION_CACHE'));
    echo $this->Html->script('lib/jquery-ui-1.13.2/jquery-ui.min.js?v=' . Configure::read('VERSION_CACHE'));
    echo $this->Html->script('app-scripts.js?v=' . Configure::read('VERSION_CACHE'));
    echo $this->Html->script('lib/jquery.i18n.js?v=' . Configure::read('VERSION_CACHE'));
    echo $this->Html->script('translations/Translation' . __s() . '.js?v=' . Configure::read('VERSION_CACHE'));
    echo $this->Html->script('lib/nicefileinput/jquery.nice-file-input.min.js?v=' . Configure::read('VERSION_CACHE'));
    echo $this->Html->script('lib/core_promises.js?v=' . Configure::read('VERSION_CACHE'));
    echo $this->Html->script('lib/tooltipster/dist/js/tooltipster.bundle.min.js?v=' . Configure::read('VERSION_CACHE'));
    echo $this->Html->script('lib/jquery.tablesorter.min.js?v=' . Configure::read('VERSION_CACHE'));
    echo $this->Html->script('lib/sweetalert2/dist/sweetalert2.min.js?v=' . Configure::read('VERSION_CACHE'));
    echo $this->Html->script('lib/timepicker/timepicker.js?v=' . Configure::read('VERSION_CACHE'));
    echo $this->Html->script('/js/jquery.basictable.min.js?v=' . Configure::read('VERSION_CACHE'));
    echo $this->Html->script('/js/lib/jquery.ui.touch-punch.min.js?v=' . Configure::read('VERSION_CACHE'));
    echo $this->Html->script('/js/jquery.nicescroll.js?v=' . Configure::read('VERSION_CACHE'));
    echo $this->Html->script('jquery.cookie.js?v=' . Configure::read('VERSION_CACHE'));
    echo $this->Html->script('lib/moment.js?v=' . Configure::read('VERSION_CACHE'));
    echo $this->Html->script('select2.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
    echo $this->Html->script('lib/select2-4.0.0/dist/js/select2.full.min.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
    echo $this->fetch('script');
    ?>

    <!--[if lt IE 9]> <?php echo $this->Html->script('lib/html5shiv.js'); ?> <![endif]-->
    <!--[if lt IE 9]> <?php echo $this->Html->script('lib/nwmatcher-1.2.5-min.js'); ?> <![endif]-->
    <!--[if lt IE 9]> <?php echo $this->Html->script('lib/selectivizr-1.0.3b.js'); ?> <![endif]-->
    <!--[if lt IE 9]> <?php echo $this->Html->script('lib/respond.min.js'); ?> <![endif]-->

</head>

<body class="<?php echo $this->fetch('body_class'); ?>">
    <!-- Google Tag Manager (noscript) -->
    <?php $body_url = 'https://www.googletagmanager.com/ns.html?id='.GOOGLE_TAG_MANAGER_ID?>
    <noscript><iframe src=<?php echo $body_url ?> height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    <div id="container">
        <div class="alert-flash-js">
            <?php echo $this->Session->flash(); ?>
        </div>

        <?php echo $this->fetch('cabecera'); ?>

        <div class="content">
            <div>
                <?php echo $this->fetch('content'); ?>
            </div>
        </div>
    </div>
    <?php
    echo $this->fetch('pie');
    echo $this->Html->script('../css/lib/foundation-6.0.0/js/vendor/foundation.min.js');
    echo $this->Html->script('../css/lib/foundation-6.0.0/js/vendor/foundation.js');
    //echo $this->Html->script("../css/lib/foundation-5.4.5/js/foundation/foundation.alerts.js");
    // echo $this->Html->script("../css/lib/foundation-5.4.5/js/foundation/foundation.topbar.js");
    ?>
    <script>
        $(document).foundation();
        var menu = $('.left-off-canvas-menu');
        $(document).on('resize', function() {
            // Beware with resize handlers...
            //  Throttle & consolidate #perfmatters
            menu.height($(this).height());
        });
        // Initialize height
        $(document).trigger('resize');
        $('table.tabla-responsive, .table-tracking').basictable();
        const language_code = "<?php echo __l() ?>";
        const primary_color = $('#c-primary').css('color');
        const STATUS_APPOINTMENTS_PLANNED = "<?php echo ConstantsStatusAppointments::PLANNED; ?>";
        const STATUS_APPOINTMENTS_PENDING = "<?php echo ConstantsStatusAppointments::PENDING; ?>";
        const STATUS_APPOINTMENTS_CANCEL = "<?php echo ConstantsStatusAppointments::CANCELED; ?>";
        const STATUS_APPOINTMENTS_COMPLETED = "<?php echo ConstantsStatusAppointments::ACCOMPLISHED; ?>";
        const STATUS_APPOINTMENTS_RUNNING = "<?php echo ConstantsStatusAppointmentsDe::RUNNING; ?>";

        if ("<?php echo CakeSession::read('Auth.User.Preferences.1'); ?>" == null) {
            var calendar_view_preferences = 'agendaWeekLabor';
        } else if ("<?php echo CakeSession::read('Auth.User.Preferences.1'); ?>" == "<?php echo ConstantsCalendarView::DAY; ?>") {
            var calendar_view_preferences = 'agendaDay';
        } else if ("<?php echo CakeSession::read('Auth.User.Preferences.1'); ?>" == "<?php echo ConstantsCalendarView::WORKWEEK; ?>") {
            var calendar_view_preferences = 'agendaWeekLabor';
        } else if ("<?php echo CakeSession::read('Auth.User.Preferences.1'); ?>" == "<?php echo ConstantsCalendarView::WEEK; ?>") {
            var calendar_view_preferences = 'agendaWeek';

        } else if ("<?php echo CakeSession::read('Auth.User.Preferences.1'); ?>" == "<?php echo ConstantsCalendarView::MONTH; ?>") {
            var calendar_view_preferences = 'month';
        } else {
            var calendar_view_preferences = 'agendaWeekLabor';
        }

        if ("<?php echo CakeSession::read('Auth.User.Preferences.2'); ?>" == null) {
            var PAGINATION_PREFERENCES = "<?php echo ConstantsPagination::SIZE_PAGE_SMALL; ?>";
        } else if ("<?php echo CakeSession::read('Auth.User.Preferences.1'); ?>" > 0) {
            var PAGINATION_PREFERENCES = "<?php echo CakeSession::read('Auth.User.Preferences.2'); ?>";
        } else {
            var PAGINATION_PREFERENCES = "<?php echo ConstantsPagination::SIZE_PAGE_SMALL; ?>";
        }
    </script>
</body>

</html>
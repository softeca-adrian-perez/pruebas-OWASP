<div class="otnotice-language-dropdown-container" style="max-width: 200px;margin: 0 0 0 auto;">
    <select id="otnotice-language-dropdown" aria-label="language selector"></select>
</div>

<div id="<?php echo $privacy_notice_code; ?>" class="otnotice"></div>

<script
    type="text/javascript"
    src="https://privacyportal-cdn.onetrust.com/privacy-notice-scripts/otnotice-1.0.min.js"
    charset="UTF-8"
    id="otprivacy-notice-script"
    settings="<?php echo $settings; ?>">
</script>

<script type="text/javascript" charset="UTF-8">
    OneTrust.NoticeApi.Initialized.then(function() {
        OneTrust.NoticeApi.LoadNotices(["<?php echo $cdn_script; ?>"], <?php echo Configure::read('ENVIRONMENT_PRO') ? 'true' : 'false' ?>);
    });
</script>
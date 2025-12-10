<?php
$this->assign('body_class', 'body-login');
echo $this->Form->create('User', array('enctype' => 'multipart/form-data'));
?>
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
<div class="logo">
    <?php
    echo $this->Html->image('aag.svg', array('alt' => __t('Logotipo')));
    ?>
    <div class="ta-center title">
        <?php echo  __t('User.Slogan') ?>
    </div>
    <?php
    echo $this->Html->link(__t('General.Back'), array('controller' => 'users', 'action' => 'login'));
    ?>
</div>
<div>
    <div class="aag-title">
        <?php echo __t('User.Retrieve_password'); ?>
    </div>
    <div>
        <?php
        echo $this->Form->input('username', array('label' => __t('User.Username'), 'type' => 'text')); ?>
        <div class="cf-turnstile ta-center m-top-1" data-sitekey="<?php echo CLOUDFLARE_SITE_KEY ?>" data-theme="light"></div>
        <?php
        echo $this->Form->submit(__t('General.Send'), array('class' => 'aag-button medium w-100p'));
        ?>
    </div>
</div>
<?php echo $this->Form->end(); ?>
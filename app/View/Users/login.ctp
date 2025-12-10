<?php $this->assign('body_class', 'body-login'); ?>
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
<?php echo $this->Form->create('User', array('url' => array('controller' => 'users', 'action' => 'login'))); ?>
<div id="logotipo-login" class="logo">
    <?php
    echo $this->Html->image('aag.svg', array('alt' => __t('Logotipo')));
    ?>
    <div class="ta-center title">
        <?php echo  __t('User.Slogan') ?>
    </div>
    <?php
    echo $this->Html->link(__t('User.Lost_password?'), array('controller' => 'users', 'action' => 'recover_password'));
    ?>
</div>
<div>
    <div class="aag-title">
        <?php echo __t('User.Login'); ?>
    </div>
    <div>
        <?php
        echo $this->Form->input(
            'User.username',
            array(
                'label' => __t('User.User'),
                'type' => 'text',
                'autofocus',
                'tabindex' => '1'
            )
        );
        echo $this->Form->input(
            'User.password',
            array(
                'label' => __t('User.Password'),
                'type' => 'password',
                'tabindex' => '2',
                'autocomplete' => 'off'
            )
        );
        ?>
        <div class="cf-turnstile ta-center m-top-1" data-sitekey="<?php echo CLOUDFLARE_SITE_KEY ?>" data-theme="light"></div>
        <?php echo $this->Form->submit(__t('Button.Login'), array('tabindex' => '3', 'class' => "aag-button medium w-100p")); ?>
    </div>
</div>
<?php echo $this->Form->end(); ?>
<footer id="pie" class="contain-to-grid">
    <div class="ta-center fs-small">
        &copy; <?php echo date('Y'); ?> · Alliance Automotive Group
        <a href="<?php echo $this->Html->url(['controller' => 'users', 'action' => 'privacy_notice']); ?>">
            <?php echo __t('General.Privacy_notice') ?>
        </a>
    </div>
</footer>
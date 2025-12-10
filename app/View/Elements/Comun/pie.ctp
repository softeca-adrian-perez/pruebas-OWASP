<footer id="pie" class="contain-to-grid">
    <div class="ta-center fs-small">
        &copy; <?php echo date('Y'); ?> · Alliance Automotive Group
        <a href="<?php echo $this->Html->url(['controller' => 'Home', 'action' => 'privacy_notice']); ?>">
            <?php echo __t('General.Privacy_notice') ?>
        </a>
    </div>
    <div title="<?php echo __t('General.Up'); ?>" id="boton-subir-cabecera">
        <div class="cursor-pointer">
            <span class="ion-ios-arrow-up"></span>
            <span class="ion-ios-arrow-up"></span>
        </div>
    </div>
</footer>
<div class="cnt-item-with-image">
    <span class="supplier-card">
        <?php echo h($valueAddedSupplier['ValueAddedSupplier']['title']); ?>
    </span>
    <div class="owl-carousel owl-theme sections_carousel">
        <div class="item">

            <div class="description-scroll">
                <?php if (isset($valueAddedSupplier['ValueAddedSupplier']['logo_image'])) { ?>
                    <div class="supplier-card-logo">
                        <img class="supplier-card-logo-2"src="<?php echo FileManager::get_url(FilePaths::VALUE_ADDED_SUPPLIERS_LOGO_IMAGES_RELATIVE . $valueAddedSupplier['ValueAddedSupplier']['logo_image']); ?>"/>
                    </div>
                <?php } ?>

                <?php if (isset($valueAddedSupplier['ValueAddedSupplier']['description'])) { ?>
                    <div> <?php echo $valueAddedSupplier['ValueAddedSupplier']['description']; ?> </div>
                <?php } ?>
            </div>

            <?php if (isset($valueAddedSupplier['ValueAddedSupplier']['supplier_url']) && !empty($valueAddedSupplier['ValueAddedSupplier']['supplier_url'])) { ?>
                <div>
                    <a href=<?php echo $valueAddedSupplier['ValueAddedSupplier']['supplier_url']; ?>>
                        <button class = "aag-button medium w-100p supplier-card-link"><?php echo __t('ValueAddedSuppliers.Link') ?></button>
                    </a>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php
$i = 1;
$info_views = array();
foreach ($suppliers as $key => $supplier) {
    if ($i % 6 == 1) {
?>
        <div class="row supliers" data-equalizer>
            <div class="medium-2 small-6 columns supplierClick end" id="supplier<?php echo $key ?>" data-id="<?php echo $supplier['Supplier']['id']; ?>" data-video-supplier-<?php echo $supplier['Supplier']['id'] ?>="<?php echo $supplier['Supplier']['url_video']; ?>" data-equalizer-watch>
                <div class="cnt-imageBrand">
                    <?php if ($supplier['SupplierImage']['file']) { ?>
                        <img src="<?php echo FileManager::get_url(FilePaths::SUPPLIERS_IMAGES_RELATIVE . $supplier['SupplierImage']['file']); ?>" alt="<?php echo $supplier['Supplier']['name']; ?>" style="display: flex;align-items: center;justify-content: center;" />
                    <?php
                    } else {
                        echo $supplier['Supplier']['name'];
                    }
                    ?>
                </div>
            </div>
        <?php
        $info_views[$key] = $supplier;
    } elseif ($i % 6 == 0) {
        ?>
            <div class="medium-2 small-6 columns supplierClick end" id="supplier<?php echo $key ?>" data-id="<?php echo $supplier['Supplier']['id']; ?>" data-video-supplier-<?php echo $supplier['Supplier']['id'] ?>="<?php echo $supplier['Supplier']['url_video']; ?>" data-equalizer-watch>
                <div class="cnt-imageBrand">
                    <?php if ($supplier['SupplierImage']['file']) { ?>
                        <img src="<?php echo FileManager::get_url(FilePaths::SUPPLIERS_IMAGES_RELATIVE . $supplier['SupplierImage']['file']); ?>" alt="<?php echo $supplier['Supplier']['name']; ?>" style="display: flex;align-items: center;justify-content: center;" />
                    <?php
                    } else {
                        echo $supplier['Supplier']['name'];
                    }
                    ?>
                </div>
            </div>
        </div>
        <?php
        $info_views[$key] = $supplier;
        $cont = 1;
        foreach ($info_views as $key2 => $info_view) {
            if ($cont % 6 == 1) {
        ?>
                <div class="columns medium-12 d-inline-block w-100p f-left" style="padding-left: 15px; padding-right: 15px;">
                    <div class="supplier<?php echo $key2 ?> supplierInfo">
                        <div class="background-color-secondary-supplier" style="padding-top: 25px;padding-bottom: 25px;">
                            <div class="row">
                                <div class="columns medium-10">
                                    <div class="style5" style="margin-bottom: 16px;">
                                        <?php echo h($info_view['Supplier']['name']); ?>
                                    </div>
                                    <div class="style4">
                                        <?php echo h($info_view['Supplier']['description']); ?>
                                    </div>
                                </div>
                                <div class="columns medium-2 ta-right">
                                    <div class="style4">
                                        <a href="<?php echo h($info_view['Supplier']['web']); ?>" class="visit-website f-right" target="_blank">
                                            <?php echo __t('Suppliers.Visit_website'); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="background-color-secondary cnt-brands-list-title">
                            <div class="row">
                                <div class="columns medium-12">
                                    <div class="style5" style="padding: 5px 0">
                                        <?php echo __t('Suppliers.Brands'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="cnt-brands-list">
                            <div class="row">
                                <div class="columns medium-12">
                                    <div>
                                        <?php
                                        foreach ($info_view['Brands'] as $brand) {
                                            if (!empty($brand['BrandImage']['file'])) {
                                        ?>
                                                <img src="<?php echo FileManager::get_url(FilePaths::BRANDS_IMAGES_RELATIVE . $brand['BrandImage']['file']); ?>" alt="<?php echo $brand['Brand']['name']; ?>" style="height: 50px; margin: 20px 20px 20px 0;" class="brands cursor-pointer" data-id="<?php echo $brand['Brand']['id'] ?>" />
                                            <?php } else { ?>
                                                <span style="height: 25px; margin: 20px 20px 20px 0;" class="brands cursor-pointer" data-id="<?php echo $brand['Brand']['id'] ?>">
                                                    <?php echo $brand['Brand']['name']; ?>
                                                </span>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="cnt-video-info">
                                <div class="row">
                                    <?php if (!empty($info_view['Supplier']['url_channel'])) { ?>
                                        <div class="columns medium-5">
                                            <div class="style5 youtube-title">
                                                <?php echo __t('Suppliers.Youtube_channel'); ?>
                                            </div>
                                            <div class="video-supplier-<?php echo $info_view['Supplier']['id'] ?>"></div>
                                            <?php if (!empty($info_view['Supplier']['url_channel'])) { ?>
                                                <div class="ta-right">
                                                    <a href="<?php echo h($info_view['Supplier']['url_channel']); ?>" target="_blank" class="channel-link style5">
                                                        <?php echo __t('Suppliers.Visit_our_youtube_channel'); ?>
                                                    </a>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                    <div class="medium-6 medium-offset-1 columns p-0">
                                        <div class="columns medium-12">
                                            <div class="youtube-title" style="margin-bottom: 13px;">
                                                <?php echo __t('Suppliers.Documents'); ?>
                                            </div>
                                        </div>
                                        <div class="columns medium-12 fs-medium">
                                            <?php
                                            if (!empty($info_view['SupplierFile'])) {
                                                foreach ($info_view['SupplierFile'] as $category_name => $suppliers_category) {
                                            ?>
                                                    <div class="medium-12 columns end titulo-category-suppliers p-top-1">
                                                        <?php echo h($category_name); ?>
                                                    </div>
                                                    <?php foreach ($suppliers_category as $file) { ?>
                                                        <div class="medium-12 columns end">
                                                            <?php
                                                            echo $this->Html->link(
                                                                substr($file['SupplierFile']['source_name'], 0, (strrpos($file['SupplierFile']['source_name'], "."))),
                                                                array(
                                                                    'controller' => 'suppliers_files',
                                                                    'action' => 'download_file',
                                                                    $file['SupplierFile']['id']
                                                                ),
                                                                array('class' => 'p-left-1')
                                                            );
                                                            ?>
                                                            <br />
                                                        </div>
                                            <?php
                                                    }
                                                }
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } elseif ($cont % 6 == 0) { ?>
                    <div class="supplier<?php echo $key2 ?> supplierInfo">
                        <div class="background-color-secondary-supplier" style="padding-top: 25px;padding-bottom: 25px;">
                            <div class="row">
                                <div class="columns medium-10">
                                    <div class="style5" style="margin-bottom: 16px;">
                                        <?php echo h($info_view['Supplier']['name']); ?>
                                    </div>
                                    <div class="style4">
                                        <?php echo h($info_view['Supplier']['description']); ?>
                                    </div>
                                </div>
                                <div class="columns medium-2 ta-right">
                                    <div class="style4">
                                        <a href="<?php echo $info_view['Supplier']['web']; ?>" class="visit-website f-right" target="_blank">
                                            <?php echo __t('Suppliers.Visit_website'); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="background-color-secondary cnt-brands-list-title">
                            <div class="row">
                                <div class="columns medium-12">
                                    <div class="style5" style="padding: 5px 0">
                                        <?php echo __t('Suppliers.Brands'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="cnt-brands-list">
                            <div class="row">
                                <div class="columns medium-12">
                                    <div>
                                        <?php
                                        foreach ($info_view['Brands'] as $brand) {
                                            if (!empty($brand['BrandImage']['file'])) {
                                        ?>
                                                <img src="<?php echo FileManager::get_url(FilePaths::BRANDS_IMAGES_RELATIVE . $brand['BrandImage']['file']); ?>" alt="<?php echo $brand['Brand']['name']; ?>" style="height: 50px; margin: 20px 20px 20px 0;" class="brands cursor-pointer" data-id="<?php echo $brand['Brand']['id'] ?>" />
                                            <?php } else { ?>
                                                <span style="height: 25px; margin: 20px 20px 20px 0;" class="brands cursor-pointer" data-id="<?php echo $brand['Brand']['id'] ?>"><?php echo $brand['Brand']['name']; ?></span>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="cnt-video-info">
                                <div class="row">
                                    <?php if (!empty($info_view['Supplier']['url_channel'])) { ?>
                                        <div class="columns medium-5">
                                            <div class="style5 youtube-title">
                                                <?php echo __t('Suppliers.Youtube_channel'); ?>
                                            </div>
                                            <div class="video-supplier-<?php echo $info_view['Supplier']['id'] ?>">
                                            </div>
                                            <?php if (!empty($info_view['Supplier']['url_channel'])) { ?>
                                                <div class="ta-right">
                                                    <a href="<?php echo h($info_view['Supplier']['url_channel']); ?>" target="_blank" class="channel-link style5">
                                                        <?php echo __t('Suppliers.Visit_our_youtube_channel'); ?>
                                                    </a>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                    <div class="medium-6 medium-offset-1 columns p-0">
                                        <div class="columns medium-12">
                                            <div class="youtube-title" style="margin-bottom: 13px;">
                                                <?php echo __t('Suppliers.Documents'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } else { ?>
                <div class="supplier<?php echo $key2 ?> supplierInfo">
                    <div class="background-color-secondary-supplier" style="padding-top: 25px;padding-bottom: 25px;">
                        <div class="row">
                            <div class="columns medium-10">
                                <div class="style5" style="margin-bottom: 16px;">
                                    <?php echo h($info_view['Supplier']['name']); ?>
                                </div>
                                <div class="style4">
                                    <?php echo h($info_view['Supplier']['description']); ?>
                                </div>
                            </div>
                            <div class="columns medium-2 ta-right">
                                <div class="style4">
                                    <a href="<?php echo h($info_view['Supplier']['web']); ?>" class="visit-website f-right" target="_blank">
                                        <?php echo __t('Suppliers.Visit_website'); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="background-color-secondary cnt-brands-list-title">
                        <div class="row">
                            <div class="columns medium-12">
                                <div class="style5" style="padding: 5px 0">
                                    <?php echo __t('Suppliers.Brands'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="cnt-brands-list">
                        <div class="row">
                            <div class="columns medium-12">
                                <div>
                                    <?php
                                    foreach ($info_view['Brands'] as $brand) {
                                        if (!empty($brand['BrandImage']['file'])) {
                                    ?>
                                            <img src="<?php echo FileManager::get_url(FilePaths::BRANDS_IMAGES_RELATIVE . $brand['BrandImage']['file']); ?>" alt="<?php echo $brand['Brand']['name']; ?>" style="height: 50px; margin: 20px 20px 20px 0;" class="brands cursor-pointer" data-id="<?php echo $brand['Brand']['id'] ?>" />
                                        <?php } else { ?>
                                            <span style="height: 25px; margin: 20px 20px 20px 0;" class="brands cursor-pointer" data-id="<?php echo $brand['Brand']['id'] ?>"><?php echo $brand['Brand']['name']; ?></span>
                                    <?php
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="cnt-video-info">
                            <div class="row">
                                <?php if (!empty($info_view['Supplier']['url_channel'])) { ?>
                                    <div class="columns medium-5">
                                        <div class="style5 youtube-title">
                                            <?php echo __t('Suppliers.Youtube_channel'); ?>
                                        </div>
                                        <div class="video-supplier-<?php echo $info_view['Supplier']['id'] ?>"></div>
                                        <?php if (!empty($info_view['Supplier']['url_channel'])) { ?>
                                            <div class="ta-right">
                                                <a href="<?php echo h($info_view['Supplier']['url_channel']); ?>" target="_blank" class="channel-link style5">
                                                    <?php echo __t('Suppliers.Visit_our_youtube_channel'); ?>
                                                </a>
                                            </div>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                                <div class="medium-6 medium-offset-1 columns p-0">
                                    <div class="columns medium-12">
                                        <div class="youtube-title" style="margin-bottom: 13px;">
                                            <?php echo __t('Suppliers.Documents'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        <?php
            }
            $cont++;
        }
        $info_views = array();
    } else {
        ?>
        <div class="medium-2 small-6 columns supplierClick end" id="supplier<?php echo $key ?>" data-id="<?php echo $supplier['Supplier']['id']; ?>" data-video-supplier-<?php echo $supplier['Supplier']['id'] ?>="<?php echo $supplier['Supplier']['url_video']; ?>" data-equalizer-watch>
            <div class="cnt-imageBrand">
                <?php if ($supplier['SupplierImage']['file']) { ?>
                    <img src="<?php echo FileManager::get_url(FilePaths::SUPPLIERS_IMAGES_RELATIVE . $supplier['SupplierImage']['file']); ?>" alt="<?php echo $supplier['Supplier']['name']; ?>" style="display: flex;align-items: center;justify-content: center;" />
                <?php
                } else {
                    echo $supplier['Supplier']['name'];
                }
                ?>
            </div>
        </div>
    <?php
        $info_views[$key] = $supplier;
    }
    if (($i == count($suppliers)) && ($i % 6 != 0)) {
    ?>
        </div>
        <div class="columns medium-12 d-inline-block w-100p f-left" style="padding-left: 15px; padding-right: 15px;">
            <?php foreach ($info_views as $key2 => $info_view) { ?>
                <div class="supplier<?php echo $key2 ?> supplierInfo">
                    <div class="background-color-secondary-supplier" style="padding-top: 25px;padding-bottom: 25px;">
                        <div class="row">
                            <div class="columns medium-10">
                                <div class="columns medium-12 p-0">
                                    <?php echo h($info_view['Supplier']['name']); ?>
                                </div>
                                <div class="columns medium-12 p-0">
                                    <?php
                                    if ($info_view['Supplier']['address1'] || $info_view['Supplier']['address2'] || $info_view['Supplier']['postcode'] || $info_view['Supplier']['town']) {
                                        echo __t('Suppliers.Address') . ': ';
                                    }
                                    if ($info_view['Supplier']['address1']) {
                                        echo h($info_view['Supplier']['address1']);
                                    }
                                    if ($info_view['Supplier']['address2']) {
                                        if ($info_view['Supplier']['address1']) {
                                            echo ', ';
                                        }
                                        echo h($info_view['Supplier']['address2']);
                                    }
                                    if ($info_view['Supplier']['postcode']) {
                                        if ($info_view['Supplier']['address2']) {
                                            echo ', ';
                                        }
                                        echo h($info_view['Supplier']['postcode']);
                                    }
                                    if ($info_view['Supplier']['town']) {
                                        if ($info_view['Supplier']['postcode']) {
                                            echo ', ';
                                        }
                                        echo h($info_view['Supplier']['town']);
                                    }
                                    ?>
                                </div>
                                <div class="columns medium-12 p-0">
                                    <?php
                                    if ($info_view['Supplier']['phone']) {
                                        echo __t('Suppliers.Phone') . ': ' . h($info_view['Supplier']['phone']);
                                    }
                                    if ($info_view['Supplier']['phone']) {
                                        if ($info_view['Supplier']['phone']) {
                                            echo ' ';
                                        }
                                        echo __t('Suppliers.Fax') . ': ' . h($info_view['Supplier']['fax']);
                                    }
                                    if ($info_view['Supplier']['email']) {
                                        if ($info_view['Supplier']['fax']) {
                                            echo ' ';
                                        }
                                        echo __t('Suppliers.Email') . ': ' . h($info_view['Supplier']['email']);
                                    }
                                    ?>
                                </div>
                                <div class="columns medium-12 p-0">
                                    <?php
                                    if ($info_view['Supplier']['VAT_number']) {
                                        echo __t('Suppliers.VAT_number') . ': ' . h($info_view['Supplier']['VAT_number']);
                                    }
                                    if ($info_view['Supplier']['siret']) {
                                        if ($info_view['Supplier']['VAT_number']) {
                                            echo ' ';
                                        }
                                        echo __t('Suppliers.Siret') . ': ' . h($info_view['Supplier']['siret']);
                                    }
                                    ?>
                                </div>
                                <div class="columns medium-12 p-0">
                                    <?php echo h($info_view['Supplier']['description']); ?>
                                </div>
                            </div>
                            <div class="columns medium-2 ta-right">
                                <div class="style4">
                                    <a href="<?php echo h($info_view['Supplier']['web']); ?>" class="visit-website f-right" target="_blank">
                                        <?php echo __t('Suppliers.Visit_website'); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="background-color-secondary cnt-brands-list-title">
                        <div class="row">
                            <div class="columns medium-12">
                                <div class="style5" style="padding: 5px 0">
                                    <?php echo __t('Suppliers.Brands'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="cnt-brands-list">
                        <div class="row">
                            <div class="columns medium-12">
                                <div style="margin-top: 10px;margin-bottom: 10px;">
                                    <?php
                                    foreach ($info_view['Brands'] as $brand) {
                                        if (!empty($brand['BrandImage']['file'])) {
                                    ?>
                                            <img src="<?php echo FileManager::get_url(FilePaths::BRANDS_IMAGES_RELATIVE . $brand['BrandImage']['file']); ?>" alt="<?php echo $brand['Brand']['name']; ?>" style="height: 50px; margin: 20px 20px 20px 0;" class="brands cursor-pointer" data-id="<?php echo $brand['Brand']['id'] ?>" />
                                        <?php } else { ?>
                                            <span style="height: 50px; margin: 20px 20px 20px 0" class="brands cursor-pointer" data-id="<?php echo $brand['Brand']['id'] ?>"><?php echo $brand['Brand']['name']; ?></span>
                                    <?php
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="cnt-video-info">
                            <div class="row">
                                <?php if (!empty($info_view['Supplier']['url_channel'])) { ?>
                                    <div class="columns medium-5">
                                        <div class="style5 youtube-title">
                                            <?php echo __t('Suppliers.Youtube_channel'); ?>
                                        </div>
                                        <div class="video-supplier-<?php echo $info_view['Supplier']['id'] ?>"></div>
                                        <?php if (!empty($info_view['Supplier']['url_channel'])) { ?>
                                            <div class="ta-right">
                                                <a href="<?php echo h($info_view['Supplier']['url_channel']); ?>" target="_blank" class="channel-link style5">
                                                    <?php echo __t('Suppliers.Visit_our_youtube_channel'); ?>
                                                </a>
                                            </div>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                                <div class="medium-7 columns">
                                    <?php
                                    if (!empty($info_view['SupplierFile'])) {
                                        foreach ($info_view['SupplierFile'] as $name => $category) {
                                            echo $name . "<br/>";
                                            foreach ($category as $file) {
                                                echo "- " . $this->Html->link(
                                                    $file['SupplierFile']['name'],
                                                    array(
                                                        'controller' => 'suppliers_files',
                                                        'action' => 'download_file',
                                                        $file['SupplierFile']['id']
                                                    )
                                                ) . "<br/>";
                                            }
                                            echo "<br/>";
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
<?php
    }
    $i++;
}
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('GarageNetwork.Garage_network'),
                array(
                    'controller' => 'garages_networks',
                    'action' => 'view',
                    $garage_network_id
                )
            ),
            __t('General.Reviews'),
        ));
        ?>
    </div>
</div>
<?php echo $this->element('../GaragesNetworks/tabs_network', array('selected' => 'button_my_garage')); ?>
<div class="cnt-data buttons-fixed">
    <div class="aag-padding">
        <?php
        echo $this->element('../GaragesNetworks/tabs_my_garage', array('selected' => 'reviews'));
        if (in_array($network_id, array(NETWORK_ID_GV, NETWORK_ID_GC))) {
            echo $this->element('../Reviews/Elements/form');
        } elseif ($network_id == NETWORK_ID_AGN) {
            echo $this->element('../Reviews/Elements/form_request_feefo_review');
        }
        ?>
        <div class="p-1 background-color-primary">
            <?php if (is_array($review_data)) : ?>
                <?php if (count($review_data) > 0) : ?>
                    <div class="row">
                        <div class="medium-12 columns" style="text-align:center">
                            <div class="titulo2">
                                <strong><?php echo __t('General.Total') . ': '; ?></strong>
                                <?php for ($i = 1; $i <= 5; $i++) { ?>
                                    <?php if ($i <= $average_rating) { ?>
                                        <img src="/img/star_yellow.svg">
                                    <?php
                                    } elseif ($i == ceil($average_rating) && $average_rating != floor($average_rating)) {
                                    ?>
                                        <img src="/img/star_half_yellow.svg">
                                    <?php } else { ?>
                                        <img src="/img/star_light.svg">
                                    <?php } ?>
                                <?php } ?>
                                &nbsp; <?php echo $average_rating . '/5' ?><br>
                                <strong><?php echo $total_reviews . ' ' ?></strong><?php echo __t('Review.Reviews'); ?>
                            </div>
                        </div>
                    </div>
                    <br><br>
                    <div>
                        <u>
                            <strong>
                                <?php echo __t('Review.Last_reviews') . '. ' . __t('Review.More_information') ?>
                                <a href="<?php echo $url_review; ?>" target="_blank"><?php echo $link; ?></a>
                                <?php if (isset($link_kiyoh)) : ?>
                                    <?php echo __t('or '); ?>
                                    <a href="<?php echo $url_kiyoh; ?>" target="_blank">
                                        <?php echo $link_kiyoh; ?></a><?php endif; ?>:
                            </strong>
                        </u>
                    </div>
                    <br>
                    <div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 10px;'>
                        <?php foreach ($reviewsPaginator as $review) { ?>
                            <div style="border: 2px solid var(--container-elements-color); border-radius: 15px; padding: 10px;">
                                <span style="display: flex; align-items: center; gap: 15px; font-size: 11px; line-height: 1;">
                                    <?php echo Texto::encryptDecryptText($review['name'], false); ?>
                                    <?php echo $review['date'] ?>
                                    <?php if ($review['source'] === 'Google'): ?>
                                        <img src="/img/gmb.svg" style="float:right;">
                                    <?php elseif ($review['source'] === 'Feefo'): ?>
                                        <img src="/img/feefo.svg" width="45" />
                                    <?php elseif ($review['source'] === 'Kiyoh'): ?>
                                        <img src="/img/klantenvertellen_logo.svg" style="float:right;">
                                    <?php endif; ?>
                                </span>
                                <p>
                                    <strong><?php echo $review['title'] ?></strong>
                                    <br>
                                    <?php $rating = $review['rating'] ?>
                                    <?php for ($i = 1; $i <= 5; $i++) : ?>
                                        <?php if ($i <= $rating) : ?>
                                            <img src="/img/star_yellow.svg">
                                        <?php elseif ($i == ceil($rating) && $rating != floor($rating)) : ?>
                                            <img src="/img/star_half_yellow.svg">
                                        <?php else : ?>
                                            <img src="/img/star_light.svg">
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                    &nbsp; <?php echo $rating . '/5' ?>
                                    <br>
                                    <?php echo $review['text'] ?>
                                </p>
                            </div>
                        <?php } ?>
                    </div>
                    <?php
                    echo $this->element(
                        '../Elements/Comun/pagination_list',
                        array(
                            'pagination_size' => $pagination_size,
                            'pagination_count' => $pagination_count
                        )
                    );
                    ?>
                <?php else : ?>
                    <p><?php echo __t('Review.Error_no_reviews'); ?></p>
                <?php endif; ?>
            <?php else : ?>
                <p><?php echo __t('Review.Error_token'); ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>
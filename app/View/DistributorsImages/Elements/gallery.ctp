<?php
$controller = $this->request->controller;
$action = $this->request->action;
?>
<?php echo $this->Html->script('gallery.js?v='.Configure::read('VERSION_CACHE'), array('block' => 'script')); ?>



<div class="row" id="gallery-js">
    <div class="orbit" role="region" data-orbit data-auto-play="false">
        <div class="orbit-wrapper">
            <?php if (count($images)>1) { ?>
                <div class="orbit-controls">
                    <button class="orbit-previous"><span class="show-for-sr">Previous Slide</span>&#9664;&#xFE0E;</button>
                    <button class="orbit-next"><span class="show-for-sr">Next Slide</span>&#9654;&#xFE0E;</button>
                </div>
            <?php } ?>
            <ul id="col-id-img" class="orbit-container">
            <?php
                $i=1;
                foreach ($images as $image) {?>
                        <li class="garages_images_carousel orbit-slide">
                            <figure class="orbit-figure">
                            <?php
                    echo $this->Html->image(
                        Router::url(
                            array(
                                'controller' => 'distributors_images',
                                'action' => 'download_file',
                                $image['DistributorImage']['id'],
                            )
                        ),
                        array(
                            'class' => 'd-block m-0-auto',
                            'url' => array(
                                'controller' => 'distributors_images',
                                'action' => 'download_file',
                                $image['DistributorImage']['id'],
                            ),
                        )
                    );
                    ?>
                            </figure>
                            <figcaption class="orbit-caption">
                            <?php if( $controller == 'distributors_images' && $this->Acceso->rol() != ConstantsRoles::DISTRIBUTOR){ ?>
                        <div class="orbit-caption">
                            <?php
                            echo $image['DistributorImage']['file'].' ';
                                if($image['DistributorImage']['principal'] ==  ConstantsBooleans::NO){
                                    echo $this->Html->link(
                                        '<span class="ion-trash-b c-fallo icono-grande f-left" style="margin: -7px 1rem -7px 0 !important;"></span>',
                                        'javascript:;',
                                        array(
                                            'class' => 'delete-file-js',
                                            'data-confirmmsg' => __t('General.Delete_file?'),
                                            'data-url' => Router::url(array(
                                                'controller' => 'distributors_images',
                                                'action' => 'ajax_delete_file',
                                            )),
                                            'data-id' => $image['DistributorImage']['id'],
                                            'data-div' => '#file-list-js',
                                            'data-yes' => __t('General.Yes'),
                                            'data-no' => __t('General.No'),
                                            'data-type' => 'warning',
                                            'escape' => false,
                                            'title' => __t('General.Delete'),
                                        )
                                    );
                                }
                                if($image['DistributorImage']['principal'] == ConstantsBooleans::NO){
                                    echo " ".$this->Html->link(
                                            '<span class="ion-ios-home-outline c-exito icono-grande f-left" style="margin: -7px 1rem -7px 0 !important;"></span>',
                                            'javascript:;',
                                            array(
                                                'class' => 'principal-file-js',
                                                'data-confirmmsg' => __t('Garage.Main_image?'),
                                                'data-url' => Router::url(array(
                                                    'controller' => 'distributors_images',
                                                    'action' => 'ajax_principal_image',
                                                )),
                                                'data-id' => $image['DistributorImage']['id'],
                                                'data-div' => '#gallery-js',
                                                'data-yes' => __t('General.Yes'),
                                                'data-no' => __t('General.No'),
                                                'data-type' => 'warning',
                                                'data-child' => $i,
                                                'escape' => false,
                                                'title' => __t('Garage.Principal'),
                                            )
                                        );
                                }
                                if($image['DistributorImage']['principal'] == ConstantsBooleans::YES){
                                    echo " ".$this->Html->link(
                                            '<span class="ion-ios-home c-exito icono-grande f-left" style="margin: -7px 1rem -7px 0 !important;"></span>',
                                            'javascript:;',
                                            array(
                                                'class' => 'principal-file-js',
                                                'data-confirmmsg' => __t('Distributor.Undo_main_image?'),
                                                'data-url' => Router::url(array(
                                                    'controller' => 'distributors_images',
                                                    'action' => 'ajax_not_principal_image',
                                                )),
                                                'data-id' => $image['DistributorImage']['id'],
                                                'data-div' => '#gallery-js',
                                                'data-yes' => __t('General.Yes'),
                                                'data-no' => __t('General.No'),
                                                'data-type' => 'warning',
                                                'data-child' => $i,
                                                'escape' => false,
                                                'title' => __t('Garage.Not_principal'),
                                            )
                                        );
                                }
                            ?>
                        </div>
                    <?php } ?>
                            </figcaption>
                        </li>
                    <?php
                    $i++;
                }?>
            </ul>
            <?php if( $controller == 'distributors_images') { ?>
                <div class="leyenda p-bottom-1 d-inline-block w-100p">
                    <div class="titulo2">
                        <?php echo __t('General.Legend'); ?>
                    </div>
                    <div class="f-left">
                        <span class="ion-trash-b c-fallo f-left"></span>
                        <div class="ws-nowrap f-left" style="margin: 4px 2rem 0 0">
                            <?php echo __t('General.Delete'); ?>
                        </div>
                    </div>
                    <div class="f-left">
                        <span class="ion-ios-home-outline c-exito f-left"></span>
                        <div class="ws-nowrap f-left" style=";margin: 4px 2rem 0 0">
                            <?php echo __t('Garage.Not_principal'); ?>
                        </div>
                    </div>
                    <div class="f-left">
                        <span class="ion-ios-home c-exito f-left"></span>
                        <div class="ws-nowrap f-left" style="margin: 4px 2rem 0 0">
                            <?php echo __t('Garage.Principal'); ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
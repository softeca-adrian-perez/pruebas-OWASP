<?php
$controller = $this->request->controller;
$action = $this->request->action;
?>
<div class="row">
    <div class="columns">
        <div class="cnt-carousel-scroll">
            <?php
            foreach( $images as $image )
            {
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
            }
            ?>
        </div>
    </div>
</div>
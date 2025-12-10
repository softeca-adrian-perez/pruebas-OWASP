<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Distributor.Distributors'),
                array(
                    'controller' => 'distributors',
                    'action' => 'home'
                )
            ),
            $this->Html->link(
                __t('Distributor.Comments'),
                array(
                    'controller' => 'distributors',
                    'action' => 'add_comments',
                    $distributor_id
                )
            ),
            __t('General.View'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium four go-back-js"><?php echo __t('General.Back')?></a>
        <?php
        if( $user['User']['id'] == $distributor_comments['DistributorComment']['user_id']){
            echo $this->Html->link(
                __t('General.Edit'),
                array(
                    'controller' => 'distributors_comments',
                    'action' => 'edit',
                    $distributor_comments['DistributorComment']['distributor_id'],
                    $distributor_comments['DistributorComment']['id']

                ),
                array(
                    'escape' => false,
                    'title' => __t('General.Edit'),
                    'class' => 'aag-button medium orange',
                )
            );
        }
        ?>
    </div>
</div>
<div class="cnt-data p-top-1">
    <div class="cnt-data-element">
        <div class="aag-title">
            <?php echo __t('Distributor.Comments_view'); ?>
        </div>
    </div>
        <div class="cnt-data-element p-top-1">
            <div class="columns">
                <div class="columns background-color-primary p-vertical-1">
                    <div class="row">
                        <div class="columns medium-9">
                            <strong><?php echo __t('Distributor.Author') ?>: </strong>
                            <br>
                            <div class="b-bottom-1">
                                <?php echo h($users[$distributor_comments['DistributorComment']['user_id']]); ?>
                            </div>
                        </div>
                        <div class="columns medium-3">
                            <strong><?php echo __t('Distributor.Creation_date') ?>: </strong>
                            <br>

                            <div class="b-bottom-1">
                                <?php echo Fecha::toFormatoVistaFecha(h($distributor_comments['DistributorComment']['creation_date'])); ?>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="columns medium-12">
                            <strong><?php echo __t('Distributor.Comment') ?>: </strong>
                            <br>

                            <div class="b-bottom-1">
                                <?php echo h($distributor_comments['DistributorComment']['body']); ?>
                            </div>
                        </div>
                    </div>
                    <?php if( $user['User']['id'] == $distributor_comments['DistributorComment']['user_id']){ ?>
                    <div class="row p-top-1">
                        <div class="columns medium-12 ta-right right-0 cnt-buttons-v2">
                            <?php
                                echo $this->Html->link(
                            __t('General.Delete'),
                            array(),
                            array(
                                'escape' => false,
                                'title' => __t('General.Delete'),
                                'class' => 'aag-button medium red swal-msg',
                                'data-confirmmsg' => __t('Distributor.Comments_delete?'),
                                'data-yes' => __t('General.Yes'),
                                'data-no' => __t('General.No'),
                                'data-type' => 'warning',
                                'data-url' => Router::url(array(
                                    'controller' => 'distributors_comments',
                                    'action' => 'delete',
                                    $distributor_comments['DistributorComment']['distributor_id'],
                                    $distributor_comments['DistributorComment']['id'],
                                )),
                            )
                        );
                        ?>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
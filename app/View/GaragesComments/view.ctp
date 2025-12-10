<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Garage.Garages'),
                array(
                    'controller' => 'garages',
                    'action' => 'home'
                )
            ),

            $this->Html->link(
                __t('Garage.Comments'),
                array(
                    'controller' => 'garages',
                    'action' => 'add_comments',
                    $garage_id
                )
            ),
            __t('General.View'),
        ));
        ?>
    </div>
    <div>
        <?php
        echo $this->Html->link(
            __t('General.Back'),
            CakeSession::read('url_referer'),
            array(
                'class' => 'aag-button medium four',
                'style' => 'margin-top:0 !important;'
            )
        );
        if( $user['User']['id'] == $garage_comments['GarageComment']['user_id']){
            if(
                $this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE) ||
                $this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::REQUEST_CHANGE_GARAGE)
            ){
                echo $this->Html->link(
                    __t('General.Edit'),
                    array(
                        'controller' => 'garages_comments',
                        'action' => 'edit',
                        $garage_comments['GarageComment']['id']
                    ),
                    array(
                        'escape' => false,
                        'title' => __t('General.Edit'),
                        'class' => 'btn-edit edit',
                        'style' => 'margin-top:0 !important;'
                    )
                );
            }
        }
        ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="aag-title">
        <?php echo __t('Garage.Comments_view'); ?>
    </div>

        <div class="cnt-form-inputs p-top-1">
            <div class="three-columns">
                <strong><?php echo __t('User.User') ?>: </strong>
                <br>

                <div class="b-bottom-1 height_input">
                    <?php echo h($users[$garage_comments['GarageComment']['user_id']]); ?>
                </div>
            </div>
            <div>
                <strong><?php echo __t('Garage.Creation_date') ?>: </strong>
                <br>

                <div class="b-bottom-1 height_input">
                    <?php echo h(date("d-m-Y / H:i", strtotime($garage_comments['GarageComment']['creation_date']))); ?>
                </div>
            </div>
            <br>
            <div class="clear-column">
                <strong><?php echo __t('Garage.Comment') ?>: </strong>
                <br>

                <div class="b-bottom-1 height_input">
                    <?php echo h($garage_comments['GarageComment']['body']); ?>
                </div>
            </div>
            <?php if( $user['User']['id'] == $garage_comments['GarageComment']['user_id']){ ?>
                <div class="row p-top-1">
                    <div class="ta-right right-0 cnt-buttons-v2">
                        <?php
                        echo $this->Html->link(
                            __t('General.Delete'),
                            array(),
                            array(
                                'escape' => false,
                                'title' => __t('General.Delete'),
                                'class' => 'aag-button medium red swal-msg',
                                'data-confirmmsg' => __t('Garage.Comments_delete?'),
                                'data-yes' => __t('General.Yes'),
                                'data-no' => __t('General.No'),
                                'data-type' => 'warning',
                                'data-url' => Router::url(array(
                                    'controller' => 'garages_comments',
                                    'action' => 'delete',
                                    $garage_comments['GarageComment']['garage_id'],
                                    $garage_comments['GarageComment']['id'],
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
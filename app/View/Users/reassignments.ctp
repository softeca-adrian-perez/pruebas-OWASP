<?php
echo $this->Html->script('reassignments.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->css('../js/lib/dataTables/css/dataTables.foundation.min.css', array('block' => 'script'));
echo $this->Html->script('lib/dataTables/js/jquery.dataTables.min.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('lib/moment.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Maintenance.Maintenance'),
                array(
                    'controller' => 'maintenance',
                    'action' => 'home'
                )
            ),
            __t('User.Reassignment'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back')?></a>
    </div>
</div>
<?php
echo $this->Form->create(
    'User',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data',
        'id' => 'reassignment-form',
        'url' => array(
            'controller' => 'users',
            'action' => 'reassign_user',
        ),
    ));
    echo $this->Form->hidden('user_origin', array('id' => 'user-origin'));
    echo $this->Form->hidden('user_destination', array('id' => 'user-destination'));
    ?>
    <div id="tmp-origin-selected" class="d-none"></div>
    <div id="tmp-destination-selected" class="d-none"></div>
    <div class="cnt-data p-top-1">
        <div class="cnt-data-element">
            <div class="aag-title">
                <?php echo __t('User.Reassign_user'); ?>
            </div>
            <div class="aag-subtitle">
                <?php echo __t('General.Search'); ?>
            </div>
            <div class="cnt-two-columns">
                <div id="list_origin">
                    <?php
                    echo $this->Form->input(
                        'search_origin',
                        array(
                            'type' => 'text',
                            'id' => 'search_origin',
                            'required' => true,
                            'data-list' => 'list_origin',
                            'data-id' => 'List1',
                            'data-url' => Router::url(
                                array(
                                    'controller' => 'users',
                                    'action' => 'ajax_search_user'
                                )
                            ),
                            'label' => __t('User.Origin_user_name'),
                        )
                    );
                    echo $this->element('../Users/Elements/ajax_list', array('id' => 'List1'));
                    ?>
                </div>
                <div id="list_destination">
                    <?php
                    echo $this->Form->input(
                        'search_destination',
                        array(
                            'type' => 'text',
                            'id' => 'search_destination',
                            'required' => true,
                            'data-list' => 'list_destination',
                            'data-id' => 'List2',
                            'data-url' => Router::url(
                                array(
                                    'controller' => 'users',
                                    'action' => 'ajax_search_user'
                                )
                            ),
                            'label' => __t('User.Destination_user_name'),
                        )
                    );
                    echo $this->element('../Users/Elements/ajax_list', array('id' => 'List2'));
                    ?>
                </div>
            </div>
            <div class="d-inline-block w-100p">
                <div class="small-5 columns">
                    <div class="columns medium-12 ta-right">
                        <strong class="p-bottom-1"><?php echo __t('User.Origin_user_name'); ?></strong><br>
                        <span id="origin_name"></span>
                    </div>
                    <div class="columns medium-12 ta-right p-top-1 label-m-r-0">
                        <?php
                        echo $this->Form->input(
                            'UserReassignment.permanent',
                            array(
                                'type' => 'checkbox',
                                'label' => __t('User.Permanent'),
                                'id' => 'permanent-check',
                                'div' => array(
                                    'class' => 'p-top-1',
                                ),
                                'checked' => false
                            )
                        );
                        ?>
                    </div>
                </div>
                <div class="small-2 columns ta-center">
                    <span class="ion-arrow-right-c icono-grande" style="margin: 0 !important;line-height: 1 !important;margin-top: -7px !important;"></span>
                </div>
                <div class="small-5 columns">
                    <strong><?php echo __t('User.Destination_user_name'); ?></strong>
                    <br>
                    <span id="destination_name"></span>
                </div>
            </div>
            <div class="columns medium-12 ta-center">
                <div class="d-inline-block ta-left datepicker datepicker-label-block end" id="div-reassignment-from-date" style="width: 150px;padding: 0 6px;">
                    <?php echo $this->Form->input(
                        'UserReassignment.date_from',
                        array(
                            'type' => 'text',
                            'required' => true,
                            'class' => 'fecha-js from-js-plus-one min-js',
                            'id' => 'start_date',
                            'data-to' => '#end_date',
                            'data-mindate' => date('d-m-Y'),
                            'label' => __t('General.From'),
                        )
                    ); ?>
                </div>
                <div class="d-inline-block ta-left datepicker datepicker-label-block end" id="div-reassignment-to-date" style="width: 150px;padding: 0 6px;">
                    <?php echo $this->Form->input(
                        'UserReassignment.date_to',
                        array(
                            'type' => 'text',
                            'required' => true,
                            'class' => 'fecha-js to-js min-js',
                            'id' => 'end_date',
                            'data-from' => '#start_date',
                            'data-mindate' => date('d-m-Y',strtotime("+1 day")),
                            'label' => __t('General.To'),
                        )
                    ); ?>
                </div>
            </div>
            <div class="columns medium-12 ta-center p-top-1">
                <?php echo $this->Form->button(
                    __t('User.Reassign_user'),
                    array(
                        'id' => 'btn-submit',
                        'class' => 'aag-button m-0 m-0-i two medium',
                        'data-url_check' => Router::url(
                            array(
                                'controller' => 'users',
                                'action' => 'ajax_check_correct_reassignment'
                            )
                        ),
                        'data-url_restructure' => Router::url(
                            array(
                                'controller' => 'users',
                                'action' => 'ajax_restructure_reassignment'
                            )
                        ),
                        'data-url' => Router::url(
                            array(
                                'controller' => 'users',
                                'action' => 'reassign_user'
                            )
                        ),
                    )
                ); ?>
                <br>
                <span id="msg_disabled" class="c-fallo fs-smaller"><?php echo __t('User.Origin_user_disabled');?></span>
            </div>
        </div>
        <?php echo $this->element('../Users/Elements/results_table'); ?>
    </div>
<?php echo $this->Form->end(); ?>

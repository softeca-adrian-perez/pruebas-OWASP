<?php echo $this->Html->script('preferences.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script')); ?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('User.Users'),
                array(
                    'controller' => 'users',
                    'action' => 'listing'
                )
            ),
            __t('UserPreference.My_preferences'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back')?></a>
    </div>
</div>
<div class="cnt-data p-vertical-1">
    <div class="aag-title cnt-data-element">
        <?php echo __t('Home.Calendar'); ?>
    </div>
    <?php
    echo $this->Form->create('UserPreference', array('enctype' => 'multipart/form-data'));
        echo $this->Form->hidden('UserPreference.id');
        echo $this->Form->hidden(
            'UserPreference.URLSaveAjax',
            array(
                'id' => 'url_save_ajax',
                'data-url' => Router::url(array(
                    'controller' => 'users',
                    'action' => 'ajax_save_preferences'
                ))
            )
        );
        ?>
        <div class="o-auto">
            <table class="table-tracking table-config">
                <tbody>
                    <tr>
                        <td>
                            <span class="section_name unselectable cursor-pointer" >
                                <?php echo __t('UserPreference.Calendar_default'); ?>
                            </span>
                        </td>
                        <td class="ta-center">
                            <?php echo $this->Form->input(
                                'UserPreference.calendar',
                                array(
                                    'label' => false,
                                    'class' => 'select2-multiple user_preference',
                                    'type' => 'select',
                                    'multiple' => false,
                                    'empty' => false,
                                    'options' => $calendar_views,
                                    'default' => $preference_calendar,
                                    'data-preference' => ConstantsPreferences::CALENDAR_VIEW
                                )
                            ); ?>
                        </td>
                    </tr>
                    <!-- <tr>
                        <td>
                            <span class="section_name unselectable cursor-pointer" >
                                <?php echo __t('UserPreference.Pagination'); ?>
                            </span>
                        </td>
                        <td class="ta-center">
                            <?php echo $this->Form->input(
                                'UserPreference.pagination',
                                array(
                                    'label' => false,
                                    'class' => 'select2-multiple',
                                    'type' => 'select',
                                    'multiple' => false,
                                    'empty' => false,
                                    'options' => $pagination,
                                    'default' => ConstantsCalendarView::WORKWEEK
                                )
                            ); ?>
                        </td>
                    </tr> -->
                </tbody>
            </table>
        </div>
    <?php echo $this->Form->end(); ?>
</div>
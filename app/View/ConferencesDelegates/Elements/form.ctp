<?php
echo $this->Html->script('/js/conferences_delegates.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Form->create(
    'ConferenceDelegate',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data'
    )
);
echo $this->Form->hidden(
    'ConferenceDelegate.id'
);
$config = CakeSession::read('Auth.User.Config');
$action = $this->request->action;
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        if ($action == ConstantsActionsNames::ADD) {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Delegate.Delegates'),
                    array(
                        'controller' => 'conferences_delegates',
                        'action' => 'home'
                    )
                ),
                __t('General.Add'),
            ));
        } else {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Delegate.Delegates'),
                    array(
                        'controller' => 'conferences_delegates',
                        'action' => 'home'
                    )
                ),
                __t('General.Edit'),
            ));
        }
        ?>
    </div>
    <div>
        <?php echo $this->element('Comun/form_actions', $cancel_action); ?>
    </div>
</div>
<div class="cnt-data p-top-1 p-bottom-1">
    <div class="cnt-data-element">
        <div class="aag-title p-bottom-1">
            <?php
            if ($action == ConstantsActionsNames::ADD) {
                echo __t('Delegate.Add_delegate');
            } else {
                echo __t('Delegate.Edit_delegate');
            }
            ?>
        </div>
        <div class="cnt-form-inputs m-bottom-1">
            <?php
            echo $this->Form->input(
                'conferences_id',
                array(
                    'label' => __t('Delegate.Conference_name'),
                    'type' => 'select',
                    'class' => 'select2-multiple',
                    'options' => $conferences
                )
            );
            echo $this->Form->input(
                'booking_date',
                array(
                    'class' => 'fecha-js from-js clear_field',
                    'type' => 'text',
                    'required' => true,
                    'div' => array(
                        'class' => 'datepicker datepicker-label-block',
                    ),
                    'label' => __t('Delegate.Booking_date'),
                )
            ); ?>
        </div>
        <div>
            <div class="garage-distributore-supplier-div m-top-1 m-bottom-1" id="div-radio">
                <input type="radio" class="disabled_fields" name="delegate_selected" id="garage_selected"
                    <?php if (!empty($garage)) {
                        echo 'checked';
                    } ?> />
                <label for="garage_selected"><?php echo __t('Delegate.Garage_name'); ?></label>

                <input type="radio" class="disabled_fields" name="delegate_selected" id="distributor_selected"
                    <?php if (!empty($distributor)) {
                        echo 'checked';
                    } ?> />
                <label for="distributor_selected"><?php echo __t('Delegate.Distributor_name'); ?></label>

                <input type="radio" class="disabled_fields" disabled="true" name="delegate_selected" id="supplier_selected" />
                <label for="supplier_selected"><?php echo __t('Delegate.Supplier_name'); ?></label>
            </div>
            <div id="div-distributor" <?php echo isset($distributor) ? $distributor : 'hidden' ?>>
                <?php
                echo $this->Form->input(
                    'ConferenceDelegate.distributors_id',
                    array(
                        'class' => 'clear_field select2Dinamico_distributor cargar_distributors',
                        'type' => 'select',
                        'multiple' => false,
                        'options' => isset($array_distributor_name) ? $array_distributor_name : array(),
                        'required' => true,
                        'empty' => true,
                        'id' => 'search_distributor',
                        'label' => __t('AppointmentObjective.Distributor'),
                        'data-texto1' => __t('General.Min_3_characters'),
                        'data-url' => Router::url(array(
                            'controller' => 'conferences_delegates',
                            'action' => 'ajax_select_delegate',
                        ))
                    )
                ); ?>
            </div>
            <div id="div-garage" <?php echo isset($garage) ? $garage : 'hidden' ?>>
                <?php
                echo $this->Form->input(
                    'ConferenceDelegate.garages_id',
                    array(
                        'class' => 'clear_field select2Dinamico_garage cargar_garages',
                        'type' => 'select',
                        'multiple' => false,
                        'options' => isset($array_garage_name) ? $array_garage_name : array(),
                        'required' => true,
                        'empty' => true,
                        'id' => 'search_garage',
                        'label' => __t('Garage.Garage'),
                        'data-texto1' => __t('General.Min_3_characters'),
                        'data-url' => Router::url(array(
                            'controller' => 'conferences_delegates',
                            'action' => 'ajax_select_delegate',
                        ))
                    )
                ); ?>
            </div>
        </div>
        <div class="cnt-form-inputs m-bottom-1">
            <div class=" delegate_id">
                <?php echo $this->Form->input(
                    'delegate_id',
                    array(
                        'label' => __t('Delegate.Delegate'),
                        'type' => 'select',
                        'class' => 'select2-multiple clear_field delegate_class',
                        'empty' => true,
                        'required' => true
                    )
                ); ?>
            </div>
            <?php echo $this->Form->input(
                'contact',
                array(
                    'type' => 'text',
                    'class' => 'contact-delegate',
                    'required' => true,
                    'label' => __t('Delegate.Contact'),
                    'disabled' => true,
                )
            );
            echo $this->Form->input(
                'email',
                array(
                    'type' => 'text',
                    'class' => 'email-delegate',
                    'required' => true,
                    'label' => __t('Delegate.Email'),
                    'disabled' => true,
                )
            );
            echo $this->Form->input(
                'venue_id',
                array(
                    'label' => __t('Venue.Hotel'),
                    'type' => 'select',
                    'class' => 'select2-multiple',
                    'options' => $hotels
                )
            );
            echo $this->Form->input(
                'room_type_id',
                array(
                    'label' => __t('Delegate.Room_type_id'),
                    'type' => 'select',
                    'class' => 'select2-multiple',
                    'options' => $rooms_types
                )
            );
            echo $this->Form->input(
                'nights',
                array(
                    'type' => 'text',
                    'required' => true,
                    'label' => __t('Delegate.Nights'),
                    'class' => 'input-conferences',
                )
            );
            echo $this->Form->input(
                'vegetarian',
                array(
                    'label' => __t('Delegate.Vegetarian'),
                    'type' => 'select',
                    'class' => 'select2-multiple clear_field',
                    'options' => $booleanOption,
                    'empty' => true,
                    'required' => true,
                )
            );
            echo $this->Form->input(
                'diet_requirements',
                array(
                    'type' => 'text',
                    'required' => false,
                    'label' => __t('Delegate.Diet_requirements'),
                )
            );
            echo $this->Form->input(
                'room',
                array(
                    'label' => __t('Delegate.Room'),
                    'type' => 'select',
                    'class' => 'select2-multiple clear_field',
                    'multiple' => 'multiple',
                    'options' => $all_week,
                    'selected' => $room_selected,
                    'empty' => true,
                    'required' => true,
                )
            );
            echo $this->Form->input(
                'trade_show',
                array(
                    'label' => __t('Delegate.Trade_show'),
                    'type' => 'select',
                    'class' => 'select2-multiple clear_field',
                    'multiple' => 'multiple',
                    'options' => $weekend,
                    'selected' => $trade_show_selected,
                    'empty' => true,
                    'required' => true,
                )
            );
            echo $this->Form->input(
                'dinner',
                array(
                    'label' => __t('Delegate.Dinner'),
                    'type' => 'select',
                    'class' => 'select2-multiple clear_field',
                    'multiple' => 'multiple',
                    'options' => $weekend,
                    'selected' => $dinner_selected,
                    'empty' => true,
                    'required' => true,
                )
            ); ?>
        </div>
        <hr>
        <div class="cnt-form-inputs m-top-1">
            <?php
            echo $this->Form->input(
                'guest_name',
                array(
                    'type' => 'text',
                    'required' => false,
                    'label' => __t('Delegate.Guest_name'),
                )
            );
            echo $this->Form->input(
                'guest_separate_room',
                array(
                    'label' => __t('Delegate.Guest_separate_room'),
                    'type' => 'select',
                    'class' => 'select2-multiple',
                    'options' => $rooms_types
                )
            );
            echo $this->Form->input(
                'stand_number',
                array(
                    'type' => 'text',
                    'required' => false,
                    'label' => __t('Delegate.Stand_number'),
                    'class' => 'input-conferences',
                )
            );
            echo $this->Form->input(
                'stand_size_id',
                array(
                    'label' => __t('Delegate.Stand_size_id'),
                    'type' => 'select',
                    'class' => 'select2-multiple',
                    'options' => $stands_sizes
                )
            );
            echo $this->Form->input(
                'stand_power_required',
                array(
                    'label' => __t('Delegate.Stand_power_required'),
                    'type' => 'select',
                    'class' => 'select2-multiple clear_field',
                    'options' => $booleanOption,
                    'empty' => true,
                    'required' => true,
                )
            );
            echo $this->Form->input(
                'bespoke_stand_details',
                array(
                    'type' => 'text',
                    'required' => false,
                    'label' => __t('Delegate.Bespoke_stand_details'),
                )
            );
            echo $this->Form->input(
                'website_entry_id',
                array(
                    'type' => 'text',
                    'required' => false,
                    'label' => __t('Delegate.Website_entry_id'),
                )
            ); ?>
        </div>
        <input type="hidden" name="garages_id" id="garages_id" value="<?php if (!empty($garage)) {
                                                                            echo $garage;
                                                                        } ?>" />
        <input type="hidden" name="distributors_id" id="distributors_id" value="<?php if (!empty($distributor)) {
                                                                                    echo $distributor;
                                                                                } ?>" />
        <input type="hidden" name="suppliers_id" id="suppliers_id" value="<?php if (!empty($supplier)) {
                                                                                echo $supplier;
                                                                            } ?>" />
    </div>
</div>
<?php echo $this->Form->end(); ?>
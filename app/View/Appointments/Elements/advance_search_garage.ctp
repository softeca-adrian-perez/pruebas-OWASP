<div class="d-inline-block w-100p">
    <div class="medium-12 columns">
        <h1><?php echo __t('General.Advanced_search') . ': ' . __t('Garage.Garages'); ?></h1>
    </div>
    <div id="search_list">
        <div class="medium-12 columns">
            <?php
            echo $this->Form->input(
                'Search.Name',
                array(
                    'label' => __t('Garage.Name'),
                    'type' => 'text',
                    'required' => true,
                    'id' => 'name_advanced_search',
                )
            );
            ?>
        </div>
        <div class="medium-12 columns">
            <?php
            echo $this->Form->input(
                'Search.Town',
                array(
                    'label' => __t('Garage.Town'),
                    'type' => 'text',
                    'required' => true,
                    'id' => 'town_advanced_search',
                )
            );
            ?>
        </div>
        <div class="medium-12 columns m-bottom-1">
            <?php echo $this->Form->input(
                'Search.network',
                array(
                    'type' => 'select',
                    'label' => __t('Network.Network'),
                    'empty' => true,
                    'class' => 'select2-multiple',
                    'options' => $networks,
                    'id' => 'network_advanced_search'
                )
            ); ?>
        </div>
        <div class="medium-12 columns m-bottom-1">
            <?php echo $this->Form->input(
                'Search.distributor',
                array(
                    'type' => 'select',
                    'label' => __t('Distributor.Distributor'),
                    'empty' => true,
                    'class' => 'select2-multiple',
                    'options' => $distributors,
                    'id' => 'distributor_advanced_search'
                )
            ); ?>
        </div>
        <button type="button" id="show_result"
                class="button-crm" data-url="
                            <?php echo Router::url(
            array(
                'controller' => 'appointments',
                'action' => 'advanced_search_ajax'
            )
        ); ?>">
            <span class="icon-search c-blanco"></span>
            <?php echo __t('General.Search'); ?>
        </button>
    </div>
    <div id="results_list" class="d-none">
        <div class="p-1" id="print_results_list" data-msg="<?php echo __t('Appointment.Big_results'); ?>"></div>
        <span id="back_to_search" class="f-right p-1 ion-backspace-outline cursor-pointer c-blanco">
            <?php echo __t('Appointment.Back_to_search'); ?>
        </span>
    </div>
</div>
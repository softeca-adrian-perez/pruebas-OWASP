<div class="d-inline-block w-100p">
    <div class="medium-12 columns">
        <h1><?php echo __t('General.Advanced_search') . ': ' . __t('Distributor.Distributors'); ?></h1>
    </div>
    <div id="search_list_distributor">
        <div class="medium-12 columns">
            <?php
            echo $this->Form->input(
                'Search.name',
                array(
                    'label' => __t('Distributor.Name'),
                    'type' => 'text',
                    'required' => true,
                    'id' => 'name_advanced_search_distributor',
                )
            );
            ?>
        </div>
        <div class="medium-12 columns">
            <?php
            echo $this->Form->input(
                'Search.town',
                array(
                    'label' => __t('Distributor.Town'),
                    'type' => 'text',
                    'required' => true,
                    'id' => 'town_advanced_search_distributor',
                )
            );
            ?>
        </div>
        <div class="medium-12 columns m-bottom-1">
            <?php echo $this->Form->input(
                'Search.account_number',
                array(
                    'label' => __t('Distributor.Account_number'),
                    'type' => 'text',
                    'required' => true,
                    'id' => 'account_number_advanced_search_distributor',
                )
            ); ?>
        </div>
        <button type="button" id="show_result_distributor"
                class="button-crm" data-url="
                            <?php echo Router::url(
            array(
                'controller' => 'appointments',
                'action' => 'advanced_search_ajax_distributor'
            )
        ); ?>">
            <span class="icon-search c-blanco"></span>
            <?php echo __t('General.Search'); ?>
        </button>
    </div>
    <div id="results_list_distributor" class="d-none">
        <div class="p-1" id="print_results_list_distributor" data-msg="<?php echo __t('Appointment.Big_results'); ?>"></div>
        <span id="back_to_search_distributor" class="f-right p-1 ion-backspace-outline cursor-pointer c-blanco">
            <?php echo __t('Appointment.Back_to_search'); ?>
        </span>
    </div>
</div>
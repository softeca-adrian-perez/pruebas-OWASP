<?php
echo $this->Html->script('contacts_lists.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('lib/easy-ticker/jquery.easing.min.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('lib/easy-ticker/jquery.easy-ticker.min.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('lib/jscolor.min.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Form->create('ContactList');
echo $this->Form->hidden('ContactList.id');
?>
    <div id="alert-div"></div>
    <div class="cnt-form-inputs m-top-1">
        <?php echo $this->Form->input(
            'name',
            array(
                'type' => 'text',
                'id' => 'contact-list-name',
                'required' => true,
                'label' => __t('CRM.List_name'),
            )
        );
        echo $this->Form->input(
                'color',
                array(
                    'label' => __t('CRM.Group_color'),
                    'type' => 'text',
                    'id' => 'contact-list-color',
                    'class' => 'd-inline-block jscolor',
                    'default' => '#7bd36f'
                )
            );
        ?>
        <label class="center-check p-top-1" style="max-width: max-content;">
            <?php echo __t('CRM.Global_list'); ?>
            <div class="aag-switch round small">
                <?php echo $this->Form->input('global', array('id'=> 'contact-list-global', 'label' => false, 'div' => false, 'type' => 'checkbox')); ?>
                <?php // echo !empty($garage_network['GarageNetwork']['quoting_active']) ? 'checked' : '' ?>
                <label for="contact-list-global"></label>
            </div>
        </label>
    </div>
    <br />
    <div class="row p-top-1 p-right-1 p-left-1" style="border: 3px solid var(--container-elements-color); border-radius: 24px;">
        <div style="text-align: end;font-size: 12px;margin-bottom: 5px;"><?php echo __t('ContactList.Limited_100'); ?></div>
        <div class="columns medium-12 iconos-dashboard" style="padding: 0 !important;position: relative;">
            <div class="small-6 columns ta-center listado-cont-izq" style="margin-left: auto;">
                <div class="ticker1">
                    <div id="List1">
                        <?php
                        if (isset($contacts_lists))
                        {
                            foreach ($contacts_lists as $contact_list)
                            {
                                ?>
                                <div class="ui-state-default" data-id="<?php echo $contact_list['Contact']['id'] ?>">
                                    <?php echo h($contact_list['Contact']['first_name']) . " " . h($contact_list['Contact']['last_name']); ?>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="ta-center cnt-flechas-contact">
                <div id="move-to-left" class="p-1 b-1">
                    <span class="ion-ios-arrow-thin-left icono-grande"></span>
                </div>
                <br />
                <div id="move-to-right" class="p-1 b-1">
                    <span class="ion-ios-arrow-thin-right icono-grande"></span>
                </div>
            </div>
            <div class="small-6 columns ta-center listado-cont-der" id="list_form">
                <?php echo $this->element('../ContactsLists/Elements/ajax_list'); ?>
            </div>
        </div>
    </div>
</div>

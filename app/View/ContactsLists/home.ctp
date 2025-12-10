<?php echo $this->Html->script('contacts_lists.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script')); ?>
<div class="cnt-breadcrumb">
    <div>
        <?php 
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Contact.Contacts_lists'),
                array(
                    'controller' => 'contacts_lists',
                    'action' => 'home',
                )
            ),
            __t('General.Home'))); ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back')?></a>
    </div>
</div>
<div class="cnt-data">
    <?php echo $this->element('../ContactsLists/Elements/search_home');?>
    <div id="dashboard-lists">
        <?php echo $this->element('../ContactsLists/Elements/ajax_home');?>
    </div>
</div>
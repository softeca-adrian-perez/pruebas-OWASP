<?php echo $this->Html->script('contacts_directory.js?v='.Configure::read('VERSION_CACHE'), array('block' => 'script')); ?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Contact.Directory'),
                array(
                    'controller' => 'contacts',
                    'action' => 'directory'
                )
            ),
            __t('General.Home'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back')?></a>  
    </div>
</div>
<div class="cnt-data aag-padding">
    <?php echo $this->element('../Contacts/Elements/search_directory'); ?>
    <div class="d-inline-block p-top-1 p-bottom-1 background-color-primary">
        <div class="background-color-primary">
            <div class="aag-subtitle">
                <?php echo __t('Maintenance.Positions'); ?>
            </div>
            <div class="link-contact-directory position" data-id="0"
                data-url=" <?php echo Router::url(array('controller' => 'contacts','action' => 'ajax_load_contacts')); ?>"
                data-div="#div_contacts"
                id="0"
                >
                    <?php echo __t('Contact.All_contacts'); ?>
            </div>

            <?php
            foreach($positions_list as $position)
            {
                ?>
                <div class="link-contact-directory position" data-id="<?php echo $position; ?>"
                data-url=" <?php echo Router::url(array('controller' => 'contacts','action' => 'ajax_load_contacts')); ?>"
                data-div="#div_contacts"
                id="<?php echo $position; ?>"
                >
                    <?php echo $positions[$position]; ?>
                </div>
                <?php
            }
            ?>
        </div>
        <div class="d-none hr-mobile">
            <hr />
        </div>
        <div class="background-color-primary" id="div_contacts">
            <?php echo $this->element('../Contacts/Elements/ajax_load_contacts');?>
        </div>
    </div>
</div>
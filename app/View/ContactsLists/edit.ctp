<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Contact.Contacts_lists'),
                array(
                    'controller' => 'contacts_lists',
                    'action' => 'home'
                )
            ),
            __t('General.Edit'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium four go-back-js"><?php echo __t('General.Back')?></a>
        <?php
        echo $this->Html->link(
            __t('General.Save'),
            array(),
            array(
                'id' => 'create-data',
                'class' => 'aag-button medium green',
                'data-url' => Router::url(array('controller' => 'contacts_lists', 'action' => 'save_contact_list_ajax',$contact_list_id)),
                'data-redirect' => Router::url(array('controller' => 'contacts_lists', 'action' => 'home')),
            )
        );
        ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <?php echo $this->element('../ContactsLists/Elements/search_form');?>
    <?php echo $this->element('../ContactsLists/Elements/form'); ?>
</div>

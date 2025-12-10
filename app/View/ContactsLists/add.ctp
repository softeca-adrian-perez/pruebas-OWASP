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
            __t('General.Add'),
        ));?>
    </div>
    <div>
        <?php
        echo $this->Html->link(
            __t('General.Back'),
            CakeSession::read('url_referer'),
            array(
                'class' => 'aag-button medium two',
            )
        );

        echo $this->Html->link(
            __t('General.Save'),
            array(),
            array(
                'id' => 'create-data',
                'class' => 'aag-button medium green',
                'data-url' => Router::url(array('controller' => 'contacts_lists', 'action' => 'create_contact_list_ajax')),
                'data-redirect' => Router::url(array('controller' => 'contacts_lists', 'action' => 'home')),
            )
        );
        ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <?php
    echo $this->element('../ContactsLists/Elements/search_form');
    echo $this->element('../ContactsLists/Elements/form');
    ?>
</div>

<?php
if (isset($error)) {
    echo $this->Form->hidden(
        'delete',
        array(
            'id' => 'delete_contact_list',
            'value' => $error
        )
    );
}
$first_time = true;
if(empty($contacts_lists))
{
    ?>
    <div class="medium-12 columns">
        <?php if($this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CREATE_CONTACT_LIST)) { ?>
            <div class="medium-3 columns p-0 end">
                <div class="medium-12 columns link-contact new-one" data-url="<?php echo Router::url(array('controller' => 'contacts_lists', 'action' => 'add')); ?>">
                    <span class="ion-ios-plus"></span>
                    <br/>
                    <?php echo __t('Contact.Create') ?>
                    <strong>
                        <?php echo __t('Contact.New_list') ?>
                    </strong>
                </div>
            </div>
        <?php } ?>
    </div>
    <?php
}
foreach ($contacts_lists as $key => $contact_list)
{
    if($first_time)
    {
        if($this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CREATE_CONTACT_LIST))
        {
            ?>
            <div class="link-contact new-one" data-url="<?php echo Router::url(array('controller' => 'contacts_lists', 'action' => 'add')); ?>">
                <span class="ion-ios-plus"></span>
                <?php echo __t('Contact.Create') ?>
                <strong>
                    <?php echo __t('Contact.New_list') ?>
                </strong>
                <span class="aag-icon-usuario"></span>
            </div>
            <?php
        }
    }
    $first_time = false;
    ?>
    <div class="cnt-group-contact" id="contact-list-<?php echo $contact_list['ContactList']['id']; ?>">
        <?php
        if($this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CREATE_CONTACT_LIST))
        {
            $dataUrl = Router::url(array('controller' => 'contacts_lists', 'action' => 'edit', $contact_list['ContactList']['id']));
        }
        else
        {
            $dataUrl = "#!";
        }
        ?>
        <div class="link-contact" style="border-top-color: var(--primary-color); border-top-color: <?php echo $contact_list['ContactList']['color']; ?> !important;" data-url="<?php echo $dataUrl; ?>">
            <h2 style="color: <?php echo $contact_list['ContactList']['color']; ?> !important;">
                <?php
                echo $contact_list['ContactList']['name'];
                ?>
                <div>
                    <span class="aag-icon-usuario"></span>
                    <div>
                        <?php echo count($contact_list['Contacts']); ?>
                    </div>
                </div>
                <?php
                if($contact_list['ContactList']['global'])
                {
                    ?>
                    <div class="global">
                        <span class="aag-icon-listado" title="Global list" style="color: <?php echo $contact_list['ContactList']['color']; ?> !important;"></span>
                    </div>
                    <?php
                }
                ?>
            </h2>
        </div>
        <ul>
            <?php
            foreach($contact_list['Contacts'] as $contact)
            {
                echo "<li>".h($contact['Contact']['first_name']) . " " . h($contact['Contact']['last_name']) . "<br></li>";
            }
            ?>
        </ul>
        <?php
        if($this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CREATE_CONTACT_LIST) && $contact_list['Tasks'])
        {
            echo $this->Html->Link(
                '<span class="aag-icon-papelera c-fallo"></span>',
                array(),
                array(
                    'escape' => false,
                    'title' => __t('General.Delete'),
                    'class' => 'new-delete-js',
                    'data-url' => Router::url(array(
                        'controller' => 'contacts_lists',
                        'action' => 'delete_contact_list',
                        $contact_list['ContactList']['id']
                    )),
                    'data-url_redirect' => Router::url(array(
                        'controller' => 'contacts_lists',
                        'action' => 'home',
                    )),
                    'data-confirmmsg' => __t('Do you want to delete this list?'),
                )
            );
        }
        ?>
    </div>
    <?php
}
?>

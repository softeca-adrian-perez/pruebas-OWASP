<?php
$controller = $this->request->controller;
$action = $this->request->action;
if (
    CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN &&
    ($controller != 'users' && $action != 'my_data') &&
    ($controller != 'user_guides' && $action != 'add') &&
    ($controller != 'contacts' && $action != 'add_contact_and_user')
) {
    $hide_save = true;
}
?>
<a class="aag-button medium two go-back-js"><?php echo __t('General.Back') ?></a>
<?php
if (!isset($hide_save)) {
    echo $this->Form->submit(__t('General.Save'), array('div' => false, 'class' => 'aag-button medium green', 'id' => 'btn-guardar'));
}

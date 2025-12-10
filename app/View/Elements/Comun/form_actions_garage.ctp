<?php
$previous_url = CakeSession::read('url_referer');
$current_url = ConstantsHTTP::HTTPS . Configure::read('URL_BASE') . $this->here;

if ($current_url == $previous_url) {
    $previous_url = Router::url(array(
        'controller' => 'home',
        'action' => 'home_page2'
    ));
}

if (!isset($hide_save)) {
    echo $this->Form->submit(
        __t('General.Save'),
        array(
            'div' => false,
            'class' => 'aag-button medium green',
            'id' => 'btn-guardar',
            'style' => $this->request->action == 'add' ? '' : 'display:none !important'
        )
    );
}

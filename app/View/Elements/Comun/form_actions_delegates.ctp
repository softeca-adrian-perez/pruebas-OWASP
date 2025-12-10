<?php
$previous_url = CakeSession::read('url_referer');
$current_url = ConstantsHTTP::HTTPS . Configure::read('URL_BASE') . $this->here;

if ($current_url == $previous_url) {
    $previous_url = Router::url(array(
        'controller' => 'home',
        'action' => 'home_page2'
    ));
}

echo $this->Html->link(
    __t('General.Back'),
    $previous_url,
    array(
        'class' => 'aag-button medium two',
    )
);

if (!isset($ocultar_guardar)) {
    echo $this->Form->submit(
        __t('General.Save'),
        array(
            'div' => false,
            'class' => 'aag-button medium green',
            'id' => 'btn-guardar-delegate',
            'data-array-reason' => $reasons_delegates,
            'data-array-garages-not-subtract' => $garages_subtractible,
            'data-url-array-garages-network' => Router::url(array(
                'controller' => 'trainings_delegates',
                'action' => 'ajax_garages_network',
            )),
        )
    );
}

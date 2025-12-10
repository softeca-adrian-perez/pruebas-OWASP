<?php

class PanelWidgetRole extends AppModel {

    public $useTable = 'panels_widgets_roles';

    public $belongsTo = array(
        'PanelWidget'=> array(
            'foreignKey' => 'widget_id'
        ),
    );

}
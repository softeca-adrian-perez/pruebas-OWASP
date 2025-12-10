<?php

class PanelWidgetUser extends AppModel {

    public $useTable = 'panels_widgets_users';

    public $belongsTo = array(
        'User' => array(
            'foreignKey' => 'id'
        ),
        'PanelWidget' => array(
            'foreignKey' => 'widget_id'
        ),
    );

    public function save_ajax($data,$user){
        $this->deleteAll(array('user_id' => $user['id']));
        $fields = array(
            'PanelWidgetUser' => array(
                'widget_id',
                'user_id',
                'order',
                'row',
                'col',
                'size_x',
            )
        );
        $i=1;
        if(!empty($data)){
            foreach ($data['widget'] as $widget){
                $widget_user = array(
                    'PanelWidgetUser' => array(
                        'widget_id' => $widget['widget_id'],
                        'user_id' => $user['id'],
                        'order' => $i,
                        'row' => $widget['row'],
                        'col' => $widget['col'],
                        'size_x' => $widget['size_x'],
                    )
                );
                $i++;
                $this->create();
                if(!$this->save($widget_user, true, $fields)){
                    return false;
                }
            }
        }
        return true;
    }
}
<?php
class PanelWidget extends AppModel{

    public $useTable = 'panels_widgets';

    public $displayField = 'name';

    public $hasMany = array(
        'Panel.PanelWidgetRole' => array(
            'foreignKey' => 'widget_id'
        ),
        'Panel.PanelWidgetUser' => array(
            'foreignKey' => 'widget_id'
        ),
    );

    public $virtualFields = array(
        'display_name' => 'name',
    );


    protected function _findList($state, $query, $results = array()){
        if($state==='before'){
            $query['fields'] = array(
                'PanelWidget.id',
                'PanelWidget.display_name',
                'PanelWidget.logic_model',
            );
            return $query;
        }elseif($state==='after'){
            $list = array();
            foreach($results as $panelWidget){
                $list[ $panelWidget['PanelWidget']['id'] ] = $panelWidget['PanelWidget']['display_name'];
            }
            return $list;
        }else{
            throw new LogicException("Unknown state: $state");
        }
    }

    public function afterFind($results, $primary=false){
        if( array_key_exists($this->alias, $results) ){
            $this->populateRoleAwareDisplayName($results);
        }else{
            foreach($results as &$row){
                $this->populateRoleAwareDisplayName($row);
            }
            unset($row);
        }
        return $results;
    }


    /**
     * Populates the display_name virtual field for current user's role, as long as the column is already present in the result-set
     * and the widget has defined per-role values
     *
     * @param array $panelWidget Single item from data set
     */
    private function populateRoleAwareDisplayName(array &$panelWidget){
        if( !array_key_exists($this->alias, $panelWidget) ){
            // We're assuming there's always a top key with the model name (CakePHP documentation is rather vague); code will need tweaking if not the case
            throw new LogicException("Could not populate display_name: data set does not include {$this->alias} top level key");
        }
        if( !array_key_exists('display_name', $panelWidget[$this->alias]) ){
            return;
        }
        if( !array_key_exists('logic_model', $panelWidget[$this->alias]) ){
            throw new LogicException("Could not populate display_name: dataset needs to include the logic_model field");
        }
        /* @var BaseWidget $widgetModel */
        $widgetModel = ClassRegistry::init('Panel.' . $panelWidget[$this->alias]['logic_model'], true);
        if($widgetModel===false){
            throw new LogicException("Could not populate display_name: model Panel.{$panelWidget[$this->alias]['logic_model']} does not exist");
        }

        $custom_display_name = $widgetModel->getDisplayNameForRole(CakeSession::read('Auth.User.CurrentNetwork.role_id'));
        if($custom_display_name){
            $panelWidget[$this->alias]['display_name'] = $custom_display_name;
        }
    }

    public function getWidgetsUser ($user_id){
        return $this->find(
            'all',
            array (
                'joins' => array(
                    array(
                        'alias' => 'PanelWidgetUser',
                        'table' => 'panels_widgets_users',
                        'conditions' => 'PanelWidgetUser.widget_id = PanelWidget.id'
                    ),
                ),
                'fields' => array (
                    'PanelWidgetUser.*',
                    'PanelWidget.*'
                ),
                'conditions' => array (
                    'PanelWidgetUser.user_id' => $user_id
                ),
                'order' => array(
                    'PanelWidgetUser.order' => 'ASC'
                )
            )
        );
    }

    public function getWidgetsRole($role_id){
        $widgets = $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'PanelWidgetRole',
                        'table' => 'panels_widgets_roles',
                        'conditions' => 'PanelWidgetRole.widget_id = PanelWidget.id'
                    ),
                ),
                'fields' => array (
                    'PanelWidgetRole.*',
                    'PanelWidget.*'
                ),
                'conditions' => array (
                    'PanelWidgetRole.role_id' => $role_id
                ),
                'order' => array(
                    'PanelWidgetRole.order' => 'ASC'
                )
            )
        );
        foreach ($widgets as &$widget) {
            $widget['PanelWidgetUser']=$widget['PanelWidgetRole'];
            unset ($widget['PanelWidgetRole']);
        }
        return $widgets;
    }

    public function getWidgetsRoleGM($role_id){
        $widgets = $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'GeneralManagerWidgetRole',
                        'table' => 'panel_gm_widgets_roles',
                        'conditions' => 'GeneralManagerWidgetRole.widget_id = PanelWidget.id'
                    ),
                ),
                'fields' => array (
                    'GeneralManagerWidgetRole.*',
                    'PanelWidget.*'
                ),
                'conditions' => array (
                    'GeneralManagerWidgetRole.role_id' => $role_id
                ),
                'order' => array(
                    'GeneralManagerWidgetRole.order' => 'ASC'
                )
            )
        );
        foreach ($widgets as &$widget) {
            $widget['PanelWidgetUser']=$widget['GeneralManagerWidgetRole'];
            unset ($widget['GeneralManagerWidgetRole']);
        }
        return $widgets;
    }

    public function getWidgetsAvailable($widgets,$role_id){
        $widget_id = Hash::extract($widgets, '{n}.PanelWidget.id');
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'PanelWidgetRole',
                        'table' => 'panels_widgets_roles',
                        'conditions' => 'PanelWidgetRole.widget_id = PanelWidget.id'
                    ),
                ),
                'fields' => array (
                    'PanelWidget.*'
                ),
                'conditions' => array (
                    'NOT' => array (
                        'PanelWidgetRole.widget_id ' => $widget_id,
                    ),
                    'PanelWidgetRole.role_id' => $role_id,
                ),
                'order' => array(
                    'PanelWidgetRole.order' => 'ASC'
                )
            )
        );
    }

}

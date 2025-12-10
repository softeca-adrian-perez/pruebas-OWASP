<?php

App::uses('BaseWidget', 'Panel.Model');

class WidgetNetwork extends BaseWidget{

    public $useTable = false;

    public function GetDataWidget($user){
        $data = array();
        if(in_array($user['CurrentNetwork']['role_id'], array(ConstantesRoles::MEMBER_COORDINATOR, ConstantesRoles::NETWORK_COORDINATOR, ConstantesRoles::MEMBER, ConstantesRoles::NETWORK_MANAGER, ConstantesRoles::GENERAL_MANAGER))){
            $this->Garage = ClassRegistry::init("Garage");
            $this->Sub = ClassRegistry::init("Sub");
            $this->GarageUnsub = ClassRegistry::init("GarageUnsub");

            if ($user['CurrentNetwork']['role_id'] == ConstantesRoles::MEMBER) {
                $subscription_user_conditions = array(
                    'GarageMember.member_id' =>$user['member_id'],
                    'GarageNetwork.network_id' =>$user['CurrentNetwork']['network_id']
                );
                $subscription_pend_user_conditions = array(
                    'SubMember.member_id' =>$user['member_id'],
                    'Sub.network_id' =>$user['CurrentNetwork']['network_id']
                );
                $unsubscription_user_conditions = array(
                    'GarageMember.member_id' =>$user['member_id'],
                    'GarageUnsub.network_id' =>$user['CurrentNetwork']['network_id']
                );
            } elseif ($user['CurrentNetwork']['role_id'] == ConstantesRoles::MEMBER_COORDINATOR || $user['CurrentNetwork']['role_id'] == ConstantesRoles::NETWORK_COORDINATOR) {
                $subscription_user_conditions = array(
                    'GarageUser.user_id' => $user['id'],
                    'GarageNetwork.network_id' =>$user['CurrentNetwork']['network_id']
                );
                $subscription_pend_user_conditions = array(
                    'SubUser.user_id' =>$user['id'],
                    'Sub.network_id' =>$user['CurrentNetwork']['network_id']
                );
                $unsubscription_user_conditions = array(
                    'GarageUser.user_id' => $user['id'],
                    'GarageUnsub.network_id' =>$user['CurrentNetwork']['network_id']
                );
            }else {
                $subscription_user_conditions = array('GarageNetwork.network_id' =>$user['CurrentNetwork']['network_id']);
                $subscription_pend_user_conditions = array('Sub.network_id' =>$user['CurrentNetwork']['network_id']);
                $unsubscription_user_conditions = array('GarageUnsub.network_id' =>$user['CurrentNetwork']['network_id']);
            }

            $subscriptions_data = $this->Garage->subscriptions_by_role_for_widget($subscription_user_conditions);
            $unsubscriptions_data = $this->GarageUnsub->unsubscription_by_role_for_widget($unsubscription_user_conditions);
            $data_months = array ();
            $months = array();
            $fecha = date('Y-m-01');
            $j=1;
            for($i=0; $i< 12;$i++){
                // $months[$i]=strtoupper(Fecha::shortNameMonth(date('n', strtotime('-'.(12-$j).' months', strtotime ($fecha)))));
                $data_months[$i]=date('n', strtotime('-'.(12-$j).' months', strtotime ($fecha)));
                $j++;
            }

            $subscriptions = array();
            $subscriptions_values = array();
            for ($i = 0; $i < 12; $i++) {
                $fecha = date('Y-m-01');
                $nuevafecha = strtotime('-' . $i . ' month', strtotime($fecha)) ;
                $nuevafecha = date('Y-m-01', $nuevafecha );
                $subscriptions[date("m", strtotime($nuevafecha)).'-'.date("Y", strtotime($nuevafecha))] = '0';
            }
            foreach($subscriptions_data as $subscription_data) {
                $key = sprintf('%02d', $subscription_data[0]['month']) . '-' . $subscription_data[0]['year'];
                if (array_key_exists($key, $subscriptions)) {
                    $subscriptions[$key] = $subscription_data[0]['quantity'];
                }
            }
            $subscriptions = array_reverse($subscriptions);
            foreach($subscriptions as $key => $value) {
                $subscriptions_values[] = intval($value);
            }

            $unsubscriptions = array();
            $unsubscriptions_values = array();
            for ($i = 0; $i < 12; $i++) {
                $fecha = date('Y-m-01');
                $nuevafecha = strtotime('-' . $i . ' month', strtotime($fecha)) ;
                $nuevafecha = date('Y-m-01', $nuevafecha );
                $unsubscriptions[date("m", strtotime($nuevafecha)).'-'.date("Y", strtotime($nuevafecha))] = '0';
            }
            foreach($unsubscriptions_data as $unsubscription_data) {
                $key = sprintf('%02d', $unsubscription_data[0]['month']) . '-' . $unsubscription_data[0]['year'];
                if (array_key_exists($key, $unsubscriptions)) {
                    $unsubscriptions[$key] = $unsubscription_data[0]['quantity'];
                }
            }
            $unsubscriptions = array_reverse($unsubscriptions);
            foreach($unsubscriptions as $key => $value) {
                $unsubscriptions_values[] = intval($value);
            }


            $data['graphic_category'] = json_encode($months);
            $data['graphic_data'] = "[{ marker:{ symbol: 'url(/panel/css/img/f-circle-blue.png)'}, color: '#30C8CA' , data:";
            $data['graphic_data'] .= json_encode($subscriptions_values);
            $data['graphic_data'] .= " },{ marker:{ symbol: 'url(/panel/css/img/f-circle-red.png)'}, color: '#FF4545', data:";
            $data['graphic_data'] .= json_encode($unsubscriptions_values);
            $data['graphic_data'] .= "}]";
            $data['subscriptions_pending'] = $this->Sub->subscriptions_pending_by_role($subscription_pend_user_conditions);
            $data['unsubscription_pending'] = $this->GarageUnsub->unsubscription_pending_by_role($unsubscription_user_conditions);
            $data['graph_options'] =
                "title: {
                    text: ''
                },
                legend: {
                    enabled: false
                },
                credits: {
                    enabled: false
                },
                navigation: {
                    buttonOptions: {
                        enabled: false
                    }
                },
                yAxis: {
                    title: {
                        text: ''
                    },
                    min: 0
                },
                tooltip: {
                    headerFormat: '',
                    pointFormat: '{point.y}'
                },
                plotOptions: {
                    spline: {
                        marker: {
                            enabled: true
                        }
                    }
                },";
        }
        return $data;
    }

    public function GetDataWidgetByNetwork($network_id){

        $this->Network = ClassRegistry::init('Network');
        $this->Garage = ClassRegistry::init("Garage");
        $network = $this->Network->findById($network_id);
        
        $data = array();
        $data_months = array();
        $months = array();
        $fecha = date('Y-m-01');
        $j = 1;
        for($i = 0; $i < 12; $i++){
            $months[$i] = strtoupper(Fecha::shortNameMonth(date('n', strtotime('-' . (12 - $j) . ' months', strtotime($fecha)))));
            $data_months[$i] = date('n', strtotime('-' . (12 - $j) . ' months', strtotime($fecha)));
            $j++;
        }

        $subscription_user_conditions = array('GarageNetwork.network_id' => $network['Network']['id']);
        $subscriptions_data = $this->Garage->subscriptions_for_widget($subscription_user_conditions);
        $unsubscriptions_data = $this->Garage->unsubscriptions_for_widget($subscription_user_conditions);

        $subscriptions = array();
        $subscriptions_values = array();
        for($i = 0; $i < 12; $i++){
            $fecha = date('Y-m-01');
            $nuevafecha = strtotime('-' . $i . ' month', strtotime($fecha));
            $nuevafecha = date('Y-m-01', $nuevafecha);
            $subscriptions[date("m", strtotime($nuevafecha)) . '-' . date("Y", strtotime($nuevafecha))] = '0';
        }
        foreach($subscriptions_data as $subscription_data){
            $key = sprintf('%02d', $subscription_data[0]['month']) . '-' . $subscription_data[0]['year'];
            if(array_key_exists($key, $subscriptions)){
                $subscriptions[$key] = $subscription_data[0]['quantity'];
            }
        }
        $subscriptions = array_reverse($subscriptions);
        foreach($subscriptions as $key => $value){
            $subscriptions_values[] = intval($value);
        }

        $unsubscriptions = array();
        $unsubscriptions_values = array();
        for($i = 0; $i < 12; $i++){
            $fecha = date('Y-m-01');
            $nuevafecha = strtotime('-' . $i . ' month', strtotime($fecha));
            $nuevafecha = date('Y-m-01', $nuevafecha);
            $unsubscriptions[date("m", strtotime($nuevafecha)) . '-' . date("Y", strtotime($nuevafecha))] = '0';
        }
        foreach($unsubscriptions_data as $unsubscription_data){
            $key = sprintf('%02d', $unsubscription_data[0]['month']) . '-' . $unsubscription_data[0]['year'];
            if(array_key_exists($key, $unsubscriptions)){
                $unsubscriptions[$key] = $unsubscription_data[0]['quantity'];
            }
        }
        $unsubscriptions = array_reverse($unsubscriptions);
        foreach($unsubscriptions as $key => $value){
            $unsubscriptions_values[] = intval($value);
        }

            $data['graphic_category'] = json_encode($months);
            $data['graphic_data'] = "[{ marker:{ symbol: 'url(/panel/css/img/f-circle-blue.png)'}, color: '#30C8CA' , data:";
            $data['graphic_data'] .= json_encode($subscriptions_values);
            $data['graphic_data'] .= " },{ marker:{ symbol: 'url(/panel/css/img/f-circle-red.png)'}, color: '#FF4545', data:";
            $data['graphic_data'] .= json_encode($unsubscriptions_values);
            $data['graphic_data'] .= "}]";
            $data['graph_options'] =
                "title: {
                    text: ''
                },
                legend: {
                    enabled: false
                },
                credits: {
                    enabled: false
                },
                navigation: {
                    buttonOptions: {
                        enabled: false
                    }
                },
                yAxis: {
                    title: {
                        text: ''
                    },
                    min: 0
                },
                tooltip: {
                    headerFormat: '',
                    pointFormat: '{point.y}'
                },
                plotOptions: {
                    spline: {
                        marker: {
                            enabled: true
                        }
                    }
                },";
        return $data;
    }

}

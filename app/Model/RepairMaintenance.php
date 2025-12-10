<?php
App::uses('HttpSocket', 'Network/Http');

class RepairMaintenance extends AppModel
{
    public $useTable = false;

    public function operacion_login(array $user)
    {
        $this->User = ClassRegistry::init('User');

        $usuario = $this->User->find(
            'first',
            array(
                'fields' => array(
                    '*'
                ),
                'joins' => array(
                    array(
                        'alias' => 'Garage',
                        'table' => 'garages',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'User.garage_id = Garage.id',
                        ),
                    ),
                    array(
                        'alias' => 'GarageNetwork',
                        'table' => 'garages_networks',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'GarageNetwork.garage_id = Garage.id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'User.id' => $user['id'],
                ),
            )
        );

        $data = array(
            'red' =>  Configure::read('repair-maintenance.gnm_code'),
            'usuario_gnm_id' => $usuario['User']['id'],
            'red_rm_id' => !empty($usuario['GarageNetwork']['network_id']) ? $usuario['GarageNetwork']['network_id'] : 1, // Autocare por defecto
        );

        $datos = json_encode($data);
        if (!empty($data['usuario_gnm_id'])) {
            return array(
                'datos' => $datos,
                'check' => sha1($datos . Texto::encryptDecryptText(REPAIR_MAINTENANCE_KEY, false) . date('Y-m-d'))
            );
        } else {
            return array();
        }
    }

    public function tiene_permiso_rm($user)
    {
        $this->Garage = ClassRegistry::init("Garage");
        $this->AagRegion = ClassRegistry::init("AagRegion");
        $garage = $this->Garage->findById($user['garage_id']);
        $aag_region = $this->AagRegion->findById($garage['Garage']['aag_region_id']);
        $url_login = $this->peticion_repair_maintenance($user['id'], $aag_region['AagRegion']['url_rm'] . 'ApiGnm/WidgetGconnect/get_auto_login_garage');
        if ($url_login && $url_login->data) {
            return $url_login->data->url_login;
        } else {
            return false;
        }
    }

    public function peticion_repair_maintenance($user_id, $url)
    {
        $user = CakeSession::read('Auth.User');

        $httpSocket = new HttpSocket(
            array(
                'ssl_verify_peer' => false,
                'ssl_verify_host' => false,
                'ssl_allow_self_signed' => true,
                'ssl_verify_peer_name' => false,
            )
        );

        $data = json_encode(
            array(
                'usuarios_gnm_ids' => array($user_id),
                'locale' => $user['language_code']
            )
        );

        $headers = array(
            'header' => array(
                'Authorization' => 'Bearer ' . JWT::encode(
                    array(
                        'id' => 1,
                        'exp' => time() + (60 * 60) // expires in 1 minute
                    ),
                    Texto::encryptDecryptText(REPAIR_MAINTENANCE_KEY, false)
                ),
            ),
            'Content-Type' => 'application/json',
            'User-Agent' => 'CakePHP'
        );

        $httpSocket->configAuth('Basic', Configure::read('gconnect.auth.g_user'), Configure::read('gconnect.auth.g_password'));
        $results = $httpSocket->post($url, $data, $headers);
        return json_decode($results);
    }

    public function acceso_externo($user_id, $id, $action_id)
    {
        $this->User = ClassRegistry::init('User');

        $usuario = $this->User->find(
            'first',
            array(
                'fields' => array(
                    '*'
                ),
                'joins' => array(
                    array(
                        'alias' => 'Garage',
                        'table' => 'garages',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'User.garage_id = Garage.id',
                        ),
                    ),
                    array(
                        'alias' => 'GarageNetwork',
                        'table' => 'garages_networks',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'GarageNetwork.garage_id = Garage.id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'User.id' => $user_id,
                ),
            )
        );

        $data = array(
            'red' =>  Configure::read('repair-maintenance.gnm_code'),
            'usuario_gnm_id' => $usuario['User']['id'],
            'red_rm_id' => !empty($usuario['GarageNetwork']['network_id']) ? $usuario['GarageNetwork']['network_id'] : 1, // Autocare por defecto
            'action' => $action_id,
            'id' => $id,
        );

        $datos = json_encode($data);
        if (!empty($data['usuario_gnm_id'])) {
            return array(
                'datos' => $datos,
                'check' => sha1($datos . Texto::encryptDecryptText(REPAIR_MAINTENANCE_KEY, false) . date('Y-m-d'))
            );
        } else {
            return array();
        }
    }

    public function crear_usuarios_talleres_repairmaintenance()
    {
        $this->Garage = ClassRegistry::init('Garage');
        $talleres = $this->Garage->find(
            'all',
            array(
                'fields' => array('Garage.id'),
                'joins' => array(
                    array(
                        'alias' => 'User',
                        'table' => 'users',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'User.garage_id = Garage.id',
                        ),
                        'fields' => array(
                            'User.id',
                            'User.garage_id',
                            'User.contact_id',
                        ),
                    ),
                    array(
                        'alias' => 'Contact',
                        'table' => 'contacts',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Contact.id = User.contact_id',
                        ),
                        'fields' => array(
                            'Contact.id',
                        ),
                    ),
                    array(
                        'alias' => 'GarageNetwork',
                        'table' => 'garages_networks',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'GarageNetwork.garage_id = Garage.id',
                        ),
                        'fields' => array(
                            'GarageNetwork.id',
                            'GarageNetwork.garage_id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'OR' => array(
                        'Garage.repairmaintenance' => 0,
                        'Garage.repairmaintenance is null'
                    ),
                    'Garage.status' => ConstantsGarageStatus::ACTIVE,
                ),
                'group' => 'Garage.id',
            )
        );

        foreach ($talleres as $taller) {
            $this->anadir_taller_repairmaintenance($taller['Garage']['id']);
            $redes = $this->Garage->GarageNetwork->findAllByGarageIdAndStatusAndLast($taller['Garage']['id'], ConstantsNetworksStatus::LIVE, ConstantsBooleans::ACTIVE);
            if (isset($redes) && !empty($redes)) {
                foreach ($redes as $red) {
                    $this->actualizar_redes_taller($taller['Garage']['id'], $red['GarageNetwork']['network_id']);
                }
            }
        }
    }

    public function crear_usuarios_repairmaintenance()
    {
        if (!REPAIR_MAINTENANCE_SEND_DATA) {
            return;
        }
        $this->User = ClassRegistry::init('User');
        $usuarios = $this->User->find(
            'all',
            array(
                'fields' => array(
                    '*'
                ),
                'conditions' => array(
                    'User.role_id' => ConstantsRoles::AAG_MANAGER,
                    'OR' => array(
                        'User.repairmaintenance' => 0,
                        'User.repairmaintenance is null'
                    ),
                    'User.active' => ConstantsBooleans::YES,
                ),
                'joins' => array(
                    array(
                        'alias' => 'Contact',
                        'table' => 'contacts',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Contact.id = User.contact_id',
                        ),
                    ),
                ),
            )
        );

        foreach ($usuarios as $usuario) {
            if (!empty($usuario)) {
                $datos = array(
                    'Usuario' => array(
                        'nombre' => $usuario['User']['name'],
                        'apellidos' => $usuario['User']['surname'],
                        'nif' => null,
                        'rol_id' => $usuario['User']['role_id'],
                        'email' => $usuario['Contact']['email'],
                        'username' => $usuario['User']['username'],
                        'password' => $usuario['User']['password'],
                        'activo' => 1,
                        'usuario_gnm_id' => $usuario['User']['id'],
                        'red_id' => 1 //Autocare,
                    ),
                );
                if (!empty($datos['Usuario']['username'])) {
                    $httpSocket = new HttpSocket(
                        array(
                            'ssl_verify_peer' => false,
                            'ssl_verify_host' => false,
                            'ssl_allow_self_signed' => true,
                            'ssl_verify_peer_name' => false
                        )
                    );

                    if (isset($usuario['User']['aag_region_id'])) {
                        $this->AagRegion = ClassRegistry::init("AagRegion");
                        $aag_region = $this->AagRegion->findById($usuario['User']['aag_region_id']);
                        if (isset($aag_region['AagRegion']['url_rm'])) {
                            $datos = JWT::encode($datos, Texto::encryptDecryptText(REPAIR_MAINTENANCE_GNM_CONNECTION_KEY, false));
                            $data = json_encode($datos);
                            $results = $httpSocket->post($aag_region['AagRegion']['url_rm'] . Configure::read('repair-maintenance.servicios.nuevo_usuario'), $data);
                            $ok = json_decode($results->body);
                            if ($ok->data) {
                                $this->markUserRepairMaintenance($usuario['User']['id']);
                            }
                        }
                    } else {
                        return;
                    }
                } else {
                    $this->markUserRepairMaintenance($usuario['User']['id']);
                }
            }
        }
    }

    private function anadir_taller_repairmaintenance($garage_id)
    {
        if (!REPAIR_MAINTENANCE_SEND_DATA) {
            return true;
        }

        $this->Garage = ClassRegistry::init('Garage');
        $this->AagRegion = ClassRegistry::init("AagRegion");
        $this->Erp = ClassRegistry::init("Erp");

        $garage = $this->Garage->findById($garage_id);
        $aag_region = $this->AagRegion->findById($garage['Garage']['aag_region_id']);

        if (isset($aag_region['AagRegion']['url_rm']) && $aag_region['AagRegion']['url_rm']) {
            if ($aag_region['AagRegion']['id'] == Configure::read('AAG_REGION_ID_BENELUX')) {
                $generalConditions = array(
                    'Garage.id' => $garage_id,
                    'GarageNetwork.network_id' => NETWORK_ID_LEASEPROF,
                    'OR' => array(
                        'Garage.repairmaintenance' => 0,
                        'Garage.repairmaintenance is null'
                    )
                );
            } else {
                $generalConditions = array(
                    'Garage.id' => $garage_id,
                    'OR' => array(
                        'Garage.repairmaintenance' => 0,
                        'Garage.repairmaintenance is null'
                    )
                );
            }

            $taller = $this->Garage->find(
                'first',
                array(
                    'fields' => array(
                        'Garage.*',
                        'User.*',
                        'Contact.*',
                        'GarageNetwork.*',
                        'Province.*',
                    ),
                    'joins' => array(
                        array(
                            'alias' => 'User',
                            'table' => 'users',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'User.garage_id = Garage.id',
                            ),
                        ),
                        array(
                            'alias' => 'Province',
                            'table' => 'provinces',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Garage.province_id = Province.id',
                            ),
                        ),
                        array(
                            'alias' => 'Contact',
                            'table' => 'contacts',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Contact.id = User.contact_id',
                            ),
                        ),
                        array(
                            'alias' => 'GarageNetwork',
                            'table' => 'garages_networks',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'GarageNetwork.garage_id = Garage.id',
                                'GarageNetwork.status' => ConstantsNetworksStatus::LIVE,
                            ),
                        ),
                    ),
                    'conditions' => $generalConditions
                )
            );

            if (!empty($taller)) {
                if (!$taller['User']['id']) {
                    $this->Contact = ClassRegistry::init('Contact');
                    $email_exist = $this->Contact->findFirstByEmail($taller['Garage']['email']);

                    if (!empty($email_exist)) {

                        if ($taller['Garage']['aag_region_id'] != Configure::read('AAG_REGION_ID_BENELUX')) {
                            $email = 'ukaag' . $taller['Garage']['garage_code'] . '@' . 'ukaag' . $taller['Garage']['garage_code'] . '.com';
                        } else {
                            $email = 'benaag' . $taller['Garage']['garage_code'] . '@' . 'benaag' . $taller['Garage']['garage_code'] . '.com';
                        }

                        $generated_email_exist = $this->Contact->findFirstByEmail($email);

                        if (!empty($generated_email_exist)) {
                            return false;
                        } else {
                            if ($taller['Garage']['aag_region_id'] != Configure::read('AAG_REGION_ID_BENELUX')) {
                                $email = 'ukaag' . $taller['Garage']['garage_code'] . '@' . 'ukaag' . $taller['Garage']['garage_code'] . '.com';
                            } else {
                                $email = 'benaag' . $taller['Garage']['garage_code'] . '@' . 'benaag' . $taller['Garage']['garage_code'] . '.com';
                            }
                        }
                    } else {
                        if ($taller['Garage']['aag_region_id'] != Configure::read('AAG_REGION_ID_BENELUX')) {
                            $email = !empty($taller['Garage']['email']) ? $taller['Garage']['email'] : 'ukaag' . $taller['Garage']['garage_code'] . '@' . 'ukaag' . $taller['Garage']['garage_code'] . '.com';
                        } else {
                            $email = !empty($taller['Garage']['email']) ? $taller['Garage']['email'] : 'benaag' . $taller['Garage']['garage_code'] . '@' . 'benaag' . $taller['Garage']['garage_code'] . '.com';
                        }
                    }

                    if ($taller['Garage']['aag_region_id'] != Configure::read('AAG_REGION_ID_BENELUX')) {
                        $contact_tmp = array(
                            'Contact' => array(
                                'title_id' => null,
                                'contact_id' => null,
                                'first_name' => !empty($taller['Garage']['business_name']) ? $taller['Garage']['business_name'] : 'ukaag' . $taller['Garage']['garage_code'],
                                'last_name' => '-',
                                'position_id' => ConstantsPositions::GARAGE_MANAGER_ID,
                                'garage_id' => $taller['Garage']['id'],
                                'distributor_id' => null,
                                'logistic_center_id' => null,
                                'phone' => null,
                                'mobile_phone' => null,
                                'email' => $email,
                                'identification_number' => 'UK_AAG_' . $taller['Garage']['garage_code'],
                                'aag_region_id' => $taller['Garage']['aag_region_id'],
                            )
                        );
                    } else {
                        $contact_tmp = array(
                            'Contact' => array(
                                'title_id' => null,
                                'contact_id' => null,
                                'first_name' => !empty($taller['Garage']['business_name']) ? $taller['Garage']['business_name'] : 'benaag' . $taller['Garage']['garage_code'],
                                'last_name' => '-',
                                'position_id' => ConstantsPositions::GARAGE_MANAGER_ID,
                                'garage_id' => $taller['Garage']['id'],
                                'distributor_id' => null,
                                'logistic_center_id' => null,
                                'phone' => null,
                                'mobile_phone' => null,
                                'email' => $email,
                                'identification_number' => 'BEN_AAG_' . $taller['Garage']['garage_code'],
                                'aag_region_id' => $taller['Garage']['aag_region_id'],
                            )
                        );
                    }

                    $contact = $this->Contact->new_contact($contact_tmp);

                    if ($taller['Garage']['aag_region_id'] != Configure::read('AAG_REGION_ID_BENELUX')) {
                        $user_tmp = array(
                            'User' => array(
                                'name' => !empty($taller['Garage']['business_name']) ? $taller['Garage']['business_name'] : 'ukaag' . $taller['Garage']['garage_code'],
                                'surname' => '-',
                                'username' => 'Uk_AAG_' . $taller['Garage']['garage_code'],
                                'password' => 'Uk_AAG_' . $taller['Garage']['garage_code'] . '#',
                                'password_repetido' => 'Uk_AAG_' . $taller['Garage']['garage_code'] . '#',
                                'active' => ConstantsBooleans::YES,
                                'role_id' => ConstantsRoles::GARAGE,
                                'language_id' => ConstantsLanguages::ENGLISH,
                                'garage_id' => $taller['Garage']['id'],
                                'contact_id' => $contact['Contact']['id'],
                                'aag_region_id' => $taller['Garage']['aag_region_id'],
                            )
                        );
                    } else {
                        $user_tmp = array(
                            'User' => array(
                                'name' => !empty($taller['Garage']['business_name']) ? $taller['Garage']['business_name'] : 'benaag' . $taller['Garage']['garage_code'],
                                'surname' => '-',
                                'username' => 'Ben_AAG_' . $taller['Garage']['garage_code'],
                                'password' => 'Ben_AAG_' . $taller['Garage']['garage_code'] . '#',
                                'password_repetido' => 'Ben_AAG_' . $taller['Garage']['garage_code'] . '#',
                                'active' => ConstantsBooleans::YES,
                                'role_id' => ConstantsRoles::GARAGE,
                                'language_id' => ConstantsLanguages::ENGLISH,
                                'garage_id' => $taller['Garage']['id'],
                                'contact_id' => $contact['Contact']['id'],
                                'aag_region_id' => $taller['Garage']['aag_region_id'],
                            )
                        );
                    }

                    $this->User = ClassRegistry::init('User');
                    $user = $this->User->add($user_tmp);
                } else {
                    $user['User'] = $taller['User'];
                }

                if ($taller['Garage']['aag_region_id'] != Configure::read('AAG_REGION_ID_BENELUX')) { // SI EL GARAGE ES DE UK
                    $datos = array(
                        'Usuario' => array(
                            'nombre' => isset($user['User']['id']) ? $user['User']['name'] : $taller['Garage']['business_name'],
                            'apellidos' =>  isset($user['User']['id']) ? $user['User']['surname'] : '',
                            'nif' => null,
                            'email' => isset($contact['Contact']['email']) && !empty($contact['Contact']['email']) ? $contact['Contact']['email'] : (isset($taller['Garage']['email']) && !empty($taller['Garage']['email']) ? $taller['Garage']['email'] : 'ukaag' . $taller['Garage']['garage_code'] . '@' . 'ukaag' . $taller['Garage']['garage_code'] . '.com'),
                            'username' =>  isset($user['User']['id']) ? $user['User']['username'] : 'Uk_AAG_' . $taller['Garage']['garage_code'],
                            'password' =>  'Uk_AAG_' . $taller['Garage']['garage_code'] . '#',
                            'activo' => ConstantsBooleans::YES,
                            'usuario_gnm_id' =>  isset($user['User']['id']) ? $user['User']['id'] : null,
                            'red_id' => $taller['GarageNetwork']['network_id'],
                        ),
                        'Taller' => array(
                            'nombre' => isset($taller['Garage']['name']) ? $taller['Garage']['name'] : null,
                            'direccion' => isset($taller['Garage']['address1']) ? $taller['Garage']['address1'] : null,
                            'bdu_id' => $garage_id,
                            'codigo_gnm' => isset($taller['Garage']['garage_code']) && !empty($taller['Garage']['garage_code']) ? $taller['Garage']['garage_code'] : null,
                            'province_code' => !empty($taller['Province']['province_code']) ? $taller['Province']['province_code'] : null,
                            'activo' => $taller['Garage']['status'] == ConstantsGarageStatus::ACTIVE ? '1' : '0',
                        ),
                    );
                } else {

                    if (isset($taller['Garage']['erp_id'])) {
                        $erp = $this->Erp->findById($taller['Garage']['erp_id']);
                    }

                    $datos = array(
                        'Usuario' => array(
                            'nombre' => isset($user['User']['id']) ? $user['User']['name'] : $taller['Garage']['business_name'],
                            'apellidos' => isset($user['User']['id']) ? $user['User']['surname'] : '',
                            'nif' => null,
                            'email' => isset($contact) && $contact['Contact']['id'] ? $contact['Contact']['email'] : (isset($taller['Garage']['email']) && !empty($taller['Garage']['email']) ? $taller['Garage']['email'] : 'benaag' . $taller['Garage']['garage_code'] . '@' . 'benaag' . $taller['Garage']['garage_code'] . '.com'),
                            'username' =>  isset($user['User']['id']) ? $user['User']['username'] : 'Ben_AAG_' . $taller['Garage']['garage_code'],
                            'password' =>  'Ben_AAG_' . $taller['Garage']['garage_code'] . '#',
                            'activo' => ConstantsBooleans::YES,
                            'usuario_gnm_id' =>  isset($user['User']['id']) ? $user['User']['id'] : null,
                            'red_id' => $taller['GarageNetwork']['network_id'],
                        ),
                        'Taller' => array(
                            'nombre' => isset($taller['Garage']['name']) ? $taller['Garage']['name'] : null,
                            'direccion' => isset($taller['Garage']['address1']) ? $taller['Garage']['address1'] : null,
                            'bdu_id' => $garage_id,
                            'codigo_gnm' => isset($taller['Garage']['garage_code']) && !empty($taller['Garage']['garage_code']) ? $taller['Garage']['garage_code'] : null,
                            'province_code' => !empty($taller['Province']['province_code']) ? $taller['Province']['province_code'] : null,
                            'activo' => $taller['Garage']['status'] == ConstantsGarageStatus::ACTIVE ? '1' : '0',
                            'mobile' => isset($taller['Garage']['mobile']) ? $taller['Garage']['mobile'] : null,
                            'rob_code' => isset($taller['Garage']['rob_code']) && !empty($taller['Garage']['rob_code']) ? $taller['Garage']['rob_code'] : null,
                            'ref_code' => isset($taller['Garage']['ref_code']) && !empty($taller['Garage']['ref_code']) ? $taller['Garage']['ref_code'] : null,
                            'erp_code' => isset($erp['Erp']['erp_code']) && !empty($erp['Erp']['erp_code']) ? strtolower($erp['Erp']['erp_code']) : null,
                            'erp_email' => isset($taller['Garage']['erp_email']) && !empty($taller['Garage']['erp_email']) ? $taller['Garage']['erp_email'] : null,
                            'creditor_number' => isset($taller['Garage']['creditor_number']) && !empty($taller['Garage']['creditor_number']) ? $taller['Garage']['creditor_number'] : null,
                            'payment_terms' => isset($taller['Garage']['payment_terms']) && !empty($taller['Garage']['payment_terms']) ? $taller['Garage']['payment_terms'] : null,
                            'company_code' => isset($taller['Garage']['company_code']) && !empty($taller['Garage']['company_code']) ? $taller['Garage']['company_code'] : null,
                        ),
                    );
                }

                if (!empty($datos['Taller']['bdu_id'])) {
                    $httpSocket = new HttpSocket(
                        array(
                            'ssl_verify_peer' => false,
                            'ssl_verify_host' => false,
                            'ssl_allow_self_signed' => true,
                            'ssl_verify_peer_name' => false
                        )
                    );
                    $datos = JWT::encode($datos, Texto::encryptDecryptText(REPAIR_MAINTENANCE_GNM_CONNECTION_KEY, false));
                    $data = json_encode($datos);
                    $results = $httpSocket->post($aag_region['AagRegion']['url_rm'] . Configure::read('repair-maintenance.servicios.nuevo_usuario'), $data);
                    $ok = json_decode($results);
                    if (is_object($ok) && $ok->data) {
                        return $this->markGarageRepairMaintenance($taller['Garage']['id']);
                    } else {
                        return false;
                    }
                } else {
                    return $this->markGarageRepairMaintenance($taller['Garage']['id']);
                }
            } else {
                return true;
            }
        } else {
            return true;
        }
    }

    private function markGarageRepairMaintenance($garage_id)
    {
        $this->Garage = ClassRegistry::init('Garage');
        $fields = array(
            'Garage' => array(
                'id',
                'repairmaintenance'
            )
        );
        $taller = array(
            'Garage' => array(
                'id' => $garage_id,
                'repairmaintenance' => true
            )
        );

        return $this->Garage->save($taller, true, $fields);
    }

    private function markUserRepairMaintenance($user_id)
    {
        $this->User = ClassRegistry::init('User');
        $fields = array(
            'User' => array(
                'id',
                'repairmaintenance'
            )
        );
        $usuario = array(
            'User' => array(
                'id' => $user_id,
                'repairmaintenance' => true
            )
        );

        return $this->User->save($usuario, true, $fields);
    }

    public function error()
    {
        return $this->return_result(0, false);
    }

    public function get_token_data($token)
    {
        try {
            $data = JWT::decode($token, Texto::encryptDecryptText(REPAIR_MAINTENANCE_GNM_CONNECTION_KEY, false));
            if (isset($data->id) && $data->id) {
                $decoded_data['id'] = $data->id;

                if (isset($data->exp) && $data->exp) {
                    $decoded_data['exp'] = $data->exp;
                }

                return $decoded_data;
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public function return_result($result, $status, $code = 0)
    {
        return array(
            'status' => $status ? 'ok' : 'error',
            'data' => $result,
            'code' => $code,
        );
    }

    public function actualizar_redes_taller($garage_id, $red_id, $eliminar = false)
    {
        if (!REPAIR_MAINTENANCE_SEND_DATA) {
            return true;
        }

        $this->Garage = ClassRegistry::init("Garage");
        $this->AagRegion = ClassRegistry::init("AagRegion");
        $garage = $this->Garage->findById($garage_id);
        $aag_region = $this->AagRegion->findById($garage['Garage']['aag_region_id']);

        if (isset($aag_region['AagRegion']['url_rm']) && $aag_region['AagRegion']['url_rm']) {

            $datos['Garage'] = $garage_id;
            $datos['Red'] = $red_id;
            $datos['Eliminar'] = $eliminar;

            $httpSocket = new HttpSocket(
                array(
                    'ssl_verify_peer' => false,
                    'ssl_verify_host' => false,
                    'ssl_allow_self_signed' => true,
                    'ssl_verify_peer_name' => false
                )
            );

            $datos = JWT::encode($datos, Texto::encryptDecryptText(REPAIR_MAINTENANCE_GNM_CONNECTION_KEY, false));
            $data = json_encode($datos);
            $results = $httpSocket->post($aag_region['AagRegion']['url_rm'] . Configure::read('repair-maintenance.servicios.actualizar_redes_garage'), $data);
            $ok = json_decode($results);
            if (is_object($ok) && $ok->data) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    /**
     * Updates the specified garage in Repair Maintenance.
     */
    public function update_garage($garage)
    {
        if (!REPAIR_MAINTENANCE_SEND_DATA) {
            return true;
        }

        $this->AagRegion = ClassRegistry::init("AagRegion");
        $aag_region = $this->AagRegion->findById($garage['Garage']['aag_region_id']);

        if (isset($aag_region['AagRegion']['url_rm'])) {
            $this->Garage = ClassRegistry::init('Garage');
            $this->Province = ClassRegistry::init('Province');
            $this->Erp = ClassRegistry::init('Erp');

            $garage_bd = $this->Garage->findById($garage['Garage']['id']);
            if (empty($garage_bd['Garage']['repairmaintenance'])) {
                return true;
            }

            if (isset($garage_bd['Garage']['erp_id'])) {
                $erp = $this->Erp->findById($garage_bd['Garage']['erp_id']);
            }

            if (isset($garage_bd['Garage']['province_id'])) {
                $province = $this->Province->findById($garage_bd['Garage']['province_id']);
            }

            $datos['PmTaller']['gnm_id'] = $garage_bd['Garage']['id'];
            $datos['PmTaller']['nombre'] = isset($garage_bd['Garage']['name']) ? $garage_bd['Garage']['name'] : null;
            $datos['PmTaller']['direccion'] = isset($garage_bd['Garage']['address1']) ? $garage_bd['Garage']['address1'] : null;
            $datos['PmTaller']['activo'] = $garage_bd['Garage']['status'] == 2 ? true : false;
            $datos['PmTaller']['province_code'] = isset($province['Province']['province_code']) ? $province['Province']['province_code'] : null;
            $datos['PmTaller']['telefono_24h'] = $garage_bd['Garage']['service_24h_phone'];
            $datos['PmTaller']['rob_code'] = isset($garage_bd['Garage']['rob_code']) ? $garage_bd['Garage']['rob_code'] : null;
            $datos['PmTaller']['ref_code'] = isset($garage['Garage']['ref_code']) ? $garage['Garage']['ref_code'] : null;
            $datos['PmTaller']['erp_code'] = isset($erp['Erp']['erp_code']) ? strtolower($erp['Erp']['erp_code']) : null;
            $datos['PmTaller']['erp_email'] = isset($garage_bd['Garage']['erp_email']) ? $garage_bd['Garage']['erp_email'] : null;
            $datos['PmTaller']['mobile'] = isset($garage_bd['Garage']['mobile']) ? $garage_bd['Garage']['mobile'] : null;
            $datos['PmTaller']['creditor_number'] = isset($garage_bd['Garage']['creditor_number']) ? $garage_bd['Garage']['creditor_number'] : null;
            $datos['PmTaller']['payment_terms'] = isset($garage_bd['Garage']['payment_terms']) ? $garage_bd['Garage']['payment_terms'] : null;
            $datos['PmTaller']['company_code'] = isset($garage_bd['Garage']['company_code']) ? $garage_bd['Garage']['company_code'] : null;

            $httpSocket = new HttpSocket(
                array(
                    'ssl_verify_peer' => false,
                    'ssl_verify_host' => false,
                    'ssl_allow_self_signed' => true,
                    'ssl_verify_peer_name' => false
                )
            );

            $datos = JWT::encode($datos, Texto::encryptDecryptText(REPAIR_MAINTENANCE_GNM_CONNECTION_KEY, false));
            $data = json_encode($datos);
            $results = $httpSocket->post($aag_region['AagRegion']['url_rm'] . Configure::read('repair-maintenance.servicios.actualizar_garage'), $data);
            $ok = json_decode($results);
            if (is_object($ok) && $ok->data) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    public function updateFleet($fleetUpdate)
    {
        if (!REPAIR_MAINTENANCE_SEND_DATA) {
            return true;
        }

        $this->AagRegion = ClassRegistry::init("AagRegion");
        $this->Fleet = ClassRegistry::init('Fleet');
        $this->FleetNetwork = ClassRegistry::init('FleetNetwork');
        $this->Erp = ClassRegistry::init('Erp');
        $fleet = $this->Fleet->findById($fleetUpdate['Fleet']['id']);

        $aag_region = $this->AagRegion->findById($fleet['Fleet']['aag_region_id']);

        if (isset($aag_region['AagRegion']['url_rm']) && isset($fleet['Fleet']['repairmaintenance']) && $fleet['Fleet']['repairmaintenance'] == ConstantsBooleans::YES) {
            $fleetData['Fleet']['guid'] = $fleet['Fleet']['guid'];
            $fleetData['Fleet']['name'] = $fleetUpdate['Fleet']['name'];
            $fleetData['Fleet']['active'] = $fleetUpdate['Fleet']['active'];
            $fleetData['Fleet']['tax_code'] = $fleetUpdate['Fleet']['tax_code'];
            $fleetData['Fleet']['erp_code'] = $this->Erp->findById($fleetUpdate['Fleet']['erp_id'])['Erp']['erp_code'];
            $fleetData['Fleet']['ref_code'] = $fleetUpdate['Fleet']['ref_code'];
            $fleetData['Fleet']['company_code'] = $fleetUpdate['Fleet']['company_code'];
            $fleetData['Fleet']['networks'] = $this->FleetNetwork->findListByFleetId($fleet['Fleet']['id'], ['network_id']);

            $httpSocket = new HttpSocket(
                array(
                    'ssl_verify_peer' => false,
                    'ssl_verify_host' => false,
                    'ssl_allow_self_signed' => true,
                    'ssl_verify_peer_name' => false
                )
            );

            $data = json_encode(JWT::encode($fleetData, Texto::encryptDecryptText(REPAIR_MAINTENANCE_GNM_CONNECTION_KEY, false)));
            $results = $httpSocket->post($aag_region['AagRegion']['url_rm'] . Configure::read('repair-maintenance.servicios.edit_fleet'), $data);
            $ok = json_decode($results);
            return is_object($ok) && $ok->data;
        } else {
            return true;
        }
    }

    public function addFleet($newFleet)
    {
        if (!REPAIR_MAINTENANCE_SEND_DATA) {
            return true;
        }

        $this->AagRegion = ClassRegistry::init("AagRegion");
        $this->Fleet = ClassRegistry::init('Fleet');
        $this->FleetNetwork = ClassRegistry::init('FleetNetwork');
        $this->Erp = ClassRegistry::init('Erp');
        $aag_region = $this->AagRegion->findById($newFleet['Fleet']['aag_region_id']);
        $fleet = $this->Fleet->findById($newFleet['Fleet']['id']);

        if (isset($aag_region['AagRegion']['url_rm']) && isset($fleet['Fleet']['repairmaintenance']) && $fleet['Fleet']['repairmaintenance'] == ConstantsBooleans::NO) {
            $fleetData['Fleet']['guid'] = $newFleet['Fleet']['guid'];
            $fleetData['Fleet']['name'] = $newFleet['Fleet']['name'];
            $fleetData['Fleet']['erp_code'] = $this->Erp->findById($newFleet['Fleet']['erp_id'])['Erp']['erp_code'];
            $fleetData['Fleet']['ref_code'] = $newFleet['Fleet']['ref_code'];
            $fleetData['Fleet']['payment_terms'] = $newFleet['Fleet']['payment_terms'];
            $fleetData['Fleet']['active'] = $newFleet['Fleet']['active'];
            $fleetData['Fleet']['tax_code'] = isset($newFleet['Fleet']['tax_code']) ? $newFleet['Fleet']['tax_code'] : null;
            $fleetData['Fleet']['company_code'] = isset($newFleet['Fleet']['company_code']) ? $newFleet['Fleet']['company_code'] : null;
            $fleetData['Fleet']['networks'] = $this->FleetNetwork->findListByFleetId($fleet['Fleet']['id'], ['network_id']);

            $httpSocket = new HttpSocket(
                array(
                    'ssl_verify_peer' => false,
                    'ssl_verify_host' => false,
                    'ssl_allow_self_signed' => true,
                    'ssl_verify_peer_name' => false
                )
            );

            $data = json_encode(JWT::encode($fleetData, Texto::encryptDecryptText(REPAIR_MAINTENANCE_GNM_CONNECTION_KEY, false)));
            $results = $httpSocket->post($aag_region['AagRegion']['url_rm'] . Configure::read('repair-maintenance.servicios.add_fleet'), $data);
            $ok = json_decode($results);
            if (is_object($ok) && $ok->data) {
                $res = $this->Fleet->setRepairMaintenance($newFleet['Fleet']['id']);
                return $res;
            } else {

                return false;
            }
        } else {
            return true;
        }
    }

    public function sendFleetsToRepair()
    {
        if (!REPAIR_MAINTENANCE_SEND_DATA) {
            return true;
        }

        $this->Fleet = ClassRegistry::init('Fleet');
        $unsentFleets = $this->Fleet->findAllByRepairmaintenance(ConstantsBooleans::NO);

        foreach ($unsentFleets as $value) {
            $this->addFleet($value);
        }
    }
}

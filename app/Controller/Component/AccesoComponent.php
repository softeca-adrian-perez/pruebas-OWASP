<?php

App::uses('SimplePasswordHasher', 'Controller/Component/Auth');

/**
 * @property AclComponent $Acl
 * @property AuthComponent $Auth
 */
class AccesoComponent extends Component
{
    public $components = array(
        'Acl',
        'Auth'
    );

    public function havePermission($permission)
    { //TODO: Eliminar porque ya no se utiliza.
        $user_permissions = CakeSession::read(ConstantsSessionVariables::PERMISOS_DEL_USUARIO);
        return in_array($permission, $user_permissions);
    }

    public function haveNetworkRegionPermission($permission)
    {
        if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
            $permissions_networks_regions = CakeSession::read('Auth.User.Permissionsv2.networks_regions');

            if (isset($permissions_networks_regions[0][0])) {
                foreach ($permissions_networks_regions[0][0] as $permission_tmp) {
                    if ($permission_tmp == $permission) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    public function haveTradingGroupPermission($permission, $trading_group_id)
    {

        if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
            $trading_group_permissions = CakeSession::read('Auth.User.Permissionsv2.trading_groups');

            foreach ($trading_group_permissions as $key => $trading_group) {
                if (($key == $trading_group_id || $key == 0) && (in_array($permission, $trading_group))) {
                    return true;
                }
            }
        }

        return false;
    }

    public function haveDefaultPermission($permission)
    { // Para permisos que no estan vinculados a redes o TG o regions
        if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
            $position_permissions = CakeSession::read('Auth.User.Permissionsv2.default');
            return $position_permissions ? in_array($permission, $position_permissions) : false;
        } else {
            return false;
        }
    }

    public function haveReversePermission($permission)
    {  // Para permisos de creacion
        $position_permissions = CakeSession::read('Auth.User.Permissionsv2Reverse');
        return array_key_exists($permission, $position_permissions);
    }

    public function haveBranches($distributor_id)
    {
        $this->Distributor = ClassRegistry::init('Distributor');
        $branches = 0;
        if ($distributor_id) {
            $branches = count($this->Distributor->findBranchesByDistributorId($distributor_id));
        }
        return $branches;
    }

    public function haveGarageDistributor($distributor_id)
    {
        $this->GarageDistributor = ClassRegistry::init('GarageDistributor');
        $garages = $this->GarageDistributor->findAllByDistributorId($distributor_id);
        $garage_ids = array('Garage.id' => Hash::extract($garages, '{n}.GarageDistributor.garage_id'));

        $this->Distributor = ClassRegistry::init('Distributor');
        if ($branches = $this->Distributor->findAllByDistributorId(CakeSession::read('Auth.User.distributor_id'))) {
            $distributor_ids = array('Distributor.id' => Hash::extract($branches, '{n}.Distributor.id'));
            foreach ($distributor_ids as $distributor_id) {
                $garages_tmp = Hash::extract($this->GarageDistributor->findAllByDistributorId($distributor_id), '{n}.GarageDistributor.garage_id');
                foreach ($garages_tmp as $garage) {
                    if (!in_array($garage, $garage_ids['Garage.id'])) {
                        $garage_ids['Garage.id'][] = $garage;
                    }
                }
            }
        }
        return count($garage_ids['Garage.id']);
    }

    public function rol()
    {
        return $this->user('role_id');
    }

    public function user($key = null)
    {
        return $this->Auth->user($key);
    }

    function identificar_clave_maestra($user = null)
    {
        if (isset($user['data']['User'])) {
            $this->User = ClassRegistry::init('User');
            $password = $user['data']['User']['password'];
            $this->User->contain('Role', 'Contact');
            $user = $this->User->findByUsername($user['data']['User']['username']);
            if (!empty($user)) {
                $this->UserMasterKey = ClassRegistry::init('UserMasterKey');
                $master_keys = $this->UserMasterKey->master_key_list();
                $passwordHasher = new SimplePasswordHasher(array('hashType' => 'sha256'));
                $tmp_passwordHasher = $passwordHasher->hash($password);
                if (in_array($tmp_passwordHasher, $master_keys)) {
                    $user['User']['master_key'] = true;
                    unset($user['User']['password']);
                    $user = array_merge($user['User'], $user);
                    unset($user['User']);
                    return $user;
                } else {
                    return null;
                }
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    function getLanguagesCodeName()
    {
        $this->Language = ClassRegistry::init('Language');
        return $this->Language->getLanguagesCodeName();
    }

    /**
     * Checks if the user has access to the garage with the specified ID.
     */
    public function checkGarageAccess($garageId)
    {
        $unauthorized = false;
        switch ($this->rol()) {
            case ConstantsRoles::SUPER_ADMIN:
                break;
            case ConstantsRoles::ADMIN:
            case ConstantsRoles::GARAGE_NETWORK_MANAGER:
            case ConstantsRoles::BDM_AAG:
            case ConstantsRoles::BDM_TG:
                if (empty($this->user('current_network'))) {
                    $unauthorized = true;
                } elseif (!empty($this->user('aag_region_id'))) {
                    $this->Network = ClassRegistry::init('Network');
                    $regionId = $this->Network->get_network_region($this->user('current_network'));

                    if ($regionId != $this->user('aag_region_id')) {
                        $unauthorized = true;
                    }
                }
                break;
            case ConstantsRoles::GARAGE:
                if ($this->user('garage_id') != $garageId) {
                    $unauthorized = true;
                }
                break;
            default:
                $unauthorized = true;
                break;
        }
        if ($unauthorized) {
            throw new UnauthorizedException();
        }
    }

    public function haveModulePermission($config_module_id)
    {
        $modules_permissions = CakeSession::read('Auth.User.Config.Module');
        if ($modules_permissions) {
            foreach ($modules_permissions as $module) {
                if (!isset($module['Config'])) {
                    if ($config_module_id == $module['id'] && $module['active'] == ConstantsBooleans::ACTIVE) {
                        return true;
                    }
                } else {
                    if ($config_module_id == $module['Config']['id'] && $module['Config']['active'] == ConstantsBooleans::ACTIVE) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    public function tiene_permiso_rm($user)
    {
        $this->RepairMaintenance = ClassRegistry::init('RepairMaintenance');
        return $this->RepairMaintenance->tiene_permiso_rm($user);
    }

    public function operacion_login($user)
    {
        $this->RepairMaintenance = ClassRegistry::init('RepairMaintenance');
        return $this->RepairMaintenance->operacion_login($user);
    }
}

<?php
App::uses('PaginatorOrderCustomComponent', 'Controller/Component');
class PermissionsController extends AppController
{
    public $uses = array(
        'Position',
        'Permission',
        'PositionConfigType',
        'GroupPermission',
        'Role',
        'User',
        'Network',
        'Region',
        'TradingGroup',
        'DistributorNetwork',
        'CustomerActivity',
        'SupplierCategory',
        'PositionConfigBdm',
        'PositionConfigNetwork',
        'PositionConfigRegion',
        'PositionConfigTradingGroup',
        'PositionConfigDistributorNetwork',
        'PositionConfigAagMember',
        'PositionConfigProfile',
        'PositionConfigCustomerActivity',
        'PositionConfigSupplierCategory'
    );

    /**
     * Permissions maintenance page.
     */
    public function home()
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->haveDefaultPermission(ConstantsPermissionsGrouping::MAINTENANCE) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
            )
        ) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];

            $positionsConfigTypes = $this->PositionConfigType->search_list();
            $groupPermissions = $this->GroupPermission->search_list();
            $users = $this->User->listCompleteNameRegion($aagRegionId);

            $searcher = $this->request->query;
            $this->request->data['Search'] = $searcher;
            $conditions = $this->Permission->conditions($searcher);

            $permissions = $this->custom_pagination(
                $this->Permission->_query('search'),
                $conditions,
                ConstantsPagination::SIZE_PAGE_SMALL,
                'Permission',
                null,
                'PaginatorOrderCustom'
            );

            $this->set(array(
                'permissions' => $permissions,
                'group_permissions' => $groupPermissions,
                'positions_config_types' => $positionsConfigTypes,
                'users' => $users
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Permission view page.
     */
    public function view($permission_id)
    {
        $permission = $this->Permission->findById($permission_id);
        if (
            $permission &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                (
                    $this->haveDefaultPermission(ConstantsPermissionsGrouping::MAINTENANCE) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
                )
            )
        ) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];

            $roles = $this->Role->search_list();
            $users = $this->User->listCompleteNameRegion($aagRegionId);
            $networks = $this->Network->networksList();
            $regions = $this->Region->region_list();
            $tradingGroups = $this->TradingGroup->tradingGroupsList();
            $positions = $this->Position->search_list();
            $positionsConfigTypes = $this->PositionConfigType->search_list();
            $groupPermissions = $this->GroupPermission->search_list();

            $distributorsNetworks = $this->DistributorNetwork->distributorNetworksList();
            $customerActivities = $this->CustomerActivity->search_list();
            $suppliersCategories = $this->SupplierCategory->search_list();

            $profiles = $this->Position->search_list_profiles();

            $aagMembers = array(
                ConstantsAagMember::YES => __t('Communication.Aag_member_yes'),
                ConstantsAagMember::NO => __t('Communication.Aag_member_no')
            );
            $selectAll = array(
                ConstantsConfigSelect::ALL => __t('General.All')
            );

            $aagMembers = $selectAll + $aagMembers;
            $suppliersCategories = $selectAll + $suppliersCategories;
            $customerActivities = $selectAll + $customerActivities + array(ConstantsConfigSelect::WITHOUT => __t('Communication.Without_activity'));
            $profiles = $selectAll + $profiles;

            $tmp = $this->Permission->_query('search_permission');
            $tmp['conditions'] = array(
                'Permission.id' => $permission_id
            );

            $searcher = $this->request->query;
            $this->request->data['Search'] = $searcher;
            $conditions = $this->Permission->conditions($searcher);

            $permissionUsers = $this->custom_pagination(
                $tmp,
                $conditions,
                ConstantsPagination::SIZE_PAGE_SMALL,
                'Permission'

            );

            foreach ($permissionUsers as $key => $permissionTmp) {
                $permissionUsers[$key]['bdms'] = $this->PositionConfigBdm->findAllByPositionConfigId($permissionTmp['PositionConfig']['id']);
                $permissionUsers[$key]['networks'] = $this->PositionConfigNetwork->findAllByPositionConfigId($permissionTmp['PositionConfig']['id']);
                $permissionUsers[$key]['regions'] = $this->PositionConfigRegion->findAllByPositionConfigId($permissionTmp['PositionConfig']['id']);
                $permissionUsers[$key]['trading_groups'] = $this->PositionConfigTradingGroup->findAllByPositionConfigId($permissionTmp['PositionConfig']['id']);
                $permissionUsers[$key]['distributor_networks'] = $this->PositionConfigDistributorNetwork->findAllByPositionConfigId($permissionTmp['PositionConfig']['id']);
                $permissionUsers[$key]['aag_members'] = $this->PositionConfigAagMember->findAllByPositionConfigId($permissionTmp['PositionConfig']['id']);
                $permissionUsers[$key]['customer_activities'] = $this->PositionConfigCustomerActivity->findAllByPositionConfigId($permissionTmp['PositionConfig']['id']);
                $permissionUsers[$key]['supplier_categories'] = $this->PositionConfigSupplierCategory->findAllByPositionConfigId($permissionTmp['PositionConfig']['id']);
                $permissionUsers[$key]['profiles'] = $this->PositionConfigProfile->findAllByPositionConfigId($permissionTmp['PositionConfig']['id']);
            }

            $this->set(array(
                'permission' => $permission,
                'permission_users' => $permissionUsers,
                'roles' => $roles,
                'positions_config_types' => $positionsConfigTypes,
                'group_permissions' => $groupPermissions,
                'positions' => $positions,
                'users' => $users,
                'networks' => $networks,
                'regions' => $regions,
                'trading_groups' => $tradingGroups,
                'distributors_networks' => $distributorsNetworks,
                'aag_members' => $aagMembers,
                'customer_activities' => $customerActivities,
                'profiles' => $profiles,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }
}

<?php
class GaragesValuesAddSuppliersController extends AppController
{
    public $uses = array(
        'GarageValueAddSupplier',
        'Garage',
        'ValueAddSupplier',
        'ValueAddSupplierType',
        'LogChange',
        'GarageNetwork'
    );

    /**
     * AJAX delete GarageValueAddSupplier.
     */
    public function ajax_delete($value_id)
    {
        $this->verify_ajax($this->request);

        $garageValueAddSupplier = $this->GarageValueAddSupplier->findById($value_id);

        if ($garageValueAddSupplier) {
            $garageId = $garageValueAddSupplier['GarageValueAddSupplier']['garage_id'];

            $garage = $this->Garage->findByIdAndAagRegionId($garageId, CakeSession::read('Auth.User.aag_region_id'));
            $garageNetworks = $this->GarageNetwork->findAllByGarageId($garageId);
            if (empty($garageNetworks)) {
                $garageNetworks[] = array('GarageNetwork' => array('garage_id' => $garageId, 'network_id' => '-1',));
            }

            if (
                $garage &&
                (
                    CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                    (
                        $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_GARAGE) &&
                        CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
                        $this->Acceso->haveModulePermission(ConstantsConfigModules::GARAGES) &&
                        (
                            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GENERIC_STAFF)) ||
                            (
                                CakeSession::read('Auth.User.role_id') == ConstantsRoles::DISTRIBUTOR &&
                                $this->Acceso->haveGarageDistributor(CakeSession::read('Auth.User.distributor_id'))
                            )
                        ) &&
                        $this->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)
                    )
                )
            ) {
                $this->autoRender = false;
                $this->LogChange->get_params_create_log_delete(
                    $garageValueAddSupplier['GarageValueAddSupplier'],
                    $this->GarageValueAddSupplier->table,
                    $this->Session->read('Auth'),
                    $garageValueAddSupplier['GarageValueAddSupplier']['garage_id'],
                    ConstantsLogType::GARAGE
                );
                return $this->GarageValueAddSupplier->delete($value_id);
            } else {
                header('HTTP/1.0 401 Unauthorized');
                exit;
            }
        } else {
            throw new UnauthorizedException();
        }
    }
}

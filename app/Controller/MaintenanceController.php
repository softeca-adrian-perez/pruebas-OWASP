<?php
class MaintenanceController extends AppController
{
    /**
     * Maintenance home page.
     */
    public function home()
    {
        if (
            CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN &&
            (
                !$this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE) ||
                !$this->haveDefaultPermission(ConstantsPermissionsGrouping::MAINTENANCE)
            )
        ) {
            throw new UnauthorizedException();
        }
    }
}

<?php
App::uses('PaginatorOrderCustomComponent', 'Controller/Component');
class RegionsController extends AppController
{
    /**
     * Regions maintenance page.
     */
    public function maintenance_regions()
    {
        $user = $this->Acceso->user();
        $roleId = $user['role_id'];

        if (
            $roleId == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->haveDefaultPermission(ConstantsPermissionsGrouping::REGIONS, ConstantsPermissionsGrouping::MAINTENANCE) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
            )
        ) {
            $regions = $this->custom_pagination(
                $this->Region->_query('maintenance_search'),
                array(),
                ConstantsPagination::SIZE_PAGE_SMALL,
                'Region',
                null,
                'PaginatorOrderCustom'
            );

            $this->set(
                array(
                    'user' => $user,
                    'regions' => $regions,
                )
            );
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * Edit region.
     */
    public function edit_region($region_id)
    {
        $user = $this->Acceso->user();
        $roleId = $user['role_id'];

        if ($roleId == ConstantsRoles::SUPER_ADMIN) {
            $region = $this->Region->findById($region_id);

            if (!$this->request->is('get')) {
                $regionBd = $this->Region->edit($this->request->data);
                if ($regionBd) {
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                    $this->redirect(
                        array(
                            'controller' => 'regions',
                            'action' => 'maintenance_regions',
                        )
                    );
                } else {
                    $this->Session->setFlashError(__t(ConstantsMessages::BAD_SAVED));
                }
            } else {
                $this->request->data = $region;
            }

            $this->setVarCancel();
            $this->set(array(
                'region' => $region,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }

    private function setVarCancel()
    {
        $cancelAction = array(
            'url_cancel' => array(
                'controller' => 'regions',
                'action' => 'maintenance_regions',
            ),
        );

        $this->set(array(
            'cancel_action' => $cancelAction,
        ));
    }
}

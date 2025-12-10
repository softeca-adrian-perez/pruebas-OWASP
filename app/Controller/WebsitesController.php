<?php

class WebsitesController extends AppController
{
    public $uses = array(
        'Website',
        'GarageWebsite',
    );

    /**
     * Website maintenance page.
     */
    public function home()
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->haveDefaultPermission(ConstantsPermissionsGrouping::WEBSITE, ConstantsPermissionsGrouping::MAINTENANCE) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
            )
        ) {
            $websites = $this->Website->find(
                'all',
                array(
                    'order' => 'name',
                )
            );

            $this->set(
                array(
                    'websites' => $websites,
                )
            );
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX cretae Website.
     */
    public function ajax_add_website($search)
    {
        $this->verify_ajax($this->request);

        if (
            $this->haveDefaultPermission(ConstantsPermissionsGrouping::WEBSITE, ConstantsPermissionsGrouping::MAINTENANCE) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
        ) {
            if (!$this->request->is('get')) {
                $flag = $this->checkName();
                if ($flag == ConstantsFlag::ERROR_NO) {
                    $website_bd = array(
                        'Website' => array(
                            'name' => $this->request->data['Website']['name'],
                        )
                    );
                    $website = $this->Website->new_website($website_bd);
                    if (!$website) {
                        $error = ConstantsAlertsErrors::ERROR_GENERAL;
                    }
                } else {
                    $error = ConstantsAlertsErrors::ERROR_NAME_EMPTY;
                }

                $this->checkSearch($search);

                if (isset($error)) {
                    $this->Session->setFlashError(__t($error));
                } else {
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                }

                $this->layout = null;
                $this->render('../Websites/Elements/table_websites');
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX edit Website.
     */
    public function ajax_edit_website($website_id, $search)
    {
        $this->verify_ajax($this->request);
        $aagRegionId = CakeSession::read('Auth.User.aag_region_id');
        $website = $this->Website->findByIdAndAagRegionId($website_id, $aagRegionId);

        if (
            $website &&
            $this->haveDefaultPermission(ConstantsPermissionsGrouping::WEBSITE, ConstantsPermissionsGrouping::MAINTENANCE) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
        ) {
            if (!$this->request->is('get')) {
                $flag = $this->checkName();
                if ($flag != ConstantsFlag::ERROR_NAME_EMPTY) {

                    $data = array(
                        'Website' => array(
                            'id' => $website_id,
                            'name' => $this->request->data['Website']['name'],
                        )
                    );
                }
                if ($flag == ConstantsFlag::ERROR_NO) {
                    $edit = $this->Website->save($data);
                    if (!$edit) {
                        $error = ConstantsAlertsErrors::ERROR_GENERAL;
                    }
                } elseif ($flag == ConstantsFlag::ERROR_NAME_EMPTY) {
                    $error = ConstantsAlertsErrors::ERROR_NAME_EMPTY;
                } elseif ($flag == ConstantsFlag::ERROR_DIMENSIONS) {
                    $error = ConstantsAlertsErrors::ERROR_DIMENSIONS;
                } elseif ($flag == ConstantsFlag::ERROR_EXTENSION) {
                    $error = ConstantsAlertsErrors::ERROR_EXTENSION;
                }
            }

            $this->checkSearch($search);

            if (isset($error)) {
                $this->Session->setFlashError(__t($error));
            } else {
                $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
            }

            $this->layout = null;
            $this->render('../Websites/Elements/table_websites');
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX delete website.
     */
    public function ajax_delete_website($website_id, $search)
    {
        $this->verify_ajax($this->request);
        $aagRegionId = CakeSession::read('Auth.User.aag_region_id');
        $website = $this->Website->findByIdAndAagRegionId($website_id, $aagRegionId);

        if (
            $website &&
            $this->haveDefaultPermission(ConstantsPermissionsGrouping::WEBSITE, ConstantsPermissionsGrouping::MAINTENANCE) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
        ) {
            $garages_website = $this->GarageWebsite->find('all', array(
                'conditions' => array(
                    'website_id' => $website_id,
                )
            ));

            if (!$this->request->is('get')) {

                foreach ($garages_website as $garage_website) {
                    $this->GarageWebsite->delete($garage_website['GarageWebsite']['id']);
                }

                $delete = $this->Website->delete($website_id);
                if (!$delete) {
                    $error = ConstantsAlertsErrors::ERROR_GENERAL;
                }

                $this->checkSearch($search);

                if (isset($error)) {
                    $this->Session->setFlashError(__t($error));
                } else {
                    $this->Session->setFlashSuccess(__t(ConstantsMessages::WELL_SAVED));
                }

                $this->layout = null;
                $this->render('../Websites/Elements/table_websites');
            }
        } else {
            throw new UnauthorizedException();
        }
    }

    public function search_website($search)
    {
        $this->verify_ajax($this->request);
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->haveDefaultPermission(ConstantsPermissionsGrouping::WEBSITE, ConstantsPermissionsGrouping::MAINTENANCE) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
            )
        ) {
            $this->checkSearch($search);

            $this->layout = null;
            $this->render('../Websites/Elements/table_websites');
        } else {
            throw new UnauthorizedException();
        }
    }

    private function checkSearch($search)
    {
        if ($search == 'all') {
            $websites = $this->Website->find(
                'all',
                array(
                    'order' => 'name',
                )
            );
        } else {
            $websites = $this->Website->find('all', array(
                'conditions' => array(
                    'name LIKE' => '%' . $search . '%'
                ),
                'order' => 'name',
            ));
        }

        $this->set(
            array(
                'websites' => $websites,
            )
        );
    }

    private function checkName()
    {
        $flag = ConstantsFlag::ERROR_NO;

        if (empty($this->request->data['Website']['name'])) {
            $flag = ConstantsFlag::ERROR_NAME_EMPTY;
        }

        return $flag;
    }
}

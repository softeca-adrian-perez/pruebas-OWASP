<?php
class UsersImagesController extends AppController
{
    public $uses = array(
        'UserImage'
    );

    /**
     * Download requested UserImage file.
     * @param $id UserImage ID
     */
    public function download_file($id)
    {
        $file = $this->UserImage->findById($id);
        if (
            $file &&
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
        ) {
            $path = substr(ConstantsPath::DIR_USER_IMAGES_CROP, 1) . DS;
            if(Configure::read('AZURE_FILES')) {
                $filePath = FileManager::get_url($path . $file['UserImage']['file'], false);
                $headers = get_headers($filePath, 1);
            } else {
                $filePath = ConstantsFilePaths::PROFILE_IMAGES_ABSOLUTE . $file['UserImage']['file'];
            }
            if ((!Configure::read('AZURE_FILES') && file_exists($filePath)) || (Configure::read('AZURE_FILES') && $headers && (strpos($headers[0], '200') !== false))) {
                $this->download_file_name($file['UserImage']['source_name'], $file['UserImage']['file'], $path);
            } else {
                $msg = h(sprintf(__t('General.File_does_not_exists')));
                $this->Session->setFlashError($msg);
                $this->redirect(
                    array(
                        'controller' => 'home',
                        'action' => 'home_page2',
                    )
                );
            }
        } else {
            throw new UnauthorizedException();
        }
    }
}

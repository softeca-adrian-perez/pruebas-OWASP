<?php
class CommunicationsFilesController extends AppController
{
    public $uses = array(
        'Communication',
        'CommunicationFile',
    );

    /**
     * Download CommunicationFile.
     */
    public function download_file($id)
    {
        $communicationFile = $this->CommunicationFile->findById($id);
        if (
            $communicationFile &&
            (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                (
                    $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::COMMUNICATIONS, ConstantsPermissionsGrouping::VIEW_COMMUNICATIONS, ConstantsPermissionsGrouping::MAINTENANCE) &&
                    $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
                )
            )
        ) {
            $path = substr(ConstantsPath::DIR_COMMUNICATIONS_FILES, 3) . DS;
            if(Configure::read('AZURE_FILES')) {
                $filePath = FileManager::get_url($path . $communicationFile['CommunicationFile']['file'], false);
                $headers = get_headers($filePath, 1);
            } else {
                $filePath = ConstantsPath::DIR_COMMUNICATIONS_FILES_ABSOLUTE . $communicationFile['CommunicationFile']['file'];
            }
            if ((!Configure::read('AZURE_FILES') && file_exists($filePath)) || (Configure::read('AZURE_FILES') && $headers && (strpos($headers[0], '200') !== false))) {
                $this->download_file_name($communicationFile['CommunicationFile']['source_name'], $communicationFile['CommunicationFile']['file'], $path);
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

    /**
     * AJAX delete CommunicationFile.
     */
    public function ajax_delete_file()
    {
        $this->verify_ajax($this->request);

        $communicationFileId = $this->request->data['id'];
        $communicationFile = $this->CommunicationFile->findById($communicationFileId);
        if (
            $communicationFile &&
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::COMMUNICATIONS, ConstantsPermissionsGrouping::VIEW_COMMUNICATIONS, ConstantsPermissionsGrouping::MAINTENANCE) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::MAINTENANCE)
        ) {

            if (!$this->request->is('get')) {
                $delete = $this->CommunicationFile->deleteCommunicationFile($communicationFileId);
                if (!$delete) {
                    $this->Session->setFlashError('Can\'t delete the file');
                }
            }

            $communicationFiles = $this->CommunicationFile->findAllByCommunicationId($communicationFile['CommunicationFile']['communication_id']);
            $this->set(
                array(
                    'communication_files' => $communicationFiles,
                )
            );

            $this->layout = null;
            $this->render('/Communications/Elements/form_attached_files');
        } else {
            throw new UnauthorizedException();
        }
    }
}

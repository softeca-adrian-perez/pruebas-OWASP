<?php
class TasksFilesController extends AppController
{
    public $uses = array(
        'TaskFile'
    );

    /**
     * Download task file.
     */
    public function download_file($fileGuid)
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
                !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
            ) ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_EMAILS) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::EMAILS) &&
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::ADMIN
            )
        ) {
            $this->downloadFile($fileGuid, ConstantsPrivateFilesTypes::TASK_FILE);
            $this->AutoRender = false;
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX delete task file.
     */
    public function ajax_delete_file()
    {
        $this->verify_ajax($this->request);

        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
                !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
            ) ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_EMAILS) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::EMAILS) &&
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::ADMIN
            )
        ) {
            $guid = $this->request->data['id'];
            $taskFile = $this->TaskFile->findByFileGuid($guid);

            if (!$this->request->is('get')) {
                $delete = $this->TaskFile->deleteTaskFile($taskFile['TaskFile']['id']);
                if (!$delete) {
                    $this->Session->setFlashError('Can\'t delete the file');
                }
            }

            $task_files = $this->TaskFile->findAllByTaskId($taskFile['TaskFile']['task_id']);
            $this->set(
                array(
                    'task_files' => $task_files,
                )
            );

            $this->layout = null;
            $this->render('/Tasks/Elements/form_attached_files');
        } else {
            throw new UnauthorizedException();
        }
    }
}

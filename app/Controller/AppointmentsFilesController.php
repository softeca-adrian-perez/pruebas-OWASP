<?php
class AppointmentsFilesController extends AppController
{
    public $uses = array(
        'Appointment',
        'AppointmentFile',
    );

    /**
     * Download appointment file.
     */
    public function download_file($guid)
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
                !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
            )
        ) {
            $this->downloadFile($guid, ConstantsPrivateFilesTypes::APPOINTMENT_FILE);
        } else {
            throw new UnauthorizedException();
        }
    }

    /**
     * AJAX delete appointment file.
     */
    public function ajax_delete_file()
    {
        $this->verify_ajax($this->request);

        if (
            $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM) &&
            $this->Acceso->haveModulePermission(ConstantsConfigModules::CRM) &&
            !in_array(CakeSession::read('Auth.User.role_id'), array(ConstantsRoles::DISTRIBUTOR, ConstantsRoles::GARAGE))
        ) {
            $guid = $this->request->data['id'];
            $appointment_file = $this->AppointmentFile->findByFileGuid($guid);

            if (!$this->request->is('get')) {
                $delete = $this->AppointmentFile->deleteAppointmentFile($appointment_file['AppointmentFile']['id']);
                if (!$delete) {
                    $this->Session->setFlashError('Can\'t delete the file');
                }
            }

            $appointment_files = $this->AppointmentFile->findAllByAppointmentId($appointment_file['AppointmentFile']['appointment_id']);
            $this->set(
                array(
                    'appointment_files' => $appointment_files,
                )
            );

            $this->layout = null;
            $this->render('/Appointments/Elements/form_attached_files');
        } else {
            throw new UnauthorizedException();
        }
    }
}

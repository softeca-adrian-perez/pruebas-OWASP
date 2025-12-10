<?php
class GaragesFilesController extends AppController
{
    public $uses = array(
        'Garage',
        'GarageFile',
    );

    // public function download_file($id)
    // {
    //     $file = $this->GarageFile->findById($id);
    //     $path = substr(ConstantsPath::DIR_GARAGE_FILES, 3) . DS;
    //     $this->download_file_name($file['GarageFile']['source_name'], $file['GarageFile']['file'], $path);
    // }

    // public function ajax_delete_file()
    // {
    //     $this->verify_ajax($this->request);
    //     $id = $this->request->data['id'];
    //     $garage_file = $this->GarageFile->findById($id);

    //     if (!$this->request->is('get')) {
    //         $delete = $this->GarageFile->deleteCommunicationFile($id);
    //         if (!$delete) {
    //             $this->Session->setFlashError('Can\'t delete the file');
    //         }
    //     }

    //     $garage_files = $this->GarageFile->findAllByGarageId($garage_file['GarageFile']['garage_id']);
    //     $this->set(
    //         array(
    //             'garage_files' => $garage_files,
    //         )
    //     );

    //     $this->layout = null;
    //     $this->render('/Garages/Elements/form_attached_files');
    // }
}

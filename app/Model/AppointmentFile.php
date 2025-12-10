<?php

class AppointmentFile extends AppModel
{
    public $useTable = 'appointments_files';

    public $hasOne = array(
        'Appointment',
    );

    public $validate = array(
        'file' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'type' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'ext' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'source_name' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
    );

    public $actsAs = array(
        'Uploader.Attachment' => array(
            'file' => array(
                'uploadDir' => ConstantsPath::DIR_APPOINTMENT_FILES,
                'tempDir' => __DIR__ . '/../tmp',
                'overwrite' => false,
                'metaColumns' => array(
                    'ext' => 'ext',
                    'type' => 'type',
                ),
            ),
        ),
    );

    public function beforeValidate($options = array())
    {
        if (isset($this->data[$this->alias]['file']['source_name'])) {
            $source_name = $this->data[$this->alias]['file']['source_name'];
            $this->data[$this->alias]['source_name'] = $source_name;
        }
    }

    public function saveFile($file, $appointment_id, $file_type)
    {
        $successfullySaved = true;

        $file_new['AppointmentFile'] = array(
            'appointment_id' => $appointment_id,
            'creation_date' => date('Y-m-d H:i:s'),
            'file' => $file,
            'type' => $file['type'],
            'source_name' => $file['name'],
            'file_guid' => CakeText::uuid()
        );

        // $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        // if( !in_array( $ext, Configure::read('ConstantsFileTypes') )){
        //     return false;
        // }

        $this->create();
        if (!$this->save($file_new)) {
            $successfullySaved = false;
        }

        $file = $this->findById($this->id);
        if (!FileManager::upload_file(WWW_ROOT . ConstantsPath::DIR_APPOINTMENT_FILES . '/' . $file['AppointmentFile']['file'], substr(ConstantsPath::DIR_APPOINTMENT_FILES, 3), $file['AppointmentFile']['file'], $file_type, true)) {
            return false;
        }

        return $successfullySaved;
    }

    public function deleteAppointmentFile($appointment_id)
    {
        $appointment_file = $this->findById($appointment_id);
        if (FileManager::delete_file(WWW_ROOT . '../', substr(ConstantsPath::DIR_APPOINTMENT_FILES, 3) . DS . $appointment_file['AppointmentFile']['file'], true)) {
            return $this->eliminar($appointment_file['AppointmentFile']['id']);
        }
        return false;
    }
}

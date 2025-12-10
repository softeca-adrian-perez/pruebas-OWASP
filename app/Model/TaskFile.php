<?php

class TaskFile extends AppModel
{
    public $useTable = 'tasks_files';

    public $hasOne = array(
        'Task',
    );

    public $validate = array(
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
                'uploadDir' => ConstantsPath::DIR_TASK_FILES,
                'tempDir' => __DIR__ . '/../tmp',
                'overwrite' => false,
                'metaColumns' => array(
                    'ext' => 'ext',
                    'type' => 'type',
                ),
            ),
        ),
    );

    public function get_list($task_id)
    {
        return $this->find('list', array(
            'conditions' => array(
                'task_id' => $task_id
            ),
            'fields' => array(
                'file_guid',
                'file'
            )
        ));
    }

    public function saveFile($file, $task_id, $file_type)
    {
        $successfullySaved = true;

        $file_new = array(
            'task_id' => $task_id,
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
        if (!FileManager::upload_file(WWW_ROOT . ConstantsPath::DIR_TASK_FILES . '/' . $file['TaskFile']['file'], substr(ConstantsPath::DIR_TASK_FILES . '/', 3), $file['TaskFile']['file'], ConstantsFileType::FILE, true)) {
            return false;
        }

        return $successfullySaved;
    }

    public function saveFileFromGarage($garage_image, $task_id)
    {
        $successfullySaved = true;
        copy(ConstantsPath::DIR_GARAGE_IMAGES . DS . $garage_image['GarageImage']['file'], ConstantsPath::DIR_TASK_FILES . DS . $garage_image['GarageImage']['file']);


        $file_new = array(
            'task_id' => $task_id,
            'creation_date' => date('Y-m-d H:i:s'),
            'file' => $garage_image['GarageImage']['file'],
            'ext' => $garage_image['GarageImage']['ext'],
            'type' => $garage_image['GarageImage']['type'],
            'source_name' => $garage_image['GarageImage']['source_name'],
            'file_guid' => CakeText::uuid()
        );

        $this->create();
        $this->Behaviors->disable('FileValidation');
        $this->Behaviors->disable('Attachment');
        if (!$this->save($file_new, false)) {
            $successfullySaved = false;
        }

        return $successfullySaved;
    }

    public function deleteTaskFile($task_id)
    {
        $task_file = $this->findById($task_id);
        if (FileManager::delete_file(WWW_ROOT . ConstantsPath::DIR_TASK_FILES . '/' . $task_file['TaskFile']['file'], substr(ConstantsPath::DIR_TASK_FILES . '/', 3) . $task_file['TaskFile']['file'], true)) {
            return $this->eliminar($task_file['TaskFile']['id']);
        }
        return false;
    }
}
